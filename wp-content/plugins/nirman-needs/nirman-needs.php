<?php

/**
 * Plugin Name: Nirman Needs Plugin
 * Description: General site customisation functionality
 * Version: 0.1.0
 * Author: Harsh Lunia
 * Text Domain: nirman-needs
 * Domain Path: /languages
 */

namespace NirmanNeeds;

defined('ABSPATH') || die;

define("NIRMAN_NEEDS_SLUG", 'nirman-needs');
define("NIRMAN_NEEDS_VERSION", '0.1.0');
define("NIRMAN_NEEDS_PATH", __DIR__);
define("NIRMAN_NEEDS_FILE", __FILE__);
define("NIRMAN_NEEDS_URL", plugin_dir_url(__FILE__));

// Load Composer autoloader
require_once NIRMAN_NEEDS_PATH . '/vendor/autoload.php';

add_action('plugins-loaded', function () {
    $plugin = new \NirmanNeeds\Plugin();
    $plugin->init();
});

