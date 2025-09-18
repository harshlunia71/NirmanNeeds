<?php

namespace NirmanNeeds\Database;

use \NirmanNeeds\Database\ProductDataTable;

defined('ABSPATH') || die;

class DatabaseManager {
    
    private static $instance = null;

    /** 
     * @var DatabaseTable[];
     */
    private $tables;

    /**
     * @param wpdb $wpdb WP Database Object
     */
    private function __construct($wpdb) {
        
        $this->tables = [
            "product_data_table"    =>  new ProductDataTable($wpdb)
        ];
        
        foreach ($this->tables as $table_name => $table) {
            $table->create_table();
        }
        
    }

    /**
     * @param wpdb $wpdb WP Database object.
     */
    public static function instance($wpdb): self {
        if (!self::$instance) {
            self::$instance = new self($wpdb);
        }
        return self::$instance;
    }

    public function get_table(string $table_name): ?DatabaseTable {
        return $this->tables[$table_name] ?? null;
    }

    
}
