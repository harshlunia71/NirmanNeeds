<?php
/**
 * Template: Projected Price Form
 * Vars: $product_id (int), $is_variation (bool)
 */
defined('ABSPATH') || die;
?>
<div class="projected-price-form form-field" data-product-id="<?php echo esc_attr($product_id); ?>" data-is-variation="<?php echo esc_attr($is_variation ? '1' : '0'); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce('wp_rest')); ?>">
	<h4><?php _e('Add New Projected Price', 'nirman-needs'); ?></h4>
	<p class="form-field">
		<label for="projected_price_<?php echo esc_attr($product_id); ?>"><?php _e('Projected Price', 'nirman-needs'); ?></label>
		<input type="number" id="projected_price_<?php echo esc_attr($product_id); ?>" name="projected_price" step="0.01" min="0" class="short wc_input_price" required />
		<span class="description"><?php _e('Enter the projected price for this period.', 'nirman-needs'); ?></span>
	</p>
	<p class="form-field">
		<label for="start_date_<?php echo esc_attr($product_id); ?>"><?php _e('Start Date', 'nirman-needs'); ?></label>
		<input type="date" id="start_date_<?php echo esc_attr($product_id); ?>" name="start_date" class="short" required />
		<span class="description"><?php _e('When this projected price becomes effective.', 'nirman-needs'); ?></span>
	</p>
	<p class="form-field">
		<button type="button" class="button button-primary save-projected-price"><?php _e('Save Projected Price', 'nirman-needs'); ?></button>
	</p>
</div>


