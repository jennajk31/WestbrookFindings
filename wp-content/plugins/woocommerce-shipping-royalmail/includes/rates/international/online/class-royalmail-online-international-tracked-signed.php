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
 * RoyalMail_Online_International_Tracked_Signed class.
 *
 * Updated on 2025-04-07 as per https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf.
 * See International Tracked & Signed page 12 - 14.
 */
class RoyalMail_Online_International_Tracked_Signed extends RoyalMail_Rate {
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
	 * List of countries that support Tracked and Signed service.
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
	 * Setup RoyalMail_Online_International_Tracked_Signed rates.
	 */
	public function setup() {
		$this->bands            = $this->initialize_bands();
		$this->additional_rates = $this->initialize_additional_rates();
	}

	/**
	 * Initializes pricing bands for package sizes and destinations for the year 2024.
	 * The pricing structure includes rates for countries categorized by different zones:
	 * Belgium, France, Germany, Ireland, Italy, Netherlands, Spain, Sweden, Switzerland, Australia, Brazil, Canada, China, Hong Kong, Japan, New Zealand, Europe 1, Europe 2, Europe 3, World Zone 1, World Zone 2, World Zone 3 (previously Zone 1).
	 *
	 * @return array The array of pricing bands.
	 */
	private function initialize_bands() {
		return array(
			'2024' => array(
				Packaging::LETTER         => array(
					100 => array( 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815, 815 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 965, 965, 965, 965, 965, 965, 965, 965, 965, 1080, 1065, 1065, 1065, 1065, 1065, 1080, 965, 965, 965, 1065, 1080, 1075 ),
					250 => array( 1020, 1020, 1020, 1020, 1020, 1020, 1020, 1020, 1020, 1280, 1160, 1160, 1160, 1160, 1160, 1280, 1020, 1020, 1020, 1160, 1280, 1180 ),
					500 => array( 1100, 1100, 1100, 1100, 1100, 1100, 1100, 1100, 1100, 1560, 1360, 1360, 1360, 1360, 1360, 1560, 1100, 1100, 1100, 1360, 1560, 1395 ),
					750 => array( 1145, 1145, 1145, 1145, 1145, 1145, 1145, 1145, 1145, 1865, 1565, 1565, 1565, 1565, 1565, 1865, 1145, 1145, 1145, 1565, 1865, 1615 ),
				),
				Packaging::PACKET         => array(
					100  => array( 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1330, 1540, 1540, 1540, 1540, 1540, 1330, 1015, 970, 1095, 1540, 1330, 1280 ),
					250  => array( 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1365, 1575, 1575, 1575, 1575, 1575, 1365, 1015, 970, 1095, 1575, 1365, 1440 ),
					500  => array( 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1815, 1665, 1665, 1665, 1665, 1665, 1815, 1135, 1125, 1250, 1665, 1815, 1880 ),
					750  => array( 1240, 1240, 1240, 1240, 1240, 1240, 1240, 1240, 1240, 2095, 1905, 1905, 1905, 1905, 1905, 2095, 1240, 1225, 1365, 1905, 2095, 1880 ),
					1000 => array( 1335, 1335, 1335, 1335, 1335, 1335, 1335, 1335, 1335, 2410, 2170, 2170, 2170, 2170, 2170, 2410, 1335, 1315, 1490, 2170, 2410, 2045 ),
					1250 => array( 1390, 1390, 1390, 1390, 1390, 1390, 1390, 1390, 1390, 2705, 2370, 2370, 2370, 2370, 2370, 2705, 1390, 1345, 1645, 2370, 2705, 2390 ),
					1500 => array( 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 3005, 2550, 2550, 2550, 2550, 2550, 3005, 1400, 1370, 1755, 2550, 3005, 2665 ),
					2000 => array( 1415, 1415, 1415, 1415, 1415, 1415, 1415, 1415, 1415, 3210, 2655, 2655, 2655, 2655, 2655, 3210, 1415, 1415, 2135, 2655, 3210, 2715 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1330, 1540, 1540, 1540, 1540, 1540, 1330, 1015, 970, 1095, 1540, 1330, 1280 ),
					250  => array( 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1015, 1365, 1575, 1575, 1575, 1575, 1575, 1365, 1015, 970, 1095, 1575, 1365, 1440 ),
					500  => array( 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1135, 1815, 1665, 1665, 1665, 1665, 1665, 1815, 1135, 1125, 1250, 1665, 1815, 1880 ),
					750  => array( 1240, 1240, 1240, 1240, 1240, 1240, 1240, 1240, 1240, 2095, 1905, 1905, 1905, 1905, 1905, 2095, 1240, 1225, 1365, 1905, 2095, 1880 ),
					1000 => array( 1335, 1335, 1335, 1335, 1335, 1335, 1335, 1335, 1335, 2410, 2170, 2170, 2170, 2170, 2170, 2410, 1335, 1315, 1490, 2170, 2410, 2045 ),
					1250 => array( 1390, 1390, 1390, 1390, 1390, 1390, 1390, 1390, 1390, 2705, 2370, 2370, 2370, 2370, 2370, 2705, 1390, 1345, 1645, 2370, 2705, 2390 ),
					1500 => array( 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 3005, 2550, 2550, 2550, 2550, 2550, 3005, 1400, 1370, 1755, 2550, 3005, 2665 ),
					2000 => array( 1415, 1415, 1415, 1415, 1415, 1415, 1415, 1415, 1415, 3210, 2655, 2655, 2655, 2655, 2655, 3210, 1415, 1415, 2135, 2655, 3210, 2715 ),
				),
				Packaging::MEDIUM_PARCEL  => array(
					100   => array( 1400, 1120, 1020, 1070, 1275, 1400, 1275, 1400, 1400, 2695, 2535, 2250, 2250, 2250, 2535, 2695, 1400, 1275, 1570, 2535, 2695, 2345 ),
					250   => array( 1400, 1120, 1020, 1070, 1275, 1400, 1275, 1400, 1400, 2695, 2535, 2250, 2250, 2250, 2535, 2695, 1400, 1275, 1570, 2535, 2695, 2345 ),
					500   => array( 1400, 1120, 1020, 1070, 1275, 1400, 1275, 1400, 1400, 2695, 2535, 2250, 2250, 2250, 2535, 2695, 1400, 1275, 1570, 2535, 2695, 2345 ),
					750   => array( 1400, 1120, 1020, 1070, 1275, 1400, 1275, 1400, 1400, 2695, 2535, 2250, 2250, 2250, 2535, 2695, 1400, 1275, 1570, 2535, 2695, 2345 ),
					1000  => array( 1400, 1120, 1020, 1070, 1275, 1400, 1275, 1400, 1400, 2695, 2535, 2250, 2250, 2250, 2535, 2695, 1400, 1275, 1570, 2535, 2695, 2345 ),
					1250  => array( 1480, 1170, 1020, 1070, 1480, 1460, 1580, 1460, 1460, 3440, 2825, 2340, 2750, 2750, 2825, 3440, 1460, 1580, 2070, 2825, 3440, 3215 ),
					1500  => array( 1460, 1170, 1020, 1070, 1480, 1460, 1580, 1460, 1460, 3440, 2825, 2340, 2750, 2750, 2825, 3440, 1460, 1580, 2070, 2825, 3440, 3215 ),
					2000  => array( 1460, 1170, 1020, 1070, 1480, 1460, 1580, 1460, 1460, 3440, 2825, 2340, 2750, 2750, 2825, 3440, 1460, 1580, 2070, 2825, 3440, 3215 ),
					3000  => array( 1665, 1260, 1180, 1090, 1830, 1665, 1830, 1665, 1665, 3810, 3225, 2745, 3200, 3200, 3200, 3810, 1665, 1830, 2575, 3225, 3810, 3340 ),
					4000  => array( 1715, 1300, 1270, 1160, 3295, 1715, 3295, 1715, 1715, 4230, 3865, 3070, 3815, 3815, 3815, 4230, 1715, 3295, 3255, 3865, 4230, 3920 ),
					5000  => array( 1770, 1385, 1395, 1290, 3595, 1770, 3595, 1770, 1770, 4670, 4845, 3395, 4745, 4745, 4745, 4870, 1770, 3595, 3835, 4845, 5080, 4500 ),
					7500  => array( 2315, 1440, 1765, 1480, 5340, 2315, 5340, 2315, 2315, 6270, 6525, 4180, 6405, 6405, 6405, 6670, 2315, 5340, 5360, 6525, 6920, 6100 ),
					10000 => array( 2815, 1675, 2130, 1605, 7310, 2815, 7310, 2815, 2815, 8600, 9780, 4910, 9780, 9780, 9780, 9100, 2815, 7310, 7320, 9780, 9600, 7600 ),
					15000 => array( 3810, 1995, 2865, 1815, 10160, 3810, 10160, 3810, 3810, 12100, 12550, 5240, 12350, 12350, 12350, 12100, 3810, 10160, 9915, 12550, 13500, 11070 ),
					20000 => array( 4755, 2560, 3600, 1919, 15710, 4755, 15710, 4755, 4755, 16700, 17300, 5440, 17000, 17000, 17000, 16700, 4755, 15710, 12215, 17300, 18700, 13300 ),
				),
			),
			'2025' => array(
				Packaging::LETTER         => array(
					100 => array( 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850, 850 ),
				),
				Packaging::LARGE_LETTER   => array(
					100 => array( 995, 995, 995, 995, 995, 995, 995, 995, 995, 1115, 1110, 1110, 1110, 1110, 1110, 1115, 995, 995, 995, 1100, 1115, 1115 ),
					250 => array( 1040, 1040, 1040, 1040, 1040, 1040, 1040, 1040, 1040, 1305, 1185, 1185, 1185, 1185, 1185, 1305, 1040, 1040, 1040, 1185, 1305, 1285 ),
					500 => array( 1120, 1120, 1120, 1120, 1120, 1120, 1120, 1120, 1120, 1590, 1385, 1385, 1385, 1385, 1385, 1590, 1120, 1120, 1120, 1385, 1590, 1440 ),
					750 => array( 1170, 1170, 1170, 1170, 1170, 1170, 1170, 1170, 1170, 1900, 1595, 1595, 1595, 1595, 1595, 1900, 1170, 1170, 1170, 1595, 1900, 1650 ),
				),
				Packaging::PACKET         => array(
					100  => array( 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1395, 1615, 1615, 1615, 1615, 1615, 1395, 1065, 1020, 1150, 1615, 1395, 1345 ),
					250  => array( 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1435, 1655, 1655, 1655, 1655, 1655, 1435, 1065, 1020, 1150, 1655, 1435, 1510 ),
					500  => array( 1190, 1190, 1190, 1190, 1190, 1190, 1190, 1190, 1190, 1905, 1750, 1750, 1750, 1750, 1750, 1905, 1190, 1180, 1315, 1750, 1905, 1975 ),
					750  => array( 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 2200, 2000, 2000, 2000, 2000, 2000, 2200, 1300, 1285, 1435, 2000, 2200, 1995 ),
					1000 => array( 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 2530, 2280, 2280, 2280, 2280, 2280, 2530, 1400, 1380, 1565, 2280, 2530, 2290 ),
					1250 => array( 1460, 1460, 1460, 1460, 1460, 1460, 1460, 1460, 1460, 2840, 2490, 2490, 2490, 2490, 2490, 2840, 1460, 1410, 1725, 2490, 2840, 2570 ),
					1500 => array( 1470, 1470, 1470, 1470, 1470, 1470, 1470, 1470, 1470, 3155, 2675, 2675, 2675, 2675, 2675, 3155, 1470, 1440, 1845, 2675, 3155, 2800 ),
					2000 => array( 1485, 1485, 1485, 1485, 1485, 1485, 1485, 1485, 1485, 3370, 2790, 2790, 2790, 2790, 2790, 3370, 1485, 1485, 2240, 2790, 3370, 2850 ),
				),
				Packaging::PRINTED_PAPERS => array(
					100  => array( 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1395, 1615, 1615, 1615, 1615, 1615, 1395, 1065, 1020, 1150, 1615, 1395, 1345 ),
					250  => array( 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1065, 1435, 1655, 1655, 1655, 1655, 1655, 1435, 1065, 1020, 1150, 1655, 1435, 1510 ),
					500  => array( 1190, 1190, 1190, 1190, 1190, 1190, 1190, 1190, 1190, 1905, 1750, 1750, 1750, 1750, 1750, 1905, 1190, 1180, 1315, 1750, 1905, 1975 ),
					750  => array( 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 1300, 2200, 2000, 2000, 2000, 2000, 2000, 2200, 1300, 1285, 1435, 2000, 2200, 1995 ),
					1000 => array( 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 1400, 2530, 2280, 2280, 2280, 2280, 2280, 2530, 1400, 1380, 1565, 2280, 2530, 2290 ),
					1250 => array( 1460, 1460, 1460, 1460, 1460, 1460, 1460, 1460, 1460, 2840, 2490, 2490, 2490, 2490, 2490, 2840, 1460, 1410, 1725, 2490, 2840, 2570 ),
					1500 => array( 1470, 1470, 1470, 1470, 1470, 1470, 1470, 1470, 1470, 3155, 2675, 2675, 2675, 2675, 2675, 3155, 1470, 1440, 1845, 2675, 3155, 2800 ),
					2000 => array( 1485, 1485, 1485, 1485, 1485, 1485, 1485, 1485, 1485, 3370, 2790, 2790, 2790, 2790, 2790, 3370, 1485, 1485, 2240, 2790, 3370, 2850 ),
				),
				Packaging::MEDIUM_PARCEL  => array(
					1000  => array( 1275, 1150, 1095, 1090, 1340, 1210, 1450, 1275, 1570, 2750, 2585, 2295, 2535, 2585, 2535, 2750, 1430, 1330, 1600, 2585, 2750, 2345 ),
					2000  => array( 1580, 1150, 1095, 1090, 1660, 1345, 1675, 1580, 2070, 3510, 2880, 2385, 2825, 2880, 2880, 3510, 1490, 1610, 2245, 2880, 3510, 3090 ),
					3000  => array( 1830, 1295, 1235, 1110, 1920, 1555, 1940, 1830, 2575, 3885, 2985, 2800, 3345, 2985, 2985, 3885, 1700, 1865, 2625, 3290, 3885, 3090 ),
					4000  => array( 3295, 1295, 1345, 1185, 3460, 2800, 3495, 3295, 3255, 4315, 3890, 3130, 3915, 3890, 3890, 4315, 1750, 3360, 3320, 3940, 4315, 3920 ),
					5000  => array( 3595, 1295, 1405, 1315, 3775, 3055, 3810, 3595, 3835, 4765, 4840, 3465, 4745, 4840, 4840, 4765, 1805, 3665, 3910, 4940, 5180, 4500 ),
					7500  => array( 5340, 1550, 1545, 1510, 5605, 4540, 5660, 5340, 5360, 6395, 6535, 4265, 6405, 6535, 6535, 6395, 2360, 5445, 5465, 6655, 7060, 6100 ),
					10000 => array( 7310, 1550, 1895, 1635, 7675, 6215, 7750, 7310, 7320, 8770, 9975, 5010, 9780, 9975, 9975, 8770, 2870, 7455, 7465, 9975, 9790, 7600 ),
					15000 => array( 10160, 1995, 2920, 1850, 10670, 8635, 10770, 10160, 9915, 12340, 12595, 5345, 12350, 12595, 12595, 12340, 3885, 10365, 10115, 12800, 13770, 11070 ),
					20000 => array( 15710, 1995, 3670, 1955, 16495, 13355, 16655, 15710, 12215, 17035, 17340, 5550, 17000, 17340, 17340, 17035, 4850, 16025, 12460, 17645, 19075, 13300 ),
				),
			),
		);
	}

	/**
	 * Initialize the additional rates for printed papers.
	 *
	 * @return array The array of additional rates.
	 */
	private function initialize_additional_rates() {
		return array(
			'2024' => array(
				Packaging::PRINTED_PAPERS => array( 135, 135, 135, 135, 135, 135, 135, 135, 135, 185, 185, 185, 185, 185, 185, 185, 135, 135, 135, 185, 230, 260 ),
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
		return Services::INTERNATIONAL_TRACKED_SIGNED;
	}

	/**
	 * Get quotes for this rate.
	 *
	 * @since 2.5.4
	 * @version 2.5.4
	 *
	 * @param  array  $items to be shipped.
	 * @param  string $packing_method the method selected.
	 * @param  string $destination Address to ship to.
	 * @param  array  $boxes User-defined boxes.
	 * @param  int    $instance_id Instance ID.
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
