<?php

/**
 * Plugin Name: Nirman Needs Plugin
 * Description: General site customisation functionality
 * Version: 0.1.0
 * Author: Harsh Lunia
 * Text Domain: nn-plugin
 * Domain Path: /languages
 */

namespace nn;

if (!defined('ABSPATH')) die;

define("NN_PLUGIN_SLUG", 'nn-plugin');
define("NN_PLUGIN_VERSION", '0.1.0');
define("NN_PLUGIN_PATH", plugin_dir_path(__FILE__));
define("NN_PLUGIN_FILE", __FILE__);
define("NN_PLUGIN_URL", plugin_dir_url(__FILE__));

require_once NN_PLUGIN_PATH . 'includes/class-nn-plugin.php';
function run_plugin(): void {
    $plugin = new NNPL();
    $plugin->run();
}

run_plugin();

