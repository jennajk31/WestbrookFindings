<?php
/**
 * Available Royal Mail Services class file.
 *
 * @package WC_Shipping_Royalmail
 */

namespace WooCommerce\RoyalMail;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Royal Mail Services class
 */
class Services {
	/**
	 * First class service.
	 *
	 * @var string
	 */
	const FIRST_CLASS = 'first-class';

	/**
	 * First class signed service.
	 *
	 * @var string
	 */
	const FIRST_CLASS_SIGNED = 'first-class-signed';

	/**
	 * Second class service.
	 *
	 * @var string
	 */
	const SECOND_CLASS = 'second-class';

	/**
	 * Second class signed service.
	 *
	 * @var string
	 */
	const SECOND_CLASS_SIGNED = 'second-class-signed';

	/**
	 * Special delivery 9am service.
	 *
	 * @var string
	 */
	const SPECIAL_DELIVERY_9AM = 'special-delivery-9am';

	/**
	 * Special delivery 1PM service.
	 *
	 * @var string
	 */
	const SPECIAL_DELIVERY_1PM = 'special-delivery-1pm';

	/**
	 * Tracked 24 service.
	 *
	 * @var string
	 */
	const TRACKED_24 = 'tracked-24';

	/**
	 * Tracked 24 signed service.
	 *
	 * @var string
	 */
	const TRACKED_24_SIGNED = 'tracked-24-signed';

	/**
	 * Tracked 24 age verification service.
	 *
	 * @var string
	 */
	const TRACKED_24_AGE_VERIFICATION = 'tracked-24-age-verification';

	/**
	 * Tracked 48 service.
	 *
	 * @var string
	 */
	const TRACKED_48 = 'tracked-48';

	/**
	 * Tracked 48 signed service.
	 *
	 * @var string
	 */
	const TRACKED_48_SIGNED = 'tracked-48-signed';

	/**
	 * Tracked 48 age verification service.
	 *
	 * @var string
	 */
	const TRACKED_48_AGE_VERIFICATION = 'tracked-48-age-verification';

	/**
	 * Parcelforce express 9 service.
	 *
	 * @var string
	 */
	const PARCELFORCE_EXPRESS_9 = 'parcelforce-express-9';

	/**
	 * Parcelforce express 10 service.
	 *
	 * @var string
	 */
	const PARCELFORCE_EXPRESS_10 = 'parcelforce-express-10';

	/**
	 * Parcelforce express AM service.
	 *
	 * @var string
	 */
	const PARCELFORCE_EXPRESS_AM = 'parcelforce-express-am';

	/**
	 * Parcelforce express 24 service.
	 *
	 * @var string
	 */
	const PARCELFORCE_EXPRESS_24 = 'parcelforce-express-24';

	/**
	 * Parcelforce express 48 service.
	 *
	 * @var string
	 */
	const PARCELFORCE_EXPRESS_48 = 'parcelforce-express-48';

	/**
	 * Parcelforce express 48 large service.
	 *
	 * @var string
	 */
	const PARCELFORCE_EXPRESS_48_LARGE = 'parcelforce-express-48-large';

	/**
	 * International standard service.
	 *
	 * @var string
	 */
	const INTERNATIONAL_STANDARD = 'international-standard';

	/**
	 * International tracked signed service.
	 *
	 * @var string
	 */
	const INTERNATIONAL_TRACKED_SIGNED = 'international-tracked-signed';

	/**
	 * International tracked service.
	 *
	 * @var string
	 */
	const INTERNATIONAL_TRACKED = 'international-tracked';

	/**
	 * International signed service.
	 *
	 * @var string
	 */
	const INTERNATIONAL_SIGNED = 'international-signed';

	/**
	 * International economy service.
	 *
	 * @var string
	 */
	const INTERNATIONAL_ECONOMY = 'international-economy';

	/**
	 * Parcelforce Ireland Express service.
	 *
	 * @var string
	 */
	const PARCELFORCE_IRELANDEXPRESS = 'parcelforce-irelandexpress';

	/**
	 * Parcelforce Global economy service.
	 *
	 * @var string
	 */
	const PARCELFORCE_GLOBALECONOMY = 'parcelforce-globaleconomy';

	/**
	 * Parcelforce Global express service.
	 *
	 * @var string
	 */
	const PARCELFORCE_GLOBALEXPRESS = 'parcelforce-globalexpress';

	/**
	 * Parcelforce Global priority service.
	 *
	 * @var string
	 */
	const PARCELFORCE_GLOBALPRIORITY = 'parcelforce-globalpriority';

	/**
	 * Parcelforce Global value service.
	 *
	 * @var string
	 */
	const PARCELFORCE_GLOBALVALUE = 'parcelforce-globalvalue';
}
