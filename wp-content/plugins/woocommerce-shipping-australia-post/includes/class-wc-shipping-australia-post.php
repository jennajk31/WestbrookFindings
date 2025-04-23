<?php
/**
 * Australia Post Shipping Method
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once WC_SHIPPING_AUSTRALIA_POST_ABSPATH . 'includes/class-logger.php';
require_once WC_SHIPPING_AUSTRALIA_POST_ABSPATH . 'includes/class-notifier.php';

use WooCommerce\AustraliaPost\Logger;
use WooCommerce\AustraliaPost\Notifier;
use WooCommerce\BoxPacker\WC_Boxpack;

/**
 * WC_Shipping_Australia_Post class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_Australia_Post extends WC_Shipping_Method {
	/**
	 * Default API.
	 *
	 * @var string
	 */
	private $default_api_key = 'smUZLviAk6JIkIHvL0xgzP1yToSu7iQJ';

	/**
	 * Max weight, Used for weight based packing only.
	 *
	 * @var float
	 */
	private $max_weight;

	/**
	 * Australia Post services.
	 *
	 * @var array
	 */
	private $services;

	/**
	 * Australia Post services extra cover.
	 *
	 * @var array
	 */
	private $extra_cover;

	/**
	 * Australia Post services that can use Signature on Delivery.
	 *
	 * @var array
	 */
	private $delivery_confirmation;

	/**
	 * Australia Post API endpoints.
	 *
	 * @var array
	 */
	private $endpoints = array(
		'calculation' => 'https://digitalapi.auspost.com.au/api/postage/{type}/{doi}/calculate.json',
		'services'    => 'https://digitalapi.auspost.com.au/api/postage/{type}/{doi}/service.json',
	);

	/**
	 * Four of the external territories of Australia are officially assigned their own country codes in ISO 3166-1.
	 *
	 * @var array
	 */
	private $au_territories = array(
		'AU',
		'CC',
		'CX',
		'HM',
		'NF',
	);

	/**
	 * Signature on Delivery cost.
	 * Based on this link : https://auspost.com.au/business/shipping/domestic-shipping/optional-extras-domestic.
	 *
	 * @var float
	 */
	private $sod_cost = 2.95;

	/**
	 * International Signature on Delivery cost.
	 * Based on this link : https://auspost.com.au/sending/send-overseas/features-extras-international.
	 *
	 * @var float
	 */
	private $int_sod_cost = 5.50;

	/**
	 * Found shipping rates after calculation shipping.
	 *
	 * @var array
	 */
	private $found_rates;

	/**
	 * Is the shipping destination international?
	 *
	 * @var bool
	 */
	private $is_international = false;

	/**
	 * Default Box Sizes.
	 *
	 * @var array
	 */
	private $default_boxes;

	/**
	 * Letter Sizes.
	 *
	 * @var array
	 */
	private $letter_sizes;

	/**
	 * Packing Method
	 *
	 * @var string
	 */
	private $packing_method;

	/**
	 * Shipping prices excluding tax.
	 *
	 * @var string
	 */
	private $excluding_tax;

	/**
	 * Origin postal code.
	 *
	 * @var string
	 */
	private $origin;

	/**
	 * Api Key.
	 *
	 * @var string
	 */
	private $api_key;

	/**
	 * Custom boxes.
	 *
	 * @var array
	 */
	private $boxes;

	/**
	 * Enabled Services.
	 *
	 * @var array
	 */
	private $custom_services;

	/**
	 * Show 'all' rates or not
	 *
	 * @var string
	 */
	private $offer_rates;

	/**
	 * Box packer library.
	 *
	 * @var string
	 */
	public string $box_packer_library;

	/**
	 * Enable debug mode.
	 *
	 * @var bool
	 */
	private $debug;

	/**
	 * Notifier instance.
	 *
	 * @var WooCommerce\AustraliaPost\Notifier
	 */
	public $notifier;

	/**
	 * Logger instance.
	 *
	 * @var WooCommerce\AustraliaPost\Logger
	 */
	public $logger;

	/**
	 * Use satchel rates. 'enable', 'disable' or 'prioritize'.
	 *
	 * @var string
	 */
	private $satchel_rates;

	/**
	 * __construct function.
	 *
	 * @param int $instance_id Instance Id.
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {
		parent::__construct( $instance_id );

		$this->id                 = 'australia_post';
		$this->method_title       = __( 'Australia Post', 'woocommerce-shipping-australia-post' );
		$this->method_description = __( 'The Australia Post extension obtains rates dynamically from the Australia Post API during cart/checkout.', 'woocommerce-shipping-australia-post' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'settings',
		);
		$this->init();
	}

	/**
	 * Is_available function.
	 *
	 * @param array $package Package to check.
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( empty( $package['destination']['country'] ) ) {
			return false;
		}

		/**
		 * Allow 3rd parties to disable this shipping method for a package.
		 *
		 * @since 2.4.0
		 *
		 * @param bool $value True if the package available.
		 * @param array $package The package to check.
		 */
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}

	/**
	 * Initialize settings
	 *
	 * @version 2.4.0
	 * @since 2.4.0
	 * @return void
	 */
	private function set_settings() {
		// Define user set variables.
		$this->title                 = $this->get_option( 'title', $this->method_title );
		$this->excluding_tax         = $this->get_option( 'excluding_tax', 'no' );
		$this->origin                = $this->get_option( 'origin', '' );
		$this->api_key               = $this->get_option( 'api_key', $this->default_api_key );
		$this->packing_method        = $this->get_option( 'packing_method', 'per_item' );
		$this->boxes                 = $this->get_option( 'boxes', array() );
		$this->custom_services       = $this->get_option( 'services', array() );
		$this->offer_rates           = $this->get_option( 'offer_rates', 'all' );
		$this->debug                 = 'yes' === $this->get_option( 'debug_mode' );
		$this->satchel_rates         = $this->get_option( 'satchel_rates' );
		$this->services              = include __DIR__ . '/data/data-services.php';
		$this->extra_cover           = include __DIR__ . '/data/data-extra-cover.php';
		$this->delivery_confirmation = include __DIR__ . '/data/data-sod.php';
		$this->default_boxes         = include __DIR__ . '/data/data-box-sizes.php';
		$this->letter_sizes          = include __DIR__ . '/data/data-letter-sizes.php';

		// Used for weight based packing only.
		$this->max_weight = $this->get_option( 'max_weight', '20' );

		$this->box_packer_library = $this->get_option( 'box_packer_library', $this->get_default_box_packer_library() );

		/**
		 * Set the notifier.
		 */
		$this->notifier = new Notifier( $this->debug );

		/**
		 * Set the logger.
		 */
		$this->logger = new Logger( $this->debug );
	}

	/**
	 * Init function.
	 *
	 * @return void
	 */
	private function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->set_settings();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'test_api_key' ), -10 );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );

		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'add_hidden_order_itemmeta_keys' ) );

		add_action( 'woocommerce_before_calculate_totals', array( $this, 'maybe_clear_shipping_cache' ), 11 );
		add_action( 'woocommerce_after_get_rates_for_package', array( $this, 'maybe_display_notices' ), 11, 2 );
	}

	/**
	 * Add meta keys to the list of keys to hide in the order item meta.
	 *
	 * @param array $keys Item meta keys.
	 *
	 * @return array
	 */
	public function add_hidden_order_itemmeta_keys( $keys ) {
		$keys[] = '_package_length';
		$keys[] = '_package_width';
		$keys[] = '_package_height';
		$keys[] = '_package_weight';

		return $keys;
	}

	/**
	 * Get default and custom boxes merged.
	 *
	 * @return array
	 */
	private function get_all_boxes() {
		$enabled_boxes = array();
		foreach ( $this->boxes as $key => &$box ) {

			if ( isset( $box['id'] ) && isset( $box['enabled'] ) ) {
				$enabled_boxes[ $box['id'] ] = $box['enabled'];
				unset( $this->boxes[ $key ] );
			}

			// Update type field for BC reasons. 'is_letter' is the old busted way 'type' is the new hotness.
			if ( empty( $box['type'] ) && ! empty( $box['is_letter'] ) ) {
				$box['type'] = 'envelope';
			} elseif ( empty( $box['type'] ) ) {
				$box['type'] = 'box';
			}
			if ( empty( $box['name'] ) ) {
				$box['name'] = 'Box ' . ( $key + 1 );
			}
		}

		foreach ( $this->default_boxes as &$box ) {
			if ( isset( $enabled_boxes[ $box['id'] ] ) ) {
				$box['enabled'] = $enabled_boxes[ $box['id'] ];
			} else {
				$box['enabled'] = true;
			}
		}
		return array_merge( $this->default_boxes, $this->boxes );
	}

	/**
	 * Process settings on save.
	 *
	 * @since 2.4.0
	 * @version 2.4.0
	 * @return void
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		$this->set_settings();
	}

	/**
	 * Output a debug message.
	 *
	 * @since 1.0.0
	 * @version 2.4.4
	 *
	 * @param string $message Debug message.
	 * @param string $type    Debug type ('notice', 'error', 'success').
	 * @param array  $data    Optional. Additional data to pass.
	 * @param string $group   Optional. Group to categorize notices.
	 */
	public function debug( $message, $type = 'notice', $data = array(), $group = '' ) {
		$this->notifier->debug( $message, $type, $data, $group );
		$this->logger->debug( $message, $data );
	}

	/**
	 * Return the debug mode status.
	 *
	 * @return bool
	 */
	public function is_debug_mode_enabled(): bool {
		return $this->debug;
	}

	/**
	 * Generate_services_html function.
	 *
	 * @return string
	 */
	public function generate_services_html() {
		ob_start();
		include __DIR__ . '/views/html-services.php';
		return ob_get_clean();
	}

	/**
	 * Generate_box_packing_html function.
	 *
	 * @return string
	 */
	public function generate_box_packing_html() {
		ob_start();
		include __DIR__ . '/views/html-box-packing.php';
		return ob_get_clean();
	}

	/**
	 * Validate_box_packing_field function.
	 *
	 * @param string $key Field key.
	 * @param string $value Posted Value.
	 *
	 * @return array
	 */
	public function validate_box_packing_field( $key, $value ) {
		$boxes = array();

		// The nonce check is already performed in WC_Settings_Shipping::instance_settings_screen(), so no need to check it again.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( isset( $_POST['boxes_outer_length'] ) ) {
			$boxes_name         = isset( $_POST['boxes_name'] ) ? wc_clean( wp_unslash( $_POST['boxes_name'] ) ) : array();
			$boxes_outer_length = isset( $_POST['boxes_outer_length'] ) ? wc_clean( wp_unslash( $_POST['boxes_outer_length'] ) ) : array();
			$boxes_outer_width  = isset( $_POST['boxes_outer_width'] ) ? wc_clean( wp_unslash( $_POST['boxes_outer_width'] ) ) : array();
			$boxes_outer_height = isset( $_POST['boxes_outer_height'] ) ? wc_clean( wp_unslash( $_POST['boxes_outer_height'] ) ) : array();
			$boxes_inner_length = isset( $_POST['boxes_inner_length'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_length'] ) ) : array();
			$boxes_inner_width  = isset( $_POST['boxes_inner_width'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_width'] ) ) : array();
			$boxes_inner_height = isset( $_POST['boxes_inner_height'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_height'] ) ) : array();
			$boxes_box_weight   = isset( $_POST['boxes_box_weight'] ) ? wc_clean( wp_unslash( $_POST['boxes_box_weight'] ) ) : array();
			$boxes_max_weight   = isset( $_POST['boxes_max_weight'] ) ? wc_clean( wp_unslash( $_POST['boxes_max_weight'] ) ) : array();
			$boxes_type         = isset( $_POST['boxes_type'] ) ? wc_clean( wp_unslash( $_POST['boxes_type'] ) ) : array();
			$boxes_enabled      = isset( $_POST['boxes_enabled'] ) ? wc_clean( wp_unslash( $_POST['boxes_enabled'] ) ) : array();
			// phpcs:enable WordPress.Security.NonceVerification.Missing

			$boxes_count         = count( $boxes_outer_length );
			$default_boxes_count = count( $this->default_boxes );

			for ( $i = 0; $i < $boxes_count; $i++ ) {

				if ( $i < $default_boxes_count ) {
					$boxes[] = array(
						'enabled' => isset( $boxes_enabled[ $i ] ) ? true : false,
						'id'      => $this->default_boxes[ $i ]['id'],
					);
				} elseif ( $boxes_outer_length[ $i ] && $boxes_outer_width[ $i ] && $boxes_outer_height[ $i ] && $boxes_inner_length[ $i ] && $boxes_inner_width[ $i ] && $boxes_inner_height[ $i ] ) {

					$boxes[] = array(
						'name'         => sanitize_text_field( $boxes_name[ $i ] ),
						'outer_length' => floatval( $boxes_outer_length[ $i ] ),
						'outer_width'  => floatval( $boxes_outer_width[ $i ] ),
						'outer_height' => floatval( $boxes_outer_height[ $i ] ),
						'inner_length' => floatval( $boxes_inner_length[ $i ] ),
						'inner_width'  => floatval( $boxes_inner_width[ $i ] ),
						'inner_height' => floatval( $boxes_inner_height[ $i ] ),
						'box_weight'   => floatval( $boxes_box_weight[ $i ] ),
						'max_weight'   => floatval( $boxes_max_weight[ $i ] ),
						'type'         => in_array( $boxes_type[ $i ], array( 'box', 'tube', 'envelope', 'packet' ), true ) ? $boxes_type[ $i ] : 'box',
						'enabled'      => isset( $boxes_enabled[ $i ] ) ? true : false,
					);
				}
			}
		}

		return $boxes;
	}

	/**
	 * Validate_services_field function.
	 *
	 * @param mixed $key Key.
	 * @return array
	 */
	public function validate_services_field( $key ) {
		// The nonce check is already performed in WC_Settings_Shipping::instance_settings_screen(), so no need to check it again.
		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['australia_post_service'] ) ) {
			return array();
		}

		if ( ! is_array( $_POST['australia_post_service'] ) ) {
			return array();
		}

		$posted_services = wc_clean( wp_unslash( $_POST['australia_post_service'] ) );
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		$services = array();
		foreach ( $posted_services as $code => $settings ) {
			$services[ $code ] = array(
				'name'                  => isset( $settings['name'] ) ? $settings['name'] : '',
				'order'                 => isset( $settings['order'] ) ? $settings['order'] : '',
				'enabled'               => isset( $settings['enabled'] ),
				'adjustment'            => isset( $settings['adjustment'] ) ? floatval( $settings['adjustment'] ) : '',
				'adjustment_percent'    => isset( $settings['adjustment_percent'] ) ? floatval( str_replace( '%', '', $settings['adjustment_percent'] ) ) : '',
				'extra_cover'           => isset( $settings['extra_cover'] ),
				'delivery_confirmation' => isset( $settings['delivery_confirmation'] ),
			);
		}

		return $services;
	}

	/**
	 * Clear_transients function.
	 *
	 * @return void
	 */
	public function clear_transients() {
		global $wpdb;

		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wc_australia_post_quotes_%') OR `option_name` LIKE ('_transient_timeout_wc_australia_post_quotes_%')" );
	}

	/**
	 * Init_form_fields function.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'          => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-australia-post' ),
				'default'     => __( 'Australia Post', 'woocommerce-shipping-australia-post' ),
			),
			'origin'         => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'description' => __( 'Enter the postcode for the <strong>sender</strong>.', 'woocommerce-shipping-australia-post' ),
				'default'     => '',
			),
			'excluding_tax'  => array(
				'title'       => __( 'Tax', 'woocommerce-shipping-australia-post' ),
				'label'       => __( 'Calculate Rates Excluding Tax', 'woocommerce-shipping-australia-post' ),
				'type'        => 'checkbox',
				'description' => __( "Calculate shipping rates excluding tax (if you plan to add tax via WooCommerce's tax system). By default rates returned by the Australia Post API include tax.", 'woocommerce-shipping-australia-post' ),
				'default'     => 'no',
			),
			'rates'          => array(
				'title'       => __( 'Rates and Services', 'woocommerce-shipping-australia-post' ),
				'type'        => 'title',
				'description' => __( 'The following settings determine the rates you offer your customers.', 'woocommerce-shipping-australia-post' ),
			),
			'packing_method' => array(
				'title'   => __( 'Parcel Packing Method', 'woocommerce-shipping-australia-post' ),
				'type'    => 'select',
				'default' => '',
				'class'   => 'packing_method',
				'options' => array(
					'per_item'    => __( 'Default: Pack items individually', 'woocommerce-shipping-australia-post' ),
					'weight'      => __( 'Weight of all items', 'woocommerce-shipping-australia-post' ),
					'box_packing' => __( 'Recommended: Pack into boxes with weights and dimensions', 'woocommerce-shipping-australia-post' ),
				),
			),
			'max_weight'     => array(
				'title'       => __( 'Maximum weight (kg)', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'default'     => '20',
				'description' => __( 'Maximum weight per package in kg.', 'woocommerce-shipping-australia-post' ),
			),
			'boxes'          => array(
				'type' => 'box_packing',
			),
			'satchel_rates'  => array(
				'title'   => __( 'Satchel Rates', 'woocommerce-shipping-australia-post' ),
				'type'    => 'select',
				'options' => array(
					'on'       => __( 'Enable Satchel Rates', 'woocommerce-shipping-australia-post' ),
					'priority' => __( 'Prioritze Satchel Rates', 'woocommerce-shipping-australia-post' ),
					'off'      => __( 'Disable Satchel Rates', 'woocommerce-shipping-australia-post' ),
				),
				'default' => 'off',
			),
			'offer_rates'    => array(
				'title'       => __( 'Offer Rates', 'woocommerce-shipping-australia-post' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'all',
				'options'     => array(
					'all'      => __( 'Offer the customer all returned rates', 'woocommerce-shipping-australia-post' ),
					'cheapest' => __( 'Offer the customer the cheapest rate only, anonymously', 'woocommerce-shipping-australia-post' ),
				),
			),
			'services'       => array(
				'type' => 'services',
			),
		);

		$this->form_fields = array(
			'api'                => array(
				'title'       => __( 'API Settings', 'woocommerce-shipping-australia-post' ),
				'type'        => 'title',
				'description' => __( 'Your API access details are obtained from the Australia Post website. You can obtain your <a href="https://developers.auspost.com.au/apis/pacpcs-registration">own key here</a>, or just use ours.', 'woocommerce-shipping-australia-post' ),
			),
			'api_key'            => array(
				'title'       => __( 'API Key', 'woocommerce-shipping-australia-post' ),
				'type'        => 'text',
				'description' => __( 'Leave blank to use our API Key.', 'woocommerce-shipping-australia-post' ),
				'default'     => '',
				'placeholder' => $this->default_api_key,
			),
			'box_packer_library' => array(
				'title'       => __( 'Box Packer Library', 'woocommerce-shipping-australia-post' ),
				'type'        => 'select',
				'default'     => '',
				'class'       => 'box_packer_library',
				'options'     => array(
					'original' => __( 'Speed Packer', 'woocommerce-shipping-australia-post' ),
					'dvdoug'   => __( 'Accurate Packer', 'woocommerce-shipping-australia-post' ),
				),
				'description' => __( 'Speed Packer packs items by volume, Accurate Packer check each dimension allowing more accurate packing but might be slow when you sell items in large quantities.', 'woocommerce-shipping-australia-post' ),
			),
			'debug_mode'         => array(
				'title'       => __( 'Debug Mode', 'woocommerce-shipping-australia-post' ),
				'label'       => __( 'Enable debug mode', 'woocommerce-shipping-australia-post' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'description' => __( 'Enable debug mode to show debugging information on your cart/checkout.', 'woocommerce-shipping-australia-post' ),
			),
		);
	}

	/**
	 * Tests the entered API key against the service to see if a forbidden error is returned.
	 * If it is, the key is rejected and an error message is displayed.
	 */
	public function test_api_key() {
		if ( empty( $_POST['woocommerce_australia_post_api_key'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$test_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'parcel',
				'domestic',
			),
			$this->endpoints['calculation']
		);
		$test_request  = 'weight=5&height=5&width=5&length=5&from_postcode=3149&to_postcode=3149&service_code=AUS_PARCEL_REGULAR';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is checked in hook invoker.
		$test_headers = array( 'AUTH-KEY' => sanitize_text_field( wp_unslash( $_POST['woocommerce_australia_post_api_key'] ) ) );

		// We don't want to use $this->get_response here because we don't want the result cached,
		// we want to avoid the front end debug notices, and we want to get back the actual status code.
		$response      = wp_remote_get(
			$test_endpoint . '?' . $test_request,
			array(
				'headers' => $test_headers,
			)
		);
		$response_code = wp_remote_retrieve_response_code( $response );

		if ( 403 !== $response_code ) {
			return;
		}

		echo '<div class="error">
			<p>' .
			sprintf(
				// translators: %1$s is the opening link tag, %2$s is the closing link tag.
				esc_html__( 'The Australia Post API key you entered is invalid. Please make sure you entered a valid key (%1$swhich can be obtained here%2$s) and not your WooCommerce license key. Our API key will be used instead.', 'woocommerce-shipping-australia-post' ),
				'<a href="https://auspost.com.au/devcentre/pacpcs-registration.asp">',
				'</a>'
			) .
			'</p>
		</div>';

		$_POST['woocommerce_australia_post_api_key'] = '';
	}

	/**
	 * Get max extra cover from quote.
	 *
	 * @since 2.4.2
	 *
	 * @param float  $item_value Item value.
	 * @param object $quote      Quote from API.
	 *
	 * @return int Max extra cover
	 */
	protected function get_max_extra_cover_from_quote( $item_value, $quote ) {
		$max_extra_cover = $quote->max_extra_cover;

		if ( ! isset( $quote->options->option ) || ! is_array( $quote->options->option ) ) {
			return $max_extra_cover;
		}

		$options = $quote->options->option;
		foreach ( $options as $option ) {
			if ( ! isset( $option->suboptions->option->max_extra_cover ) ) {
				continue;
			}

			$max_extra_cover_option = $option->suboptions->option->max_extra_cover;

			if ( $item_value > $max_extra_cover_option ) {
				continue;
			}

			$max_extra_cover = $max_extra_cover_option;
		}

		return $max_extra_cover;
	}

	/**
	 * Calculates the extra cover cost
	 * For international it's $4.00 per $100 over $100
	 * For domestic it's $2.50 per $100 over $100
	 * We are assuming max cover for each items
	 *
	 * @since 2.3.12
	 * @version 2.4.18
	 *
	 * @param float $item_value Value of current item.
	 * @param int   $max_extra_cover The maximum allowed value to cover.
	 * @return float $cover_cost
	 */
	public function calculate_extra_cover_cost( $item_value, $max_extra_cover ) {
		$extra_cover_cost = 2.50;

		// If there's no item value, or the value is below $100 return 0.
		if ( empty( $item_value ) || 100 >= $item_value ) {
			return 0;
		}

		// Make sure item value is no more than max cover value.
		if ( $item_value > $max_extra_cover ) {
			$item_value = $max_extra_cover;
		}

		// International extra cover is $4.00.
		if ( $this->is_international ) {
			$extra_cover_cost = 4.00;
		}

		// Add the extra cover cost for each $100 over $100.
		return $extra_cover_cost * ( ceil( ( $item_value - 100 ) / 100 ) );
	}

	/**
	 * See if rate is satchel
	 *
	 * @param string $code Service code.
	 * @return boolean
	 */
	public function is_satchel( $code ) {
		if ( ! is_string( $code ) ) {
			return false;
		}

		return strpos( $code, '_SATCHEL_' ) !== false;
	}

	/**
	 * See if rate is Courier Post.
	 *
	 * @param string $code Service code.
	 * @return bool
	 */
	public function is_courier_post( $code ) {
		if ( ! is_string( $code ) ) {
			return false;
		}

		return strpos( $code, 'COURIER' ) !== false;
	}

	/**
	 * Calculate_shipping function.
	 *
	 * @param array $package Current package.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$this->found_rates      = array();
		$this->is_international = $this->is_international( $package );
		$headers                = $this->get_request_header();
		$package_requests       = $this->get_package_requests( $package );
		$is_valid_request       = true;

		$this->debug( __( 'Australia Post debug mode is on - to hide these messages, turn debug mode off in the settings.', 'woocommerce-shipping-australia-post' ) );

		// Prepare endpoints.
		$letter_services_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'letter',
				( $this->is_international ? 'international' : 'domestic' ),
			),
			$this->endpoints['services']
		);

		$services_endpoint = str_replace(
			array( '{type}', '{doi}' ),
			array(
				'parcel',
				( $this->is_international ? 'international' : 'domestic' ),
			),
			$this->endpoints['services']
		);

		if ( empty( $this->get_to_postcode( $package ) ) && ! $this->is_international ) {
			$this->debug( __( 'to_postcode is missing. Destination post code is missing.' ) );
			$is_valid_request = false;
		}

		if ( empty( $this->get_from_postcode( $package ) ) ) {
			$this->debug( __( 'from_postcode is missing. Please check your Origin Postcode.' ) );
			$is_valid_request = false;
		}

		if ( $package_requests && true === $is_valid_request ) {

			foreach ( $package_requests as $key => $package_request ) {
				/**
				 * The Australia Post API would not return correct Satchel rates if we sent the exact Satchel
				 * dimensions in the API request. It would return rates for the next Satchel size up which was
				 * causing customers to be charged too much and merchants to use the wrong packaging. In order to
				 * get accurate shipping rates for packed Satchels, we had to slightly reduce the dimensions that
				 * are sent to the Australia Post API.
				 *
				 * However, in order to display the exact Satchel dimensions to the merchant
				 * in the order details, we needed to pass the exact dimensions in the $package_request.
				 * That is why we are extracting the exact dimensions here. And we are un-setting them from the
				 * $package_request so that they are not sent in the API request to avoid potential API request errors.
				 *
				 * @see https://github.com/woocommerce/woocommerce-shipping-australia-post/pull/198
				 */
				$exact_dimensions = isset( $package_request['exact_dimensions'] ) ? $package_request['exact_dimensions'] : array();
				unset( $package_request['exact_dimensions'] );

				/**
				 * The Australia Post API doesn't accept dimension parameters for
				 * international letter service endpoints. It only accepts weight. If
				 * our package has large envelope dimensions but is under 250g, the
				 * API will give us small envelope rates.
				 *
				 * Because of that fact, we need to adjust the weight to 251 if the
				 * package has large envelope dimensions, so that the customer doesn't
				 * pay small envelope rates while the merchant has to pay large
				 * envelope rates.
				 *
				 * This only applies for international rates.
				 *
				 * @see https://github.com/woocommerce/woocommerce-shipping-australia-post/issues/164
				 */
				if ( $this->is_international && $this->is_large_envelope( $package_request ) && $package_request['weight'] < 250 ) {
					$package_request['weight'] = 251;
				}

				$request = http_build_query( array_merge( $package_request, $this->get_request( $package ) ), '', '&' );

				if ( isset( $package_request['thickness'] ) ) {
					$response = $this->get_response( $letter_services_endpoint, $request, $headers );
				} else {
					$response = $this->get_response( $services_endpoint, $request, $headers );
				}

				// If a single service is returned, it's an object an not an array, so put it into an array for further processing.
				if ( isset( $response->services->service ) && is_object( $response->services->service ) ) {
					$response->services->service = array( $response->services->service );
				}

				if ( isset( $response->services->service ) && is_array( $response->services->service ) ) {

					// Loop our known services.
					foreach ( $this->services as $service => $values ) {

						$rate_code            = (string) $service;
						$rate_id              = $this->id . ':' . $rate_code;
						$rate_name            = (string) $values['name'];
						$rate_cost            = null;
						$optional_extras_cost = 0;
						$name                 = $rate_code;

						// Use this array to pass metadata to the order item.
						$meta_data = array();

						// Use the exact dimensions if its exist.
						if ( ! empty( $exact_dimensions ) ) {
							$meta_data['package_length'] = $exact_dimensions['length'];
							$meta_data['package_width']  = $exact_dimensions['width'];
							$meta_data['package_height'] = $exact_dimensions['height'];
						} else {
							$meta_data['package_length'] = $package_request['length'];
							$meta_data['package_width']  = $package_request['width'];
							$meta_data['package_height'] = isset( $package_request['height'] ) ? $package_request['height'] : '';
						}

						$meta_data['package_weight']    = $package_request['weight'];
						$meta_data['package_thickness'] = isset( $package_request['thickness'] ) ? $package_request['thickness'] : '';

						// Main service code.
						foreach ( $response->services->service as $quote ) {
							if ( ( isset( $values['alternate_services'] ) && in_array( $quote->code, $values['alternate_services'], true ) ) || $service === $quote->code ) {

								$delivery_confirmation = false;
								$rate_set              = false;

								/**
								 * If our package has large envelope dimensions, we want to skip
								 * using any returned rates that are for small envelopes, because
								 * they would override the correct large envelope rates.
								 *
								 * This only applies for international rates.
								 *
								 * @see https://github.com/woocommerce/woocommerce-shipping-australia-post/issues/164
								 */
								if ( $this->is_international && $this->is_large_envelope( $package_request ) && preg_match( '/(?:INT_LETTER)(?:[\_]+[A-Z]*)+(?:MEDIUM|SMALL){1}(?!_SATCHEL)(?:[\_]+[A-Z]*)*/', $quote->code ) ) {
									continue;
								}

								if ( $this->is_satchel( $quote->code ) && 'off' === $this->satchel_rates ) {
									continue;
								}

								if ( $this->is_satchel( $quote->code ) ) {

									if ( 'priority' === $this->satchel_rates ) {
										$rate_cost = $quote->price;
										$rate_set  = true;
									}
									if ( ! empty( $this->custom_services[ $rate_code ]['delivery_confirmation'] ) ) {
										$delivery_confirmation = true;
									}
								} elseif ( ! empty( $this->custom_services[ $rate_code ]['delivery_confirmation'] ) ) {
									$delivery_confirmation = true;
								}

								/**
								 * You must add $2.95 for Signature on Delivery
								 * if your item is valued above $300.
								 *
								 * Please note that this doesn't apply to Courier
								 * Post.
								 *
								 * Don't be confused why we're checking `$package_request['extra_cover']`,
								 * because it's actually product's price.
								 *
								 * @see https://auspost.com.au/parcels-mail/sending-in-australia/domestic-parcels/optional-extras-domestic
								 * @see https://github.com/woocommerce/woocommerce-shipping-australia-post/issues/84
								 */
								if ( ! $this->is_courier_post( $quote->code ) && ! empty( $this->custom_services[ $rate_code ]['extra_cover'] ) && $package_request['extra_cover'] >= 300 ) {
									$delivery_confirmation = true;
								}

								if ( is_null( $rate_cost ) ) {
									$rate_cost = $quote->price;
									$rate_set  = true;
									$name      = $quote->code;
								} elseif ( $quote->price < $rate_cost ) {
									$rate_cost = $quote->price;
									$rate_set  = true;
									$name      = $quote->code;
								}

								if ( $rate_set ) {
									// Reset extras cost to 0 since we do not want to duplicate costs for each service.
									$optional_extras_cost = 0;

									// User wants extra cover.
									if ( ! empty( $this->custom_services[ $rate_code ]['extra_cover'] ) && isset( $package_request['extra_cover'] ) && isset( $quote->max_extra_cover ) ) {
										$max_extra_cover       = $this->get_max_extra_cover_from_quote( $package_request['extra_cover'], $quote );
										$optional_extras_cost += $this->calculate_extra_cover_cost( $package_request['extra_cover'], $max_extra_cover );
									}

									// User wants SOD or an item is valued above $300.
									if ( $delivery_confirmation ) {
										if ( $this->is_international ) {
											$optional_extras_cost += $this->int_sod_cost;
										} else {
											$optional_extras_cost += $this->sod_cost;
										}
									}
								}
							}
						}

						$meta_data['package_description'] = $this->get_rate_package_description(
							array(
								'length'    => $meta_data['package_length'],
								'width'     => $meta_data['package_width'],
								'height'    => $meta_data['package_height'],
								'weight'    => $meta_data['package_weight'],
								'thickness' => $meta_data['package_thickness'],
								'qty'       => 'per_item' === $this->packing_method ? 1 : 0,
								'name'      => $name,
							)
						);

						if ( $rate_cost ) {
							$rate_cost += $optional_extras_cost;
							$this->prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $package_request, $package, $meta_data );
						}
					}
				}
			}
		}

		// Ensure rates were found for all packages.
		if ( $this->found_rates ) {
			foreach ( $this->found_rates as $key => $value ) {
				if ( $value['packages'] < count( $package_requests ) ) {
					unset( $this->found_rates[ $key ] );
				}
			}
		}

		// Add rates.
		if ( $this->found_rates ) {
			if ( 'all' === $this->offer_rates ) {

				uasort( $this->found_rates, array( $this, 'sort_rates' ) );

				foreach ( $this->found_rates as $key => $rate ) {
					$this->add_rate( $rate );
				}
			} else {

				$cheapest_rate = array(
					'cost' => PHP_INT_MAX,
				);

				foreach ( $this->found_rates as $key => $rate ) {
					if ( $cheapest_rate['cost'] > $rate['cost'] ) {
						$cheapest_rate = $rate;
					}
				}

				$cheapest_rate['label'] = $this->title; // will use generic rate label defined by user.
				$this->add_rate( $cheapest_rate );

			}
		}

		$this->maybe_display_notices();
	}

	/**
	 * Checks if destination is international.
	 *
	 * @since 2.3.12
	 * @version 2.3.12
	 * @param array $package Current Package.
	 * @return bool
	 */
	public function is_international( $package ) {
		if ( ! in_array( $package['destination']['country'], $this->au_territories, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Prepare_rate function.
	 *
	 * @param mixed  $rate_code Rate code.
	 * @param mixed  $rate_id Rate ID.
	 * @param mixed  $rate_name Rate name.
	 * @param mixed  $rate_cost Rate cost.
	 * @param string $package_request Package request.
	 * @param array  $package Shipping Package.
	 * @param array  $meta_data Rate meta data.
	 */
	private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost, $package_request = '', $package = array(), $meta_data = array() ) {
		// Name adjustment.
		if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) ) {
			$rate_name = $this->custom_services[ $rate_code ]['name'];
		}

		// Cost adjustment %.
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment_percent'] ) ) {
			$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $rate_code ]['adjustment_percent'] ) / 100 ) );
		}

		// Cost adjustment..
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment'] ) ) {
			$rate_cost = $rate_cost + floatval( $this->custom_services[ $rate_code ]['adjustment'] );
		}

		// Exclude Tax?
		if ( 'yes' === $this->excluding_tax && ! $this->is_international ) {
			/**
			 * Filter Tax Rate, Allow 3rd parties to modify tax rate.
			 *
			 * @param float $value Tax rate.
			 *
			 * @since 2.3.5
			 */
			$tax_rate  = apply_filters( 'woocommerce_shipping_australia_post_tax_rate', 0.10 );
			$rate_cost = $rate_cost / ( $tax_rate + 1 );
		}

		// Enabled check.
		if ( isset( $this->custom_services[ $rate_code ] ) && empty( $this->custom_services[ $rate_code ]['enabled'] ) ) {
			return;
		}

		// Merging.
		if ( isset( $this->found_rates[ $rate_id ] ) ) {
			$rate_cost = $rate_cost + $this->found_rates[ $rate_id ]['cost'];
			$packages  = 1 + $this->found_rates[ $rate_id ]['packages'];
		} else {
			$packages = 1;
		}

		// Sort.
		if ( isset( $this->custom_services[ $rate_code ]['order'] ) ) {
			$sort = $this->custom_services[ $rate_code ]['order'];
		} else {
			$sort = 999;
		}

		// Package metadata.
		$meta_data_value = array();
		if ( ! empty( $meta_data ) ) {
			// translators: %d is package ID.
			$meta_key = sprintf( __( 'Package %d', 'woocommerce-shipping-australia-post' ), $packages );

			if ( isset( $this->found_rates[ $rate_id ] ) && array_key_exists( 'meta_data', $this->found_rates[ $rate_id ] ) ) {
				$meta_data_value = $this->found_rates[ $rate_id ]['meta_data'];
			}

			$meta_data_value[ $meta_key ] = isset( $meta_data['package_description'] ) ? $meta_data['package_description'] : '';

			foreach ( array( 'length', 'width', 'height', 'weight' ) as $detail ) {
				// If no value, don't save anything.
				if ( empty( $meta_data[ 'package_' . $detail ] ) ) {
					continue;
				}

				// The new value to add to the JSON string.
				$new_value = $meta_data[ 'package_' . $detail ];

				// If this rate already has metadata, decode it and add the new value to the array.
				if ( ! empty( $meta_data_value[ '_package_' . $detail ] ) ) {
					$value                                    = json_decode( $meta_data_value[ '_package_' . $detail ], true );
					$value[ $meta_key ]                       = $new_value;
					$meta_data_value[ '_package_' . $detail ] = wp_json_encode( $value );
					continue;
				}

				$meta_data_value[ '_package_' . $detail ] = wp_json_encode( array( $meta_key => $new_value ) );
			}
		}

		// Weight based shipping doesn't have package information.
		if ( 'weight' === $this->packing_method ) {
			$meta_data_value = array( 'Packing' => 'Weight Based Shipping' );
		}

		/**
		 * Allow 3rd parties to process the rate before it's added to the list.
		 *
		 * @param array $rate Rate to add.
		 *
		 * @since 2.4.12
		 */
		$this->found_rates[ $rate_id ] = apply_filters(
			'woocommerce_shipping_australia_post_add_rate',
			array(
				'id'        => $rate_id,
				'label'     => $rate_name,
				'cost'      => $rate_cost,
				'sort'      => $sort,
				'packages'  => $packages,
				'meta_data' => $meta_data_value,
			)
		);
	}

	/**
	 * Perform remote request to AU Post API and returns the response if succeed.
	 *
	 * @since 1.0.0
	 * @version 2.4.4
	 *
	 * @param string $endpoint Endpoint URL where the request is made into.
	 * @param string $request  Request args.
	 * @param array  $headers  Request headers.
	 *
	 * @return mixed Response.
	 */
	private function get_response( $endpoint, $request, $headers ) {
		// If response exists in the cache, returns it.
		$response = get_transient( 'wc_australia_post_quotes_' . md5( $request ) );
		if ( $response ) {
			$this->debug( 'Using cached Australia Post REQUEST and RESPONSE.' );
			$this->debug_request_response( $request, $response );

			return $response;
		}

		$response = wp_remote_get(
			$endpoint . '?' . $request,
			array(
				'timeout' => 70,
				'headers' => $headers,
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->debug( sprintf( 'Australia Post request error (%1$s): %2$s', $response->get_error_code(), $response->get_error_message() ), 'error' );
			return false;
		}

		$response = json_decode( $response['body'] );
		if ( is_null( $response ) ) {
			$this->debug( 'Unable to decode JSON body from Australia Post response.', 'error' );
			return false;
		}

		// Cache the result in case the same request is made again.
		set_transient( 'wc_australia_post_quotes_' . md5( $request ), $response, WEEK_IN_SECONDS );

		$this->debug_request_response( $request, $response );

		return $response;
	}

	/**
	 * Debug request and response.
	 *
	 * @since 2.4.4
	 * @version 2.4.4
	 *
	 * @param string $request  HTTP request to the Australia Post API.
	 * @param array  $response HTTP response from the Australia Post API.
	 */
	private function debug_request_response( $request, $response ) {
		// phpcs:disable WordPress.PHP.DevelopmentFunctions.error_log_print_r -- This is a log function.
		parse_str( $request, $request_data );
		$this->debug( 'Australia Post REQUEST:', 'debug', $request_data );
		$this->debug( 'Australia Post RESPONSE:', 'debug', (array) $response );
		// phpcs:enable WordPress.PHP.DevelopmentFunctions.error_log_print_r
	}

	/**
	 * Sort_rates function.
	 *
	 * @param array $a Rate to sort.
	 * @param array $b Rate to sort.
	 *
	 * @return int
	 */
	public function sort_rates( $a, $b ) {
		if ( $a['sort'] === $b['sort'] ) {
			return 0;
		}

		return ( $a['sort'] < $b['sort'] ) ? -1 : 1;
	}

	/**
	 * Get header with auth key.
	 *
	 * @return array
	 */
	private function get_request_header() {
		return array(
			'AUTH-KEY' => $this->api_key,
		);
	}

	/**
	 * Get request array from package.
	 *
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function get_request( $package ) {
		$request = array();

		$request['from_postcode'] = $this->get_from_postcode( $package );

		$request['country_code'] = $package['destination']['country'];

		$to_postcode = $this->get_to_postcode( $package );
		if ( ! empty( $to_postcode ) ) {
			$request['to_postcode'] = $to_postcode;
		}

		return $request;
	}

	/**
	 * Trim space and return the uppercase post code.
	 *
	 * @param array $package Current package.
	 * @since 2.4.15
	 * @return string
	 */
	private function get_from_postcode( $package ) {
		return str_replace( ' ', '', strtoupper( $this->origin ) );
	}

	/**
	 * Return the destination to_postcode. Strip space if it is in australia territories.
	 *
	 * @param array $package Current package.
	 * @since 2.4.15
	 * @return string
	 */
	private function get_to_postcode( $package ) {
		if ( in_array( $package['destination']['country'], $this->au_territories, true ) ) {
			return str_replace( ' ', '', strtoupper( $package['destination']['postcode'] ) );
		}
		return $package['destination']['postcode'];
	}

	/**
	 * Get_request function.
	 *
	 * @param array $package Current package.
	 * @return array
	 */
	private function get_package_requests( $package ) {

		// Choose selected packing.
		switch ( $this->packing_method ) {
			case 'weight':
				$requests = $this->weight_only_shipping( $package );
				break;
			case 'box_packing':
				$requests = $this->box_shipping( $package );
				break;
			case 'per_item':
			default:
				$requests = $this->per_item_shipping( $package );
				break;
		}

		return $requests;
	}

	/**
	 * Weight_only_shipping function.
	 *
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function weight_only_shipping( $package ) {

		$packer = ( new WC_Boxpack( 'cm', 'kg', $this->box_packer_library, array( 'prefer_packets' => true ) ) )->get_packer();

		// Get weight of order.
		foreach ( $package['contents'] as $item_id => $values ) {

			/**
			 * Product object.
			 *
			 * @var WC_Product $values['data']
			 */

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return null;
			}

			$weight = wc_get_weight( $values['data']->get_weight(), 'kg' );
			$price  = $values['data']->get_price();
			$packer->add_item( 0, 0, 0, $weight, $price, array(), $values['quantity'] );

		}

		$box = $packer->add_box( 1, 1, 1, 0 );
		$box->set_id( 'weight-based-box' );
		$box->set_max_weight( $this->max_weight );
		$packer->pack();
		$packages = $packer->get_packages();

		if ( count( $packages ) > 1 ) {
			$this->debug( __( 'Package is too heavy. Splitting.', 'woocommerce-shipping-australia-post' ), 'error' );
			$this->debug( 'Splitting into ' . count( $packages ) . ' packages.' );
		}

		$requests = array();
		foreach ( $packages as $p ) {
			$parcel                = array();
			$parcel['weight']      = str_replace( ',', '.', round( $p->weight, 2 ) );
			$parcel['extra_cover'] = ceil( $p->value );

			// Domestic parcels require dimensions.
			$dimension        = 1;
			$parcel['height'] = $dimension;
			$parcel['width']  = $dimension;
			$parcel['length'] = $dimension;

			if ( $parcel['weight'] > 22 || $parcel['length'] > 105 ) {
				// translators: %d is product ID.
				$this->debug( sprintf( __( 'Product %d has invalid weight/dimensions. Aborting. See <a href="https://auspost.com.au/business/shipping/check-sending-guidelines/size-weight-guidelines">the Australia Post package guidelines</a>.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return;
			}

			$requests[] = $parcel;
		}

		return $requests;
	}

	/**
	 * Generate shipping request based on individual product dimensions.
	 *
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function per_item_shipping( $package ) {
		$requests = array();

		// Get weight of order.
		foreach ( $package['contents'] as $item_id => $values ) {

			/**
			 * Product object.
			 *
			 * @var WC_Product $values['data']
			 */

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ) );
				continue;
			}

			if ( ! $values['data']->get_weight() || ! $values['data']->get_length() || ! $values['data']->get_height() || ! $values['data']->get_width() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is missing weight/dimensions. Aborting.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return $requests;
			}

			$parcel = array();

			$parcel['weight'] = wc_get_weight( $values['data']->get_weight(), 'kg' );

			$dimensions = array(
				wc_get_dimension( $values['data']->get_length(), 'cm' ),
				wc_get_dimension( $values['data']->get_height(), 'cm' ),
				wc_get_dimension( $values['data']->get_width(), 'cm' ),
			);

			sort( $dimensions );

			// Min sizes - girth minimum is 16.
			$girth = $dimensions[0] + $dimensions[0] + $dimensions[1] + $dimensions[1];

			if ( $girth < 16 ) {
				if ( $dimensions[0] < 4 ) {
					$dimensions[0] = 4;
				}
				if ( $dimensions[1] < 5 ) {
					$dimensions[1] = 5;
				}
			}

			if ( $parcel['weight'] > 22 || $dimensions[2] > 105 ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product %d has invalid weight/dimensions. Aborting. See <a href="https://auspost.com.au/business/shipping/check-sending-guidelines/size-weight-guidelines">the Australia Post package guidelines</a>.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return $requests;
			}

			$parcel['height'] = $dimensions[0];
			$parcel['width']  = $dimensions[1];
			$parcel['length'] = $dimensions[2];

			$parcel['extra_cover'] = ceil( $values['data']->get_price() );

			for ( $i = 0; $i < $values['quantity']; $i++ ) {
				$requests[] = $parcel;
			}
		}

		return $requests;
	}

	/**
	 * Generate shipping requests by packing items in boxes.
	 *
	 * @since 2.4.5 Reset $request variable on each loop.
	 * @param array $package Current package.
	 *
	 * @return array
	 */
	private function box_shipping( $package ) {
		$requests = array();

		$boxpack = ( new WC_Boxpack( 'cm', 'kg', $this->box_packer_library, array( 'prefer_packets' => true ) ) )->get_packer();

		// Needed to ensure box packer works correctly.
		$boxes = $this->get_all_boxes();

		// Define boxes.
		if ( $boxes ) {
			foreach ( $boxes as $key => $box ) {

				if ( isset( $box['enabled'] ) && ! $box['enabled'] ) {
					continue;
				}
				$newbox = $boxpack->add_box( $box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight'] );
				$newbox->set_id( $box['name'] );

				if ( ! empty( $box['inner_length'] ) && ! empty( $box['inner_width'] ) && ! empty( $box['inner_height'] ) ) {
					$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );
				}

				if ( $box['max_weight'] ) {
					$newbox->set_max_weight( $box['max_weight'] );
				}

				if ( ! empty( $box['type'] ) ) {
					$newbox->set_type( $box['type'] );
				}
			}
		}

		// Add items.
		foreach ( $package['contents'] as $item_id => $values ) {

			/**
			 * Product object.
			 *
			 * @var WC_Product $values['data']
			 */

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ) );
				continue;
			}

			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() && $values['data']->get_weight() ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

				$boxpack->add_item(
					wc_get_dimension( $dimensions[2], 'cm' ),
					wc_get_dimension( $dimensions[1], 'cm' ),
					wc_get_dimension( $dimensions[0], 'cm' ),
					wc_get_weight( $values['data']->get_weight(), 'kg' ),
					$values['data']->get_price(),
					array(),
					$values['quantity']
				);
			} else {
				// translators: %d product id.
				$this->debug( sprintf( __( 'Product #%d is missing weight/dimensions. Aborting.', 'woocommerce-shipping-australia-post' ), $values['data']->get_id() ), 'error' );

				return $requests;
			}
		}

		// Pack it.
		$boxpack->pack();

		// Get packages.
		$packages = $boxpack->get_packages();

		foreach ( $packages as $package ) {
			$this->debug(
				( $package->unpacked ? 'Unpacked Item ' : 'Packed ' ) . $package->id . ' - ' . $package->length . 'x' . $package->width . 'x' . $package->height
			);

			$request = array();

			$dimensions = array( $package->length, $package->width, $package->height );

			sort( $dimensions );

			if ( 'envelope' !== $package->type ) {
				$request['height'] = $dimensions[0];
				$request['weight'] = $package->weight;
				$request['width']  = $dimensions[1];
				$request['length'] = $dimensions[2];

				/**
				 * Satchels are a special case.
				 *
				 * We can't send the exact package sizes for satchels, so we need to adjust them.
				 * We need to set the height to 0, and reduce the width and length by 1, otherwise
				 * the API will return rates for the next size up.
				 */
				if ( $this->matches_enabled_satchel_size( floatval( $request['length'] ), floatval( $request['width'] ), floatval( $request['height'] ), floatval( $request['weight'] ) ) ) {
					// Pass the exact dimensions to save it in the order meta.
					$request['exact_dimensions'] = array(
						'length' => $request['length'],
						'width'  => $request['width'],
						'height' => $request['height'],
					);

					$request['length'] += - 1;
					$request['width']  += - 1;
					$request['height']  = 0;

					$this->debug( sprintf( 'Adjusted %s REQUEST dimensions to %scm X %scm X %scm to ensure we get the correct rates.', $package->id, $request['length'], $request['width'], $request['height'] ) );
				}
			} else {
				$request['thickness'] = wc_get_dimension( $dimensions[0], 'mm', 'cm' );
				$request['width']     = wc_get_dimension( $dimensions[1], 'mm', 'cm' );
				$request['length']    = wc_get_dimension( $dimensions[2], 'mm', 'cm' );
				$request['weight']    = wc_get_weight( $package->weight, 'g', 'kg' );
			}

			$request['extra_cover'] = ceil( $package->value );

			$requests[] = $request;
		}

		return $requests;
	}

	/**
	 * Check if the package matches any enabled satchel sizes.
	 *
	 * @param float $length Shipment package length.
	 * @param float $width Shipment package width.
	 * @param float $height Shipment package height.
	 * @param float $weight Shipment  package weight.
	 *
	 * @return bool
	 */
	private function matches_enabled_satchel_size( $length, $width, $height, $weight ) {
		$boxes = $this->get_all_boxes();

		// If we don't have any enabled boxes, return false.
		if ( empty( $boxes ) || ! is_array( $boxes ) ) {
			return false;
		}

		// Loop through the enabled boxes.
		foreach ( $boxes as $box ) {
			// Skip non-satchels.
			if ( empty( $box['id'] ) || ! $this->is_satchel( $box['id'] ) ) {
				continue;
			}

			// Ensure we have float values.
			$satchel_length = floatval( $box['outer_length'] );
			$satchel_width  = floatval( $box['outer_width'] );
			$satchel_height = floatval( $box['outer_height'] );
			$satchel_weight = floatval( $box['max_weight'] );

			// If the dimensions and weight match, return true.
			if ( $satchel_length === $length && $satchel_width === $width && $satchel_height === $height && $weight <= $satchel_weight ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get metadata package description string for the shipping rate.
	 *
	 * @param array $params Meta data info to join.
	 *
	 * @return string Rate meta data.
	 */
	private function get_rate_package_description( array $params ) {
		$meta_data = array();

		if ( ! empty( $params['name'] ) ) {
			$meta_data[] = $params['name'] . ' -';
		}

		$params['height'] = $params['height'] ? $params['height'] : $params['thickness'];

		if ( $params['length'] && $params['width'] && $params['height'] ) {
			$meta_data[] = sprintf( '%1$s × %2$s × %3$s (cm)', $params['length'], $params['width'], $params['height'] );
		}
		if ( $params['weight'] ) {
			$meta_data[] = round( $params['weight'], 2 ) . 'kg';
		}
		if ( $params['qty'] ) {
			$meta_data[] = '× ' . $params['qty'];
		}

		return implode( ' ', $meta_data );
	}

	/**
	 * Check if the package is a large envelope.
	 *
	 * @param array $package Package to check.
	 *
	 * @return bool
	 */
	public function is_large_envelope( array $package ) {
		foreach ( array( 'length', 'width', 'thickness' ) as $dimension ) {

			if ( empty( $package[ $dimension ] ) || $package[ $dimension ] <= $this->letter_sizes['INT_LETTER_AIR_OWN_PACKAGING_MEDIUM'][ $dimension ] ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if the current page is the classic checkout page.
	 * This is the case when the checkout block is not present.
	 *
	 * @return bool
	 */
	public function is_classic_checkout_page(): bool {
		return is_checkout() && ! has_block( 'woocommerce/checkout' );
	}

	/**
	 * Check if the current page is the classic cart page.
	 * This is the case when the cart block is not present.
	 *
	 * @return bool
	 */
	public function is_classic_cart_page(): bool {
		return is_cart() && ! has_block( 'woocommerce/cart' );
	}

	/**
	 * If the conditions are met, display the notices.
	 *
	 * @return void
	 */
	public function maybe_display_notices() {
		if ( ! $this->is_classic_checkout_page() && ! $this->is_classic_cart_page() ) {
			return;
		}

		$this->notifier->print_notices();

		$this->notifier->clear_notices();
	}

	/**
	 * Maybe clear the shipping cache.
	 */
	public function maybe_clear_shipping_cache() {
		if ( ! is_cart() && ! is_checkout() ) {
			return;
		}

		// Only clear the cache if debug mode is enabled for at least one Australia Post method.
		if ( ! $this->is_debug_mode_enabled() ) {
			return;
		}

		$this->clear_shipping_cache();
	}

	/**
	 * Clear the shipping cache.
	 */
	public function clear_shipping_cache() {
		WC_Cache_Helper::get_transient_version( 'shipping', true );
	}

	/**
	 * If the box packer library option is not yet set and there are existing
	 * Australia Post shipping method instances, we can assume that this is not a
	 * new/fresh installation of the Australia Post plugin,
	 * so we should default to 'original'
	 *
	 * If the box packer library option is not set and there are no
	 * Australia Post shipping method instances, then this is likely a new
	 * installation of the Australia Post plugin,
	 * so we should default to 'dvdoug'
	 *
	 * @return string
	 */
	public function get_default_box_packer_library(): string {
		if ( ( empty( $this->get_option( 'box_packer_library' ) ) && $this->instances_exist() ) ) {
			return 'original';
		} else {
			return 'dvdoug';
		}
	}

	/**
	 * Helper method to get the number of Australia Post method instances.
	 *
	 * @return int The number of Australia Post method instances
	 */
	public function instance_count(): int {
		global $wpdb;

		// phpcs:ignore --- Need to use WPDB::get_var() to count the existing Australia Post in the shipping zone
		return absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'australia_post'" ) );
	}

	/**
	 * Helper method to check if there are existing Australia Post method instances.
	 *
	 * @return bool
	 */
	public function instances_exist(): bool {
		return $this->instance_count() > 0;
	}
}
