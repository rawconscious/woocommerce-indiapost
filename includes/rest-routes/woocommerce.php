<?php
/**
 * Rest Endpoints For Woocommerce.
 *
 * @package rawconscious
 */

add_action( 'rest_api_init', 'register_rest_routes_rc_wcip_woocommerce' );
/**
 * Register Rest Routes which call the function to get or update woocommerce order data.
 */
function register_rest_routes_rc_wcip_woocommerce() {
	// Register Route for get order list which is ready to ship.
	register_rest_route(
		'rc-wcip/v1',
		'order/get-list/(?P<order_status>[a-za-z0-0-]+)/(?P<filter_status>[a-zA-Z0-9\-]+)/(?P<offset>[\d]+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'rc_wcip_get_eligible_orders',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					},
				),
			),
		)
	);
	// Register Route for Registering Default Status between Drop off and Pickup.
	register_rest_route(
		'rc-wcip/v1',
		'get-default-status',
		array(
			'methods'             => 'GET',
			'callback'            => 'rc_wcip_get_default_status',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					},
				),
			),
		)
	);
	// Register Route for Registering Default Status between Drop off and Pickup.
	register_rest_route(
		'rc-wcip/v1',
		'register-default-status/(?P<status>[a-za-z0-0-]+)',
		array(
			'methods'             => 'GET',
			'callback'            => 'rc_wcip_register_default_status',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					},
				),
			),
		)
	);
	// Register Route for Registering Default Status between Drop off and Pickup.
	register_rest_route(
		'rc-wcip/v1',
		'update-order-status/(?P<order_status>[a-za-z0-0-]+)',
		array(
			'methods'             => 'POST',
			'callback'            => 'rc_wcip_update_order_status',
			'permission_callback' => '__return_true',
			'args'                => array(
				'id' => array(
					'validate_callback' => function( $param, $request, $key ) {
						return is_numeric( $param );
					},
				),
			),
		)
	);
}


/**
 * Callback functions for register rest route.
 *
 * @param WP_REST_Request $request .
 *
 * @return array $results.
 */
function rc_wcip_get_eligible_orders( WP_REST_Request $request ) {

	global $wpdb;

	$table_rc_wcip     = $wpdb->prefix . 'rc_wcip';
	$table_rc_wcip_log = $wpdb->prefix . 'rc_wcip_log';

	$order_status  = $request->get_param( 'order_status' );
	$filter_status = $request->get_param( 'filter_status' );
	$offset        = (int) $request->get_param( 'offset' );

	$order_status = 'wc-wcip-' . $order_status;

	if ( 'today' === $filter_status ) {

		$current_date = date( 'Y-m-d' );//phpcs:ignore
		$date_created = '>=' . strtotime( $current_date );

	} elseif ( 'last-3-days' === $filter_status ) {

		$start_date   = date( 'Y-m-d', strtotime( '-3 days' ) );//phpcs:ignore
		$date_created = '>=' . strtotime( $start_date );

	} elseif ( 'this-week' === $filter_status ) {

		$start_date   = date( 'Y-m-d', strtotime( 'this week' ) );//phpcs:ignore
		$date_created = '>=' . strtotime( $start_date );

	} else {

		$date_created = '';

	}

	$args1 = array(
		'date_created' => $date_created,
		'status'       => $order_status,
		'limit'        => -1,
	);

	$total_orders = wc_get_orders( $args1 );// todo: consider improving this query.

	// todo: change sizeof to count.
	$orders_count = sizeof( $total_orders );// phpcs:ignore 

	$args2 = array(
		'date_created' => $date_created,
		'limit'        => 10,
		'offset'       => $offset,
		'status'       => $order_status,
	);

	$orders = wc_get_orders( $args2 );

	$has_more = 0 >= ( $orders_count - ( $offset + 10 ) ) ? false : true;

	$results = array();
	$data    = array();
	$header  = array();

	$header = array(
		'orderId'      => 'Order ID',
		'orderDate'    => 'Order Date',
		'billingName'  => 'Billing Name',
		'billingEmail' => 'Billing Email',
		'remarks'      => 'Remarks',
		'articleId'    => 'Article Id',
	);

	$results['header'] = $header;

	if ( $orders ) {
		foreach ( $orders as $key => $order ) {
			$order_date           = $order->get_date_created();
			$order_date_formatted = $order_date ? $order_date->date_i18n( 'Y-m-d H:i:s' ) : '';
			$order_id             = $order->get_id();
			$order_status         = $order->status;

			$remarks    = $wpdb->get_var( "SELECT return_json FROM $table_rc_wcip_log WHERE request_id = $order_id ORDER BY created_at DESC LIMIT 1;" );//phpcs:ignore
			$remarks = json_decode( $remarks );
			$errors  = '-';

			if ( 'wcip-ship' === $order_status ) {
				$errors = $remarks->details[0];
			}

			$data[ $key ] = array(
				'orderDate'    => $order_date_formatted,
				'orderId'      => $order_id,
				'billingName'  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
				'billingEmail' => $order->get_billing_email(),
				'remarks'      => isset( $errors ) ? $errors : '-',
			);

			if ( 'wcip-dropoff' === $order_status || 'wcip-pickup' === $order_status || 'wcip-dropoff-done' === $order_status || 'wcip-pickup-done' === $order_status ) {
				$article_id                = $wpdb->get_var( "SELECT indiapost_article_id FROM $table_rc_wcip WHERE order_id = $order_id LIMIT 1" );
				$data[ $key ]['articleId'] = isset( $article_id ) ? $article_id : '';
			}
		}
		$results['data'] = $data;
	}

	if ( $data ) {
		return array(
			'isSuccess' => true,
			'results'   => $results,
			'hasMore'   => $has_more,
		);
	} else {
		return array(
			'isSuccess' => false,
			'results'   => $results,
		);
	}

}

