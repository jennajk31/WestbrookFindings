<?php
/**
 * International Standard rate.
 *
 * @package WC_RoyalMail/Rate
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use WooCommerce\RoyalMail\Services;
use WooCommerce\RoyalMail\Packaging;
use WooCommerce\RoyalMail\Shipping_Zones;

/**
 * RoyalMail_Regular_International_Standard class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See International Standard page 17.
 */
class RoyalMail_Regular_International_Standard extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE = 20;

	/**
	 * Pricing bands - Europe, Zone 1, Zone 2, Zone 3 (previously Zone 1)
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
	 * Setup RoyalMail_Regular_International_Standard rates.
	 */
	public function setup() {
		$this->bands            = $this->initialize_bands();
		$this->additional_rates = $this->initialize_additional_rates();
	}

	/**
	 * Initializes Pricing bands - Europe, Zone 1, Zone 2, Zone 3 (previously Zone 1)
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER         => array(
					100 => array( 280, 280, 280, 280, 280, 280 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 325, 325, 325, 420, 420, 420 ),
					250 => array( 545, 545, 545, 705, 825, 720 ),
					500 => array( 655, 655, 655, 955, 1160, 990 ),
					750 => array( 765, 765, 765, 1250, 1570, 1300 ),
				),
				Packaging::PACKET         => array(
					100  => array( 830, 845, 925, 1100, 1245, 1250 ),
					250  => array( 830, 845, 925, 1250, 1350, 1425 ),
					500  => array( 1040, 1075, 1140, 1700, 1870, 2045 ),
					750  => array( 1170, 1210, 1295, 2000, 2210, 2350 ),
					1000 => array( 1305, 1335, 1445, 2310, 2570, 2750 ),
					1250 => array( 1415, 1470, 1575, 2575, 2900, 3130 ),
					1500 => array( 1415, 1470, 1700, 2820, 3240, 3430 ),
					2000 => array( 1580, 1635, 1850, 2955, 3425, 3570 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 830, 845, 925, 1100, 1245, 1250 ),
					250  => array( 830, 845, 925, 1250, 1350, 1425 ),
					500  => array( 1040, 1075, 1140, 1700, 1870, 2045 ),
					750  => array( 1170, 1210, 1295, 2000, 2210, 2350 ),
					1000 => array( 1305, 1335, 1445, 2310, 2570, 2750 ),
					1250 => array( 1415, 1470, 1575, 2575, 2900, 3130 ),
					1500 => array( 1415, 1470, 1700, 2820, 3240, 3430 ),
					2000 => array( 1580, 1635, 1850, 2955, 3425, 3570 ),
				),
			),
			'2025' => array(
				Packaging::LETTER         => array(
					100 => array( 320, 320, 320, 320, 320, 320 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 350, 350, 350, 430, 430, 430 ),
					250 => array( 580, 580, 580, 770, 900, 800 ),
					500 => array( 720, 720, 720, 1080, 1310, 1150 ),
					750 => array( 955, 955, 955, 1600, 2010, 1710 ),
				),
				Packaging::PACKET         => array(
					100  => array( 895, 970, 1065, 1305, 1455, 1695 ),
					250  => array( 895, 970, 1065, 1405, 1520, 1695 ),
					500  => array( 1125, 1235, 1310, 1930, 2120, 2385 ),
					750  => array( 1240, 1390, 1490, 2270, 2510, 2695 ),
					1000 => array( 1355, 1535, 1660, 2620, 2915, 3005 ),
					1250 => array( 1425, 1690, 1810, 2925, 3290, 3415 ),
					1500 => array( 1425, 1690, 1955, 3200, 3675, 3415 ),
					2000 => array( 1625, 1880, 2125, 3355, 3885, 3570 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 895, 970, 1065, 1305, 1455, 1695 ),
					250  => array( 895, 970, 1065, 1405, 1520, 1695 ),
					500  => array( 1125, 1235, 1310, 1930, 2120, 2385 ),
					750  => array( 1240, 1390, 1490, 2270, 2510, 2695 ),
					1000 => array( 1355, 1535, 1660, 2620, 2915, 3005 ),
					1250 => array( 1425, 1690, 1810, 2925, 3290, 3415 ),
					1500 => array( 1425, 1690, 1955, 3200, 3675, 3415 ),
					2000 => array( 1625, 1880, 2125, 3355, 3885, 3570 ),
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
				Packaging::PRINTED_PAPERS => array( 155, 155, 155, 210, 260, 285 ),
			),
			'2025' => array(
				Packaging::PRINTED_PAPERS => array( 165, 165, 165, 225, 275, 300 ),
			),
		);
	}

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::INTERNATIONAL_STANDARD;
	}

	/**
	 * Get quotes for this rate
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 * @param array  $boxes User-defined boxes.
	 * @param int    $instance_id Instance ID.
	 *
	 * @return array{ 'international-standard': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		$standard_quote = false;

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

		$zone                    = $this->get_zone( $destination );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );

		/**
		 * Allow third party to enable/disable printed papers rate.
		 *
		 * @param boolean $rate_enabled Flag for enabling/disabling the rate.
		 * @param int $instance_id Instance ID.
		 * @param string $rate_slug Name of the rate.
		 * @param array $destination Destination.
		 * @param string $packing_method Packing method.
		 *
		 * @since 3.0.0
		 */
		$printed_paper_packages = apply_filters( 'woocommerce_shipping_royal_mail_printed_papers_enabled', true, $instance_id, 'standard', $destination, $packing_method ) ? $this->get_printed_papers_packages( $items, $destination, $packing_method ) : array();
		$regular_packages       = $this->get_packages( $items, $packing_method );
		$packages               = array_merge( $regular_packages, $printed_paper_packages );

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > self::COMPENSATION_UP_TO_VALUE && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 20.
				}

				$this->validate_package( $package );

				if ( in_array( $package->id, array( Packaging::PACKET, Packaging::PRINTED_PAPERS ), true ) && 900 < ( $package->length + $package->width + $package->height ) ) {
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
						switch ( $zone ) {
							case Shipping_Zones::ZONE_EUR_1:
								$quote += $value[0];
								break;
							case Shipping_Zones::ZONE_EUR_2:
								$quote += $value[1];
								break;
							case Shipping_Zones::ZONE_EUR_3:
							case Shipping_Zones::ZONE_EU:
								$quote += $value[2];
								break;
							case Shipping_Zones::ZONE_1:
								$quote += $value[3];
								break;
							case Shipping_Zones::ZONE_2:
								$quote += $value[4];
								break;
							case Shipping_Zones::ZONE_3:
								// Fallback to zone 1 for older prices.
								$quote += isset( $value[5] ) ? $value[5] : $value[3];
								break;
						}
						$matched = true;
						break;
					}
				}

				if ( ! $matched ) {
					return;
				}

				$standard_quote += $quote;
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $standard_quote / 100;

		return $quotes;
	}
}
