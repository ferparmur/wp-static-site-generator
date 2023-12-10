<?php
/**
 * Plugin Name: Static Site Generator for WordPress
 */

namespace Ferparmur\WpStaticSiteGenerator;


//Load Composer
require __DIR__ . '/vendor/autoload.php';

const VERSION = '1.0.0';
const PLUGIN_ROOT = __DIR__ . '/';
const PLUGIN_FILE = __FILE__;
const ASSET_DIR = PLUGIN_ROOT . 'assets/';
const TEMPLATE_DIR = PLUGIN_ROOT . 'templates/';

define('Carbon_Fields\URL', plugin_dir_url(PLUGIN_FILE) . '/vendor/htmlburger/carbon-fields');

$plugin = new Plugin();
$plugin->init();
