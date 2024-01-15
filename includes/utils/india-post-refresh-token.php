<?php
/**
 * Refresh Token India Post.
 *
 * @package rawconscious.
 */

/**
 * Refresh Token from India POST.
 *
 * @param array $data data.
 */

function get_token( $data ) {

	$curl = curl_init();

    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL            => 'https://gateway.cept.gov.in/auth/keycloak/refreshtoken',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'GET',
            CURLOPT_HTTPHEADER     => array(
                'RefreshToken: eyJhbGciOiJIUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJhMzNjMGRlNy1kMzhhLTQ0YTMtODA4OC0wN2QxOWQ1ZDIwZmUifQ.eyJleHAiOjE2MzExNzI4MDYsImlhdCI6MTYzMTE3MTAwNiwianRpIjoiNWZkZjk2ODQtZWI3ZS00YzZhLWIwZTYtZmFlMGYyNDc4ZTliIiwiaXNzIjoiaHR0cDovLzE3Mi4yNC4xOS4xODo4MDgwL2F1dGgvcmVhbG1zL1NwcmluZ0tleUNsb2NrIiwiYXVkIjoiaHR0cDovLzE3Mi4yNC4xOS4xODo4MDgwL2F1dGgvcmVhbG1zL1NwcmluZ0tleUNsb2NrIiwic3ViIjoiNzY2NWJiZDQtYTY1ZS00YTJjLTk5ZWEtYjk5YTFhMGMyNDhhIiwidHlwIjoiUmVmcmVzaCIsImF6cCI6IktleWNsb2FrU2VjdXJpdHkiLCJzZXNzaW9uX3N0YXRlIjoiZDlmNDk3ZmYtY2YzYS00NTcwLThlNTUtNDAxNjAyNThmM2IyIiwic2NvcGUiOiJlbWFpbCBwcm9maWxlIiwic2lkIjoiZDlmNDk3ZmYtY2YzYS00NTcwLThlNTUtNDAxNjAyNThmM2IyIn0.noQW8Gz_1XFJAzEVEBbFGRUc44slTiOlyMR5VCdpHqk',
                'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCIgOiAiSldUIiwia2lkIiA6ICJWNWZtNHhERDI5RUg3WXZqZVM3aU9hNjdtd1FvcWlxcXJLUU9hcG5pOEcwIn0.eyJleHAiOjE2NDM0NDk0MTUsImlhdCI6MTY0MzQ0NTgxNSwianRpIjoiNWFiNzQ2NDMtZjdlOC00MmI1LWIwZWItYjEzYjRiNjUxNDU3IiwiaXNzIjoiaHR0cDovLzE3Mi4yNC4xOS4xODo4MDgwL2F1dGgvcmVhbG1zL1NwcmluZ0tleUNsb2NrIiwiYXVkIjoiYWNjb3VudCIsInN1YiI6Ijc2NjViYmQ0LWE2NWUtNGEyYy05OWVhLWI5OWExYTBjMjQ4YSIsInR5cCI6IkJlYXJlciIsImF6cCI6IktleWNsb2FrU2VjdXJpdHkiLCJzZXNzaW9uX3N0YXRlIjoiNTFlZjllNTUtYmM3Ny00NWM0LWFhMDQtZTgxNDRmNDdiMGNiIiwiYWNyIjoiMSIsInJlYWxtX2FjY2VzcyI6eyJyb2xlcyI6WyJvZmZsaW5lX2FjY2VzcyIsInVtYV9hdXRob3JpemF0aW9uIiwiYXBwLXVzZXIiLCJkZWZhdWx0LXJvbGVzLXNwcmluZ2tleWNsb2NrIl19LCJyZXNvdXJjZV9hY2Nlc3MiOnsiS2V5Y2xvYWtTZWN1cml0eSI6eyJyb2xlcyI6WyJ1c2VyIl19LCJhY2NvdW50Ijp7InJvbGVzIjpbIm1hbmFnZS1hY2NvdW50IiwibWFuYWdlLWFjY291bnQtbGlua3MiLCJ2aWV3LXByb2ZpbGUiXX19LCJzY29wZSI6ImVtYWlsIHByb2ZpbGUiLCJzaWQiOiI1MWVmOWU1NS1iYzc3LTQ1YzQtYWEwNC1lODE0NGY0N2IwY2IiLCJlbWFpbF92ZXJpZmllZCI6ZmFsc2UsIm5hbWUiOiJCdWxrQ3VzdG9tZXIgVGVzdGluZyIsInByZWZlcnJlZF91c2VybmFtZSI6InRlc3RjdXN0b21lciIsImdpdmVuX25hbWUiOiJCdWxrQ3VzdG9tZXIiLCJmYW1pbHlfbmFtZSI6IlRlc3RpbmciLCJlbWFpbCI6InRlc3RjdXN0b21lckBnbWFpbC5jb20ifQ.dMan9wqAy_STKstKnUCvuPzamRx5piErZeIuxVNgwZ9kcgBOem2mUuu-Nc5lZT9HoH7gp5KFzstltFNQLtdJfRukYbFNQCAIY2xtnxKRnTGL4bPcJERMa0UBia5ay5wiBtX11hdN7bVUGX1Yi3TIIAJOwQhtEaJP5JCqYKtri8uQdKp_aM15deBey7NvfAQFzwjbIFisEOpzuunHVQUGsVQBwfkhyYwjO7XhU8bhy9weawNAQOA4an8GKAaZNHj5vNuBYP6Gc5oXL49r21q1ASk3Ijbphb4GU3T6po_XCukmx5I1q3W2a9cvlXF3Vio8FAK3n9mf03IZd02xnlll8A',
                'Cookie: JSESSIONID=D0F89D42F44308DE15A305DE9BD0E441',
            ),
        )
    );

    $response = curl_exec( $curl );

    curl_close( $curl );
    echo $response;

    return $response;
}