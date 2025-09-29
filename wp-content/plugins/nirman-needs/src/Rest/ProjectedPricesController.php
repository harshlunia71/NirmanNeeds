<?php

namespace NirmanNeeds\Rest;

use \WP_REST_Request;
use \WP_REST_Response;
use \WP_Error;
use \NirmanNeeds\Database\DatabaseManager;
use \NirmanNeeds\Database\ProductDataTable;

defined('ABSPATH') || die;

class ProjectedPricesController {

    private string $namespace = 'nirman-needs/v1';
    private string $rest_base = 'projected-prices';
    private ProductDataTable $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = DatabaseManager::instance($wpdb)->get_table('product_data_table');
    }

    public function register_routes(): void
    {
        register_rest_route($this->namespace, '/' . $this->rest_base, [
            [
                'methods' => 'GET',
                'callback' => [$this, 'list_items'],
                'permission_callback' => [$this, 'can_edit_products']
            ],
            [
                'methods' => 'POST',
                'callback' => [$this, 'create_item'],
                'permission_callback' => [$this, 'can_edit_products']
            ],
        ]);

        register_rest_route($this->namespace, '/' . $this->rest_base . '/(?P<id>\d+)', [
            [
                'methods' => 'PUT, PATCH',
                'callback' => [$this, 'update_item'],
                'permission_callback' => [$this, 'can_edit_products']
            ],
            [
                'methods' => 'DELETE',
                'callback' => [$this, 'delete_item'],
                'permission_callback' => [$this, 'can_edit_products']
            ],
        ]);
    }

    public function can_edit_products(): bool
    {
        return current_user_can('edit_products');
    }

    public function list_items(WP_REST_Request $request)
    {
        $product_id = (int) $request->get_param('product_id');
        $is_variation = (bool) $request->get_param('is_variation');
        if (!$product_id) {
            return new WP_Error('invalid_product', __('Invalid product_id', 'nirman-needs'), ['status' => 400]);
        }
        $rows = $this->table->get_future_with_computed_ends($product_id, $is_variation);
        return new WP_REST_Response($rows, 200);
    }

    public function create_item(WP_REST_Request $request)
    {
        $data = [
            'product_id' => (int) $request->get_param('product_id'),
            'is_variation' => (bool) $request->get_param('is_variation'),
            'projected_price' => (float) $request->get_param('projected_price'),
            'start' => sanitize_text_field((string) $request->get_param('start')),
        ];
        $result = $this->table->save_projected_price($data);
        if (!$result['success']) {
            return new WP_Error('save_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result, 201);
    }

    public function update_item(WP_REST_Request $request)
    {
        $id = (int) $request['id'];
        if (!$id) {
            return new WP_Error('invalid_id', __('Invalid id', 'nirman-needs'), ['status' => 400]);
        }
        $data = [
            'id' => $id,
            'product_id' => (int) $request->get_param('product_id'),
            'is_variation' => (bool) $request->get_param('is_variation'),
            'projected_price' => (float) $request->get_param('projected_price'),
            'start' => sanitize_text_field((string) $request->get_param('start')),
        ];
        $result = $this->table->save_projected_price($data);
        if (!$result['success']) {
            return new WP_Error('update_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result, 200);
    }

    public function delete_item(WP_REST_Request $request)
    {
        $id = (int) $request['id'];
        if (!$id) {
            return new WP_Error('invalid_id', __('Invalid id', 'nirman-needs'), ['status' => 400]);
        }
        $result = $this->table->delete_projected_price($id);
        if (!$result['success']) {
            return new WP_Error('delete_failed', $result['message'], ['status' => 400]);
        }
        return new WP_REST_Response($result, 200);
    }
}


