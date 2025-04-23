<?php
/**
 * Main Royal Mail class file.
 *
 * @package WC_Shipping_Royalmail
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Utilities\FeaturesUtil;
use WooCommerce\RoyalMail\Services;

/**
 * Main Royal Mail class
 */
class WC_RoyalMail {
	/**
	 * Plugin's version.
	 *
	 * @since 2.5.0 introduced.
	 *
	 * @var string
	 */
	public $version = WOOCOMMERCE_SHIPPING_ROYALMAIL_VERSION;

	/**
	 * Centralized all service information.
	 *
	 * @var array
	 */
	public static $service_names = array(
		Services::FIRST_CLASS                  => 'Royal Mail 1st Class',
		Services::FIRST_CLASS_SIGNED           => 'Royal Mail Signed For&reg; 1st Class',
		Services::SECOND_CLASS                 => 'Royal Mail 2nd Class',
		Services::SECOND_CLASS_SIGNED          => 'Royal Mail Signed For&reg; 2nd Class',
		Services::SPECIAL_DELIVERY_9AM         => 'Royal Mail Special Delivery Guaranteed by 9am&reg;',
		Services::SPECIAL_DELIVERY_1PM         => 'Royal Mail Special Delivery Guaranteed by 1pm&reg;',
		Services::TRACKED_24                   => 'Royal Mail Tracked 24',
		Services::TRACKED_24_SIGNED            => 'Royal Mail Tracked 24 with Signature',
		Services::TRACKED_24_AGE_VERIFICATION  => 'Royal Mail Tracked 24 with Age Verification',
		Services::TRACKED_48                   => 'Royal Mail Tracked 48',
		Services::TRACKED_48_SIGNED            => 'Royal Mail Tracked 48 with Signature',
		Services::TRACKED_48_AGE_VERIFICATION  => 'Royal Mail Tracked 48 with Age Verification',
		Services::PARCELFORCE_EXPRESS_9        => 'Parcelforce Worldwide Express 9',
		Services::PARCELFORCE_EXPRESS_10       => 'Parcelforce Worldwide Express 10',
		Services::PARCELFORCE_EXPRESS_AM       => 'Parcelforce Worldwide Express AM',
		Services::PARCELFORCE_EXPRESS_24       => 'Parcelforce Worldwide Express 24',
		Services::PARCELFORCE_EXPRESS_48       => 'Parcelforce Worldwide Express 48',
		Services::PARCELFORCE_EXPRESS_48_LARGE => 'Parcelforce Worldwide Express 48 Large',
		Services::INTERNATIONAL_STANDARD       => 'Royal Mail International Standard',
		Services::INTERNATIONAL_TRACKED_SIGNED => 'Royal Mail International Tracked &amp; Signed',
		Services::INTERNATIONAL_TRACKED        => 'Royal Mail International Tracked',
		Services::INTERNATIONAL_SIGNED         => 'Royal Mail International Signed',
		Services::INTERNATIONAL_ECONOMY        => 'Royal Mail International Economy',
		Services::PARCELFORCE_IRELANDEXPRESS   => 'Parcelforce Worldwide Ireland Express',
		Services::PARCELFORCE_GLOBALECONOMY    => 'Parcelforce Worldwide Global Economy',
		Services::PARCELFORCE_GLOBALEXPRESS    => 'Parcelforce Worldwide Global Express',
		Services::PARCELFORCE_GLOBALPRIORITY   => 'Parcelforce Worldwide Global Priority',
		Services::PARCELFORCE_GLOBALVALUE      => 'Parcelforce Worldwide Global Value',
	);

	/**
	 * Regular service enums.
	 *
	 * @var string
	 */
	const REGULAR_SERVICE = 'regular';

	/**
	 * Online service enums.
	 *
	 * @var string
	 */
	const ONLINE_SERVICE = 'online';

	/**
	 * UK service enums.
	 *
	 * @var string
	 */
	const UK_SERVICE = 'uk';

	/**
	 * UK service enums.
	 *
	 * @var string
	 */
	const INTERNATIONAL_SERVICE = 'international';

