<?php

namespace NirmanNeeds;

use \NirmanNeeds\Admin\Settings;
use \NirmanNeeds\Admin\ProductFields;
use \NirmanNeeds\Rest\ProjectedPricesController;

defined('ABSPATH') || die;

class Plugin {

    public function __construct() 
    {
    }

    public function init(): void
    {
        if (is_admin()) {
            $admin_settings = new Settings();
            $admin_settings->init();
            
            // Initialize product fields functionality
            $product_fields = new ProductFields();
            $product_fields->init();
        } 

        add_action('rest_api_init', function() {
            $controller = new ProjectedPricesController();
            $controller->register_routes();
        });
    } 

    public function get_version(): string 
    {
        return $this->version;
    }

    public function get_plugin_name(): string 
    {
        return $this->plugin_name;
    }
}
