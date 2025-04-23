<?php
/**
 * Parcelforce globalpriority rates.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Shipping_Zones;
use WooCommerce\RoyalMail\Services;

/**
 * Rates for Parcelforce globalpriority.
 *
 * Based on https://www.parcelforce.com/sites/default/files/parcelforce-uk-and-international-services-and-prices.pdf
 */
class RoyalMail_Regular_Parcelforce_Globalpriority extends Parcelforce_Rate {
	/**
	 * Pricing bands.
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Regular_Parcelforce_Globalpriority rates.
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
					500   => 2600,
					1000  => 2704,
					1500  => 2808,
					2000  => 2912,
					2500  => 3122,
					3000  => 3332,
					3500  => 3542,
					4000  => 3752,
					4500  => 3962,
					5000  => 4172,
					5500  => 4216,
					6000  => 4260,
					6500  => 4304,
					7000  => 4348,
					7500  => 4392,
					8000  => 4436,
					8500  => 4480,
					9000  => 4524,
					9500  => 4568,
					10000 => 4612,
				),
				Shipping_Zones::ZONE_5        => array(
					500   => 2704,
					1000  => 2808,
					1500  => 2912,
					2000  => 3016,
					2500  => 3450,
					3000  => 3884,
					3500  => 4318,
					4000  => 4752,
					4500  => 5186,
					5000  => 5620,
					5500  => 5738,
					6000  => 5856,
					6500  => 5974,
					7000  => 6092,
					7500  => 6210,
					8000  => 6328,
					8500  => 6446,
					9000  => 6564,
					9500  => 6682,
					10000 => 6800,
				),
				Shipping_Zones::ZONE_6        => array(
					500   => 2704,
					1000  => 2808,
					1500  => 2912,
					2000  => 3016,
					2500  => 3465,
					3000  => 3914,
					3500  => 4363,
					4000  => 4812,
					4500  => 5261,
					5000  => 5710,
					5500  => 5816,
					6000  => 5922,
					6500  => 6028,
					7000  => 6134,
					7500  => 6240,
					8000  => 6346,
					8500  => 6452,
					9000  => 6558,
					9500  => 6664,
					10000 => 6770,
				),
				Shipping_Zones::ZONE_7        => array(
					500   => 3120,
					1000  => 3224,
					1500  => 3328,
					2000  => 3432,
					2500  => 3809,
					3000  => 4186,
					3500  => 4563,
					4000  => 4940,
					4500  => 5317,
					5000  => 5694,
					5500  => 5856,
					6000  => 6018,
					6500  => 6180,
					7000  => 6342,
					7500  => 6504,
					8000  => 6666,
					8500  => 6828,
					9000  => 6990,
					9500  => 7152,
					10000 => 7314,
				),
				Shipping_Zones::ZONE_8        => array(
					500   => 3952,
					1000  => 4056,
					1500  => 4160,
					2000  => 4264,
					2500  => 4584,
					3000  => 4904,
					3500  => 5224,
					4000  => 5544,
					4500  => 5864,
					5000  => 6184,
					5500  => 6384,
					6000  => 6584,
					6500  => 6784,
					7000  => 6984,
					7500  => 7184,
					8000  => 7384,
					8500  => 7584,
					9000  => 7784,
					9500  => 7984,
					10000 => 8184,
				),
				Shipping_Zones::ZONE_9        => array(
					500   => 4387,
					1000  => 4603,
					1500  => 4819,
					2000  => 5035,
					2500  => 5383,
					3000  => 5731,
					3500  => 6079,
					4000  => 6427,
					4500  => 6775,
					5000  => 7123,
					5500  => 7366,
					6000  => 7609,
					6500  => 7852,
					7000  => 8095,
					7500  => 8338,
					8000  => 8581,
					8500  => 8824,
					9000  => 9067,
					9500  => 9310,
					10000 => 9553,
				),
				Shipping_Zones::ZONE_9_NON_EU => array(
					500   => 4387,
					1000  => 4603,
					1500  => 4819,
					2000  => 5035,
					2500  => 5383,
					3000  => 5731,
					3500  => 6079,
					4000  => 6427,
					4500  => 6775,
					5000  => 7123,
					5500  => 7366,
					6000  => 7609,
					6500  => 7852,
					7000  => 8095,
					7500  => 8338,
					8000  => 8581,
					8500  => 8824,
					9000  => 9067,
					9500  => 9310,
					10000 => 9553,
				),
				Shipping_Zones::ZONE_10       => array(
					500   => 4659,
					1000  => 4886,
					1500  => 5113,
					2000  => 5340,
					2500  => 6035,
					3000  => 6730,
					3500  => 7425,
					4000  => 8120,
					4500  => 8815,
					5000  => 9510,
					5500  => 9900,
					6000  => 10290,
					6500  => 10680,
					7000  => 11070,
					7500  => 11460,
					8000  => 11850,
					8500  => 12240,
					9000  => 12630,
					9500  => 13020,
					10000 => 13410,
				),
				Shipping_Zones::ZONE_11       => array(
					500   => 4940,
					1000  => 5425,
					1500  => 5910,
					2000  => 6395,
					2500  => 7089,
					3000  => 7783,
					3500  => 8477,
					4000  => 9171,
					4500  => 9865,
					5000  => 10559,
					5500  => 11032,
					6000  => 11505,
					6500  => 11978,
					7000  => 12451,
					7500  => 12924,
					8000  => 13397,
					8500  => 13870,
					9000  => 14343,
					9500  => 14816,
					10000 => 15289,
				),
				Shipping_Zones::ZONE_12       => array(
					500   => 6256,
					1000  => 6979,
					1500  => 7702,
					2000  => 8425,
					2500  => 9143,
					3000  => 9861,
					3500  => 10579,
					4000  => 11297,
					4500  => 12015,
					5000  => 12733,
					5500  => 13378,
					6000  => 14023,
					6500  => 14668,
					7000  => 15313,
					7500  => 15958,
					8000  => 16603,
					8500  => 17248,
					9000  => 17893,
					9500  => 18538,
					10000 => 19183,
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
	protected $maximum_total_cover = 2500;

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::PARCELFORCE_GLOBALPRIORITY;
	}
}
