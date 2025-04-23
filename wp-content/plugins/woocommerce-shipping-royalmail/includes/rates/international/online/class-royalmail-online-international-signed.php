<?php
/**
 * International Signed rate.
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
 * RoyalMail_Online_International_Signed class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf.
 * See International Signed page 15 - 16.
 */
class RoyalMail_Online_International_Signed extends RoyalMail_Rate {
	const COMPENSATION_UP_TO_VALUE    = 250;
	const COMPENSATION_INCLUDED_VALUE = 50;

	/**
	 * Pricing bands
	 *
	 * @var array
	 */
	protected $bands;

	/**
	 * List of countries that support Signed service.
	 *
	 * @see https://www.royalmail.com/business/system/files/Royal-Mail-International-Business-Services-List-March2017.pdf.
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @var array
	 */
	protected $supported_countries = array(
		'AF',
		'AL',
		'DZ',
		'AO',
		'AI',
		'AG',
		'AM',
		'AW',
		'AU',
		'AZ',
		'BS',
		'BH',
		'BD',
		'BJ',
		'BM',
		'BT',
		'BO',
		'BQ',
		'BA',
		'BW',
		'BR',
		'IO',
		'VG',
		'BN',
		'BF',
		'BI',
		'CM',
		'CV',
		'CF',
		'TD',
		'CL',
		'CN',
		'CX',
		'CO',
		'KM',
		'CG',
		'CD',
		'CR',
		'CU',
		'CW',
		'DJ',
		'DM',
		'DO',
		'EG',
		'SV',
		'GQ',
		'ER',
		'EE',
		'ET',
		'FK',
		'FJ',
		'GF',
		'PF',
		'TF',
		'GA',
		'GM',
		'GH',
		'GD',
		'GP',
		'GT',
		'GN',
		'GW',
		'GY',
		'HT',
		'HN',
		'IN',
		'IR',
		'IQ',
		'IL',
		'CI',
		'JM',
		'JO',
		'KZ',
		'KE',
		'KI',
		'KW',
		'KG',
		'LA',
		'LS',
		'LR',
		'LY',
		'MO',
		'MK',
		'MG',
		'YT',
		'MW',
		'MV',
		'ML',
		'MQ',
		'MR',
		'MU',
		'MX',
		'MN',
		'ME',
		'MS',
		'MA',
		'MZ',
		'MM',
		'NA',
		'NR',
		'NP',
		'NC',
		'NI',
		'NE',
		'NG',
		'NU',
		'KP',
		'NO',
		'OM',
		'PK',
		'PW',
		'PA',
		'PG',
		'PY',
		'PE',
		'PH',
		'PN',
		'PR',
		'QA',
		'RE',
		'RW',
		'ST',
		'SA',
		'SN',
		'SC',
		'SL',
		'SB',
		'ZA',
		'SS',
		'LK',
		'BQ',
		'SH',
		'KN',
		'LC',
		'MF',
		'SX',
		'VC',
		'SD',
		'SR',
		'SZ',
		'SY',
		'TW',
		'TJ',
		'TZ',
		'TL',
		'TG',
		'TK',
		'TN',
		'TM',
		'TC',
		'TV',
		'UA',
		'UY',
		'UZ',
		'VU',
		'VE',
		'VN',
		'WF',
		'EH',
		'WS',
		'YE',
		'ZM',
		'ZW',
	);

	/**
	 * Setup RoyalMail_Online_International_Signed rates.
	 */
	public function setup() {
		$this->bands = $this->initialize_bands();
	}

	/**
	 * Initializes Pricing bands - Europe 1, Europe 2, Europe 3, World Zone 1, World Zone 2.
	 *
	 * @return array
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER       => array(
					100 => array( 815, 815, 815, 815, 815 ),
				),
				Packaging::LARGE_LETTER => array(
					100 => array( 965, 965, 965, 1065, 1080 ),
					250 => array( 1020, 1020, 1020, 1160, 1280 ),
					500 => array( 1100, 1100, 1100, 1360, 1560 ),
					750 => array( 1145, 1145, 1145, 1565, 1865 ),
				),
				Packaging::PACKET       => array(
					100  => array( 1015, 970, 1095, 1540, 1330 ),
					250  => array( 1015, 970, 1095, 1575, 1365 ),
					500  => array( 1135, 1125, 1250, 1665, 1815 ),
					750  => array( 1240, 1225, 1365, 1905, 2095 ),
					1000 => array( 1335, 1315, 1490, 2170, 2410 ),
					1250 => array( 1390, 1345, 1645, 2370, 2705 ),
					1500 => array( 1400, 1370, 1755, 2550, 3005 ),
					2000 => array( 1415, 1415, 2135, 2655, 3210 ),
				),
			),
			'2025' => array(
				Packaging::LETTER       => array(
					100 => array( 850, 850, 850, 850, 850 ),
				),
				Packaging::LARGE_LETTER => array(
					100 => array( 995, 995, 995, 1100, 1115 ),
					250 => array( 1040, 1040, 1040, 1185, 1305 ),
					500 => array( 1120, 1120, 1120, 1385, 1590 ),
					750 => array( 1170, 1170, 1170, 1595, 1900 ),
				),
				Packaging::PACKET       => array(
					100  => array( 1065, 1020, 1150, 1615, 1395 ),
					250  => array( 1065, 1020, 1150, 1655, 1435 ),
					500  => array( 1190, 1180, 1315, 1750, 1905 ),
					750  => array( 1300, 1285, 1435, 2000, 2200 ),
					1000 => array( 1400, 1380, 1565, 2280, 2530 ),
					1250 => array( 1460, 1410, 1725, 2490, 2840 ),
					1500 => array( 1470, 1440, 1845, 2675, 3155 ),
					2000 => array( 1485, 1485, 2240, 2790, 3370 ),
				),
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
		return Services::INTERNATIONAL_SIGNED;
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
	 * @return array{ 'international-signed': float }|false|null
	 */
	public function get_quotes( $items, $packing_method, $destination, $boxes = array(), $instance_id = '' ) {
		if ( ! in_array( $destination, $this->supported_countries, true ) ) {
			return array();
		}

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

		$packages                = $this->get_packages( $items, $packing_method );
		$class_quote             = false;
		$options                 = $this->get_instance_options( $instance_id );
		$ignore_max_compensation = ( ! empty( $options['compensation_optional'] ) && 'yes' === $options['compensation_optional'] );
		$additional_compensation = ( ! empty( $options['enable_addit_compensation'] ) && 'yes' === $options['enable_addit_compensation'] );
		$max_compensation        = ( true === $additional_compensation ) ? self::COMPENSATION_UP_TO_VALUE : self::COMPENSATION_INCLUDED_VALUE;

		if ( $packages ) {
			foreach ( $packages as $package ) {
				if ( $package->value > $max_compensation && ! $ignore_max_compensation ) {
					return false; // Max. compensation is 50 by default but 250 if additional compensation is ticked.
				}

				$quote = $this->get_quote( $package, $destination );

				// Do not return a quote if one of the packages exceeds a limitation.
				if ( false === $quote ) {
					return array();
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

	/**
	 * Get quote.
	 *
	 * @since 2.5.1
	 * @version 2.5.1
	 *
	 * @param stdClass $package     Package to ship.
	 * @param string   $destination Destination.
	 *
	 * @return bool|int|void
	 */
	public function get_quote( $package, $destination ) {

		$zone = $this->get_zone( $destination );

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
				}
				$matched = true;
				break;
			}
		}

		if ( ! $matched ) {
			return;
		}
		return $quote;
	}
}
