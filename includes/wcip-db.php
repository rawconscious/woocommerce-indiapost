<?php
/**
 * DB.
 *
 * @package RawConscious.
 */

/**
 * WCIP Database.
 */
function create_rc_wcip() {

	global $wpdb;
	$table_name  = $wpdb->prefix . 'rc_wcip';
	$db_version  = get_option( 'rc_wcip' );
	$new_version = '1.0.0';
	$wp_post     = $wpdb->prefix . 'posts';

	if ( $wpdb->get_var( "show tables like '{$table_name}'" ) !== $table_name ||
	version_compare( $update_db_version, $db_version ) < 0 ) {

		$charset_collate = $wpdb->get_charset_collate();

		$sql[] = 'CREATE TABLE ' . $table_name . " (
            wcip_id int(11) NOT NULL AUTO_INCREMENT,
            order_id bigint(20) unsigned,
            indiapost_id varchar(50) NOT NUll,
            status varchar(50) NOT NULL,
            indiapost_article_id varchar(50) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (wcip_id),
            FOREIGN KEY (order_id) REFERENCES $wp_post(ID)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			/**
			 * It seems IF NOT EXISTS isn't needed if you're using dbDelta - if the table already exists it'll
			 * compare the schema and update it instead of overwriting the whole table.
		 *
		 * @link https://code.tutsplus.com/tutorials/custom-database-tables-maintaining-the-database--wp-28455
		 */
		dbDelta( $sql );

		add_option( 'rc_wcip', $update_db_version );

	}

}


/**
 * Log Database.
 */
function create_rc_wcip_log() {

	global $wpdb;
	$table_name  = $wpdb->prefix . 'rc_wcip_log';
	$db_version  = get_option('rc_wcip_log');
	$new_version = '1.0.0';
	$wp_post     = $wpdb->prefix . 'posts';

	if ( $wpdb->get_var( "show tables like '{$table_name}'" ) !== $table_name ||
	version_compare( $new_version, $db_version ) < 0 ) {

		$charset_collate = $wpdb->get_charset_collate();

		$sql[] = 'CREATE TABLE ' . $table_name . " (
            log_id int(11) NOT NULL AUTO_INCREMENT,
            request_id bigint(20) unsigned,
            request_type varchar(20) NOT NUll,
            request_json varchar(5000) NOT NULL,
            return_json varchar(5000) NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (log_id),
            FOREIGN KEY (request_id) REFERENCES $wp_post(ID)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			/**
			 * It seems IF NOT EXISTS isn't needed if you're using dbDelta - if the table already exists it'll
			 * compare the schema and update it instead of overwriting the whole table.
			 *
			 * @link https://code.tutsplus.com/tutorials/custom-database-tables-maintaining-the-database--wp-28455
			 */
			dbDelta( $sql );

			add_option( 'rc_wcip_log', $update_db_version );

	}

}

/**
 * Article Id Database.
 */
function create_rc_wcip_article_id() {

	global $wpdb;
	$table_name  = $wpdb->prefix . 'rc_wcip_article_id';
	$db_version  = get_option('rc_wcip_article_id');
	$new_version = '1.0.0';

	if ( $wpdb->get_var( "show tables like '{$table_name}'" ) !== $table_name ||
	version_compare( $new_version, $db_version ) < 0 ) {

		$charset_collate = $wpdb->get_charset_collate();

		$sql[] = 'CREATE TABLE ' . $table_name . " (
			article_id varchar(50) NOT NULL,
            state varchar(20) NOT NULL DEFAULT 'unused',
            PRIMARY KEY (article_id)
            ) $charset_collate;";

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			/**
			 * It seems IF NOT EXISTS isn't needed if you're using dbDelta - if the table already exists it'll
			 * compare the schema and update it instead of overwriting the whole table.
			 *
			 * @link https://code.tutsplus.com/tutorials/custom-database-tables-maintaining-the-database--wp-28455
			 */
			dbDelta( $sql );

			add_option( 'rc_wcip_article_id', $new_version );

	}

}

