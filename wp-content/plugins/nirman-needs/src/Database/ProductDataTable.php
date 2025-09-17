<?php

namespace NirmanNeeds\Database;
use \NirmanNeeds\Database\DatabaseTable;

defined('ABSPATH') || die;

class ProductDataTable extends DatabaseTable {

    protected function set_table_name(): void
    {
        $main_name = 'product_data_table';
        $this->table_name = $this->wpdb->prefix . NIRMAN_NEEDS_TABLE_PREFIX . $main_name;
    }

    protected function set_column_formats(): void
    {
        $this->column_formats = [
            "id"                =>  "%d",
            "product_id"        =>  "%d",
            "is_variation"      =>  "%d",
            "projected_price"   =>  "%f",
            "start"             =>  "%s",
            "end"               =>  "%s",
            "created_on"        =>  "%s",
        ]; 
    }

    public function get_schema(): string
    {
        $table_name = esc_sql($this->table_name);
        return "CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `product_id` BIGINT UNSIGNED NOT NULL,
                `is_variation` TINYINT(1) NOT NULL DEFAULT 0,
                `projected_price` DECIMAL UNSIGNED NOT NULL,
                `start` DATETIME NOT NULL,
                `end` DATETIME,
                `created_on` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`product_id`, `is_variation`),
                INDEX (`product_id`)
            ) ENGINE = InnoDB";
    }
    
    
}
