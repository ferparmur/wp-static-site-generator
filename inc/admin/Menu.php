<?php

namespace Ferparmur\WpStaticSiteGenerator\Admin;

use const Ferparmur\WpStaticSiteGenerator\ASSET_DIR;

class Menu
{

    public function init(): void
    {
        add_action('admin_menu', [$this, 'createMainMenuPage']);
    }

    public function createMainMenuPage(): void
    {
        add_menu_page(
            __('WP Static Site', 'wpssg'),
            __('WP Static Site', 'wpssg'),
            'manage_options',
            'wpssg',
            false,
            $this->getSvqUri('logo'),
            80
        );

        add_submenu_page(
            'wpssg',
            __('WordPress Static Site Generator', 'wpssg'),
            __('Generate Site', 'wpssg'),
            'manage_options',
            'wpssg',
            function () {
                echo '';
            });
    }

    private function getSvqUri(string $svg): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(ASSET_DIR . 'svg/' . $svg . '.svg'));
    }
}
