<?php

namespace Ferparmur\WpStaticSiteGenerator\Utilities;

use Ferparmur\WpStaticSiteGenerator\Config;
use WP_HTTP_Requests_Response;

class ResponseHandler
{
    private string $permalink;
    private string $realPermalink;

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
        $verboseResponse = $httpResponse->get_response_object();

        $this->realPermalink = $verboseResponse->url;
        $this->body = wp_remote_retrieve_body($response);
    }

    public function findAndReplace()
    {
        $this->body = str_replace('https://wp.htg.local', 'https://www.htg.local', $this->body);
    }

    public function getStaticFilePath(): string
    {
        $path = untrailingslashit(str_replace('https://wp.htg.local', '', $this->realPermalink));
        $pathinfo = pathinfo($path);
        $relativeStaticPath = isset($pathinfo['extension']) ? $path : trailingslashit($path) . 'index.html';

        return trailingslashit($this->config->getSetting('local_deployment_dir')) . ltrim($relativeStaticPath, '/');
    }

    public function saveStaticFile(): void
    {
        $dirname = pathinfo($this->getStaticFilePath())['dirname'];

        if ( ! is_dir($dirname)) {
            mkdir($dirname);
        }
        file_put_contents($this->getStaticFilePath(), $this->body);
    }
}
