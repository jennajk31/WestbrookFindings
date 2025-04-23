<?php
/**
 * Privacy class file.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * Privacy class.
 */
class WC_Shipping_Canada_Post_Privacy extends WC_Abstract_Privacy {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct( __( 'Canada Post', 'woocommerce-shipping-canada-post' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		// translators: %s is a canada post plugin documentation URL.
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-shipping-canada-post' ), 'https://docs.woocommerce.com/document/privacy-shipping/#woocommerce-shipping-canada-post' ) );
	}
}

new WC_Shipping_Canada_Post_Privacy();
