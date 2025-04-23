<?php
/**
 * First class rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Packaging;

/**
 * RoyalMail_Regular_First_Class_Signed class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See UK Signed page 5.
 */
class RoyalMail_Regular_First_Class_Signed extends RoyalMail_Regular_First_Class {

	const COMPENSATION_UP_TO_VALUE = 20;

	/**
	 * Cost for signed for delivery.
	 *
	 * @var array
	 */
	protected $signed_for_cost = array(
		'2024' => 170,
		'2025' => 190,
	);

	/**
	 * Cost for signed for delivery of a package.
	 *
	 * @var array
	 */
	protected $signed_for_package_cost = array(
		'2024' => 140,
		'2025' => 150,
	);

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::FIRST_CLASS_SIGNED;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 * @param array  $boxes User-defined boxes.
	 * @param string $instance_id Instance ID.
	 *
	 * @return array{ 'first-class-signed': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$class_quote             = 0;
		$packages                = $this->get_packages( $items, $packing_method );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 50.
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
					return null;
				}

				if ( Packaging::LETTER === $package->id || Packaging::LARGE_LETTER === $package->id ) {
					$class_quote += $quote + $this->get_signed_cost( 'signed_for_cost' );
				} else {
					$class_quote += $quote + $this->get_signed_cost( 'signed_for_package_cost' );
				}
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $class_quote / 100;

		return $quotes;
	}
}
