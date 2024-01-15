<?php
/**
 * Plugin Name: Woocommerce - India Post Integration
 * Description: This plugin allows shipment creation via India Post API
 * Author: RawConscious
 * Version: 1.3.0
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Rawconscious
 */

define( 'WC_INDIA_POST_VERSION', '1.3.0' );
define( 'WC_INDIA_POST_PREFIX', 'rc_wcip' );
define( 'RC_LICENSE_KEY', 'rawconscious123' );
define( 'WC_INDIA_POST_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_INDIA_POST_URI', plugin_dir_url( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 */
function activate_wcip() {
	require_once WC_INDIA_POST_PATH . '/includes/wcip-db.php';
	if ( function_exists( 'create_rc_wcip' ) ) {
		create_rc_wcip();
	}
	if ( function_exists( 'create_rc_wcip_log' ) ) {
		create_rc_wcip_log();
	}
	if ( function_exists( 'create_rc_wcip_article_id' ) ) {
		create_rc_wcip_article_id();
	}
}
register_activation_hook( __FILE__, 'activate_wcip' );

// Required enqueue style and scripts.
require WC_INDIA_POST_PATH . '/includes/enqueue.php';
// Required Load Action Hook.
require WC_INDIA_POST_PATH . '/includes/action-hook.php';
// Required to RC API Routes For Woocommerce.
// Required to RC API Routes.
require WC_INDIA_POST_PATH . '/includes/rest-routes/woocommerce.php';
// Required to RC API Routes For India Post.
require WC_INDIA_POST_PATH . '/includes/rest-routes/indiapost.php';
// Required to get article ids.
require WC_INDIA_POST_PATH . '/includes/rest-routes/article-id.php';
// Required to Credentials Routes.
require WC_INDIA_POST_PATH . '/includes/rest-routes/user-credentials.php';
// Required to Booking Routes.
require WC_INDIA_POST_PATH . '/includes/utils/india-post-booking-api.php';
// Required to Booking Routes.
require WC_INDIA_POST_PATH . '/includes/utils/india-post-pickup-api.php';
// Required to get token.
require WC_INDIA_POST_PATH . '/includes/utils/india-post-get-token.php';
// Required to cancel request.
require WC_INDIA_POST_PATH . '/includes/utils/india-post-cancel-api.php';
