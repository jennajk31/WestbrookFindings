<?php
/**
 * Parcelforce irelandexpress rates.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Shipping_Zones;
use WooCommerce\RoyalMail\Services;

/**
 * Rates for Parcelforce irelandexpress.
 *
 * Based on https://www.parcelforce.com/sites/default/files/parcelforce-retail-uk-and-international-online-prices.pdf
 */
class RoyalMail_Online_Parcelforce_Irelandexpress extends Parcelforce_Rate {
	/**
	 * Pricing bands.
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Online_Parcelforce_Irelandexpress rates.
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
			'2025' => array(
				Shipping_Zones::ZONE_5 => array(
					500   => 1668,
					1000  => 1668,
					1500  => 1668,
					2000  => 1668,
					2500  => 1770,
					3000  => 1770,
					3500  => 1770,
					4000  => 1770,
					4500  => 1770,
					5000  => 1770,
					5500  => 2112,
					6000  => 2112,
					6500  => 2112,
					7000  => 2112,
					7500  => 2112,
					8000  => 2112,
					8500  => 2112,
					9000  => 2112,
					9500  => 2112,
					10000 => 2112,
					10500 => 2796,
					11000 => 2796,
					11500 => 2796,
					12000 => 2796,
					12500 => 2796,
					13000 => 2796,
					13500 => 2796,
					14000 => 2796,
					14500 => 2796,
					15000 => 2796,
					15500 => 3336,
					16000 => 3336,
					16500 => 3336,
					17000 => 3336,
					17500 => 3336,
					18000 => 3336,
					18500 => 3336,
					19000 => 3336,
					19500 => 3336,
					20000 => 3336,
					20500 => 4458,
					21000 => 4458,
					21500 => 4458,
					22000 => 4458,
					22500 => 4458,
					23000 => 4458,
					23500 => 4458,
					24000 => 4458,
					24500 => 4458,
					25000 => 4458,
					25500 => 4878,
					26000 => 4878,
					26500 => 4878,
					27000 => 4878,
					27500 => 4878,
					28000 => 4878,
					28500 => 4878,
					29000 => 4878,
					29500 => 4878,
					30000 => 4878,
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
	protected $maximum_inclusive_compensation = 200;

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
		return Services::PARCELFORCE_IRELANDEXPRESS;
	}
}
