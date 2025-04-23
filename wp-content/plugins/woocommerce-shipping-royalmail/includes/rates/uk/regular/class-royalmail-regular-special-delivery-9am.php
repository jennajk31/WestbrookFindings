<?php
/**
 * Special Delivery Guaranteed by 9am rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;

/**
 * RoyalMail_Regular_Special_Delivery_9am class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See UK Guaranteed page 4.
 */
class RoyalMail_Regular_Special_Delivery_9am extends RoyalMail_Rate {

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
			50   => array(
				100  => 3195,
				500  => 3695,
				1000 => 4195,
				2000 => 5295,
			),
			1000 => array(
				100  => 3895,
				500  => 4395,
				1000 => 4895,
				2000 => 5995,
			),
			2500 => array(
				100  => 4695,
				500  => 5195,
				1000 => 5695,
				2000 => 6795,
			),
		),
		'2025' => array(
			50   => array(
				100  => 3895,
				500  => 4395,
				1000 => 4895,
				2000 => 6095,
			),
			1000 => array(
				100  => 4595,
				500  => 5095,
				1000 => 5595,
				2000 => 6795,
			),
			2500 => array(
				100  => 5395,
				500  => 5895,
				1000 => 6395,
				2000 => 7595,
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
			'length' => 610,  // Max length.
			'width'  => 460,  // Max width.
			'height' => 460,  // Max height.
			'weight' => 2000, // Max weight.
		),
	);

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::SPECIAL_DELIVERY_9AM;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 *
	 * @return array{ 'special-delivery-9am': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination ) {
		$quote    = false;
		$packages = $this->get_packages( $items, $packing_method );

		// Service not available for the following destinations.
		$excluded_destinations = array(
			'GG', // Guernsey.
			'IM', // Isle of Man.
			'JE', // Jersey.
		);

		if ( in_array( $destination, $excluded_destinations, true ) ) {
			return false;
		}

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

		// Rates include 20% VAT.
		$quote = $quote / 1.2;
		$quote = $quote / 100;

		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $quote;

		return $quotes;
	}
}