	/**
	 * Centralized all service information.
	 *
	 * @var array
	 */
	public static $services = array(
		self::REGULAR_SERVICE => array(
			self::UK_SERVICE            => array(
				Services::FIRST_CLASS,
				Services::SECOND_CLASS,
				Services::FIRST_CLASS_SIGNED,
				Services::SECOND_CLASS_SIGNED,
				Services::SPECIAL_DELIVERY_9AM,
				Services::SPECIAL_DELIVERY_1PM,
				Services::TRACKED_24,
				Services::TRACKED_24_SIGNED,
				Services::TRACKED_48,
				Services::TRACKED_48_SIGNED,
				Services::PARCELFORCE_EXPRESS_9,
				Services::PARCELFORCE_EXPRESS_10,
				Services::PARCELFORCE_EXPRESS_AM,
				Services::PARCELFORCE_EXPRESS_24,
				Services::PARCELFORCE_EXPRESS_48,
				Services::PARCELFORCE_EXPRESS_48_LARGE,
			),
			self::INTERNATIONAL_SERVICE => array(
				Services::INTERNATIONAL_TRACKED,
				Services::INTERNATIONAL_TRACKED_SIGNED,
				Services::INTERNATIONAL_STANDARD,
				Services::INTERNATIONAL_ECONOMY,
				Services::INTERNATIONAL_SIGNED,
				Services::PARCELFORCE_IRELANDEXPRESS,
				Services::PARCELFORCE_GLOBALECONOMY,
				Services::PARCELFORCE_GLOBALEXPRESS,
				Services::PARCELFORCE_GLOBALPRIORITY,
				Services::PARCELFORCE_GLOBALVALUE,
			),
		),
		self::ONLINE_SERVICE  => array(
			self::UK_SERVICE            => array(
				Services::FIRST_CLASS,
				Services::SECOND_CLASS,
				Services::FIRST_CLASS_SIGNED,
				Services::SECOND_CLASS_SIGNED,
				Services::SPECIAL_DELIVERY_1PM,
				Services::TRACKED_24,
				Services::TRACKED_24_SIGNED,
				Services::TRACKED_24_AGE_VERIFICATION,
				Services::TRACKED_48,
				Services::TRACKED_48_SIGNED,
				Services::TRACKED_48_AGE_VERIFICATION,
				Services::PARCELFORCE_EXPRESS_48,
				Services::PARCELFORCE_EXPRESS_24,
				Services::PARCELFORCE_EXPRESS_AM,
				Services::PARCELFORCE_EXPRESS_10,
				Services::PARCELFORCE_EXPRESS_9,
			),
			self::INTERNATIONAL_SERVICE => array(
				Services::INTERNATIONAL_TRACKED,
				Services::INTERNATIONAL_TRACKED_SIGNED,
				Services::INTERNATIONAL_STANDARD,
				Services::INTERNATIONAL_ECONOMY,
				Services::INTERNATIONAL_SIGNED,
				Services::PARCELFORCE_GLOBALVALUE,
				Services::PARCELFORCE_GLOBALPRIORITY,
				Services::PARCELFORCE_GLOBALEXPRESS,
				Services::PARCELFORCE_IRELANDEXPRESS,
			),
		),
	);

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
		add_filter( 'plugin_action_links_' . plugin_basename( WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ), array( $this, 'plugin_action_links' ) );
		add_action( 'woocommerce_shipping_init', array( $this, 'shipping_init' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'shipping_methods' ) );
		add_action( 'admin_notices', array( $this, 'environment_check' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_wc_features_compatibility' ) );

		include_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-wc-shipping-royalmail-admin.php';
		new WC_Shipping_Royalmail_Admin();
	}

	/**
	 * Check environment.
	 *
	 * Hooked into `admin_notices` so it'll render admin notice if there's
	 * a failed check.
	 *
	 * @return void
	 */
	public function environment_check() {

		if ( 'GBP' !== get_woocommerce_currency() ) {
			echo '<div class="error">
				<p>' .
				// translators: %s is a link to currency settings.
				wp_kses_post( sprintf( __( 'Royal Mail requires that the <a href="%s">currency</a> is set to Pound sterling.', 'woocommerce-shipping-royalmail' ), esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) ) ) .
				'</p>
			</div>';
		}

		if ( 'GB' !== WC()->countries->get_base_country() ) {
			echo '<div class="error">
				<p>' .
				// translators: %s is a link to base country/region settings.
				wp_kses_post( sprintf( __( 'Royal Mail requires that the <a href="%s">base country/region</a> is set to United Kingdom.', 'woocommerce-shipping-royalmail' ), esc_url( admin_url( 'admin.php?page=wc-settings&tab=general' ) ) ) ) .
				'</p>
			</div>';
		}
	}

