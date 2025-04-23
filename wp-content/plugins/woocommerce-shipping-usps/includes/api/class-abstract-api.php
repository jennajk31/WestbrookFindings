<?php
/**
 * USPS Abstract API class file.
 *
 * @package WC_Shipping_USPS
 */

namespace WooCommerce\USPS\API;

use WC_Shipping_USPS;

/**
 * FedEx Abstract API class.
 */
abstract class Abstract_API {

	/**
	 * Endpoint for the API.
	 *
	 * @var string
	 */
	protected $endpoint;

	/**
	 * FedEx Shipping Method Class.
	 *
	 * @var WC_Shipping_USPS
	 */
	protected $shipping_method;

	/**
	 * Calculate shipping cost.
	 *
	 * @param array $package Package to ship.
	 *
	 * @return void
	 */
	abstract public function calculate_shipping( $package );

	/**
	 * Perform a request to check user ID validness.
	 *
	 * Error notice will displayed if user ID is invalid.
	 */
	abstract public function validate_credentials();
}
