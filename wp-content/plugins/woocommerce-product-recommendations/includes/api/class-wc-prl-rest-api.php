<?php
/**
 * WC_PRL_REST_API class
 *
 * @package  Woo Product Recommendations
 * @since    4.1.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The REST controller.
 *
 * @class    WC_PRL_REST_API
 * @version  4.1.0
 */
class WC_PRL_REST_API {

	/**
	 * Setup.
	 */
	public static function init() {
		add_action( 'rest_api_init', array( __CLASS__, 'rest_api_init' ), 0 );
	}

	public static function rest_api_init() {
		require_once WC_PRL_ABSPATH . 'includes/api/class-wc-prl-rest-amplifier-products-controller.php';
		$controller = new WC_PRL_Rest_Amplifier_Products_V1_Controller();
		$controller->register_routes();
	}
}

WC_PRL_REST_API::init();
