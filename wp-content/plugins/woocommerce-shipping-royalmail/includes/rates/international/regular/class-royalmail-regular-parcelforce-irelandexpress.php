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
 * Based on https://www.parcelforce.com/sites/default/files/parcelforce-uk-and-international-services-and-prices.pdf.
 */
class RoyalMail_Regular_Parcelforce_Irelandexpress extends Parcelforce_Rate {
	/**
	 * Pricing bands.
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Regular_Parcelforce_Irelandexpress rates.
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
