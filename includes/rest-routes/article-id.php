<?php
/**
 * Rest Route File For Article IDs.
 *
 * @package rawconscious.
 */

add_action( 'rest_api_init', 'register_rest_routes_article_ids' );
/**
 * Register Rest Routes.
 */
function register_rest_routes_article_ids() {
	// Register Route for import or append article ids.
	register_rest_route(
		'rc-wcip/v1',
		'import/append-article-ids',
		array(
			'methods'             => 'POST',
			'callback'            => 'append_article_ids',
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
	// Register Route import/overwrite article ids.
	register_rest_route(
		'rc-wcip/v1',
		'import/overwrite-article-ids',
		array(
			'methods'             => 'POST',
			'callback'            => 'overwrite_article_ids',
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
	// Register Route export article ids.
	// register_rest_route(
	// 'rc-wcip/v1',
	// 'export-article-ids',
	// array(
	// 'methods'             => 'POST',
	// 'callback'            => 'export_article_ids',
	// 'permission_callback' => '__return_true',
	// 'args'                => array(
	// 'id' => array(
	// 'validate_callback' => function( $param, $request, $key ) {
	// return is_numeric( $param );
	// },
	// ),
	// ),
	// )
	// );
	// Register Route for get unused article ids.
	register_rest_route(
		'rc-wcip/v1',
		'get-unused-article-id-count',
		array(
			'methods'             => 'GET',
			'callback'            => 'get_unused_article_id_count',
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
 * Callback functions for import/append article ids from CSV file.
 *
 * @param WP_REST_Request $request .
 */
function overwrite_article_ids( WP_REST_Request $request ) {
	global $wpdb;

	$table_rc_wcip_article_id = $wpdb->prefix . 'rc_wcip_article_id';

	$article_content = isset( $_FILES['articleId'] ) ? $_FILES['articleId'] : null;

	if ( empty( $article_content ) ) {
		return array(
			'isSuccess' => false,
			'message'   => 'No file uploaded.',
		);
	}

	$article_temp_name = $article_content['tmp_name'];
	$article_id_size   = $article_content['size'];

	$wpdb->query( "TRUNCATE TABLE $table_rc_wcip_article_id" );

	if ( $article_id_size > 0 ) {
		$article_file = fopen( $article_temp_name, 'r' );

		if ( ! $article_file ) {
			return array(
				'isSuccess' => false,
				'message'   => 'Failed to open the file.',
			);
		}

		$current_count = 0;

		while ( ( $get_data = fgetcsv( $article_file, 10000, ',' ) ) !== false ) {
			$article_id = sanitize_text_field( $get_data[0] );

			if ( ! empty( $article_id ) ) {
				$insert_data = array(
					'article_id' => $article_id,
				);

				$insert_results = $wpdb->insert( $table_rc_wcip_article_id, $insert_data );
				$current_count  = $insert_results ? $current_count + 1 : $current_count;

			}
		}

		fclose( $article_file );

		if ( 0 < $current_count ) {
			return array(
				'isSuccess' => true,
				'message'   => $current_count . ' Article IDs imported successfully.',
			);
		} else {
			return array(
				'isSuccess' => false,
				'message'   => 'Failed to insert article IDs.',
			);
		}
	} else {
		return array(
			'isSuccess' => false,
			'message'   => 'Empty CSV file.',
		);
	}
}

/**
 * Callback functions for import/override article ids from CSV file.
 *
 * @param WP_REST_Request $request .
 */
function append_article_ids( WP_REST_Request $request ) {
	global $wpdb;

	$table_rc_wcip_article_id = $wpdb->prefix . 'rc_wcip_article_id';

	$article_content = isset( $_FILES['articleId'] ) ? $_FILES['articleId'] : null;

	if ( empty( $article_content ) ) {
		return array(
			'isSuccess' => false,
			'message'   => 'No file uploaded.',
		);
	}

	$article_temp_name = $article_content['tmp_name'];
	$article_id_size   = $article_content['size'];

	if ( $article_id_size > 0 ) {
		$article_file = fopen( $article_temp_name, 'r' );

		if ( ! $article_file ) {
			return array(
				'isSuccess' => false,
				'message'   => 'Failed to open the file.',
			);
		}

		$current_count = 0;

		while ( ( $get_data = fgetcsv( $article_file, 10000, ',' ) ) !== false ) {
			$article_id  = sanitize_text_field( $get_data[0] );
			$is_existing = false;

			if ( ! empty( $article_id ) ) {
				$article_data = array(
					'article_id' => $article_id,
					'state'      => 'unused',
				);

				$update_location = array(
					'article_id' => $article_id,
				);

				$is_existing = $wpdb->get_var( "SELECT article_id FROM $table_rc_wcip_article_id WHERE article_id = '$article_id'" );

				if ( $is_existing ) {
					$update_results = $wpdb->update( $table_rc_wcip_article_id, $article_data, $update_location );
					$current_count  = ( false !== $update_results ) ? $current_count + 1 : $current_count;
				} else {
					$insert_results = $wpdb->insert( $table_rc_wcip_article_id, $article_data );
					$current_count  = $insert_results ? $current_count + 1 : $current_count;
				}
			}
		}

		fclose( $article_file );

		if ( 0 < $current_count ) {
			return array(
				'isSuccess' => true,
				'message'   => $current_count . ' Article IDs imported successfully.',
			);
		} else {
			return array(
				'isSuccess' => false,
				'message'   => 'Failed to insert article IDs.',
			);
		}
	} else {
		return array(
			'isSuccess' => false,
			'message'   => 'Empty CSV file.',
		);
	}
}

/**
 * Callback function retrieve count of unused article ids.
 *
 * @param WP_REST_Request $request.
 */
function get_unused_article_id_count( WP_REST_Request $request ) {
	global $wpdb;

	$table_rc_wcip_article_id = $wpdb->prefix . 'rc_wcip_article_id';

	$aricle_id_count = $wpdb->get_var( "SELECT count(article_id) AS articleIds from $table_rc_wcip_article_id WHERE state = 'unused'" );

	if ( $aricle_id_count ) {
		return array(
			'isSuccess' => true,
			'count'     => $aricle_id_count,
		);
	} else {
		return array(
			'isSuccess' => false,
		);
	}

}
