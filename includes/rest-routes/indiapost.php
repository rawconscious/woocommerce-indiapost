<?php
/**
 * Rest Endpoints For India Post.
 *
 * @package rawconscious
 */

add_action( 'rest_api_init', 'register_rest_routes_rc_wcip_indiapost' );
/**
 * Register Rest Routes which call the function to get or update India post data.
 */
function register_rest_routes_rc_wcip_indiapost() {
	// Register Route for handle booking request with India post.
	register_rest_route(
		'rc-wcip/v1',
		'india-post-booking-request/(?P<request_type>[a-za-z0-0-]+)',
		array(
			'methods'             => 'POST',
			'callback'            => 'rc_wcip_call_india_post_api_booking',
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
	// Register Route for handle cancel request with India Post.
	register_rest_route(
		'rc-wcip/v1',
		'india-post-cancel-request/',
		array(
			'methods'             => 'POST',
			'callback'            => 'rc_wcip_call_india_post_api_cancel',
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
 * Callback functions for send booking request ( Dropoff or Pickup ) to India Post.
 *
 * @param WP_REST_Request $request .
 *
 * @return array $results.
 */
function rc_wcip_call_india_post_api_booking( WP_REST_Request $request ) {

	global $wpdb;

	$table_rc_wcip            = $wpdb->prefix . 'rc_wcip';
	$table_rc_wcip_log        = $wpdb->prefix . 'rc_wcip_log';
	$table_rc_wcip_article_id = $wpdb->prefix . 'rc_wcip_article_id';

	$order_ids    = $request->get_param( 'orderIds' );
	$request_type = $request->get_param( 'request_type' );

	if ( null !== $request_type ) {
		$function_name = 'booking' === $request_type ? 'booking_api' : 'pickup_api';
		$request_type  = 'booking' === $request_type ? 'dropoff' : 'pickup';
	}

	$customer_id        = get_option( 'rc_wcip_indiapost_username' );
	$identifier         = get_option( 'rc_wcip_indiapost_identifier' );
	$contract_id        = get_option( 'rc_wcip_indiapost_contract_id' );
	$address_serialized = get_option( 'rc_wcip_indiapost_warehouse_address' );
	$address            = $address_serialized? json_decode( $address_serialized  ) : false ;

	$current_time = new DateTime();
	// Format the current time in the desired format.
	$time_stamp = $current_time->format( 'Y-m-d\TH:i:s.uP' );

	$is_invalid_credential = false;
	$is_invalid_credential = $is_invalid_credential || empty( $customer_id );
	$is_invalid_credential = $is_invalid_credential || empty( $identifier );
	$is_invalid_credential = $is_invalid_credential || empty( $contract_id );
	$is_invalid_credential = $is_invalid_credential || empty( $address );

	if ( $is_invalid_credential ) {
		$error_message = 'Credential Missing';
		$response      = '{
			"timestamp": "' . $time_stamp . '",
			"message": "Details Not Found",
			"details": [
				"Error:' . $error_message . '"
				]
		}';

		foreach ( $order_ids as $order_id ) {

			$response_data = array(
				'request_id'   => $order_id,
				'request_type' => $request_type,
				'request_json' => json_encode(array()),
				'return_json'  => $response,
			);

			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $response_data );

		}

		return array(
			'isSuccess' => false,
		);

	}

	$sender_name     = isset( $address->senderName ) ? $address->senderName : '';//phpcs:ignore
	$sender_email    = isset( $address->senderEmail ) ? $address->senderEmail : '';//phpcs:ignore
	$sender_phone    = isset( $address->senderPhone ) ? $address->senderPhone : '';//phpcs:ignore
	$sender_address1 = isset( $address->address1 ) ? $address->address1 : '';
	$sender_address2 = isset( $address->address2 ) ? $address->address2 : '';
	$sender_city     = isset( $address->city ) ? $address->city : '';
	$sender_pincode  = isset( $address->pincode ) ? (int) $address->pincode : '';

	$is_invalid_sender_details = false;
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->senderName );//phpcs:ignore
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->senderEmail );//phpcs:ignore
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->senderPhone );//phpcs:ignore
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->address1 );
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->address2 );
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->city );
	$is_invalid_sender_details = $is_invalid_sender_details || empty( $address->pincode );

	if ( $is_invalid_sender_details ) {

		$error_message = 'Sender Address Missing';
		$response      = '{
			"timestamp": "' . $time_stamp . '",
			"message": "Details Not Found",
			"details": [
				"Error:' . $error_message . '"
				]
		}';

		foreach ( $order_ids as $order_id ) {

			$response_data = array(
				'request_id'   => $order_id,
				'request_type' => $request_type,
				'request_json' => json_encode(array()),
				'return_json'  => $response,
			);

			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $response_data );

		}

		return array(
			'isSuccess' => false,
		);

	}

	$success_count = 0;
	$failure_count = 0;
	$error_message = '';
	foreach ( $order_ids as $order_id ) {

		$order_details = wc_get_order( $order_id );

		$receipient_name     = ! empty( $order_details->get_shipping_first_name() ) ? $order_details->get_shipping_first_name() : $order_details->get_billing_first_name();
		$receipient_email    = $order_details->get_billing_email() ?? '';
		$receipient_phone    = ! empty( $order_details->get_shipping_phone() ) ? $order_details->get_shipping_phone() : $order_details->get_billing_phone();
		$receipient_address1 = ! empty( $order_details->get_shipping_address_1() ) ? $order_details->get_shipping_address_1() : $order_details->get_billing_address_1();
		$receipient_address2 = ! empty( $order_details->get_shipping_address_2() ) ? $order_details->get_shipping_address_2() : $order_details->get_billing_address_2();
		$receipient_city     = ! empty( $order_details->get_shipping_city() ) ? $order_details->get_shipping_city() : $order_details->get_billing_city();
		$receipient_pincode  = ! empty( $order_details->get_shipping_postcode() ) ? (int) $order_details->get_shipping_postcode() : (int) $order_details->get_billing_postcode();
		$receipient_country  = 'India';

		$is_invalid_receipient_details = false;
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_name );
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_email );
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_phone );
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_address1 );
		// $is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_address2 );
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_city );
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_pincode );
		$is_invalid_receipient_details = $is_invalid_receipient_details || empty( $receipient_country );

		if ( $is_invalid_receipient_details ) {
			$error_message = 'Receipient Data Missing';
			$error_details = '{
				"timestamp": "' . $time_stamp . '",
				"message": "Details Not Found",
				"details": [
					"Error:' . $error_message . '"
					]
			}';

			$insert_data = array(
				'request_id'   => $order_id,
				'request_type' => $request_type,
				'request_json' => '',
				'return_json'  => $error_details,
			);

			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $insert_data );

			$failure_count++;

			continue;

		}

		$article_id = $wpdb->get_var( "SELECT article_id FROM $table_rc_wcip_article_id WHERE state = 'unused' LIMIT 1" ); //phpcs:ignore

		if ( empty( $article_id ) ) {
			$error_message = 'Article Data Missing';
			$error_details = '{
						"timestamp": "' . $time_stamp . '",
						"message": "Details Not Found",
						"details": [
							"Error:' . $error_message . '"
							]
					}';

			$insert_data = array(
				'request_id'   => $order_id,
				'request_type' => $request_type,
				'request_json' => '',
				'return_json'  => $error_details,
			);

			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $insert_data );

			$failure_count++;

			continue;
		}

		$request_json = [
			'identifier'             => $identifier,
			'articleid'              => $article_id,
			'articletype'            => 'SP',
			'articlelength'          => 0.0,
			'articlewidth'           => 0.0,
			'articleheight'          => 0.0,
			'articleweight'          => 34000,
			'codvalue'               => 0,
			'insurancevalue'         => 0,
			'proofofdeliveryflag'    => false,
			'customerid'             => $customer_id,
			'contractnumber'         => $contract_id,
			'sendername'             => $sender_name,
			'senderaddressline1'     => $sender_address1,
			'senderaddressline2'     => $sender_address2,
			'senderaddressline3'     => '',
			'sendercity'             => $sender_city,
			'senderpincode'          => $sender_pincode,
			'sendercountry'          => 'India',
			'senderemail'            => $sender_email,
			'sendermobile'           => $sender_phone,
			'nameofreceipient'       => $receipient_name,
			'receipientaddressline1' => $receipient_address1,
			'receipientaddressline2' => $receipient_address2,
			'receipientaddressline3' => '',
			'receipientcity'         => $receipient_city,
			'receipientpincode'      => $receipient_pincode,
			'receipientcountry'      => $receipient_country,
			'receipientemail'        => $receipient_email,
			'receipientmobile'       => $receipient_phone,
			'refid'                  => $order_id,
		];

		if ( 'pickup' === $request_type ) {
			$request_json['pickupname']           = $sender_name;
			$request_json['pickupaddressline1']   = $sender_address1;
			$request_json['pickupaddressline2']   = $sender_address2;
			$request_json['pickupaddressline3']   = '';
			$request_json['pickupcity']           = $sender_city;
			$request_json['pickupaddresspincode'] = $sender_pincode;
			$request_json['pickupcountry']        = 'India';
			$request_json['pickupaddressemail']   = $sender_email;
			$request_json['pickupaddressmobile']  = $sender_phone;
		}

		if ( empty( $request_json ) ) {
			$message = 'orders updates failed';

			$failure_count++;

			continue;
		}

		$response = $function_name( $request_json );

		// check if curl falied.
		if ( false === $response['is_success'] ) {

			$request_data       = array(
				'request_id'   => $order_id,
				'request_type' => $request_type,
				'request_json' => wp_json_encode( $request_json ),
				'return_json'  => $response['error'],
			);
			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $request_data );

			$article_id_db_update = $wpdb->update( $table_rc_wcip_article_id, array( 'state' => 'unused' ), array( 'article_id' => $article_id ) );

			$error_message .= $response['error'] . '  ';
			$failure_count++;

		} else {

			$request_id = isset( $response['data'][0]->requestid ) ? $response['data'][0]->requestid : null;

			$order_details = wc_get_order( $order_id );
			$update_status = $order_details->update_status( 'wc-wcip-' . $request_type );

			$order_data = array(
				'order_id'             => $order_id,
				'indiapost_id'         => $request_id,
				'status'               => $request_type,
				'indiapost_article_id' => $article_id,
			);

			$request_data = array(
				'request_id'   => $order_id,
				'request_type' => $request_type,
				'request_json' => wp_json_encode( $request_json ),
				'return_json'  => wp_json_encode( $response['data'][0] ),
			);

			$rc_wcip_status       = $wpdb->insert( $table_rc_wcip, $order_data );
			$rc_wcip_log_status   = $wpdb->insert( $table_rc_wcip_log, $request_data );
			$article_id_db_delete = $wpdb->delete( $table_rc_wcip_article_id, array( 'article_id' => $article_id ) );

			$order_note = 'You order has been booked with India post. Tracking number :' . $article_id;

			$order_details->add_order_note( $order_note, $order_note, true );

			$success_count++;

		}
	}

	$message     = '';
	$status_type = '';
	$is_success  = false;

	if ( 0 === $success_count && 0 === $failure_count ) {
		$is_success  = false;
		$status_type = 'error';
		$message     = 'no orders are updated';
	} elseif ( 0 < $success_count && 0 < $failure_count ) {
		$is_success  = true;
		$status_type = '';
		$message     = $success_count . ' orders are updated and ' . $failure_count . ' orders are not updated. Errors: ' . $error_message;
	} elseif ( 0 === $success_count && 0 < $failure_count ) {
		$is_success  = false;
		$status_type = 'error';
		$message     = 'All ' . $failure_count . ' orders are not updated. Errors: ' . $error_message;
	} else {
		$is_success  = true;
		$status_type = 'success';
		$message     = 'All ' . $success_count . ' orders are completed';
	}

	return array(
		'isSuccess'  => $is_success,
		'statusType' => $status_type,
		'message'    => $message,
	);

}

