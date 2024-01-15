<?php
/**
 * Enque Required Files.
 *
 * @package Rawconscious.
 */

/**
 * Enqueue Scripts and Styles.
 */
function wcip_enqueue_scripts() {

	$version = WC_INDIA_POST_VERSION;
	if ( defined( 'LOCAL_ENV' ) && true === LOCAL_ENV ) {
		$version = wp_rand( 1, 9999 );
	}
	if ( 'woocommerce_page_woocommerce-indiapost' === get_current_screen()->id ) {
		$license_key        = RC_LICENSE_KEY;
		$validation_request = "http://admin.rawconscious.com/api/validate?license_key=" . $license_key; 
		$license_validation = file_get_contents($validation_request);
		$license_validation = unserialize( $license_validation );
		if ( true === $license_validation['valid'] || false === $license_validation ) {
			
			$dep_rc_wcip = require WC_INDIA_POST_PATH . 'build/rc-wcip/rc-wcip.asset.php';
			$dep_rc_wcip = array_unique( array_merge( $dep_rc_wcip['dependencies'], array() ), SORT_REGULAR );
			wp_enqueue_script( WC_INDIA_POST_PREFIX . '-script', WC_INDIA_POST_URI . 'build/rc-wcip/rc-wcip.js', $dep_rc_wcip, $version, true );
			wp_enqueue_style( WC_INDIA_POST_PREFIX . '-style', WC_INDIA_POST_URI . 'build/rc-wcip/rc-wcip.css', array(), $version );
			wp_add_inline_script( WC_INDIA_POST_PREFIX .'-script', 'window.location.wcipSiteUrl = "' . site_url() .'";', 'before' );
		} else {

			$dep_rc_invalid = require WC_INDIA_POST_PATH . 'build/invalid/rc-invalid.asset.php';
			$dep_rc_invalid = array_unique( array_merge( $dep_rc_invalid['dependencies'], array() ), SORT_REGULAR );
			wp_enqueue_script( WC_INDIA_POST_PREFIX . '-script-error', WC_INDIA_POST_URI . 'build/invalid/rc-invalid.js', $dep_rc_invalid, $version, true );
		}
	}
}

add_action( 'admin_enqueue_scripts', 'wcip_enqueue_scripts' );

