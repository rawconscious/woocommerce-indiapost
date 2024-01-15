<?php
/**
 * Handles Cancel Orders with India Post API.
 *
 * @package rawconscious.
 */

 /**
  * Call India Post API to cancel order.
  *
  * @param string $article_id India Post Article Id.
  * @return array Response from the server or WP Error object if there is an error.
  */
function india_post_cancel_api( $article_id ) {

	$token         = json_decode( india_post_get_token() );
	$token_type    = isset( $token->token_type ) ? $token->token_type : null;
	$access_token  = isset( $token->access_token ) ? $token->access_token : null;
	$session_state = isset( $token->session_state ) ? $token->session_state : null;

	$current_time     = new DateTime();
	$time_stamp       = $current_time->format( 'Y-m-d\TH:i:s.uP' );
	$http_header_json = array(
		'x-request-id: ' . $session_state,
		'date: ' . $time_stamp,
		'Content-Type: application/json',
		'Authorization: ' . $token_type . ' ' . $access_token,
	);

	$curl = curl_init();

	curl_setopt_array(
		$curl,
		array(
			CURLOPT_URL            => 'https://apiservices.cept.gov.in/pickupreq/api/cancel/' . $article_id,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'PUT',
			CURLOPT_HTTPHEADER     => $http_header_json,
		)
	);

	$curl_response = curl_exec( $curl );
	$curl_status   = curl_getinfo( $curl, CURLINFO_HTTP_CODE );

	curl_close( $curl );

	if ( 200 === $curl_status ) {
		  $response['is_success'] = true;
		  $response['data']       = json_decode( $curl_response );
	} else {
		  $response['is_success'] = false;
		  $response['error']      = $curl_response;
	}

	return $response;
}
