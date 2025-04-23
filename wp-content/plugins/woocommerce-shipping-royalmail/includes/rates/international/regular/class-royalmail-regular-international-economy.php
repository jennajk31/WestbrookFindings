<?php
/**
 * International Economy rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Packaging;

/**
 * RoyalMail_Regular_International_Economy class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See International Economy 19.
 */
class RoyalMail_Regular_International_Economy extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE = 20;

	/**
	 * Pricing bands
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Additional rates for printed papers.
	 *
	 * @var array.
	 */
	protected $additional_rates;

	/**
	 * Setup RoyalMail_Regular_International_Economy rates.
	 */
	public function setup() {
		$this->bands = $this->initialize_bands();
	}

	/**
	 * Pricing bands
	 *
	 * Key is size (e.g. 'letter') and value is an array where key is weight in
	 * gram and value is the price (in penny).
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER         => array(
					100 => 260, // new price for 100g.
				),
				Packaging::LARGE_LETTER   => array(
					100 => 350,  // new price for 100g.
					250 => 585,  // new price for 250g.
					500 => 660,  // new price for 500g.
					750 => 775,  // new price for 750g.
				),
				Packaging::PACKET         => array(
					250  => 745,
					500  => 1035,
					750  => 1165,
					1000 => 1300,
					1500 => 1430,
					2000 => 1620,
				),
				Packaging::PRINTED_PAPERS => array(
					250  => 745,
					500  => 1035,
					750  => 1165,
					1000 => 1300,
					1500 => 1430,
					2000 => 1620,
				),
			),
			'2025' => array(
				Packaging::LETTER         => array(
					100 => 310, // new price for 100g.
				),
				Packaging::LARGE_LETTER   => array(
					100 => 410,  // new price for 100g.
					250 => 730,  // new price for 250g.
					500 => 845,  // new price for 500g.
					750 => 1025,  // new price for 750g.
				),
				Packaging::PACKET         => array(
					250  => 930,
					500  => 1400,
					750  => 1445,
					1000 => 1560,
					1500 => 1715,
					2000 => 2025,
				),
				Packaging::PRINTED_PAPERS => array(
					250  => 930,
					500  => 1400,
					750  => 1445,
					1000 => 1560,
					1500 => 1715,
					2000 => 2025,
				),
			),
		);
	}

	/**
	 * Initializes Additional rates for printed papers.
	 *
	 * @return array.
	 */
	private function initialize_additional_rates() {
		return array(
			'2024' => array(
				Packaging::PRINTED_PAPERS => 180,
			),
			'2025' => array(
				Packaging::PRINTED_PAPERS => 190,
			),
		);
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::INTERNATIONAL_ECONOMY;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method selected.
	 * @param string $destination address.
	 * @param array  $boxes User-defined boxes.
	 * @param int    $instance_id Instance ID of the shipping.
	 *
	 * @return array{ 'international-economy': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$class_quote = false;

		$this->apply_additional_rates();

		if ( ! empty( $boxes ) ) {
			$this->boxes = array();

			foreach ( $boxes as $key => $box ) {
				$this->boxes[ $key ] = array(
					'length'     => $box['inner_length'],
					'width'      => $box['inner_width'],
					'height'     => $box['inner_height'],
					'box_weight' => $box['box_weight'],
					'weight'     => 2000,
				);
			}
		} else {
			$this->boxes = $this->international_default_box;
		}

		if ( in_array( $destination, $this->get_all_european_countries(), true ) ) {
			foreach ( $this->bands as $year => $bands_group ) {
				unset( $this->bands[ $year ][ Packaging::LETTER ], $this->boxes[ Packaging::LETTER ] );
			}
		}

		$zone                    = $this->get_zone( $destination );
		$packages                = $this->get_packages( $items, $packing_method );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 20.
				}

				$this->validate_package( $package );

				if ( Packaging::PACKET === $package->id && 900 < ( $package->length + $package->width + $package->height ) ) {
					return false; // Exceeding parcels requirement, unpacked.
				}

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$bands   = $this->get_rate_bands( $package->id );
				$quote   = 0;
				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( $package->weight <= $band ) {
						$quote  += $value;
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return;
				}

				$class_quote += $quote;
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $class_quote / 100;

		return $quotes;
	}
}
