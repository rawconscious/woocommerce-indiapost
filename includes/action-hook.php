<?php
/**
 * Creates WooCommerce India Post Subpage
 *
 * @package rawconscious.
 */

// Hook into WooCommerce admin menu.
add_action( 'admin_menu', 'woocommerce_indiapost_subpage_menu' );

/**
 * Create subpage under WooCommerce menu
 */
function woocommerce_indiapost_subpage_menu() {
	add_submenu_page(
		'woocommerce', // parent slug.
		'India Post', // Subpage Title.
		'India Post', // Subpage Menu Label.
		'manage_woocommerce',
		'woocommerce-indiapost', // Submenu page slug.
		'woocommerce_indiapost_subpage_callback' // Call backfunction.
	);
}

/**
 * Callback function for the subpage content
 */
function woocommerce_indiapost_subpage_callback() {
	$order_ids[] = isset( $_GET['order_id'] ) ? $_GET['order_id'] : '';

	?> 
	<div id="wcip-react-app" class="wrap">
	</div>
	<?php
}



add_action( 'init', 'register_ready_to_ship_status' );
/**
 * Register Post status Ready to Ship.
 */
function register_ready_to_ship_status() {

	register_post_status(
		'wc-wcip-ship',
		array(
			'label'                     => 'Ready To Ship',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Ready To Ship (%s)', 'Ready To Ship (%s)' ),
		)
	);

	register_post_status(
		'wc-wcip-dropoff',
		array(
			'label'                     => 'Drop Off Booked',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Drop Off Booked (%s)', 'Drop Off Booked (%s)' ),
		)
	);

	register_post_status(
		'wc-wcip-pickup',
		array(
			'label'                     => 'Pickup Booked',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pickup Booked (%s)', 'Pickup Booked (%s)' ),
		)
	);

	register_post_status(
		'wc-wcip-dropoff-done',
		array(
			'label'                     => 'Drop Off Completed',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Drop Off Completed (%s)', 'Drop Off Completed (%s)' ),
		)
	);

	register_post_status(
		'wc-wcip-pickup-done',
		array(
			'label'                     => 'Pickup Completed',
			'public'                    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pickup Completed (%s)', 'Pickup Completed (%s)' ),
		)
	);

}

// Add registered status to list of WC Order statuses.
add_filter( 'wc_order_statuses', 'wcip_add_status_to_list' );
/**
 * Add Custom Order Status to woocommerce status list.
 *
 * @param array $order_statuses woocommerce oder status.
 */
function wcip_add_status_to_list( $order_statuses ) {

	$new = array();

	foreach ( $order_statuses as $id => $label ) {

		if ( 'wc-completed' === $id ) {
			$new['wc-wcip-ship'] = 'Ready To Ship';
			$new['wc-wcip-dropoff'] = 'Drop Off Booked';
			$new['wc-wcip-pickup'] = 'Pickup Booked';
			$new['wc-wcip-dropoff-done'] = 'Drop Off Completed';
			$new['wc-wcip-pickup-done'] = 'Pickup Completed';
		}

		$new[ $id ] = $label;

	}

	return $new;

}

add_filter( 'bulk_actions-edit-shop_order', 'register_rc_wcip_bulk_actions' );
/**
 * Settings link for Register Page
 *
 * @param array $bulk_actions available bulk actions.
 *
 * @return array
 */
function register_rc_wcip_bulk_actions( $bulk_actions ) {
	$bulk_actions['mark_wcip_ship'] = 'Change Status to Ready to Ship';

	return $bulk_actions;
}

add_action( 'handle_bulk_actions-edit-shop_order', 'wcip_bulk_process_custom_status', 20, 3 );
/**
 * Process Custom Bulk Status Ready to ship.
 *
 * @param string $redirect url.
 *
 * @param string $doaction actions.
 *
 * @param array  $object_ids Order Ids.
 */
function wcip_bulk_process_custom_status( $redirect, $doaction, $object_ids ) {

	$order_statuses = wc_get_order_statuses();
	$custom_status  = 'wc-wcip-ship';

	if ( isset( $order_statuses['wc-wcip-ship'] ) && 'mark_wcip_ship' === $doaction ) {

		// change status of every selected order .
		foreach ( $object_ids as $order_id ) {
			$order = wc_get_order( $order_id );
			$order->update_status( $custom_status );
		}

		// do not forget to add query args to URL because we will show notices later.
		$redirect = add_query_arg(
			array(
				'bulk_action' => 'marked_wcip_ship',
				'changed'     => count( $object_ids ),
			),
			$redirect
		);

	}

	return $redirect;
}


add_action( 'admin_notices', 'wcip_custom_order_status_notices' );
/**
 * Displays Woocommerce Bulk actions Status Upadate Notices.
 */
function wcip_custom_order_status_notices() {

	if ( isset( $_REQUEST['bulk_action'] )
		&& 'marked_wcip_ship' == $_REQUEST['bulk_action']
		&& isset( $_REQUEST['changed'] )
		&& $_REQUEST['changed']
	) {

		// displaying the message .
		printf(
			'<div id="message" class="updated notice is-dismissible"><p>' . _n( '%d order status changed.', '%d order statuses changed.', $_REQUEST['changed'] ) . '</p></div>',
			$_REQUEST['changed']
		);

	}
}

