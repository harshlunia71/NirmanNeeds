<?php
/**
 * Template: Projected Price Table
 * Vars: $rows (array)
 */
defined('ABSPATH') || die;
?>
<h4 class="wc-settings-sub-title"><?php _e('Future Projected Prices', 'nirman-needs'); ?></h4>
<?php if (empty($rows)) : ?>
	<p><?php _e('No future projected prices set.', 'nirman-needs'); ?></p>
<?php else : ?>
	<table class="wp-list-table widefat fixed striped projected-prices-table wc_emails">
		<thead>
			<tr>
				<th><?php _e('Projected Price', 'nirman-needs'); ?></th>
				<th><?php _e('Start Date', 'nirman-needs'); ?></th>
				<th><?php _e('Computed End Date', 'nirman-needs'); ?></th>
				<th><?php _e('Actions', 'nirman-needs'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($rows as $price): ?>
				<tr data-id="<?php echo esc_attr($price['id']); ?>">
					<td><?php echo wc_price($price['projected_price']); ?></td>
					<td><?php echo esc_html(date('Y-m-d', strtotime($price['start']))); ?></td>
					<td><?php echo !empty($price['computed_end']) ? esc_html(date('Y-m-d', strtotime($price['computed_end']))) : ''; ?></td>
					<td>
						<button type="button" class="button button-small delete-projected-price" data-id="<?php echo esc_attr($price['id']); ?>"><?php _e('Delete', 'nirman-needs'); ?></button>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>


