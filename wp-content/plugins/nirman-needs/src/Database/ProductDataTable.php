<?php

namespace NirmanNeeds\Database;
use \NirmanNeeds\Database\DatabaseTable;
use \Exception;

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
                `projected_price` DECIMAL(12, 2) UNSIGNED NOT NULL,
                `start` DATETIME NOT NULL,
                `created_on` DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX (`product_id`, `is_variation`),
                INDEX (`product_id`)
            ) ENGINE = InnoDB";
    }  

    /**
     * Get all projected prices for a product
     * 
     * @param int $product_id
     * @param bool $is_variation
     * @return array
     */
    /*public function get_product_projected_prices(int $product_id, bool $is_variation = false): array
    {
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
             WHERE product_id = %d AND is_variation = %d 
             ORDER BY start ASC",
            $product_id,
            $is_variation ? 1 : 0
        );
        
        return $this->get_results($query);
    }
     */

    /**
     * Get future projected prices for a product
     * 
     * @param int $product_id
     * @param bool $is_variation
     * @return array
     */
    public function get_future_projected_prices(int $product_id, bool $is_variation = false): array
    {
        $current_date = current_time('Y-m-d H:i:s');
        $query = $this->wpdb->prepare(
            "SELECT * FROM {$this->table_name} 
            WHERE product_id = %d AND is_variation = %d AND start > %s 
            ORDER BY start ASC;",
            $product_id,
            $is_variation ? 1 : 0,
            $current_date
        );
        //var_dump($query);
         
        return $this->get_results($query);
    }

    /**
     * Validate start datetime: syntactic validity and uniqueness per product/variation.
     * @return array<string, mixed>
     */
    public function validate_start(int $product_id, string $start_date, bool $is_variation = false, ?int $exclude_id = null): array
    {
        if (!strtotime($start_date)) {
            return ['valid' => false, 'message' => 'Invalid start date.'];
        }

        $query = $this->wpdb->prepare(
            "SELECT id FROM {$this->table_name}
            WHERE product_id = %d AND is_variation = %d AND start = %s 
            ORDER BY id LIMIT 1",
            $product_id,
            $is_variation ? 1 : 0,
            $start_date
        );
        $existing_id = (int) $this->wpdb->get_var($query);
        if ($existing_id && (!$exclude_id || $existing_id !== $exclude_id)) {
            return ['valid' => false, 'message' => 'A record with the same start date already exists.'];
        }

        return ['valid' => true, 'message' => ''];
    }

    /**
     * Get the next start date for a product after the given date
     * 
     * @param int $product_id
     * @param string $after_date
     * @param bool $is_variation
     * @return string|null
     */
    /*public function get_next_start_date(int $product_id, string $after_date, bool $is_variation = false): ?string
    {
        $query = $this->wpdb->prepare(
            "SELECT start FROM {$this->table_name} 
             WHERE product_id = %d AND is_variation = %d AND start > %s
             ORDER BY start ASC LIMIT 1",
            $product_id,
            $is_variation ? 1 : 0,
            $after_date
        );
        
        $result = $this->wpdb->get_var($query);
        return $result ?: null;
    }
     */

    /**
     * Get future projected prices with computed end (1 day before next start).
     */
    public function get_future_with_computed_ends(int $product_id, bool $is_variation = false): array
    {
        $rows = $this->get_future_projected_prices($product_id, $is_variation);
        $count = count($rows);
        for ($i = 0; $i < $count; $i++) {
            $next_start = $rows[$i + 1]['start'] ?? null;
            $rows[$i]['computed_end'] = $next_start ? date('Y-m-d H:i:s', strtotime($next_start . ' -1 day')) : null;
        }
        return $rows;
    }

    /**
     * Save projected price data with validation
     * 
     * @param array $data
     * @return array ['success' => bool, 'message' => string, 'id' => int|null]
     */
    public function save_projected_price(array $data): array
    {
        // Validate required fields
        if (empty($data['product_id']) || empty($data['projected_price']) || empty($data['start'])) {
            return ['success' => false, 'message' => 'Product ID, projected price, and start date are required.'];
        }

        // Validate start (no overlap logic needed)
        $validation = $this->validate_start(
            (int) $data['product_id'],
            $data['start'],
            (bool) ($data['is_variation'] ?? false),
            $data['id'] ?? null
        );

        if (!$validation['valid']) {
            return ['success' => false, 'message' => $validation['message']];
        }

        // Prepare data for insertion/update
        $save_data = [
            'product_id' => (int) $data['product_id'],
            'is_variation' => (int) ($data['is_variation'] ?? 0),
            'projected_price' => (float) $data['projected_price'],
            'start' => $data['start'],
        ];

        try {
            if (isset($data['id']) && $data['id']) {
                // Update existing record
                $result = $this->update($save_data, ['id' => $data['id']]);
                return ['success' => true, 'message' => 'Projected price updated successfully.', 'id' => $data['id']];
            } else {
                // Insert new record
                $id = $this->insert($save_data);
                return ['success' => true, 'message' => 'Projected price saved successfully.', 'id' => $id];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete projected price record
     * 
     * @param int $id
     * @return array ['success' => bool, 'message' => string]
     */
    public function delete_projected_price(int $id): array
    {
        try {
            $result = $this->delete(['id' => $id]);
            if ($result > 0) {
                return ['success' => true, 'message' => 'Projected price deleted successfully.'];
            } else {
                return ['success' => false, 'message' => 'Record not found or already deleted.'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Database error: ' . $e->getMessage()];
        }
    }
}
