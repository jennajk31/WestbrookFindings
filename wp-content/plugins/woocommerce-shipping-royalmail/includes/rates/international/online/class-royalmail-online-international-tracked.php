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
 * RoyalMail_Online_International_Tracked class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf.
 * See International Tracked page 8 - 10.
 */
class RoyalMail_Online_International_Tracked extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE    = 250;
	const COMPENSATION_INCLUDED_VALUE = 50;

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
	 * List of countries that support Tracked service.
	 *
	 * @see https://www.royalmail.com/business/system/files/Royal-Mail-International-Business-Services-List-March2017.pdf.
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
	 * Setup RoyalMail_Online_International_Tracked rates.
	 */
	public function setup() {
		$this->bands            = $this->initialize_bands();
		$this->additional_rates = $this->initialize_additional_rates();
	}

	/**
	 * Initializes Pricing bands - Belgium, France, Germany, Ireland, Italy, Netherlands, Spain, Sweden, Switzerland, Australia, Brazil, Canada, China, Hong Kong, Japan, New Zealand, Europe 1, Europe 2, Europe 3, World Zone 1, World Zone 2, World Zone 3 (previously Zone 1).
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER         => array(
					100 => array( 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790, 790 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 950, 950, 950, 950, 950, 950, 950, 950, 950, 1060, 1055, 1055, 1055, 1055, 1055, 1060, 950, 950, 950, 1055, 1060, 1060 ),
					250 => array( 980, 980, 980, 980, 980, 980, 980, 980, 980, 1245, 1125, 1125, 1125, 1125, 1125, 1245, 980, 980, 980, 1125, 1245, 1170 ),
					500 => array( 1090, 1090, 1090, 1090, 1090, 1090, 1090, 1090, 1090, 1545, 1350, 1350, 1350, 1350, 1350, 1545, 1090, 1090, 1090, 1350, 1545, 1380 ),
					750 => array( 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1855, 1555, 1555, 1555, 1555, 1555, 1855, 1135, 1135, 1135, 1555, 1855, 1600 ),
				),
				Packaging::PACKET         => array(
					100  => array( 865, 800, 800, 865, 900, 865, 900, 865, 865, 1235, 1120, 1375, 1120, 1120, 1120, 1235, 865, 900, 985, 1120, 1235, 1170 ),
					250  => array( 865, 800, 800, 865, 900, 865, 900, 865, 865, 1235, 1120, 1375, 1120, 1120, 1120, 1235, 865, 900, 985, 1120, 1235, 1170 ),
					500  => array( 985, 920, 920, 865, 990, 985, 990, 985, 985, 1585, 1420, 1375, 1420, 1420, 1420, 1585, 985, 990, 1170, 1420, 1585, 1490 ),
					750  => array( 1080, 1010, 975, 1010, 990, 1080, 1045, 1080, 1080, 1965, 1665, 1620, 1420, 1420, 1640, 1765, 1080, 1045, 1305, 1665, 1965, 1550 ),
					1000 => array( 1150, 1075, 995, 1010, 990, 1150, 1110, 1150, 1150, 2310, 1930, 1930, 1420, 1420, 1640, 1965, 1150, 1110, 1405, 1930, 2310, 1720 ),
					1250 => array( 1190, 1095, 995, 1010, 1150, 1190, 1150, 1190, 1190, 2640, 2175, 2165, 2000, 2000, 2175, 2340, 1190, 1150, 1500, 2175, 2640, 2000 ),
					1500 => array( 1190, 1095, 995, 1010, 1150, 1190, 1150, 1190, 1190, 2915, 2465, 2335, 2200, 2200, 2265, 2665, 1190, 1150, 1680, 2465, 2915, 2000 ),
					2000 => array( 1210, 1095, 995, 1010, 1150, 1210, 1215, 1210, 1210, 3110, 2645, 2455, 2200, 2200, 2265, 3010, 1210, 1215, 1950, 2645, 3110, 2000 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 865, 800, 800, 865, 900, 865, 900, 865, 865, 1235, 1120, 1375, 1120, 1120, 1120, 1235, 865, 900, 985, 1120, 1235, 1170 ),
					250  => array( 865, 800, 800, 865, 900, 865, 900, 865, 865, 1235, 1120, 1375, 1120, 1120, 1120, 1235, 865, 900, 985, 1120, 1235, 1170 ),
					500  => array( 985, 920, 920, 865, 990, 985, 990, 985, 985, 1585, 1420, 1375, 1420, 1420, 1420, 1585, 985, 990, 1170, 1420, 1585, 1490 ),
					750  => array( 1080, 1010, 975, 1010, 990, 1080, 1045, 1080, 1080, 1965, 1665, 1620, 1420, 1420, 1640, 1765, 1080, 1045, 1305, 1665, 1965, 1550 ),
					1000 => array( 1150, 1075, 995, 1010, 990, 1150, 1110, 1150, 1150, 2310, 1930, 1930, 1420, 1420, 1640, 1965, 1150, 1110, 1405, 1930, 2310, 1720 ),
					1250 => array( 1190, 1095, 995, 1010, 1150, 1190, 1150, 1190, 1190, 2640, 2175, 2165, 2000, 2000, 2175, 2340, 1190, 1150, 1500, 2175, 2640, 2000 ),
					1500 => array( 1190, 1095, 995, 1010, 1150, 1190, 1150, 1190, 1190, 2915, 2465, 2335, 2200, 2200, 2265, 2665, 1190, 1150, 1680, 2465, 2915, 2000 ),
					2000 => array( 1210, 1095, 995, 1010, 1150, 1210, 1215, 1210, 1210, 3110, 2645, 2455, 2200, 2200, 2265, 3010, 1210, 1215, 1950, 2645, 3110, 2000 ),
				),
			),
			'2025' => array(
				Packaging::LETTER         => array(
					100 => array( 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810, 810 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 965, 965, 965, 965, 965, 965, 965, 965, 965, 1075, 1070, 1070, 1070, 1070, 1070, 1075, 965, 965, 965, 1070, 1075, 1060 ),
					250 => array( 980, 980, 980, 980, 980, 980, 980, 980, 980, 1260, 1140, 1140, 1140, 1140, 1140, 1260, 980, 980, 980, 1140, 1260, 1170 ),
					500 => array( 1090, 1090, 1090, 1090, 1090, 1090, 1090, 1090, 1090, 1565, 1365, 1365, 1365, 1365, 1365, 1565, 1090, 1090, 1090, 1365, 1565, 1380 ),
					750 => array( 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1880, 1575, 1575, 1575, 1575, 1575, 1880, 1135, 1135, 1135, 1575, 1880, 1600 ),
				),
				Packaging::PACKET         => array(
					100  => array( 945, 970, 800, 865, 945, 900, 940, 945, 985, 1235, 1230, 1375, 1120, 1130, 1130, 1245, 875, 965, 1055, 1250, 1295, 1170 ),
					250  => array( 945, 970, 800, 865, 945, 900, 940, 945, 985, 1235, 1230, 1375, 1120, 1130, 1130, 1245, 875, 965, 1055, 1250, 1295, 1170 ),
					500  => array( 1040, 1065, 920, 865, 1040, 990, 1000, 1040, 1170, 1585, 1560, 1375, 1290, 1435, 1435, 1600, 985, 1050, 1250, 1580, 1665, 1425 ),
					750  => array( 1095, 1090, 975, 865, 1095, 1045, 1055, 1095, 1200, 1965, 1830, 1620, 1290, 1680, 1680, 1985, 1050, 1110, 1305, 1850, 2065, 1650 ),
					1000 => array( 1165, 1095, 975, 865, 1165, 1065, 1120, 1165, 1295, 2110, 2125, 1930, 1290, 1950, 1950, 1985, 1075, 1175, 1405, 2145, 2425, 1650 ),
					1250 => array( 1205, 1095, 975, 1010, 1205, 1105, 1160, 1205, 1350, 2510, 2395, 2165, 1740, 2195, 2195, 2665, 1155, 1220, 1500, 2415, 2770, 2020 ),
					1500 => array( 1205, 1095, 975, 1010, 1205, 1105, 1160, 1205, 1430, 2510, 2490, 2335, 1740, 2490, 2465, 2945, 1155, 1220, 1560, 2510, 3060, 2020 ),
					2000 => array( 1275, 1095, 975, 1010, 1275, 1165, 1225, 1275, 1455, 2510, 2670, 2335, 1740, 2670, 2645, 3140, 1175, 1290, 1605, 2690, 3265, 2020 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 945, 970, 800, 865, 945, 900, 940, 945, 985, 1235, 1230, 1375, 1120, 1130, 1130, 1245, 875, 965, 1055, 1250, 1295, 1170 ),
					250  => array( 945, 970, 800, 865, 945, 900, 940, 945, 985, 1235, 1230, 1375, 1120, 1130, 1130, 1245, 875, 965, 1055, 1250, 1295, 1170 ),
					500  => array( 1040, 1065, 920, 865, 1040, 990, 1000, 1040, 1170, 1585, 1560, 1375, 1290, 1435, 1435, 1600, 985, 1050, 1250, 1580, 1665, 1425 ),
					750  => array( 1095, 1090, 975, 865, 1095, 1045, 1055, 1095, 1200, 1965, 1830, 1620, 1290, 1680, 1680, 1985, 1050, 1110, 1305, 1850, 2065, 1650 ),
					1000 => array( 1165, 1095, 975, 865, 1165, 1065, 1120, 1165, 1295, 2110, 2125, 1930, 1290, 1950, 1950, 1985, 1075, 1175, 1405, 2145, 2425, 1650 ),
					1250 => array( 1205, 1095, 975, 1010, 1205, 1105, 1160, 1205, 1350, 2510, 2395, 2165, 1740, 2195, 2195, 2665, 1155, 1220, 1500, 2415, 2770, 2020 ),
					1500 => array( 1205, 1095, 975, 1010, 1205, 1105, 1160, 1205, 1430, 2510, 2490, 2335, 1740, 2490, 2465, 2945, 1155, 1220, 1560, 2510, 3060, 2020 ),
					2000 => array( 1275, 1095, 975, 1010, 1275, 1165, 1225, 1275, 1455, 2510, 2670, 2335, 1740, 2670, 2645, 3140, 1175, 1290, 1605, 2690, 3265, 2020 ),
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
				Packaging::PRINTED_PAPERS => array( 130, 130, 130, 130, 130, 130, 130, 130, 130, 180, 180, 180, 180, 180, 180, 180, 130, 130, 130, 180, 225, 250 ),
			),
			'2025' => array(
				Packaging::PRINTED_PAPERS => array( 135, 135, 135, 135, 135, 135, 135, 135, 135, 185, 185, 185, 185, 185, 185, 185, 135, 135, 135, 185, 230, 260 ),
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
	 * @param int    $instance_id .
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

		$individual_zones = array( 'BE', 'FR', 'DE', 'IE', 'IT', 'NL', 'ES', 'SE', 'CH', 'AU', 'BR', 'CA', 'CN', 'HK', 'JP', 'NZ' );
		$zone             = in_array( $destination, $individual_zones, true ) ? $destination : $this->get_zone( $destination );

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
							case 'BE':
								$quote += $value[0];
								break;
							case 'FR':
								$quote += $value[1];
								break;
							case 'DE':
								$quote += $value[2];
								break;
							case 'IE':
								$quote += $value[3];
								break;
							case 'IT':
								$quote += $value[4];
								break;
							case 'NL':
								$quote += $value[5];
								break;
							case 'ES':
								$quote += $value[6];
								break;
							case 'SE':
								$quote += $value[7];
								break;
							case 'CH':
								$quote += $value[8];
								break;
							case 'AU':
								$quote += $value[9];
								break;
							case 'BR':
								$quote += $value[10];
								break;
							case 'CA':
								$quote += $value[11];
								break;
							case 'CH':
								$quote += $value[12];
								break;
							case 'HK':
								$quote += $value[13];
								break;
							case 'JP':
								$quote += $value[14];
								break;
							case 'NZ':
								$quote += $value[15];
								break;
							case Shipping_Zones::ZONE_EUR_1:
								$quote += $value[16];
								break;
							case Shipping_Zones::ZONE_EUR_2:
								$quote += $value[17];
								break;
							case Shipping_Zones::ZONE_EUR_3:
							case Shipping_Zones::ZONE_EU:
								$quote += $value[18];
								break;
							case Shipping_Zones::ZONE_1:
								$quote += $value[19];
								break;
							case Shipping_Zones::ZONE_2:
								$quote += $value[20];
								break;
							case Shipping_Zones::ZONE_3:
								// Fallback to zone 1 for older prices.
								$quote += isset( $value[21] ) ? $value[21] : $value[19];
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
