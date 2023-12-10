<?php

namespace Ferparmur\WpStaticSiteGenerator\Admin;

use Carbon_Fields\Carbon_Fields;
use Carbon_Fields\Container as Carb;
use Carbon_Fields\Field;

use const Ferparmur\WpStaticSiteGenerator\ASSET_DIR;

class Menu
{

    public function init(): void
    {
        add_action('admin_menu', [$this, 'createMainMenuPage']);
        add_action('carbon_fields_register_fields', [$this, 'createSettingsPage']);
        add_action('after_setup_theme', [$this, 'bootCarbonFields']);
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

    public function createSettingsPage(): void
    {
        Carb::make('theme_options', __('WPSSG Settings', 'wpssg'))
            ->set_page_parent('wpssg') // reference to a top level container
            ->set_page_menu_title('Settings') // reference to a top level container
            ->set_page_file('wpssg-settings')
            ->add_tab(__('General', 'wpssg'), [
                Field::make('text', 'wpssg_static_site_url', __('Static Site URL', 'wpssg'))
                     ->set_help_text(sprintf(
                         __('This will replace references to the WordPress site URL (%1$s) in your static site',
                             'wpssg'),
                         get_site_url()
                     ))
                     ->set_attributes([
                         'placeholder' => __('https://www.example.com', 'wpssg'),
                     ]),
            ])
            ->add_tab(__('Deployment'), [
                Field::make('select', 'wpssg_deployment_method', __('Deployment Method', 'wpssg'))
                     ->set_options([
                         'local' => __('Local Directory', 'wpssg'),
                     ]),

                Field::make('text', 'wpssg_local_deployment_dir', __('Local Directory Path', 'wpssg'))
                     ->set_help_text(sprintf(
                         __('The directory where your static site will be saved. As a reference, your WordPress site path is: %1$s',
                             'wpssg'),
                         ABSPATH
                     ))
                     ->set_attributes([
                         'placeholder' => __(ABSPATH, 'wpssg'),
                     ]),
            ])
            ->add_tab(__('Advanced Options'), [
                Field::make('checkbox', 'wpssg_disable_ssl_verify', __('Disable SSL Verify', 'wpssg'))
                     ->set_help_text(__('A common way to get around the “cURL error 60” issue on local environments. Must not be used in production.',
                         'wpssg'),
                     ),
            ]);
    }

    public function bootCarbonFields(): void
    {
        Carbon_Fields::boot();
    }

    private function getSvqUri(string $svg): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(file_get_contents(ASSET_DIR . 'svg/' . $svg . '.svg'));
    }
}
