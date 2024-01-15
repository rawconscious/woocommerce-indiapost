<?php
/**
 * Booking API from India Post.
 *
 * @package rawconscious.
 */

add_action( 'rest_api_init', 'register_rest_routes_credentials' );
/**
 * Register Rest Routes.
 */
function register_rest_routes_credentials() {
	// Register Route for add/update credentials.
	register_rest_route(
		'rc-wcip/v1',
		'credentials/save-credentials',
		array(
			'methods'             => 'POST',
			'callback'            => 'save_credentials',
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
	// Register Route for get credentials.
	register_rest_route(
		'rc-wcip/v1',
		'credentials/get-credentials',
		array(
			'methods'             => 'GET',
			'callback'            => 'get_credentials',
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
 * Callback functions to save credentials.
 *
 * @param WP_REST_Request $request .
 */
function save_credentials( WP_REST_Request $request ) {
	global $wpdb;

	$credentials = $request->get_param( 'credentials' );
	$address     = $request->get_param( 'address' );

	$username    = $credentials['username'];
	$password    = $credentials['password'];
	$identifier  = $credentials['identifier'];
	$contract_id = $credentials['contractId'];

	$option_username    = 'rc_wcip_indiapost_username';
	$option_password    = 'rc_wcip_indiapost_password';
	$option_identifier  = 'rc_wcip_indiapost_identifier';
	$option_contract_id = 'rc_wcip_indiapost_contract_id';
	$option_address     = 'rc_wcip_indiapost_warehouse_address';

	// Username.
	$username_result = get_option( $option_username );

	if ( $username_result === $username ) {
		$username_status = true;
	} else if ( false !== $username_result ) {
		$username_status = update_option( $option_username, $username );
	} else {
		$username_status = add_option( $option_username, $username );
	}

	// Password.
	$password_result = get_option( $option_password );

	if ( $password_result === $password ) {
		$password_status = true;
	} else if ( false !== $password_result ) {
		$password_status = update_option( $option_password, $password );
	} else {
		$password_status = add_option( $option_password, $password );
	}
	
	// Identifier.
	$identifier_result = get_option( $option_identifier );

	if ( $identifier_result === $identifier ){
		$identifier_status = true;
	} else if ( false !== $identifier_result ) {
		$identifier_status = update_option( $option_identifier, $identifier );
	} else {
		$identifier_status = add_option( $option_identifier, $identifier );
	}

	// Contract Id.
	$contract_id_result = get_option( $option_contract_id );

	if ( $contract_id_result === $contract_id ) {
		$contract_id_status = $contract_id;
	} else if ( false !== $contract_id_result ) {
		$contract_id_status = update_option( $option_contract_id, $contract_id );
	} else {
		$contract_id_status = add_option( $option_contract_id, $contract_id );
	}

	// Address.
	$address_result = get_option( $option_address );

	if ( $address === $option_address ) {
		$address_status = true;
	} else if ( false !== $address_result ) {
		$address_status = update_option( $option_address, $address ); 
	} else {
		$address_status = add_option( $option_address, $address );
	}

	if ( $username_status && $password_status && $identifier_status && $contract_id_status && $option_address ) {
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
 * Callback functions to get credentials.
 *
 * @param WP_REST_Request $request .
 */
function get_credentials( WP_REST_Request $request ) {
	global $wpdb;

	$option_username    = 'rc_wcip_indiapost_username';
	$option_password    = 'rc_wcip_indiapost_password';
	$option_identifier  = 'rc_wcip_indiapost_identifier';
	$option_contract_id = 'rc_wcip_indiapost_contract_id';
	$option_address     = 'rc_wcip_indiapost_warehouse_address';

	$username    = get_option( $option_username );
	$password    = get_option( $option_password );
	$identifier  = get_option( $option_identifier );
	$contract_id = get_option( $option_contract_id );
	$address     = get_option( $option_address );

	if ( $username && $password && $identifier && $contract_id && $address ) {
		$credentials = array(
			'username' 	 => $username,
			'password' 	 => $password,
			'identifier' => $identifier,
			'contractId' => $contract_id,
			'address'    => $address,
		);

		return array(
			'isSuccess'   => true,
			'credentials' => $credentials,
		);
	} else {
		return array(
			'isSuccess' => false,
		);
	}
}