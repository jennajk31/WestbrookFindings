<?php
/**
 * Special Delivery Guaranteed by 1pm rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;

/**
 * RoyalMail_Regular_Special_Delivery_1pm class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See UK Guaranteed page 4.
 */
class RoyalMail_Regular_Special_Delivery_1pm extends RoyalMail_Rate {

	/**
	 * Pricing bands.
	 *
	 * Key is coverage / compensation for loss or damage and value is key-value
	 * array where key is weight (up to and including) and value is the price
	 * in penny.
	 *
	 * @var array
	 */
	protected $bands = array(
		'2024' => array(
			750  => array(
				100   => 835,
				500   => 935,
				1000  => 1035,
				2000  => 1335,
				10000 => 1855,
				20000 => 2255,
			),
			1000 => array(
				100   => 1135,
				500   => 1235,
				1000  => 1335,
				2000  => 1635,
				10000 => 2155,
				20000 => 2555,
			),
			2500 => array(
				100   => 1835,
				500   => 1935,
				1000  => 2035,
				2000  => 2335,
				10000 => 2855,
				20000 => 3255,
			),
		),
		'2025' => array(
			750  => array(
				100   => 875,
				500   => 985,
				1000  => 1095,
				2000  => 1405,
				10000 => 1955,
				20000 => 2375,
			),
			1000 => array(
				100   => 1175,
				500   => 1285,
				1000  => 1395,
				2000  => 1705,
				10000 => 2255,
				20000 => 2675,
			),
			2500 => array(
				100   => 1875,
				500   => 1985,
				1000  => 2095,
				2000  => 2405,
				10000 => 2955,
				20000 => 3375,
			),
		),
	);

	/**
	 * Shipping boxes.
	 *
	 * @var array
	 */
	protected $boxes = array(
		'packet' => array(
			'length' => 610,   // Max length.
			'width'  => 460,   // Max width.
			'height' => 460,   // Max height.
			'weight' => 20000, // Max weight.
		),
	);

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::SPECIAL_DELIVERY_1PM;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 *
	 * @return array{ 'special-delivery-1pm': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination ) {
		$quote    = false;
		$packages = $this->get_packages( $items, $packing_method );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( empty( $package->id ) ) {
					// Try a tube or fail.
					if ( $package->length < 900 && $package->length + ( $package->width * 2 ) < 1040 ) {
						$package->id = 'packet';
					} else {
						return false; // Unpacked item.
					}
				}

				$bands   = $this->get_rate_bands();
				$matched = false;

				foreach ( $bands as $coverage => $weight_bands ) {
					if ( is_numeric( $coverage ) && $package->value > $coverage ) {
						continue;
					}
					foreach ( $weight_bands as $weight => $value ) {

						if ( is_numeric( $weight ) && $package->weight <= $weight ) {
							$quote  += $value;
							$matched = true;
							break 2;
						}
					}
				}

				if ( ! $matched ) {
					return;
				}
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $quote / 100;

		return $quotes;
	}
}
