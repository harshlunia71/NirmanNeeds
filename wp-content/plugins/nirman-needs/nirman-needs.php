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

use \NirmanNeeds\Database\DatabaseManager;

defined('ABSPATH') || die;

define("NIRMAN_NEEDS_SLUG", 'nirman-needs');
define("NIRMAN_NEEDS_VERSION", '0.1.0');
define("NIRMAN_NEEDS_PATH", __DIR__);
define("NIRMAN_NEEDS_FILE", __FILE__);
define("NIRMAN_NEEDS_URL", plugin_dir_url(__FILE__));
define("NIRMAN_NEEDS_TABLE_PREFIX", NIRMAN_NEEDS_SLUG . '_');

// Load Composer autoloader
require_once NIRMAN_NEEDS_PATH . '/vendor/autoload.php';

register_activation_hook(NIRMAN_NEEDS_FILE, function () {
    global $wpdb;
    $db = DatabaseManager::instance($wpdb);
});

add_action('plugins-loaded', function () {
    $plugin = new \NirmanNeeds\Plugin();
    $plugin->init();
});

