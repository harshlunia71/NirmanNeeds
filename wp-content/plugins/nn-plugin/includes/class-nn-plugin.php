<?php

namespace nn;

if (!defined('ABSPATH')) die;

class NNPL {
    protected $version;
    protected $plugin_name;
    protected $loader;

    public function __construct() 
    {
        $this->version = NN_PLUGIN_VERSION ?? '';
        $this->plugin_name = "nn-plugin";

        $this->load_dependencies();
        if (is_admin()) {
            $this->define_admin_hooks();
        } else {
            $this->define_public_hooks();
        }
    }

    public function load_dependencies(): void 
    {
        require_once NN_PLUGIN_PATH . 'includes/class-nn-plugin-loader.php';
        require_once NN_PLUGIN_PATH . 'includes/class-nn-plugin-i18n.php';
        require_once NN_PLUGIN_PATH . 'includes/class-nn-plugin-admin.php';
        require_once NN_PLUGIN_PATH . 'includes/class-nn-plugin-public.php';

        $this->loader = new NNPL_Loader();
    }

    public function set_locale(): void 
    {
        $plugin_i18n = new NNPL_I18N();

        $this->loader->add_action('plugins_loaded', $plugin_i18n, 'load_plugin_textdomain');
    }

    public function define_admin_hooks(): void 
    {
        $plugin_admin = new NNPL_Admin($this->plugin_name, $this->version);
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');
    }

    public function define_public_hooks(): void 
    {
        $plugin_public = new NNPL_Public($this->plugin_name, $this->version);
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this>loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    public function run(): void {
        $this->loader->run();
    }

    public function get_version(): string 
    {
        return $this->version;
    }

    public function get_loader(): string 
    {
        return $this->loader;
    }

    public function get_plugin_name(): string 
    {
        return $this->plugin_name;
    }
}
