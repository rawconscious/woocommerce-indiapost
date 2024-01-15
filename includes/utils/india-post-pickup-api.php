<?php
/**
 * Pickup API from India Post.
 *
 * @package rawconscious.
 */

/**
 * Pickup API from India POST.
 *
 * @param array $data data.
 */

function pickup_api( $request_json ) {

	$token 		   = json_decode( india_post_get_token() );
	$token_type    = isset( $token->token_type ) ? $token->token_type : null;
	$access_token  = isset( $token->access_token ) ? $token->access_token : null;
	$session_state = isset( $token->session_state ) ? $token->session_state : null;

	$current_time = new DateTime();
	$time_stamp = $current_time->format( 'Y-m-d\TH:i:s.uP' );

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
			CURLOPT_URL            => 'https://gateway.cept.gov.in/pickupreq/api/createbulkreq',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => json_encode( array ( $request_json ) ),
			CURLOPT_HTTPHEADER     => $http_header_json,
		)
	);

	$curl_response = curl_exec( $curl );
	$curl_status   = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	curl_close( $curl );

// todo : process curl response ( status 200, 400, 500 etc )
if ( 201 === $curl_status ) {
	$response['is_success'] = true;
	$response['data']       = json_decode( $curl_response );
	
} else {
	$response['is_success'] = false;
	$response['error']      = $curl_response;
}

return $response;

}