<?php

namespace NirmanNeeds\Admin;

use \NirmanNeeds\Database\DatabaseManager;
use \NirmanNeeds\Database\ProductDataTable;

defined('ABSPATH') || die;

class ProductFields {

    private ProductDataTable $product_data_table;

    public function __construct() {
        global $wpdb;
        $db_manager = DatabaseManager::instance($wpdb);
        $this->product_data_table = $db_manager->get_table('product_data_table');
    }

    public function init(): void {
        // Add custom fields to product edit page
        add_action('woocommerce_product_options_general_product_data', [$this, 'add_custom_fields']);
        
        // Save hooks (not used; kept for extensibility)
        add_action('woocommerce_process_product_meta', [$this, 'save_custom_fields']);
        add_action('woocommerce_save_product_variation', [$this, 'save_variation_custom_fields'], 10, 2);

        // Add custom fields to variations
        add_action('woocommerce_product_after_variable_attributes', [$this, 'add_variation_custom_fields'], 10, 3);

        // Enqueue scripts and styles
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_scripts']);
    }

    /**
     * Add custom fields to the general product data section
     */
    public function add_custom_fields(): void {
        global $post;
        
        if (!$post || !$post->ID) {
            return;
        }

        $product = wc_get_product($post->ID);
        if (!$product) {
            return;
        }

        echo '<div class="options_group">';
        echo '<h3 class="wc-settings-sub-title">' . __('Projected Price Management', 'nirman-needs') . '</h3>';
        
        // Only show for non-variable products
        if ($product && $product->get_type() !== 'variable') {
            $this->render_projected_price_form($post->ID, false);
        }
        
        // Display existing projected prices table
        $this->render_projected_prices_table($post->ID, false);
        
        echo '</div>';
    }

    /**
     * Add custom fields to product variations
     */
    public function add_variation_custom_fields($loop, $variation_data, $variation): void {
        $variation_id = $variation->ID;
        
        echo '<div class="form-row form-row-full">';
        echo '<h4 class="wc-settings-sub-title">' . sprintf(__('Projected Price Management - Variation #%d', 'nirman-needs'), $loop + 1) . '</h4>';
        
        // Add new projected price form for variation
        $this->render_projected_price_form($variation_id, true);
        
        // Display existing projected prices table for variation
        $this->render_projected_prices_table($variation_id, true);
        
        echo '</div>';
    }

    /**
     * Render the form to add new projected prices
     */
    private function render_projected_price_form(int $product_id, bool $is_variation): void {
        $template = NIRMAN_NEEDS_PATH . '/src/Admin/templates/projected-price-form.php';
        if (file_exists($template)) {
            $is_variation = $is_variation; // keep name for template
            $product_id = $product_id;
            include $template;
        }
    }

    /**
     * Render the table showing existing projected prices
     */
    private function render_projected_prices_table(int $product_id, bool $is_variation): void {
        $rows = $this->product_data_table->get_future_with_computed_ends($product_id, $is_variation);
        $template = NIRMAN_NEEDS_PATH . '/src/Admin/templates/projected-price-table.php';
        if (file_exists($template)) {
            include $template;
        }
    }

    /**
     * Save custom fields for simple products
     */
    public function save_custom_fields(int $post_id): void {
        // This is handled via AJAX now
    }

    /**
     * Save custom fields for product variations
     */
    public function save_variation_custom_fields(int $variation_id, int $loop): void {
        // This is handled via AJAX now
    }

    // Removed old AJAX handlers in favor of REST API

    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts(string $hook): void {
        global $post;
        
        // Only load on product edit pages
        if (!in_array($hook, ['post.php', 'post-new.php']) || 
            !$post || 
            $post->post_type !== 'product') {
            return;
        }

        wp_enqueue_script(
            'nirman-needs-admin-projected-prices',
            NIRMAN_NEEDS_URL . 'assets/js/admin-projected-prices.js',
            ['wp-api-fetch'],
            NIRMAN_NEEDS_VERSION,
            true
        );
        wp_localize_script('nirman-needs-admin-projected-prices', 'NirmanNeedsAdmin', [
            'namespace' => 'nirman-needs/v1',
            'confirmDelete' => __('Are you sure you want to delete this projected price?', 'nirman-needs'),
        ]);
    }
}