/**
 * Call back function for send cancel booking request to India Post.
 *
 * @param WP_REST_Request $request.
 *
 * @return array $results.
 */
function rc_wcip_call_india_post_api_cancel( WP_REST_Request $request ) {
	global $wpdb;

	$table_rc_wcip     		  = $wpdb->prefix . 'rc_wcip';
	$table_rc_wcip_log 	 	  = $wpdb->prefix . 'rc_wcip_log';
	$table_rc_wcip_article_id = $wpdb->prefix . 'rc_wcip_article_id';

	$order_ids = $request->get_param( 'orderIds' );

	$success_count = 0;
	$failure_count = 0;
	$error_message = '';
	foreach ( $order_ids as $order_id ) {

		$order_details = wc_get_order( $order_id );

		$article_id = $wpdb->get_var( "SELECT indiapost_article_id FROM $table_rc_wcip WHERE order_id = $order_id LIMIT 1" );

		$request_json = array(
			'requestType' => 'cancel',
			'orderId'     => $order_id,
			'articleId'   => $article_id,
		);

		if ( null === $article_id || empty( $article_id ) ) {
			$failure_count++;
			continue;
		}

		$response = india_post_cancel_api( $article_id );

		// check if curl falied.
		if ( false === $response['is_success'] ) {

			$encoded_request_json = json_encode( $request_json );

			$request_data = array(
				'request_id'   => $order_id,
				'request_type' => 'cancel',
				'request_json' => $encoded_request_json,
				'return_json'  => $response['error'],
			);
			$error_message .= $response['error'] . '  ';

			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $request_data );
			$failure_count++;

		} else {

			$order_details = wc_get_order( $order_id );
			$update_status = $order_details->update_status( 'wc-wcip-ship' );

			$order_data = array(
				'order_id'             => $order_id,
				'indiapost_id'         => $order_id,
				'status'               => 'cancel',
				'indiapost_article_id' => $article_id,
			);

			$request_data = array(
				'request_id'   => $order_id,
				'request_type' => 'cancel',
				'request_json' => wp_json_encode( $request_json ),
				'return_json'  => wp_json_encode( array() ),
			);

			$rc_wcip_status     = $wpdb->insert( $table_rc_wcip, $order_data );
			$rc_wcip_log_status = $wpdb->insert( $table_rc_wcip_log, $request_data );

			$order_note = 'You shipment with tracking number ' . $article_id . ' has been delayed due to technical issues. It will be rebooked soon.';
			$order_details->add_order_note( $order_note, $order_note, true );
			
			$article_data = array(
				'article_id' => $article_id,
				'state'      => 'unused',
			);
			$insert_results = $wpdb->insert( $table_rc_wcip_article_id, $article_data );

			$success_count++;
		}
	}

	$message     = '';
	$status_type = '';
	$is_success  = false;

	if ( 0 === $success_count && 0 === $failure_count ) {
		$is_success  = false;
		$status_type = 'error';
		$message     = 'no orders are canceled';
	} elseif ( 0 < $success_count && 0 < $failure_count ) {
		$is_success  = true;
		$status_type = '';
		$message     = ml2br( $success_count . ' orders are updated and ' . $failure_count . ' orders are not canceled. Errors: ' . $error_message );
	} elseif ( 0 === $success_count && 0 < $failure_count ) {
		$is_success  = false;
		$status_type = 'error';
		$message     = nl2br( 'All ' . $failure_count . ' orders are not canceled. Erros: ' . $error_message );
	} else {
		$is_success  = true;
		$status_type = 'success';
		$message     = 'All ' . $success_count . ' orders are canceled';
	}

	return array(
		'isSuccess'  => $is_success,
		'statusType' => $status_type,
		'message'    => $message,
	);
}
