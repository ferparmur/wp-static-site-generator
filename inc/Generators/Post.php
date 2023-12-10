<?php

namespace Ferparmur\WpStaticSiteGenerator\Generators;

use Ferparmur\WpStaticSiteGenerator\Utilities\FakeScreen;
use Ferparmur\WpStaticSiteGenerator\Utilities\HtmlPage;
use WP_Query;
use WP_Scripts;
use WP_User;

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

    private function getPostHtml(int $postId): string
    {
        global $wp_query;
        $wp_query = new WP_Query(['p' => $postId]);

        $this->fakeLogOut();

        ob_start();

        include apply_filters('template_include', get_single_template());
        $postHtml = ob_get_contents();
        ob_end_clean();

        return $postHtml;
    }

    public function fakeLogOut(): void
    {
        global $current_user;
        global $wp_admin_bar;
        global $show_admin_bar;
        global $pagenow;
        global $hook_suffix;
        global $wp_scripts;

        $current_user = new WP_User();
        $wp_admin_bar = null;
        $show_admin_bar = false;

        $fakeScreen = new FakeScreen();
        $GLOBALS['current_screen'] = $fakeScreen;

        $pagenow = 'index.php';
        $hook_suffix = null;

        $wp_scripts = new WP_Scripts();
        wp_default_scripts($wp_scripts);

        wp_dequeue_style('admin-bar');
    }

    private function findAndReplace(string $postHtml): string
    {
        return str_replace('https://wp.htg.local', 'https://www.htg.local', $postHtml);
    }
}
