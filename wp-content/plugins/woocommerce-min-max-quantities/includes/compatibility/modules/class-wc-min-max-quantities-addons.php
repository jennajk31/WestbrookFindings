<?php
/**
 * WooCommerce Min/Max Quantities Addons Compatibility
 *
 * @package  WooCommerce Min Max Quantities
 * @since    2.3.15
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Addons Compatibility.
 *
 * @version 2.3.15
 */
class WC_Min_Max_Quantities_Addons {
	/**
	 * Checks if checkout page is on set multiple shipping addresses.
	 *
	 * @since 2.3.15
	 * @version 2.3.15
	 * @return bool
	 */
	public function is_multiple_shipping_address_page() {
		$page_id = wc_get_page_id( 'multiple_addresses' );

		if ( -1 !== $page_id && is_page( $page_id ) ) {
			return true;
		}

		return false;
	}
}
