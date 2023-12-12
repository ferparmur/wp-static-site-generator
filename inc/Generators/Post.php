<?php

namespace Ferparmur\WpStaticSiteGenerator\Generators;

use Ferparmur\WpStaticSiteGenerator\Utilities\ResponseHandler;

class Post
{
    private string $realPermalink;

    public function init(): void
    {
        add_action('post_updated', [$this, 'generatePost'], 1, PHP_INT_MAX);
    }

    public function generatePost(int $postId): void
    {
        $permalink = get_post_permalink($postId);
        $response = new ResponseHandler($permalink);
        $response->fetch();
        $response->findAndReplace();
        $response->saveStaticFile();
    }
}
