<?php
/**
 * Parcelforce globalvalue rates.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Shipping_Zones;
use WooCommerce\RoyalMail\Services;

/**
 * Rates for Parcelforce globalvalue.
 *
 * Based on https://www.parcelforce.com/sites/default/files/parcelforce-uk-and-international-services-and-prices.pdf
 * and 'Prices' tab on https://www.parcelforce.com/sending-parcel/international-parcel-delivery/global-value.
 */
class RoyalMail_Regular_Parcelforce_Globalvalue extends Parcelforce_Rate {
	/**
	 * Pricing bands.
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Regular_Parcelforce_Globalvalue rates.
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
				Shipping_Zones::ZONE_4        => array(
					500   => 1156,
					1000  => 1268,
					1500  => 1380,
					2000  => 1492,
					2500  => 1579,
					3000  => 1666,
					3500  => 1753,
					4000  => 1840,
					4500  => 1927,
					5000  => 2014,
					5500  => 2057,
					6000  => 2100,
					6500  => 2143,
					7000  => 2186,
					7500  => 2229,
					8000  => 2272,
					8500  => 2315,
					9000  => 2358,
					9500  => 2401,
					10000 => 2444,
				),
				Shipping_Zones::ZONE_5        => array(
					500   => 1752,
					1000  => 2012,
					1500  => 2272,
					2000  => 2532,
					2500  => 2786,
					3000  => 3040,
					3500  => 3294,
					4000  => 3548,
					4500  => 3802,
					5000  => 4056,
					5500  => 4168,
					6000  => 4280,
					6500  => 4392,
					7000  => 4504,
					7500  => 4616,
					8000  => 4728,
					8500  => 4840,
					9000  => 4952,
					9500  => 5064,
					10000 => 5176,
				),
				Shipping_Zones::ZONE_6        => array(
					500   => 2359,
					1000  => 2474,
					1500  => 2589,
					2000  => 2704,
					2500  => 2999,
					3000  => 3294,
					3500  => 3589,
					4000  => 3884,
					4500  => 4179,
					5000  => 4474,
					5500  => 4580,
					6000  => 4686,
					6500  => 4792,
					7000  => 4898,
					7500  => 5004,
					8000  => 5110,
					8500  => 5216,
					9000  => 5322,
					9500  => 5428,
					10000 => 5534,
				),
				Shipping_Zones::ZONE_7        => array(
					500   => 2367,
					1000  => 2421,
					1500  => 2475,
					2000  => 2529,
					2500  => 2767,
					3000  => 3005,
					3500  => 3243,
					4000  => 3481,
					4500  => 3719,
					5000  => 3957,
					5500  => 4169,
					6000  => 4381,
					6500  => 4593,
					7000  => 4805,
					7500  => 5017,
					8000  => 5229,
					8500  => 5441,
					9000  => 5653,
					9500  => 5865,
					10000 => 6077,
				),
				Shipping_Zones::ZONE_8        => array(
					500   => 2696,
					1000  => 2940,
					1500  => 3184,
					2000  => 3428,
					2500  => 3640,
					3000  => 3852,
					3500  => 4064,
					4000  => 4276,
					4500  => 4488,
					5000  => 4700,
					5500  => 4868,
					6000  => 5036,
					6500  => 5204,
					7000  => 5372,
					7500  => 5540,
					8000  => 5708,
					8500  => 5876,
					9000  => 6044,
					9500  => 6212,
					10000 => 6380,
				),
				Shipping_Zones::ZONE_9        => array(
					500   => 2820,
					1000  => 3164,
					1500  => 3508,
					2000  => 3852,
					2500  => 4133,
					3000  => 4414,
					3500  => 4695,
					4000  => 4976,
					4500  => 5257,
					5000  => 5538,
					5500  => 5775,
					6000  => 6012,
					6500  => 6249,
					7000  => 6486,
					7500  => 6723,
					8000  => 6960,
					8500  => 7197,
					9000  => 7434,
					9500  => 7671,
					10000 => 7908,
				),
				Shipping_Zones::ZONE_9_NON_EU => array(
					500   => 2820,
					1000  => 3164,
					1500  => 3508,
					2000  => 3852,
					2500  => 4133,
					3000  => 4414,
					3500  => 4695,
					4000  => 4976,
					4500  => 5257,
					5000  => 5538,
					5500  => 5775,
					6000  => 6012,
					6500  => 6249,
					7000  => 6486,
					7500  => 6723,
					8000  => 6960,
					8500  => 7197,
					9000  => 7434,
					9500  => 7671,
					10000 => 7908,
				),
				Shipping_Zones::ZONE_10       => array(
					500   => 2881,
					1000  => 3146,
					1500  => 3411,
					2000  => 3676,
					2500  => 4347,
					3000  => 5018,
					3500  => 5689,
					4000  => 6360,
					4500  => 7031,
					5000  => 7702,
					5500  => 8087,
					6000  => 8472,
					6500  => 8857,
					7000  => 9242,
					7500  => 9627,
					8000  => 10012,
					8500  => 10397,
					9000  => 10782,
					9500  => 11167,
					10000 => 11552,
				),
				Shipping_Zones::ZONE_11       => array(
					500   => 3593,
					1000  => 4243,
					1500  => 4893,
					2000  => 5543,
					2500  => 6178,
					3000  => 6813,
					3500  => 7448,
					4000  => 8083,
					4500  => 8718,
					5000  => 9353,
					5500  => 9826,
					6000  => 10299,
					6500  => 10772,
					7000  => 11245,
					7500  => 11718,
					8000  => 12191,
					8500  => 12664,
					9000  => 13137,
					9500  => 13610,
					10000 => 14083,
				),
				Shipping_Zones::ZONE_12       => array(
					500   => 3718,
					1000  => 4462,
					1500  => 5206,
					2000  => 5950,
					2500  => 6694,
					3000  => 7438,
					3500  => 8182,
					4000  => 8926,
					4500  => 9670,
					5000  => 10414,
					5500  => 11090,
					6000  => 11766,
					6500  => 12442,
					7000  => 13118,
					7500  => 13794,
					8000  => 14470,
					8500  => 15146,
					9000  => 15822,
					9500  => 16498,
					10000 => 17174,
				),
			),
		);
	}

	/**
	 * Maximum inclusive compensation.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_inclusive_compensation = 100;

	/**
	 * Maximum total cover.
	 *
	 * @version 2.5.3
	 * @since 2.5.3
	 *
	 * @var int
	 */
	protected $maximum_total_cover = 500;

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::PARCELFORCE_GLOBALVALUE;
	}
}
