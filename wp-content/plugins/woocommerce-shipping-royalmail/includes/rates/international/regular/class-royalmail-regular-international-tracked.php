<?php
/**
 * International-Tracked rate.
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
 * RoyalMail_Regular_International_Tracked class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See International Tracked page 11 - 12.
 */
class RoyalMail_Regular_International_Tracked extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE    = 250;
	const COMPENSATION_INCLUDED_VALUE = 50;

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
	 * List of countries that support Tracked service.
	 *
	 * @see https://www.royalmail.com/business/system/files/Royal-Mail-International-Business-Services-List-March2017.pdf
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @var array
	 */
	protected $supported_countries = array(
		'AX',
		'AD',
		'AU',
		'AT',
		'BE',
		'BR',
		'CA',
		'HR',
		'CY',
		'DK',
		'EE',
		'FO',
		'FI',
		'FR',
		'DE',
		'GI',
		'GR',
		'GL',
		'HK',
		'HU',
		'IS',
		'IN',
		'IE',
		'IL',
		'IT',
		'LV',
		'LB',
		'LI',
		'LT',
		'LU',
		'MY',
		'MT',
		'NL',
		'NZ',
		'NO',
		'PL',
		'PT',
		'RU',
		'SM',
		'RS',
		'SG',
		'SK',
		'SI',
		'KR',
		'ES',
		'SE',
		'CH',
		'TR',
		'US',
		'VA',
	);

	/**
	 * Setup RoyalMail_Regular_International_Tracked rates.
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
					100 => array( 790, 790, 790, 790, 790, 790 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 950, 950, 950, 1055, 1060, 1060 ),
					250 => array( 1050, 1050, 1050, 1215, 1335, 1240 ),
					500 => array( 1160, 1160, 1160, 1420, 1615, 1450 ),
					750 => array( 1205, 1205, 1205, 1625, 1925, 1670 ),
				),
				Packaging::PACKET         => array(
					250  => array( 1205, 1240, 1325, 1525, 1670, 1720 ),
					500  => array( 1335, 1370, 1500, 1970, 2180, 2185 ),
					750  => array( 1435, 1475, 1595, 2240, 2485, 2470 ),
					1000 => array( 1500, 1545, 1670, 2530, 2810, 2765 ),
					1250 => array( 1550, 1590, 1755, 2745, 3120, 3185 ),
					1500 => array( 1550, 1590, 1845, 2900, 3395, 3185 ),
					2000 => array( 1550, 1745, 1920, 3020, 3580, 3185 ),
				),
				Packaging::PRINTED_PAPERS => array(
					250  => array( 1205, 1240, 1325, 1525, 1670, 1720 ),
					500  => array( 1335, 1370, 1500, 1970, 2180, 2185 ),
					750  => array( 1435, 1475, 1595, 2240, 2485, 2470 ),
					1000 => array( 1500, 1545, 1670, 2530, 2810, 2765 ),
					1250 => array( 1550, 1590, 1755, 2745, 3120, 3185 ),
					1500 => array( 1550, 1590, 1845, 2900, 3395, 3185 ),
					2000 => array( 1550, 1745, 1920, 3020, 3580, 3185 ),
				),
			),
			'2025' => array(
				Packaging::LETTER         => array(
					100 => array( 810, 810, 810, 810, 810, 810 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 975, 975, 975, 1080, 1085, 1070 ),
					250 => array( 1075, 1075, 1075, 1245, 1370, 1250 ),
					500 => array( 1190, 1190, 1190, 1455, 1655, 1465 ),
					750 => array( 1235, 1235, 1235, 1665, 1975, 1690 ),
				),
				Packaging::PACKET         => array(
					250  => array( 1240, 1270, 1390, 1600, 1755, 1685 ),
					500  => array( 1370, 1405, 1575, 2070, 2290, 2185 ),
					750  => array( 1425, 1510, 1675, 2350, 2610, 2495 ),
					1000 => array( 1500, 1585, 1755, 2655, 2950, 2805 ),
					1250 => array( 1550, 1630, 1845, 2880, 3275, 3235 ),
					1500 => array( 1550, 1630, 1935, 3045, 3565, 3235 ),
					2000 => array( 1550, 1790, 2015, 3170, 3760, 3235 ),
				),
				Packaging::PRINTED_PAPERS => array(
					250  => array( 1240, 1270, 1390, 1600, 1755, 1685 ),
					500  => array( 1370, 1405, 1575, 2070, 2290, 2185 ),
					750  => array( 1425, 1510, 1675, 2350, 2610, 2495 ),
					1000 => array( 1500, 1585, 1755, 2655, 2950, 2805 ),
					1250 => array( 1550, 1630, 1845, 2880, 3275, 3235 ),
					1500 => array( 1550, 1630, 1935, 3045, 3565, 3235 ),
					2000 => array( 1550, 1790, 2015, 3170, 3760, 3235 ),
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
	 * Fixed compensation
	 *
	 * @var string
	 */
	private $compensation = '250';

	/**
	 * Slug of the rate (e.g. 'special-delivery-1pm').
	 *
	 * @return string
	 */
	public function get_rate_slug() {
		return Services::INTERNATIONAL_TRACKED;
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
	 * @return array{ 'international-tracked': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		if ( ! in_array( $destination, $this->supported_countries, true ) ) {
			return;
		}

		$this->apply_additional_rates();

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

		$zone = $this->get_zone( $destination );

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
		$printed_paper_packages  = apply_filters( 'woocommerce_shipping_royal_mail_printed_papers_enabled', true, $instance_id, 'tracked', $destination, $packing_method ) ? $this->get_printed_papers_packages( $items, $destination, $packing_method ) : array();
		$regular_packages        = $this->get_packages( $items, $packing_method );
		$packages                = array_merge( $regular_packages, $printed_paper_packages );
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );
		$additional_compensation = ( ! empty( $options['enable_addit_compensation'] ) && 'yes' === $options['enable_addit_compensation'] );
		$max_compensation        = ( true === $additional_compensation ) ? self::COMPENSATION_UP_TO_VALUE : self::COMPENSATION_INCLUDED_VALUE;

		if ( $packages ) {
			foreach ( $packages as $package ) {

				$this->validate_package( $package );

				if ( in_array( $package->id, array( Packaging::PACKET, Packaging::PRINTED_PAPERS ), true ) && 900 < ( $package->length + $package->width + $package->height ) ) {
					return false; // Exceeding parcels requirement, unpacked.
				}

				if ( $package->value > $max_compensation && ! $ignore_max_compensation ) {
					return false;
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

				$class_quote += $quote;

				if ( $package->value > self::COMPENSATION_INCLUDED_VALUE && $additional_compensation ) {
					$class_quote += $this->compensation;
				}
			}
		}

		// Return pounds.
		$quotes                           = array();
		$quotes[ $this->get_rate_slug() ] = $class_quote / 100;

		return $quotes;
	}
}
