<?php
/**
 * Privacy handler class.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

/**
 * Privacy handler.
 */
class WC_Royalmail_Privacy extends WC_Abstract_Privacy {
	/**
	 * Class constructor.
	 */
	public function __construct() {
		parent::__construct( __( 'Royalmail', 'woocommerce-shipping-royalmail' ) );
	}

	/**
	 * Gets the message of the privacy to display.
	 */
	public function get_privacy_message() {
		// translators: %s is a URL to plugin documentation.
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-shipping-royalmail' ), 'https://docs.woocommerce.com/document/privacy-shipping/#woocommerce-shipping-royalmail' ) );
	}
}

new WC_Royalmail_Privacy();
