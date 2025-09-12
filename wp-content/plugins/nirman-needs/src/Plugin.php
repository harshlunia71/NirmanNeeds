<?php

namespace NirmanNeeds;

use \NirmanNeeds\Admin\Settings;

defined('ABSPATH') || die;

class Plugin {

    public function __construct() 
    {
    }

    public function init(): void
    {
        if (is_admin()) {
            $admin_settings = Settings();
            $admin_settings->init();
        } 
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
