<?php
/**
 * Second class rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Packaging;

/**
 * RoyalMail_Regular_Second_Class class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See UK Standard page 6.
 */
class RoyalMail_Regular_Second_Class extends RoyalMail_Rate {

	const COMPENSATION_UP_TO_VALUE = 20;

	/**
	 * Pricing bands
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * Shipping boxes.
	 *
	 * @var array $boxes
	 */
	protected $boxes;

	/**
	 * Setup RoyalMail_Regular_Second_Class rates.
	 */
	public function setup() {
		$this->bands = $this->initialize_bands();
		$this->boxes = $this->initialize_boxes();
	}

	/**
	 * Initializes Pricing bands
	 *
	 * Key is size (e.g. 'letter') and value is an array where key is weight in
	 * gram and value is the price (in penny).
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER        => array(
					100 => 85,
				),
				Packaging::LARGE_LETTER  => array(
					100 => 155,
					250 => 210,
					500 => 250,
					750 => 270,
				),
				Packaging::SMALL_PARCEL  => array(
					2000 => 375,
				),
				Packaging::MEDIUM_PARCEL => array(
					2000  => 615,
					10000 => 765,
					20000 => 1155,
				),
				Packaging::TUBE          => array(
					2000  => 615,
					10000 => 765,
					20000 => 1155,
				),
			),
			'2025' => array(
				Packaging::LETTER        => array(
					100 => 87,
				),
				Packaging::LARGE_LETTER  => array(
					100 => 155,
					250 => 200,
					500 => 240,
					750 => 270,
				),
				Packaging::SMALL_PARCEL  => array(
					2000 => 390,
				),
				Packaging::MEDIUM_PARCEL => array(
					2000  => 629,
					10000 => 789,
					20000 => 1189,
				),
				Packaging::TUBE          => array(
					2000  => 629,
					10000 => 789,
					20000 => 1189,
				),
			),
		);
	}

	/**
	 * Initializes Shipping boxes.
	 *
	 * @return array $boxes
	 */
	private function initialize_boxes() {
		return array(
			Packaging::LETTER        => array(
				'length' => 240, // Max L in mm.
				'width'  => 165, // Max W in mm.
				'height' => 5,   // Max H in mm.
				'weight' => 100, // Max Weight in grams.
			),
			Packaging::LARGE_LETTER  => array(
				'length' => 353,
				'width'  => 250,
				'height' => 25,
				'weight' => 750,
			),
			Packaging::SMALL_PARCEL  => array(
				'length' => 450,
				'width'  => 350,
				'height' => 160,
				'weight' => 2000,
			),
			Packaging::MEDIUM_PARCEL => array(
				'length' => 610,
				'width'  => 460,
				'height' => 460,
				'weight' => 20000,
			),
			Packaging::TUBE          => array(
				'length' => 900,
				'width'  => 70,
				'height' => 70,
				'weight' => 2000,
			),
		);
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::SECOND_CLASS;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method selected.
	 * @param string $destination address.
	 * @param array  $boxes User-defined boxes.
	 * @param string $instance_id Instance ID.
	 *
	 * @return array{ 'second-class': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$class_quote = false;

		/**
		 * Allow third party to enable/disable tube rate.
		 *
		 * @param boolean $rate_enabled Flag for enabling/disabling the rate.
		 * @param int $instance_id Instance ID.
		 * @param string $rate_slug Name of the rate.
		 * @param array $destination Destination.
		 * @param string $packing_method Packing method.
		 *
		 * @since 3.2.4
		 */
		$tube_packages           = apply_filters( 'woocommerce_shipping_royal_mail_tube_enabled', true, $instance_id, $this->get_rate_slug(), $destination, $packing_method ) ? $this->get_tube_packages( $items, $destination, $packing_method ) : array();
		$regular_packages        = $this->get_packages( $items, $packing_method );
		$packages                = array_merge( $regular_packages, $tube_packages );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 20.
				}

				$quote = 0;

				if ( ! $this->get_rate_bands( $package->id ) ) {
					return false; // Unpacked item.
				}

				$bands = $this->get_rate_bands( $package->id );

				$matched = false;

				foreach ( $bands as $band => $value ) {
					if ( is_numeric( $band ) && $package->weight <= $band ) {
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