	/**
	 * Add plugin action links to the plugins page.
	 *
	 * @param array $links Links.
	 *
	 * @return array Links.
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'<a href="https://woocommerce.com/my-account/create-a-ticket?broken=primary&select=182719">' . __( 'Support', 'woocommerce-shipping-royalmail' ) . '</a>',
			'<a href="https://www.woocommerce.com/products/royal-mail">' . __( 'Docs', 'woocommerce-shipping-royalmail' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Load our shipping class.
	 */
	public function shipping_init() {
		require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-wc-royalmail-privacy.php';
		include_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-wc-shipping-royalmail.php';
	}

	/**
	 * Add our shipping method to woocommerce.
	 *
	 * @param array $methods Shipping methods.
	 *
	 * @return array Shipping methods.
	 */
	public function shipping_methods( $methods ) {
		$methods['royal_mail'] = 'WC_Shipping_Royalmail';
		return $methods;
	}

	/**
	 * Checks the plugin version.
	 *
	 * @since 2.5.0
	 * @version 2.5.0
	 *
	 * @return bool
	 */
	public function maybe_install() {
		// Only need to do this for versions less than 2.5.0 to migrate
		// settings to shipping zone instance.
		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
		if ( ! $doing_ajax
			&& ! defined( 'IFRAME_REQUEST' )
			&& version_compare( get_option( 'wc_royal_mail_version' ), '2.5.0', '<' ) ) {

			$this->install();

		}

		return true;
	}

