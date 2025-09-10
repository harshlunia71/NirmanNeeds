<?php

namespace nn;

if (!defined('ABSPATH')) die;

class NNPL_I18N {
    public function load_plugin_textdomain(): void {
        load_plugin_textdomain(
            NN_PLUGIN_SLUG,
            false,
            NN_PLUGIN_PATH
        );
    }
}
