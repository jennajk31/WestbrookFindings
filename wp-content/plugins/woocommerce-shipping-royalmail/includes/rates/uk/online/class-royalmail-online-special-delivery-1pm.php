<?php
/**
 * Special Delivery Guaranteed by 1pm rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Packaging;
use WooCommerce\RoyalMail\Services;

/**
 * RoyalMail_Online_Special_Delivery_1pm class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf.
 * See UK Guaranteed page 4.
 */
class RoyalMail_Online_Special_Delivery_1pm extends RoyalMail_Rate {

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
				100   => 775,
				500   => 875,
				1000  => 975,
				2000  => 1275,
				10000 => 1735,
				20000 => 2135,
			),
			1000 => array(
				100   => 1075,
				500   => 1175,
				1000  => 1275,
				2000  => 1575,
				10000 => 2035,
				20000 => 2435,
			),
			2500 => array(
				100   => 1775,
				500   => 1875,
				1000  => 1975,
				2000  => 2275,
				10000 => 2735,
				20000 => 3135,
			),
		),
		'2025' => array(
			750  => array(
				100   => 815,
				500   => 925,
				1000  => 1035,
				2000  => 1345,
				10000 => 1835,
				20000 => 2255,
			),
			1000 => array(
				100   => 1115,
				500   => 1225,
				1000  => 1335,
				2000  => 1645,
				10000 => 2135,
				20000 => 2555,
			),
			2500 => array(
				100   => 1815,
				500   => 1925,
				1000  => 2035,
				2000  => 2345,
				10000 => 2835,
				20000 => 3255,
			),
		),
	);

	/**
	 * Shipping boxes.
	 *
	 * @var array
	 */
	protected $boxes = array(
		Packaging::PACKET => array(
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
