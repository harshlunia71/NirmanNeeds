<?php

if (!isset($_ENV["CONFIG_PATH"])) {
    echo 'Bad path to config'; 
    die;
}

$CONFIG_PATH = $_ENV["CONFIG_PATH"];
$options_map = json_decode(file_get_contents($CONFIG_PATH . '/wp-options.json'), true);

foreach ($options_map as $option_name => $env_var) {
    if (isset($_ENV[$env_var])) {
        $value = $_ENV[$env_var];
        update_option($option_name, $value);
        //echo "Updated $option_name from $env_var\n";
    } else {
        echo "Env var $env_var not set, skipped $option_name\n";
    }
}

