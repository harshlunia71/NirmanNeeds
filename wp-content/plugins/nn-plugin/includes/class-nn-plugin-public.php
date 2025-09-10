<?php

namespace nn;

if (!defined('ABSPATH')) die;

class NNPL_Public {
    protected $plugin_name;
    protected $version;

    public function __construct(string $plugin_name, string $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles():void 
    {
        
    }

    public function enqueue_scripts():void
    {
    
    }
}
