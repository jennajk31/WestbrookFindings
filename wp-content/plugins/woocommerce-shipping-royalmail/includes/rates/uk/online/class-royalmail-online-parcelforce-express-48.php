<?php
/**
 * Parcelforce express48 rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;

/**
 * RoyalMail_Online_Parcelforce_Express_48 class.
 *
 * Up-to-date as of 1/1/2025.
 * As per https://www.parcelforce.com/sites/default/files/parcelforce-retail-uk-and-international-online-prices.pdf.
 * See page 4.
 */
class RoyalMail_Online_Parcelforce_Express_48 extends RoyalMail_Rate {

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
		'2025' => array(
			150 => array(
				5000  => 965,
				10000 => 1175,
				20000 => 1495,
				30000 => 1795,
			),
		),
	);

	/**
	 * Boxes for express9, 10, AM, 24, 48 – maximum length of 1.5m and 3m length/girth
	 * combined
	 *
	 * Boxes express48large – maximum length of 2.5m and 5m length/girth combined.
	 *
	 * Include few variations of this box to cover odd shaped items.
	 *
	 * @var array Shipping boxes
	 */
	protected $boxes = array(
		'packet' => array(
			'length' => 1500,
			'width'  => 750,
			'height' => 750,
			'weight' => 30000,
		),
	);

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::PARCELFORCE_EXPRESS_48;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 *
	 * @return array{ 'parcelforce-express-48': float }|false|null
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

		// Rates include 20% VAT.
		$quote                            = $quote / 1.2;
		$quote                            = $quote / 100;
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $quote;

		return $quotes;
	}
}
