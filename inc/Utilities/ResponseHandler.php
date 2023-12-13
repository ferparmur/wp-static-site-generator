<?php

namespace Ferparmur\WpStaticSiteGenerator\Utilities;

use Ferparmur\WpStaticSiteGenerator\Config;
use JetBrains\PhpStorm\ArrayShape;
use voku\helper\HtmlDomParser;
use WP_HTTP_Requests_Response;
use WpOrg\Requests\Response;

use const Ferparmur\WpStaticSiteGenerator\DATA_DIR;

class ResponseHandler
{
    private string $permalink;
    private string $realPermalink;
    private string $mimeType;
    private array $internalUrls;
    private array $internalAssetUrls;
    private HtmlDomParser $dom;
    private Response $response;

    private string $body;

    private Config $config;

    public function __construct(string $permalink)
    {
        $this->permalink = $permalink;
        $this->config = Config::getInstance();
    }

    public function fetch(): void
    {
        add_filter('https_ssl_verify', '__return_false');
        $response = wp_remote_get($this->permalink);

        /** @var WP_HTTP_Requests_Response $httpResponse */
        $httpResponse = $response['http_response'];
        $this->response = $httpResponse->get_response_object();
        $headers = $this->response->headers;

        $this->realPermalink = $this->response->url;
        $this->mimeType = isset($headers->getValues('content-teype')[0]) ? strtok($headers->getValues('content-type')[0],
            ';') : 'text/html';
        $this->body = wp_remote_retrieve_body($response);
    }

    public function getHttpStatus(): int
    {
        return $this->response->status_code;
    }

    public function findAndReplace()
    {
        $this->body = str_replace('https://wp.htg.local', 'https://www.htg.local', $this->body);
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getStaticFilePath(): string
    {
        $path = untrailingslashit(str_replace(site_url(), '', $this->realPermalink));
        $pathinfo = pathinfo($path);
        $relativeStaticPath = isset($pathinfo['extension']) ? $path : trailingslashit($path) . 'index.html';

        return trailingslashit($this->config->getSettingValue('local_deployment_dir')) . ltrim($relativeStaticPath,
                '/');
    }

    public function saveStaticFile(): void
    {
        $dirname = pathinfo($this->getStaticFilePath())['dirname'];

        $this->makeDirPath($dirname);
        file_put_contents($this->getStaticFilePath(), $this->body);
    }

    private function makeDirPath($path): void
    {
        file_exists($path) || mkdir($path, 0777, true);
    }

    public function loadLinkedInternalUrls(): void
    {
        $internalUrls = [];
        $internalAssetUrls = [];

        if ($this->isHtml()) {
            $this->loadDom();

            if ( ! isset($this->dom)) {
                return;
            }

            $htmlUrls = $this->extractLinkedInternalUrlsFromHtml($this->dom);

            $htmlInternalUrls = $htmlUrls['internalUrls'] ?? [];
            $htmlInternalAssetUrls = $htmlUrls['internalAssetUrls'] ?? [];

            //Look at stuff in embedded css
            $combinedCss = '';
            $inlineCssSnippets = $this->dom->find('style');
            foreach ($inlineCssSnippets as $inlineCssSnippet) {
                $combinedCss .= $inlineCssSnippet->innerhtml;
            }

            $cssUrls = $this->extractLinkedInternalUrlsFromCss($combinedCss);
            $cssInternalUrls = $cssUrls['internalAssetUrls'];
            $cssInternalAssetUrls = $cssUrls['internalAssetUrls'];

            $internalUrls = array_merge($htmlInternalUrls, $cssInternalUrls);
            $internalAssetUrls = array_merge($htmlInternalAssetUrls, $cssInternalAssetUrls);
        }

        $this->internalUrls = $internalUrls;
        $this->internalAssetUrls = $internalAssetUrls;
    }

    #[ArrayShape([
        'internalUrls' => "array",
        'internalAssetUrls' => "array",
    ])] private function extractLinkedInternalUrlsFromHtml(HtmlDomParser $dom): array
    {
        $tags = include DATA_DIR . 'linkTags.php';

        $urls = [];

        foreach ($tags as $tag => $attributes) {
            $nodes = $dom->find($tag);
            foreach ($nodes as $node) {
                foreach ($attributes as $attribute) {
                    if ($node->hasAttribute($attribute)) {
                        $urls[] = $node->getAttribute($attribute);
                    }
                }
            }
        }


        return $this->getInternalAndInternalAssetUrlsFromUrlSet($urls);
    }

    #[ArrayShape([
        'internalUrls' => "array",
        'internalAssetUrls' => "array",
    ])] public function extractLinkedInternalUrlsFromCss(string $css): array
    {
        preg_match_all('/url\((.*?)\)/', $css, $matches);

        return $this->getInternalAndInternalAssetUrlsFromUrlSet($matches[1]);
    }

    #[ArrayShape([
        'internalUrls' => "array",
        'internalAssetUrls' => "array",
    ])] public function getInternalAndInternalAssetUrlsFromUrlSet(array $urls): array
    {
        $siteHost = parse_url(site_url())['host'];
        $internalUrls = [];
        $internalAssetUrls = [];

        foreach ($urls as $url) {
            $urlParts = parse_url($url);

            if ( ! isset($urlParts['host']) || $urlParts['host'] === $siteHost) {
                $internalUrls[] = strtok($url, '?');

                if (
                    isset(pathinfo($urlParts['path'])['extension'])
                    && in_array(pathinfo($urlParts['path'])['extension'], include DATA_DIR . 'assetExtensions.php')
                ) {
                    $internalAssetUrls[] = strtok($url, '?');
                }
            }
        }

        return [
            'internalUrls' => $internalUrls,
            'internalAssetUrls' => $internalAssetUrls,
        ];
    }

    public function saveInternalAssets(bool $recursive = true): void
    {
        foreach ($this->internalAssetUrls as $internalAssetUrl) {
            $handler = new self($internalAssetUrl);
            $handler->fetch();
            if ($handler->getHttpStatus() === 200) {
                $handler->loadLinkedInternalUrls();
                if ($recursive) {
                    $handler->saveInternalAssets(true);
                }
                $handler->findAndReplace();
                $handler->saveStaticFile();
            }
        }
    }

    private function loadDom(): void
    {
        if ( ! isset($this->dom) && $this->isHtml()) {
            $this->dom = HtmlDomParser::str_get_html($this->body);
        }
    }

    private function isHtml(): bool
    {
        return $this->mimeType === 'text/html';
    }

    private function isCss(): bool
    {
        return $this->mimeType === 'text/css';
    }
}
