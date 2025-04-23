<?php
/**
 * Parcelforce express48 Large rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;

/**
 * RoyalMail_Regular_Parcelforce_Express_48_Large class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See Parcelforce WorldWide page 7.
 */
class RoyalMail_Regular_Parcelforce_Express_48_Large extends RoyalMail_Rate {

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
			150 => array(
				5000  => 4795,
				10000 => 5495,
				20000 => 6795,
				30000 => 9095,
			),
		),
		'2025' => array(
			150 => array(
				5000  => 5035,
				10000 => 5770,
				20000 => 7135,
				30000 => 9550,
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
			'length' => 2500,
			'width'  => 1250,
			'height' => 1250,
			'weight' => 30000,
		),
	);

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::PARCELFORCE_EXPRESS_48_LARGE;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 *
	 * @return array{ 'parcelforce-express-48-large': float }|false|null
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
		$quote = $quote / 1.2;
		$quote = $quote / 100;

		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $quote;

		return $quotes;
	}
}
