<?php

namespace Ferparmur\WpStaticSiteGenerator\Generators;

use Ferparmur\WpStaticSiteGenerator\Utilities\HtmlPage;

class Post
{
    public function init(): void
    {
        add_action('post_updated', [$this, 'generatePost'], 1, PHP_INT_MAX);
    }

    public function generatePost(int $postId)
    {
        $html = new HtmlPage($this->getPostHtmlViaURL($postId));
        $html->findAndReplace();

        echo $html->getHtml();
        die();
    }

    private function getPostHtmlViaURL(int $postId): string
    {
        add_filter('https_ssl_verify', '__return_false');
        $permalink = get_post_permalink($postId);
        $response = wp_remote_get($permalink);

        return wp_remote_retrieve_body($response);
    }

    private function findAndReplace(string $postHtml): string
    {
        return str_replace('https://wp.htg.local', 'https://www.htg.local', $postHtml);
    }
}
