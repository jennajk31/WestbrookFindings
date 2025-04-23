<?php
/**
 * International-Tracked-and-Signed rate.
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
 * RoyalMail_Regular_International_Tracked_Signed class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf.
 * See International Tracked & Signed page 13 - 14.
 */
class RoyalMail_Regular_International_Tracked_Signed extends RoyalMail_Rate {
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
	 * List of countries that support Tracked and Signed service.
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
		'AR',
		'AT',
		'BB',
		'BY',
		'BE',
		'BZ',
		'BG',
		'KH',
		'CA',
		'KY',
		'CK',
		'HR',
		'CY',
		'CZ',
		'DK',
		'EC',
		'FO',
		'FI',
		'FR',
		'GE',
		'DE',
		'GI',
		'GR',
		'GL',
		'HK',
		'HU',
		'IS',
		'ID',
		'IE',
		'IT',
		'JP',
		'LV',
		'LB',
		'LI',
		'LT',
		'LU',
		'MY',
		'MT',
		'MD',
		'NL',
		'NZ',
		'PL',
		'PT',
		'RO',
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
		'TH',
		'TO',
		'TT',
		'TR',
		'UG',
		'AE',
		'US',
		'VA',
	);

	/**
	 * Setup RoyalMail_Regular_International_Tracked_Signed rates.
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
					100 => array( 815, 815, 815, 815, 815, 815 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 965, 965, 965, 1065, 1080, 1075 ),
					250 => array( 1090, 1090, 1090, 1230, 1350, 1250 ),
					500 => array( 1170, 1170, 1170, 1430, 1630, 1465 ),
					750 => array( 1215, 1215, 1215, 1635, 1935, 1685 ),
				),
				Packaging::PACKET         => array(
					100  => array( 1375, 1390, 1530, 1770, 1900, 1845 ),
					250  => array( 1375, 1390, 1530, 1810, 1935, 2020 ),
					500  => array( 1520, 1560, 1700, 2240, 2430, 2615 ),
					750  => array( 1635, 1670, 1825, 2500, 2740, 2835 ),
					1000 => array( 1740, 1770, 1955, 2795, 3085, 3235 ),
					1250 => array( 1800, 1805, 2025, 3015, 3355, 3615 ),
					1500 => array( 1810, 1830, 2090, 3170, 3630, 3915 ),
					2000 => array( 1825, 1880, 2140, 3220, 3745, 3970 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 1375, 1390, 1530, 1770, 1900, 1845 ),
					250  => array( 1375, 1390, 1530, 1810, 1935, 2020 ),
					500  => array( 1520, 1560, 1700, 2240, 2430, 2615 ),
					750  => array( 1635, 1670, 1825, 2500, 2740, 2835 ),
					1000 => array( 1740, 1770, 1955, 2795, 3085, 3235 ),
					1250 => array( 1800, 1805, 2025, 3015, 3355, 3615 ),
					1500 => array( 1810, 1830, 2090, 3170, 3630, 3915 ),
					2000 => array( 1825, 1880, 2140, 3220, 3745, 3970 ),
				),
			),
			'2025' => array(
				Packaging::LETTER         => array(
					100 => array( 850, 850, 850, 850, 850, 850 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 1005, 1005, 1005, 1110, 1125, 1125 ),
					250 => array( 1135, 1135, 1135, 1280, 1405, 1305 ),
					500 => array( 1215, 1215, 1215, 1485, 1695, 1530 ),
					750 => array( 1265, 1265, 1265, 1700, 2010, 1760 ),
				),
				Packaging::PACKET         => array(
					100  => array( 1425, 1460, 1635, 1895, 1995, 1950 ),
					250  => array( 1425, 1460, 1635, 1895, 1995, 2080 ),
					500  => array( 1580, 1640, 1820, 2350, 2550, 2720 ),
					750  => array( 1700, 1755, 1955, 2625, 2875, 2950 ),
					1000 => array( 1810, 1860, 2090, 2935, 3240, 3320 ),
					1250 => array( 1870, 1895, 2165, 3165, 3525, 3830 ),
					1500 => array( 1880, 1920, 2235, 3330, 3810, 3995 ),
					2000 => array( 1900, 1975, 2290, 3380, 3930, 4090 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 1425, 1460, 1635, 1895, 1995, 1950 ),
					250  => array( 1425, 1460, 1635, 1895, 1995, 2080 ),
					500  => array( 1580, 1640, 1820, 2350, 2550, 2720 ),
					750  => array( 1700, 1755, 1955, 2625, 2875, 2950 ),
					1000 => array( 1810, 1860, 2090, 2935, 3240, 3320 ),
					1250 => array( 1870, 1895, 2165, 3165, 3525, 3830 ),
					1500 => array( 1880, 1920, 2235, 3330, 3810, 3995 ),
					2000 => array( 1900, 1975, 2290, 3380, 3930, 4090 ),
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
		return Services::INTERNATIONAL_TRACKED_SIGNED;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @param array  $items to be shipped.
	 * @param string $packing_method the method selected.
	 * @param string $destination Address to ship to.
	 * @param array  $boxes User-defined boxes.
	 * @param int    $instance_id Instance ID.
	 *
	 * @return array{ 'international-tracked-signed': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		if ( ! in_array( $destination, $this->supported_countries, true ) ) {
			return array();
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
		$printed_paper_packages  = apply_filters( 'woocommerce_shipping_royal_mail_printed_papers_enabled', true, $instance_id, 'tracked-signed', $destination, $packing_method ) ? $this->get_printed_papers_packages( $items, $destination, $packing_method ) : array();
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
