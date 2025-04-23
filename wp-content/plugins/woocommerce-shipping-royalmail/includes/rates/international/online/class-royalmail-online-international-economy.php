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
 * RoyalMail_Online_International_Economy class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf.
 * See International Economy 19.
 */
class RoyalMail_Online_International_Economy extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE = 20;

	/**
	 * Pricing bands.
	 *
	 * Associative array where the key is the year and the value is another
	 * associative array. The nested associative array uses package size as the key
	 * (e.g., Packaging::LETTER) and another array as the value, where the key is
	 * weight in grams and the value is the price in pence.
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Setup RoyalMail_Online_International_Economy rates.
	 */
	public function setup() {
		$this->bands = $this->initialize_bands();
	}

	/**
	 * Initializes the pricing bands for different package sizes and weights.
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER       => array(
					100 => 260,
				),
				Packaging::LARGE_LETTER => array(
					100 => 350,
					250 => 585,
					500 => 660,
					750 => 775,
				),
				Packaging::PACKET       => array(
					100  => 740,
					250  => 740,
					500  => 1030,
					750  => 1160,
					1000 => 1295,
					1250 => 1425,
					1500 => 1425,
					2000 => 1615,
				),
			),
			'2025' => array(
				Packaging::LETTER       => array(
					100 => 310,
				),
				Packaging::LARGE_LETTER => array(
					100 => 410,
					250 => 730,
					500 => 845,
					750 => 1025,
				),
				Packaging::PACKET       => array(
					250  => 925,
					500  => 1395,
					750  => 1440,
					1000 => 1555,
					1500 => 1710,
					2000 => 2020,
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
		return Services::INTERNATIONAL_ECONOMY;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method selected.
	 * @param string $destination address.
	 * @param array  $boxes User-defined boxes.
	 * @param int    $instance_id Instance ID.
	 *
	 * @return array{ 'international-economy': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {

		$class_quote = false;
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