/**
 * Callback Function to get Default status.
 */
function rc_wcip_get_default_status() {

	$option_name = 'rc_wcip_indiapost_booking_status';

	$status = get_option( $option_name );

	if ( $status ) {
		return array(
			'isSuccess' => true,
			'status'    => $status,
		);
	} else {
		return array(
			'isSuccess' => false,
		);
	}
}

/**
 * Callback Function to store Default status.
 *
 * @param WP_REST_Request $request .
 */
function rc_wcip_register_default_status( WP_REST_Request $request ) {

	$default_status = $request->get_param( 'status' );

	$option_name = 'rc_wcip_indiapost_booking_status';

	$existing_status = get_option( $option_name );

	if ( $existing_status ) {
		$update_result = update_option( $option_name, $default_status );
	} else {
		$update_result = add_option( $option_name, $default_status );
	}

	if ( $update_result ) {
		return array(
			'isSuccess' => true,
		);
	} else {
		return array(
			'isSuccess' => false,
		);
	}
}

/**
 * Callback function to Updating order status.
 *
 * @param WP_REST_Request $request .
 */
function rc_wcip_update_order_status( WP_REST_Request $request ) {
	global $wpdb;
	$table_rc_wcip = $wpdb->prefix . 'rc_wcip';

	$order_status = ! empty( $request->get_param( 'order_status' ) ) ? $request->get_param( 'order_status' ) : null;
	$order_ids    = ! empty( $request->get_param( 'orderIds' ) ) ? $request->get_param( 'orderIds' ) : null;

	if ( empty( $order_status ) || empty( $order_ids ) ) {
		return array(
			'isSuccess' => false,
		);
	}

	$success_count = 0;
	$failure_count = 0;

	foreach ( $order_ids as $order_id ) {

		$order_details = wc_get_order( $order_id );

		$status = $order_details->update_status( 'wc-wcip-' . $order_status );

		$article_id = $wpdb->get_var( "SELECT indiapost_article_id FROM $table_rc_wcip WHERE order_id = $order_id ORDER BY created_at DESC LIMIT 1;" );

		// Todo: handle the order note based on order status
		// $order_note    = "You shipment with tracking number " . $article_id . " has been shipped";
		// $order_details->add_order_note( $order_note, $order_note, true );

		if ( $status ) {
			$success_count++;
		} else {
			$failure_count++;
		}
	}

	if ( 0 === $success_count && 0 < $failure_count ) {
		$status_type = 'error';
		$message     = 'No orders are updated';
	} elseif ( 0 === $failure_count && 0 < $success_count ) {
		$status_type = 'success';
		$message     = $success_count . ' orders are updated successfully';
	} else {
		$status_type = 'warning';
		$message     = $success_count . ' orders are updated successfully and ' . $failure_count . ' orders are not updated';
	}

	return array(
		'isSuccess'  => true,
		'statusType' => $status_type,
		'message'    => $message,
	);

}
