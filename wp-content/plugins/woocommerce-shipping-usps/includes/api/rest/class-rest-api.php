<?php
/**
 * USPS Legacy API class file.
 *
 * @package WC_Shipping_USPS
 */

namespace WooCommerce\USPS\API;

require_once WC_USPS_API_DIR . '/class-abstract-api.php';

// phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase --- USPS API provides an object with camelCase properties and method

/**
 * USPS REST API class.
 */
class REST_API extends Abstract_API {

	/**
	 * Class constructor.
	 *
	 * @param WC_Shipping_USPS $shipping_method USPS shipping method object.
	 */
	public function __construct( $shipping_method ) {
		$this->shipping_method = $shipping_method;
	}

	/**
	 * Calculate shipping cost.
	 *
	 * @since   4.4.7
	 * @version 4.4.7
	 *
	 * @param array $package Package to ship.
	 *
	 * @return void
	 */
	public function calculate_shipping( $package ) {
	}

	/**
	 * Perform a request to check REST API credentials.
	 */
	public function validate_credentials() {
	}
}