	/**
	 * Update/migration script.
	 *
	 * @since 2.5.0
	 * @version 2.5.0
	 */
	public function install() {
		// Get all saved settings and cache it.
		$royal_mail_settings = get_option( 'woocommerce_royal_mail_settings', false );

		// If settings exists.
		if ( $royal_mail_settings ) {
			global $wpdb;

			// Unset un-needed settings.
			unset( $royal_mail_settings['enabled'] );
			unset( $royal_mail_settings['availability'] );
			unset( $royal_mail_settings['countries'] );

			// First add it to the "rest of the world" zone when no Royal Mail
			// instance.
			if ( ! $this->is_zone_has_royal_mail( 0 ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'royal_mail', 1, 1 ) );
				// Add settings to the newly created instance to options table.
				$instance = $wpdb->insert_id;
				add_option( 'woocommerce_royal_mail_' . $instance . '_settings', $royal_mail_settings );
			}
		}
		update_option( 'wc_royal_mail_version', $this->version );
	}

	/**
	 * Get All Royalmail Services.
	 *
	 * @return array
	 */
	public static function get_all_services() {
		return self::$services;
	}

	/**
	 * Get All Royalmail Regular Services.
	 *
	 * @return array
	 */
	public static function get_regular_services() {
		return self::$services[ self::REGULAR_SERVICE ];
	}

	/**
	 * Get Royalmail Regular UK Services.
	 *
	 * @return array
	 */
	public static function get_regular_uk_services() {
		return self::$services[ self::REGULAR_SERVICE ][ self::UK_SERVICE ];
	}

	/**
	 * Get Royalmail Regular International Services.
	 *
	 * @return array
	 */
	public static function get_regular_international_services() {
		return self::$services[ self::REGULAR_SERVICE ][ self::INTERNATIONAL_SERVICE ];
	}

	/**
	 * Get All Royalmail Online Services.
	 *
	 * @return array
	 */
	public static function get_online_services() {
		return self::$services[ self::ONLINE_SERVICE ];
	}

	/**
	 * Get Royalmail Online UK Services.
	 *
	 * @return array
	 */
	public static function get_online_uk_services() {
		return self::$services[ self::ONLINE_SERVICE ][ self::UK_SERVICE ];
	}

	/**
	 * Get Royalmail Online International Services.
	 *
	 * @return array
	 */
	public static function get_online_international_services() {
		return self::$services[ self::ONLINE_SERVICE ][ self::INTERNATIONAL_SERVICE ];
	}

	/**
	 * Get Royalmail UK Services ( Online and Regular ).
	 * This method will return services that available on both regular and online by using intersect.
	 *
	 * @return array
	 */
	public static function get_available_both_uk_services() {
		$regular_services = self::get_regular_uk_services();
		$online_services  = self::get_online_uk_services();

		return array_intersect( $regular_services, $online_services );
	}

	/**
	 * Get Royalmail International Services ( Online and Regular ).
	 * This method will return services that available on both regular and online by using intersect.
	 *
	 * @return array
	 */
	public static function get_available_both_international_services() {
		$regular_services = self::get_regular_international_services();
		$online_services  = self::get_online_international_services();

		return array_intersect( $regular_services, $online_services );
	}

	/**
	 * Get All Royalmail Services ( Online and Regular ).
	 * It's different with `get_all_services()` method.
	 * This method will return a flat array.
	 *
	 * @return array
	 */
	public static function get_available_both_services() {
		return array_merge( self::get_available_both_uk_services(), self::get_available_both_international_services() );
	}

	/**
	 * Get Royalmail Regular Services only.
	 * This method will return a flat array.
	 *
	 * @return array
	 */
	public static function get_regular_services_only() {
		$uk_services    = self::get_regular_uk_services_only();
		$inter_services = self::get_regular_international_services_only();

		return array_merge( $uk_services, $inter_services );
	}

	/**
	 * Get Royalmail Regular UK Services only.
	 * This method will return the uk services that only available on regular only.
	 *
	 * @return array
	 */
	public static function get_regular_uk_services_only() {
		$regular_services = self::get_regular_uk_services();
		$online_services  = self::get_online_uk_services();

		return array_diff( $regular_services, $online_services );
	}

	/**
	 * Get Royalmail Regular International Services only.
	 * This method will return the international services that only available on regular only.
	 *
	 * @return array
	 */
	public static function get_regular_international_services_only() {
		$regular_services = self::get_regular_international_services();
		$online_services  = self::get_online_international_services();

		return array_diff( $regular_services, $online_services );
	}

	/**
	 * Get Royalmail Online Services only.
	 * This method will return the international services that only available on online only.
	 *
	 * @return array
	 */
	public static function get_online_services_only() {
		$uk_services    = self::get_online_uk_services();
		$inter_services = self::get_online_international_services();

		return array_merge( $uk_services, $inter_services );
	}

	/**
	 * Get Royalmail Online Services for UK only.
	 * This method will return the UK services that only available on online only.
	 *
	 * @return array
	 */
	public static function get_online_uk_services_only() {
		$regular_services = self::get_regular_uk_services();
		$online_services  = self::get_online_uk_services();

		return array_diff( $online_services, $regular_services );
	}

	/**
	 * Get Royalmail Online Services for International only.
	 * This method will return the international services that only available on online only.
	 *
	 * @return array
	 */
	public static function get_online_international_services_only() {
		$regular_services = self::get_regular_international_services();
		$online_services  = self::get_online_international_services();

		return array_diff( $online_services, $regular_services );
	}

	/**
	 * Helper method to check whether given zone_id has royal_mail method instance.
	 *
	 * @since 2.5.0
	 * @version 2.5.0
	 *
	 * @param int $zone_id Zone ID.
	 *
	 * @return bool True if given zone_id has royal_mail method instance.
	 */
	public function is_zone_has_royal_mail( $zone_id ) {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'royal_mail' AND zone_id = %d", $zone_id ) ) > 0;
	}

	/**
	 * Declaring WooCommerce features compatibility.
	 */
	public function declare_wc_features_compatibility() {
		if ( class_exists( FeaturesUtil::class ) ) {
			FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ), true );
			FeaturesUtil::declare_compatibility( 'product_block_editor', plugin_basename( WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ), true );
		}
	}
}
