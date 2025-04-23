<?php
/**
 * Parcelforce globaleconomy rates.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Shipping_Zones;
use WooCommerce\RoyalMail\Services;

/**
 * Rates for Parcelforce globaleconomy.
 *
 * Based on https://www.parcelforce.com/sites/default/files/parcelforce-uk-and-international-services-and-prices.pdf
 */
class RoyalMail_Regular_Parcelforce_Globaleconomy extends Parcelforce_Rate {
	/**
	 * Pricing bands.
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Regular_Parcelforce_Globaleconomy rates.
	 */
	public function setup() {
		$this->bands = $this->initialize_bands();
	}

	/**
	 * Initializes Pricing bands.
	 *
	 * Key is zone and value is an array where key is weight (up to) in gram
	 * and value is the price (in penny).
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Shipping_Zones::ZONE_10 => array(
					500   => 2569,
					1000  => 2834,
					1500  => 3099,
					2000  => 3364,
					2500  => 4035,
					3000  => 4706,
					3500  => 5377,
					4000  => 6048,
					4500  => 6719,
					5000  => 7390,
					5500  => 7775,
					6000  => 8160,
					6500  => 8545,
					7000  => 8930,
					7500  => 9315,
					8000  => 9700,
					8500  => 10085,
					9000  => 10470,
					9500  => 10855,
					10000 => 11240,
				),
				Shipping_Zones::ZONE_11 => array(
					500   => 3312,
					1000  => 3962,
					1500  => 4612,
					2000  => 5262,
					2500  => 5897,
					3000  => 6532,
					3500  => 7167,
					4000  => 7802,
					4500  => 8437,
					5000  => 9072,
					5500  => 9545,
					6000  => 10018,
					6500  => 10491,
					7000  => 10964,
					7500  => 11437,
					8000  => 11910,
					8500  => 12383,
					9000  => 12856,
					9500  => 13329,
					10000 => 13802,
				),
				Shipping_Zones::ZONE_12 => array(
					500   => 3468,
					1000  => 4212,
					1500  => 4956,
					2000  => 5700,
					2500  => 6444,
					3000  => 7188,
					3500  => 7932,
					4000  => 8676,
					4500  => 9420,
					5000  => 10164,
					5500  => 10840,
					6000  => 11516,
					6500  => 12192,
					7000  => 12868,
					7500  => 13544,
					8000  => 14220,
					8500  => 14896,
					9000  => 15572,
					9500  => 16248,
					10000 => 16924,
				),
			),
		);
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::PARCELFORCE_GLOBALECONOMY;
	}
}
