<?php
/**
 * Class for Shipping Zones.
 *
 * Provides a list of zones used for pricing calculations in shipping.
 *
 * @package WC_RoyalMail/Rate
 */

namespace WooCommerce\RoyalMail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Shipping_Zone class.
 */
class Shipping_Zones {
	/**
	 * Zone 1.
	 *
	 * @var string
	 */
	const ZONE_1 = '1';

	/**
	 * Zone 2.
	 *
	 * @var string
	 */
	const ZONE_2 = '2';

	/**
	 * Zone 3.
	 *
	 * @var string
	 */
	const ZONE_3 = '3';

	/**
	 * Zone 4
	 *
	 * @var string
	 */
	const ZONE_4 = '4';

	/**
	 * Zone 5
	 *
	 * @var string
	 */
	const ZONE_5 = '5';

	/**
	 * Zone 6
	 *
	 * @var string
	 */
	const ZONE_6 = '6';

	/**
	 * Zone 7
	 *
	 * @var string
	 */
	const ZONE_7 = '7';

	/**
	 * Zone 8
	 *
	 * @var string
	 */
	const ZONE_8 = '8';

	/**
	 * Zone 9
	 *
	 * @var string
	 */
	const ZONE_9 = '9';

	/**
	 * Zone 9 Non EU
	 *
	 * @var string
	 */
	const ZONE_9_NON_EU = '9_NON_EU';

	/**
	 * Zone 10
	 *
	 * @var string
	 */
	const ZONE_10 = '10';

	/**
	 * Zone 11
	 *
	 * @var string
	 */
	const ZONE_11 = '11';

	/**
	 * Zone 12
	 *
	 * @var string
	 */
	const ZONE_12 = '12';

	/**
	 * Zone UK.
	 *
	 * @var string
	 */
	const ZONE_UK = 'UK';

	/**
	 * Zone EU.
	 *
	 * @var string
	 */
	const ZONE_EU = 'EU';

	/**
	 * Europe Zone 1.
	 *
	 * @var string
	 */
	const ZONE_EUR_1 = 'EUR_1';

	/**
	 * Europe Zone 2.
	 *
	 * @var string
	 */
	const ZONE_EUR_2 = 'EUR_2';

	/**
	 * Europe Zone 1.
	 *
	 * @var string
	 */
	const ZONE_EUR_3 = 'EUR_3';

	/**
	 * Country-specific pricing zone prefix.
	 *
	 * @var string
	 */
	const CSP_PREFIX = 'CSP_';

	/**
	 * Get the country-specific pricing zone prefix for a given country code.
	 *
	 * @param string $country_code Country code.
	 *
	 * @return string
	 */
	public static function country_specific_pricing_zone( string $country_code ): string {
		return self::CSP_PREFIX . $country_code;
	}
}
