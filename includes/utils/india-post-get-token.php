<?php
/**
 * Get Token from India Post.
 *
 * @package rawconscious.
 */

/**
 * Get Token from India POST.
 *
 * @param array $data data.
 */
function india_post_get_token( ) {

	$username = get_option( 'rc_wcip_indiapost_username' );
	$password = get_option( 'rc_wcip_indiapost_password' );

	$credentils_json = json_encode([
		"username"  =>  $username,
		"password"	=> $password , 
	]);

	$curl = curl_init();

	curl_setopt_array(
		$curl,
		array(
			CURLOPT_URL            => 'https://gateway.cept.gov.in/auth/keycloak/token',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => '',
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => 'POST',
			CURLOPT_POSTFIELDS     => $credentils_json,
			CURLOPT_HTTPHEADER     => array(
				'Content-Type: application/json',
			),
		)
	);

	$response = curl_exec( $curl );

	curl_close( $curl );

	return $response;

}
