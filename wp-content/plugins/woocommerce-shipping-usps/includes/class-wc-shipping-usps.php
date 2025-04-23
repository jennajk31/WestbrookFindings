<?php
/**
 * WC_Shipping_USPS class.
 *
 * @package WC_Shipping_USPS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WC_USPS_ABSPATH . 'includes/class-logger.php';
require_once WC_USPS_ABSPATH . 'includes/trait-util.php';
require_once WC_USPS_API_DIR . 'rest/class-oauth.php';
require_once WC_USPS_API_DIR . 'rest/class-rest-api.php';
require_once WC_USPS_API_DIR . 'legacy/class-legacy-api.php';

use WooCommerce\USPS\Logger;
use WooCommerce\USPS\Util;
use WooCommerce\USPS\API\Abstract_API;
use WooCommerce\USPS\API\Legacy_API;
use WooCommerce\USPS\API\REST_API;

/**
 * Shipping method main class.
 *
 * @version 4.4.0
 * @extends WC_Shipping_Method
 */
class WC_Shipping_USPS extends WC_Shipping_Method {

	use Util;

	/**
	 * API type.
	 *
	 * @var string
	 */
	public $api_type;

	/**
	 * API class.
	 *
	 * @var Abstract_API
	 */
	public $api;

	/**
	 * Countries considered as domestic.
	 *
	 * @var array
	 */
	public $domestic = array( 'US', 'PR', 'VI', 'MH', 'FM', 'GU', 'MP', 'AS', 'UM' );

	/**
	 * Found rates.
	 *
	 * @var array
	 */
	public $found_rates;

	/**
	 * Raw Found rates.
	 * Allows us to filter all found_rates before finally being returned.
	 *
	 * @var array
	 */
	public $raw_found_rates;

	/**
	 * Flat rate boxes.
	 *
	 * @var array
	 */
	public $flat_rate_boxes;

	/**
	 * Whether Flat Rate Box Weights are enabled or not.
	 *
	 * @var bool
	 */
	public $enable_flat_rate_box_weights;

	/**
	 * Flat rate empty box weights.
	 *
	 * @var array
	 */
	public $flat_rate_box_weights;

	/**
	 * Services.
	 *
	 * @var array
	 */
	public $services;

	/**
	 * Origin postcode.
	 *
	 * @var string
	 */
	public $origin;

	/**
	 * Whether debug is enabled or not.
	 *
	 * @var bool
	 */
	public $debug;

	/**
	 * Whether flat rate boxes is enabled or not.
	 *
	 * Valid values are "yes" and "no".
	 *
	 * @var string
	 */
	public $enable_flat_rate_boxes;

	/**
	 * Shipping classes whose restricted to Media Mail.
	 *
	 * @var array
	 */
	public $mediamail_restriction;

	/**
	 * USPS user ID.
	 *
	 * @var string
	 */
	public $user_id;

	/**
	 * Packing method.
	 *
	 * Possible values are "per_item", "box_packing", and "weight_based".
	 *
	 * @var string
	 */
	public $packing_method;

	/**
	 * User defined boxes.
	 *
	 * @var array
	 */
	public $boxes;

	/**
	 * Defined services from setting.
	 *
	 * @var array
	 */
	public $custom_services;

	/**
	 * Rates to offer.
	 *
	 * Valid values are "all" and "cheapest".
	 *
	 * @var string
	 */
	public $offer_rates;

	/**
	 * Shipping rate type ONLINE|ALL.
	 *
	 * @var string
	 */
	public $shippingrates;

	/**
	 * Fallback rate amount if no matching rates from API.
	 *
	 * @var string
	 */
	public $fallback;

	/**
	 * Default product dimensions to use for products without set dimensions.
	 *
	 * @var array
	 */
	public $product_dimensions;

	/**
	 * Default product weight to use for products without a set weight.
	 *
	 * @var float
	 */
	public $product_weight;

	/**
	 * Flat rate fee.
	 *
	 * @var string
	 */
	public $flat_rate_fee;

	/**
	 * Method to handle unpacked item.
	 *
	 * Possible values are "", "ignore", "fallback", and "abort".
	 *
	 * @var string
	 */
	public $unpacked_item_handling;

	/**
	 * Whether standard service (rates API) is enabled or not.
	 *
	 * @var bool
	 */
	public $enable_standard_services;

	/**
	 * Whether sort the rate by price.
	 *
	 * @var bool
	 */
	public $sort_by_price;

	/**
	 * Total cost of unpacked items.
	 *
	 * @var float
	 */
	public $unpacked_item_costs;

	/**
	 * Transient's name for USPS API request.
	 *
	 * Saved the request params in property because transient need to created
	 * in another method.
	 *
	 * @since   4.4.9
	 * @version 4.4.9
	 *
	 * @see     https://github.com/woocommerce/woocommerce-shipping-usps/issues/145
	 *
	 * @var string
	 */
	public $request_transient;

	/**
	 * Logger instance.
	 *
	 * @var Logger
	 */
	public Logger $logger;

	/**
	 * Sets the box packer library to use.
	 *
	 * @var string
	 */
	public $box_packer_library;

	/**
	 * Constructor.
	 *
	 * @param int $instance_id Instance ID.
	 */
	public function __construct( $instance_id = 0 ) {
		$this->id                 = 'usps';
		$this->instance_id        = absint( $instance_id );
		$this->method_title       = __( 'USPS', 'woocommerce-shipping-usps' );
		$this->method_description = __( 'The USPS extension obtains rates dynamically from the USPS API during cart/checkout.', 'woocommerce-shipping-usps' );
		$this->services           = include WC_USPS_ABSPATH . 'includes/data/data-services.php';
		$this->flat_rate_boxes    = include WC_USPS_ABSPATH . 'includes/data/data-flat-rate-boxes.php';
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'settings',
		);
		$this->init();
	}

	/**
	 * Chceks whether this shipping instance is available or not.
	 *
	 * @param array $package Package to ship.
	 *
	 * @return bool True if available.
	 */
	public function is_available( $package ) {
		if ( empty( $package['destination']['country'] ) ) {
			return false;
		}

		/**
		 * Filter to modify the availability of the shipping method.
		 *
		 * @var boolean Whether the shipping method is available or not.
		 * @var array   Current package data.
		 *
		 * @since 4.4.1
		 */
		return apply_filters( 'woocommerce_shipping_usps_is_available', true, $package );
	}

	/**
	 * Initialize settings.
	 *
	 * @version 4.4.0
	 * @since   4.4.0
	 * @return bool
	 */
	private function set_settings() {
		// Define user set variables.
		$this->title                        = $this->get_option( 'title', $this->method_title );
		$this->api_type                     = $this->get_option( 'api_type', $this->get_default_api_type() );
		$this->api                          = 'rest' === $this->api_type ? new REST_API( $this ) : new Legacy_API( $this );
		$this->origin                       = $this->get_option( 'origin', '' );
		$this->user_id                      = $this->get_option( 'user_id', '' );
		$this->packing_method               = $this->get_option( 'packing_method', 'per_item' );
		$this->custom_services              = $this->get_option( 'services', array() );
		$this->boxes                        = $this->get_option( 'boxes', array() );
		$this->offer_rates                  = $this->get_option( 'offer_rates', 'all' );
		$this->fallback                     = $this->get_option( 'fallback', '' );
		$this->product_dimensions           = $this->get_option( 'product_dimensions', array( '', '', '' ) );
		$this->product_weight               = $this->get_option( 'product_weight', '' );
		$this->flat_rate_box_weights        = $this->get_option( 'flat_rate_box_weights', array() );
		$this->enable_flat_rate_box_weights = 'yes' === $this->get_option( 'enable_flat_rate_box_weights' );
		$this->flat_rate_fee                = $this->get_option( 'flat_rate_fee', '' );
		$this->mediamail_restriction        = array_filter( (array) $this->get_option( 'mediamail_restriction', array() ) );
		$this->unpacked_item_handling       = $this->get_option( 'unpacked_item_handling', '' );
		$this->enable_standard_services     = 'yes' === $this->get_option( 'enable_standard_services', 'no' );
		$this->sort_by_price                = 'yes' === $this->get_option( 'sort_by_price', 'no' );
		$this->enable_flat_rate_boxes       = $this->get_option( 'enable_flat_rate_boxes', 'yes' );
		$this->debug                        = 'yes' === $this->get_option( 'debug_mode' );
		$this->shippingrates                = $this->get_option( 'shippingrates', 'ALL' );

		// Create the logger.
		$this->logger = new Logger( $this->debug );

		/**
		 * Filter to modify the flat rate box list.
		 *
		 * @var array List of flat rate box.
		 *
		 * @since 3.6.3
		 */
		$this->flat_rate_boxes    = apply_filters( 'usps_flat_rate_boxes', $this->flat_rate_boxes );
		$this->tax_status         = $this->get_option( 'tax_status' );
		$this->box_packer_library = $this->get_option( 'box_packer_library', $this->get_default_box_packer_library() );

		return true;
	}

	/**
	 * Output a debug message.
	 *
	 * @param string $message Debug message.
	 * @param array  $data    Optional. Additional data to pass.
	 */
	public function debug( string $message, array $data = array() ) {
		$this->logger->debug( $message, $data );
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
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this->api, 'validate_credentials' ), - 10 );

		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'add_hidden_order_itemmeta_keys' ) );
	}

	/**
	 * If the box packer library option is not yet set and there are existing
	 * USPS shipping method instances, we can assume that this is not a
	 * new/fresh installation of the USPS plugin,
	 * so we should default to 'original'
	 *
	 * If the box packer library option is not set and there are no
	 * USPS shipping method instances, then this is likely a new
	 * installation of the USPS plugin,
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
	 * Add meta keys to the list of keys
	 * to hide in the order item meta
	 *
	 * @param array $keys Item meta keys.
	 *
	 * @return array|mixed
	 */
	public function add_hidden_order_itemmeta_keys( $keys ) {
		$keys[] = '_package_length';
		$keys[] = '_package_width';
		$keys[] = '_package_height';
		$keys[] = '_package_weight';

		return $keys;
	}

	/**
	 * Process settings on save.
	 *
	 * @return void
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		$this->set_settings();
	}

	/**
	 * HTML for services option.
	 *
	 * @return string HTML for services option.
	 */
	public function generate_services_html(): string {
		$sort             = 0;
		$ordered_services = array();

		foreach ( $this->services as $code => $values ) {

			if ( isset( $this->custom_services[ $code ]['order'] ) ) {
				$sort = (int) $this->custom_services[ $code ]['order'];
			}

			while ( isset( $ordered_services[ $sort ] ) ) {
				++$sort;
			}

			$ordered_services[ $sort ] = array( $code, $values );

			++$sort;
		}

		ob_start();
		include WC_USPS_ABSPATH . 'includes/views/html-services.php';

		return ob_get_clean();
	}

	/**
	 * HTML for box packing option.
	 *
	 * @return string HTML for box packing option.
	 */
	public function generate_box_packing_html() {
		ob_start();
		include WC_USPS_ABSPATH . 'includes/views/html-box-packing.php';

		return ob_get_clean();
	}

	/**
	 * HTML for flat_rate_box_weights option.
	 *
	 * @return string HTML for flat_rate_box_weights option.
	 */
	public function generate_flat_rate_box_weights_html() {
		ob_start();
		include WC_USPS_ABSPATH . 'includes/views/html-flat-rate-box-weights.php';

		return ob_get_clean();
	}

	/**
	 * HTML for the product dimensions option.
	 *
	 * @return string HTML for the product dimensions option.
	 */
	public function generate_product_dimensions_html(): string {
		ob_start();
		include WC_USPS_ABSPATH . 'includes/views/html-product-dimensions.php';

		return ob_get_clean();
	}

	/**
	 * Validate product_dimensions field.
	 *
	 * @return array
	 */
	public function validate_product_dimensions_field(): array {
		$dimensions = array();
		//phpcs:disable WordPress.Security.NonceVerification.Missing --- nonce is taken care of by the caller.
		$dimensions[] = ! empty( $_POST['default_product_length'] ) ? floatval( $_POST['default_product_length'] ) : '';
		$dimensions[] = ! empty( $_POST['default_product_width'] ) ? floatval( $_POST['default_product_width'] ) : '';
		$dimensions[] = ! empty( $_POST['default_product_height'] ) ? floatval( $_POST['default_product_height'] ) : '';

		//phpcs:enable WordPress.Security.NonceVerification.Missing
		return $dimensions;
	}

	/**
	 * Validate flat_rate_box_weights field.
	 *
	 * @return array
	 */
	public function validate_flat_rate_box_weights_field(): array {
		$weights = array();
		//phpcs:disable WordPress.Security.NonceVerification.Missing --- nonce is taken care of by the caller.
		$submitted_weights = isset( $_POST['flat_rate_box_weights'] ) ? wp_unslash( $_POST['flat_rate_box_weights'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized --- the $_POST global is sanitized below
		//phpcs:enable WordPress.Security.NonceVerification.Missing

		if ( $this->flat_rate_boxes ) {
			foreach ( $this->flat_rate_boxes as $key => $box ) {
				$weights[ $key ] = ! empty( $submitted_weights[ $key ] ) ? floatval( $submitted_weights[ $key ] ) : '';
			}
		}

		return $weights;
	}

	/**
	 * Validate flat_rate_fee field.
	 *
	 * @since   4.4.6
	 * @version 4.4.6
	 *
	 * @param string $key   Key.
	 * @param string $value Value.
	 *
	 * @return string
	 */
	public function validate_flat_rate_fee_field( $key, $value ) {
		$value  = is_null( $value ) ? '' : $value;
		$suffix = substr( $value, - 1, 1 ) === '%' ? '%' : '';

		return ( '' === $value ) ? '' : wc_format_decimal( trim( stripslashes( $value ) ) ) . $suffix;
	}

	/**
	 * Validate box packing field.
	 *
	 * @param string $key Field's key.
	 *
	 * @return array Validated value.
	 */
	public function validate_box_packing_field( $key ) {
		$boxes = array();
		//phpcs:disable WordPress.Security.NonceVerification.Missing --- nonce is taken care of by the caller.
		if ( ! empty( $_POST['boxes_outer_length'] ) && is_array( $_POST['boxes_outer_length'] ) ) {
			// The global is looped through and type cast in the loop below.
			$boxes_name         = isset( $_POST['boxes_name'] ) ? wp_unslash( $_POST['boxes_name'] ) : array();                 // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_outer_length = isset( $_POST['boxes_outer_length'] ) ? wp_unslash( $_POST['boxes_outer_length'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_outer_width  = isset( $_POST['boxes_outer_width'] ) ? wp_unslash( $_POST['boxes_outer_width'] ) : array();   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_outer_height = isset( $_POST['boxes_outer_height'] ) ? wp_unslash( $_POST['boxes_outer_height'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_inner_length = isset( $_POST['boxes_inner_length'] ) ? wp_unslash( $_POST['boxes_inner_length'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_inner_width  = isset( $_POST['boxes_inner_width'] ) ? wp_unslash( $_POST['boxes_inner_width'] ) : array();   // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_inner_height = isset( $_POST['boxes_inner_height'] ) ? wp_unslash( $_POST['boxes_inner_height'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_box_weight   = isset( $_POST['boxes_box_weight'] ) ? wp_unslash( $_POST['boxes_box_weight'] ) : array();     // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_max_weight   = isset( $_POST['boxes_max_weight'] ) ? wp_unslash( $_POST['boxes_max_weight'] ) : array();     // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$boxes_is_letter    = isset( $_POST['boxes_is_letter'] ) ? wp_unslash( $_POST['boxes_is_letter'] ) : array();       // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			//phpcs:enable WordPress.Security.NonceVerification.Missing

			$num_of_boxes = count( $boxes_outer_length );
			for ( $i = 0; $i < $num_of_boxes; $i++ ) {

				if ( $boxes_outer_length[ $i ] && $boxes_outer_width[ $i ] && $boxes_outer_height[ $i ] && $boxes_inner_length[ $i ] && $boxes_inner_width[ $i ] && $boxes_inner_height[ $i ] ) {

					$boxes[] = array(
						'name'         => wc_clean( $boxes_name[ $i ] ),
						'outer_length' => floatval( $boxes_outer_length[ $i ] ),
						'outer_width'  => floatval( $boxes_outer_width[ $i ] ),
						'outer_height' => floatval( $boxes_outer_height[ $i ] ),
						'inner_length' => floatval( $boxes_inner_length[ $i ] ),
						'inner_width'  => floatval( $boxes_inner_width[ $i ] ),
						'inner_height' => floatval( $boxes_inner_height[ $i ] ),
						'box_weight'   => floatval( $boxes_box_weight[ $i ] ),
						'max_weight'   => floatval( $boxes_max_weight[ $i ] ),
						'is_letter'    => isset( $boxes_is_letter[ $i ] ),
					);

				}
			}
		}

		return $boxes;
	}

	/**
	 * Validate services field.
	 *
	 * @param string $key Field's key.
	 *
	 * @return array Validated value.
	 */
	public function validate_services_field( $key ) {
		$services = array();
		//phpcs:disable WordPress.Security.NonceVerification.Missing --- nonce is taken care of by the caller.
		$posted_services = isset( $_POST['usps_service'] ) ? wp_unslash( $_POST['usps_service'] ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, input is sanitized in the loop below.
		//phpcs:enable WordPress.Security.NonceVerification.Missing
		foreach ( $posted_services as $code => $settings ) {

			$services[ $code ] = array(
				'name'  => wc_clean( $settings['name'] ),
				'order' => wc_clean( $settings['order'] ),
			);

			foreach ( $this->services[ $code ]['services'] as $key => $name ) {
				// Process sub sub services.
				if ( 0 === $key ) {
					foreach ( $name as $subsub_service_key => $subsub_service ) {
						$services[ $code ][ $key ][ $subsub_service_key ]['enabled']            = isset( $settings[ $key ][ $subsub_service_key ]['enabled'] );
						$services[ $code ][ $key ][ $subsub_service_key ]['adjustment']         = wc_clean( $settings[ $key ][ $subsub_service_key ]['adjustment'] );
						$services[ $code ][ $key ][ $subsub_service_key ]['adjustment_percent'] = wc_clean( $settings[ $key ][ $subsub_service_key ]['adjustment_percent'] );
					}
				} else {
					$services[ $code ][ $key ]['enabled']            = isset( $settings[ $key ]['enabled'] );
					$services[ $code ][ $key ]['adjustment']         = wc_clean( $settings[ $key ]['adjustment'] );
					$services[ $code ][ $key ]['adjustment_percent'] = wc_clean( $settings[ $key ]['adjustment_percent'] );
				}
			}
		}

		return $services;
	}

	/**
	 * Clear transients used by this shipping method.
	 */
	public function clear_transients() {
		global $wpdb;

		// phpcs:ignore --- Need to use WPDB::query to delete USPS transient
		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_usps_quote_%') OR `option_name` LIKE ('_transient_timeout_usps_quote_%')" );
	}

	/**
	 * Initialize form fields.
	 */
	public function init_form_fields() {
		$shipping_classes = array();

		$classes = get_terms(
			array(
				'taxonomy'   => 'product_shipping_class',
				'hide_empty' => '0',
			)
		);

		if ( is_wp_error( $classes ) || empty( $classes ) ) {
			$classes = array();
		}

		foreach ( $classes as $class ) {
			$shipping_classes[ $class->term_id ] = $class->name;
		}

		$this->instance_form_fields = array(
			'title'                    => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-usps' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-usps' ),
				'default'     => __( 'USPS', 'woocommerce-shipping-usps' ),
				'desc_tip'    => true,
			),
			'origin'                   => array(
				'title'             => __( 'Origin Postcode (required)', 'woocommerce-shipping-usps' ),
				'type'              => 'text',
				'description'       => __( 'Enter the postcode for the <strong>sender</strong>.', 'woocommerce-shipping-usps' ),
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array(
					'required' => true,
				),
			),
			'tax_status'               => array(
				'title'       => __( 'Tax Status', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'taxable',
				'options'     => array(
					'taxable' => __( 'Taxable', 'woocommerce-shipping-usps' ),
					'none'    => __( 'None', 'woocommerce-shipping-usps' ),
				),
			),
			'shippingrates'            => array(
				'title'       => __( 'Shipping Rates', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'default'     => 'ALL',
				'options'     => array(
					'ONLINE' => __( 'Use Commercial Rates', 'woocommerce-shipping-usps' ),
					'ALL'    => __( 'Use Retail Rates', 'woocommerce-shipping-usps' ),
				),
				'desc_tip'    => true,
				'description' => __( 'Choose which rates to show your customers: Standard retail or discounted commercial rates.', 'woocommerce-shipping-usps' ),
			),
			'offer_rates'              => array(
				'title'       => __( 'Offer Rates', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'all',
				'options'     => array(
					'all'      => __( 'Offer the customer all returned rates', 'woocommerce-shipping-usps' ),
					'cheapest' => __( 'Offer the customer the cheapest rate only', 'woocommerce-shipping-usps' ),
				),
			),
			'fallback'                 => array(
				'title'       => __( 'Fallback', 'woocommerce-shipping-usps' ),
				'type'        => 'price',
				'desc_tip'    => true,
				'description' => __( 'If USPS returns no matching rates, offer this amount for shipping so that the user can still checkout. Leave blank to disable.', 'woocommerce-shipping-usps' ),
				'default'     => '',
				'placeholder' => __( 'Disabled', 'woocommerce-shipping-usps' ),
			),
			'flat_rates'               => array(
				'title'       => __( 'Flat Rates', 'woocommerce-shipping-usps' ),
				'type'        => 'title',
				'description' => __( 'These are USPS flat rate boxes services.', 'woocommerce-shipping-usps' ),
			),
			'enable_flat_rate_boxes'   => array(
				'title'       => __( 'Flat Rate Boxes &amp; envelopes', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'default'     => 'yes',
				'options'     => array(
					'yes'      => __( 'Yes - Enable flat rate services', 'woocommerce-shipping-usps' ),
					'no'       => __( 'No - Disable flat rate services', 'woocommerce-shipping-usps' ),
					'priority' => __( 'Enable Priority flat rate services only', 'woocommerce-shipping-usps' ),
					'express'  => __( 'Enable Express flat rate services only', 'woocommerce-shipping-usps' ),
				),
				'description' => __( 'Enable this option to offer shipping using USPS Flat Rate services. Items will be packed into the boxes/envelopes and the customer will be offered a single rate from these.', 'woocommerce-shipping-usps' ),
				'desc_tip'    => true,
			),
			'flat_rate_express_title'  => array(
				'title'       => __( 'Express Flat Rate Title', 'woocommerce-shipping-usps' ),
				'type'        => 'text',
				'description' => '',
				'default'     => '',
				'placeholder' => 'Priority Mail Express Flat Rate&#0174;',
			),
			'flat_rate_priority_title' => array(
				'title'       => __( 'Priority Flat Rate Title', 'woocommerce-shipping-usps' ),
				'type'        => 'text',
				'description' => '',
				'default'     => '',
				'placeholder' => 'Priority Mail Flat Rate&#0174;',
			),
			'flat_rate_fee'            => array(
				'title'       => __( 'Additional Fee', 'woocommerce' ),
				'type'        => 'price',
				'description' => __( 'Fee per-box excluding tax. Enter an amount, e.g. 2.50, or a percentage, e.g. 5%. Leave blank to disable.', 'woocommerce' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'standard_rates'           => array(
				'title'       => __( 'API Rates', 'woocommerce-shipping-usps' ),
				'type'        => 'title',
				'description' => __( 'These are standard service rates pulled from the USPS API.', 'woocommerce-shipping-usps' ),
			),
			'enable_standard_services' => array(
				'title'       => __( 'Enable API Rates', 'woocommerce-shipping-usps' ),
				'label'       => __( 'Retrieve Standard Service rates from the USPS API', 'woocommerce-shipping-usps' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Enable non-flat rate services.', 'woocommerce-shipping-usps' ),
			),
			'sort_by_price'            => array(
				'title'       => __( 'Sort by Price', 'woocommerce-shipping-usps' ),
				'label'       => __( 'Sort the returned rates by price', 'woocommerce-shipping-usps' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'If this box is checked, the returned shipping rates will be sorted by price.', 'woocommerce-shipping-usps' ),
			),
			'packing_method'           => array(
				'title'       => __( 'Parcel Packing Method', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'default'     => '',
				'class'       => 'packing_method',
				'options'     => array(
					'per_item'     => __( 'Default: Pack items individually', 'woocommerce-shipping-usps' ),
					'box_packing'  => __( 'Recommended: Pack into boxes with weights and dimensions', 'woocommerce-shipping-usps' ),
					'weight_based' => __( 'Weight based: Regular sized items (< 12 inches) are grouped and quoted for weights only. Large items are quoted individually.', 'woocommerce-shipping-usps' ),
				),
				'description' => __( 'Not applicable to the flat rate service.', 'woocommerce-shipping-usps' ),
			),
			'boxes'                    => array(
				'type' => 'box_packing',
			),
			'unpacked_item_handling'   => array(
				'title'       => __( 'Unpacked item handling', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'all',
				'options'     => array(
					''         => __( 'Get a quote for the unpacked item by itself', 'woocommerce-shipping-usps' ),
					'ignore'   => __( 'Ignore the item - do not quote', 'woocommerce-shipping-usps' ),
					'fallback' => __( 'Use the fallback price (above)', 'woocommerce-shipping-usps' ),
					'abort'    => __( 'Abort - do not return any quotes for the standard services', 'woocommerce-shipping-usps' ),
				),
			),
			'services'                 => array(
				'type' => 'services',
			),
			'mediamail_restriction'    => array(
				'title'             => __( 'Restrict Media Mail to...', 'woocommerce-shipping-usps' ),
				'type'              => 'multiselect',
				'class'             => 'chosen_select',
				'css'               => 'width: 450px;',
				'default'           => '',
				'options'           => $shipping_classes,
				'custom_attributes' => array(
					'data-placeholder' => __( 'No restrictions', 'woocommerce-shipping-usps' ),
				),
			),
		);

		$this->form_fields = array(
			'api_type'                     => array(
				'title'       => __( 'API Type', 'woocommerce-shipping-usps' ),
				'description' => __( 'Select whether to use the legacy SOAP API or the new REST API.', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'default'     => $this->get_default_api_type(),
				'class'       => 'chosen_select',
				'options'     => array(
					'rest' => __( 'REST', 'woocommerce-shipping-usps' ),
					'soap' => __( 'SOAP (legacy)', 'woocommerce-shipping-usps' ),
				),
			),
			'user_id'                      => array(
				'title'             => __( 'USPS User ID', 'woocommerce-shipping-usps' ),
				'type'              => 'text',
				'description'       => sprintf(
				// translators: %1$1s: is an anchor link to USPS api link; %2$2s: is an anchor link to plugin documentation.
					__( 'You can obtain a USPS user ID by %1$1s. This is not the same username used to sign in to USPS.com, %2$2s.', 'woocommerce-shipping-usps' ),
					'<a href="https://www.usps.com/business/web-tools-apis/welcome.htm" target="_blank">' . __( 'signing up on the USPS website', 'woocommerce-shipping-usps' ) . '</a>',
					'<a href="https://woocommerce.com/document/usps-shipping-method/#your-usps-user-id" target="_blank">' . __( 'check documentation for instructions', 'woocommerce-shipping-usps' ) . '</a>'
				),
				'default'           => '',
				'custom_attributes' => array( 'data-usps_api_type' => 'soap' ),
			),
			'client_id'                    => array(
				'title'             => __( 'REST API Key', 'woocommerce-shipping-usps' ),
				'type'              => 'text',
				'description'       => __( 'Obtained from USPS after creating a Project in the USPS Developer Portal.', 'woocommerce-shipping-usps' ),
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array( 'data-usps_api_type' => 'rest' ),
			),
			'client_secret'                => array(
				'title'             => __( 'REST API Secret', 'woocommerce-shipping-usps' ),
				'type'              => 'password',
				'description'       => __( 'Obtained from USPS after creating a Project in the USPS Developer Portal.', 'woocommerce-shipping-usps' ),
				'default'           => '',
				'desc_tip'          => true,
				'custom_attributes' => array( 'data-usps_api_type' => 'rest' ),
			),
			'product_dimensions'           => array(
				'type' => 'product_dimensions',
			),
			'product_weight'               => array(
				// translators: $s is a woocommerce weight unit value.
				'title'       => sprintf( __( 'Default Product Weight (%s)', 'woocommerce-shipping-usps' ), get_option( 'woocommerce_weight_unit' ) ),
				'type'        => 'decimal',
				'desc_tip'    => true,
				'description' => __( 'This weight will be used for products that do not have a weight set.', 'woocommerce-shipping-usps' ),
				'default'     => '',
				'placeholder' => __( '1', 'woocommerce-shipping-usps' ),
			),
			'enable_flat_rate_box_weights' => array(
				'title'       => __( 'Enable Flat Rate Box Weights', 'woocommerce-shipping-usps' ),
				'label'       => __( 'Include Flat Rate box weights in the box packer calculation?', 'woocommerce-shipping-usps' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Enable Flat Rate box weights to factor in the empty flat rate box/envelope weights in the box packer algorithm. Only the product weights will be taken into account when this setting is disabled.', 'woocommerce-shipping-usps' ),
			),
			'flat_rate_box_weights'        => array(
				'type' => 'flat_rate_box_weights',
			),
			'debug_mode'                   => array(
				'title'       => __( 'Debug Mode', 'woocommerce-shipping-usps' ),
				'label'       => __( 'Enable debug mode', 'woocommerce-shipping-usps' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Enable debug mode to show debugging information on your cart/checkout.', 'woocommerce-shipping-usps' ),
			),
		);

		if ( ! get_option( 'woocommerce_usps_rest' ) ) {
			unset( $this->form_fields['api_type'] );
			unset( $this->form_fields['client_id'] );
			unset( $this->form_fields['client_secret'] );
		}

		/**
		 * Add Box Packer Library select field
		 * if using PHP 7.1 or newer
		 */
		if ( version_compare( phpversion(), '7.1', '>=' ) ) {
			$this->form_fields['box_packer_library'] = array(
				'title'       => __( 'Box Packer Library', 'woocommerce-shipping-usps' ),
				'type'        => 'select',
				'default'     => '',
				'class'       => 'box_packer_library',
				'options'     => array(
					'original' => __( 'Speed Packer', 'woocommerce-shipping-usps' ),
					'dvdoug'   => __( 'Accurate Packer', 'woocommerce-shipping-usps' ),
				),
				'description' => __( 'Speed Packer packs items by volume, Accurate Packer check each dimension allowing more accurate packing but might be slow when you sell items in large quantities.', 'woocommerce-shipping-usps' ),
			);
		}
	}

	/**
	 * Calculate shipping cost.
	 *
	 * @since   1.0.0
	 * @version 4.4.7
	 *
	 * @param array $package Package to ship.
	 *
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$this->api->calculate_shipping( $package );
	}

	/**
	 * Get metadata package description string for the shipping rate.
	 *
	 * @since   4.4.7
	 * @version 4.4.7
	 *
	 * @param array $params Meta data info to join.
	 *
	 * @return string Rate meta data.
	 */
	public function get_rate_package_description( $params ) {
		$meta_data = array();

		if ( ! empty( $params['name'] ) ) {
			$meta_data[] = $params['name'] . ' -';
		}

		if ( $params['length'] && $params['width'] && $params['height'] ) {
			$meta_data[] = sprintf( '%1$s × %2$s × %3$s (in)', $params['length'], $params['width'], $params['height'] );
		}
		if ( $params['weight'] ) {
			$meta_data[] = round( $params['weight'], 2 ) . 'lbs';
		}
		if ( $params['qty'] ) {
			$meta_data[] = '× ' . $params['qty'];
		}

		return implode( ' ', $meta_data );
	}

	/**
	 * Sort rate.
	 *
	 * @param mixed $a A.
	 * @param mixed $b B.
	 *
	 * @return int
	 */
	public function sort_rates( $a, $b ) {
		if ( $this->sort_by_price ) {
			return ( floatval( $a['cost'] ) < floatval( $b['cost'] ) ) ? - 1 : 1;
		}

		if ( $a['sort'] === $b['sort'] ) {
			return 0;
		}

		return ( $a['sort'] < $b['sort'] ) ? - 1 : 1;
	}

	/**
	 * Get country name from given country code.
	 *
	 * @param string $code Country's code.
	 *
	 * @return bool|string False if country code is not found or string if there's
	 *                     a match from a given country's code.
	 */
	public function get_country_name( $code ) {
		/**
		 * Filter to modify USPS country code and name.
		 *
		 * @var array List of countries.
		 *
		 * @since 3.3.2
		 */
		$countries = apply_filters(
			'usps_countries',
			array(
				'AF' => 'Afghanistan',
				'AX' => 'Aland Island (Finland)',
				'AL' => 'Albania',
				'DZ' => 'Algeria',
				'AS' => 'American Samoa',
				'AD' => 'Andorra',
				'AO' => 'Angola',
				'AI' => 'Anguilla',
				'AG' => 'Antigua and Barbuda',
				'AR' => 'Argentina',
				'AM' => 'Armenia',
				'AW' => 'Aruba',
				'AU' => 'Australia',
				'AT' => 'Austria',
				'AZ' => 'Azerbaijan',
				'BS' => 'Bahamas',
				'BH' => 'Bahrain',
				'BD' => 'Bangladesh',
				'BB' => 'Barbados',
				'BY' => 'Belarus',
				'BE' => 'Belgium',
				'BZ' => 'Belize',
				'BJ' => 'Benin',
				'BM' => 'Bermuda',
				'BT' => 'Bhutan',
				'BO' => 'Bolivia',
				'BQ' => 'Bonaire (Curacao)',
				'BA' => 'Bosnia-Herzegovina',
				'BW' => 'Botswana',
				'BV' => 'Norway',
				'BR' => 'Brazil',
				'IO' => 'Great Britain and Northern Ireland',
				'VG' => 'British Virgin Islands',
				'BN' => 'Brunei Darussalam',
				'BG' => 'Bulgaria',
				'BF' => 'Burkina Faso',
				'BI' => 'Burundi',
				'KH' => 'Cambodia',
				'CM' => 'Cameroon',
				'CA' => 'Canada',
				'CV' => 'Cape Verde',
				'KY' => 'Cayman Islands',
				'CF' => 'Central African Republic',
				'TD' => 'Chad',
				'CL' => 'Chile',
				'CN' => 'China',
				'CX' => 'Christmas Island',
				'CC' => 'Cocos Island (Australia)',
				'CO' => 'Colombia',
				'KM' => 'Comoros',
				'CG' => 'Congo, Republic of the',
				'CD' => 'Congo, Democratic Republic of the',
				'CK' => 'Cook Islands',
				'CR' => 'Costa Rica',
				'HR' => 'Croatia',
				'CU' => 'Cuba',
				'CW' => 'Curacao',
				'CY' => 'Cyprus',
				'CZ' => 'Czech Republic',
				'DK' => 'Denmark',
				'DJ' => 'Djibouti',
				'DM' => 'Dominica',
				'DO' => 'Dominican Republic',
				'EC' => 'Ecuador',
				'EG' => 'Egypt',
				'SV' => 'El Salvador',
				'GQ' => 'Equatorial Guinea',
				'ER' => 'Eritrea',
				'EE' => 'Estonia',
				'ET' => 'Ethiopia',
				'FK' => 'Falkland Islands',
				'FO' => 'Faroe Islands',
				'FJ' => 'Fiji',
				'FI' => 'Finland',
				'FR' => 'France',
				'GF' => 'French Guiana',
				'PF' => 'French Polynesia',
				'TF' => 'France',
				'GA' => 'Gabon',
				'GM' => 'Gambia',
				'GE' => 'Georgia',
				'DE' => 'Germany',
				'GH' => 'Ghana',
				'GI' => 'Gibraltar',
				'GR' => 'Greece',
				'GL' => 'Greenland',
				'GD' => 'Grenada',
				'GP' => 'Guadeloupe',
				// phpcs:ignore --- Guam is considered a country but USPS currently sees it as a state.
				/*'GU' => 'Guam', */
				'GT' => 'Guatemala',
				'GG' => 'Guernsey',
				'GN' => 'Guinea',
				'GW' => 'Guinea-Bissau',
				'GY' => 'Guyana',
				'HT' => 'Haiti',
				'HM' => 'Australia',
				'HN' => 'Honduras',
				'HK' => 'Hong Kong',
				'HU' => 'Hungary',
				'IS' => 'Iceland',
				'IN' => 'India',
				'ID' => 'Indonesia',
				'IR' => 'Iran',
				'IQ' => 'Iraq',
				'IE' => 'Ireland',
				'IM' => 'Isle of Man',
				'IL' => 'Israel',
				'IT' => 'Italy',
				'CI' => 'Ivory Coast',
				'JM' => 'Jamaica',
				'JP' => 'Japan',
				'JE' => 'Jersey',
				'JO' => 'Jordan',
				'KZ' => 'Kazakhstan',
				'KE' => 'Kenya',
				'KI' => 'Kiribati',
				'KW' => 'Kuwait',
				'KG' => 'Kyrgyzstan',
				'LA' => 'Laos',
				'LV' => 'Latvia',
				'LB' => 'Lebanon',
				'LS' => 'Lesotho',
				'LR' => 'Liberia',
				'LY' => 'Libya',
				'LI' => 'Liechtenstein',
				'LT' => 'Lithuania',
				'LU' => 'Luxembourg',
				'MO' => 'Macao',
				'MK' => 'Macedonia',
				'MG' => 'Madagascar',
				'MW' => 'Malawi',
				'MY' => 'Malaysia',
				'MV' => 'Maldives',
				'ML' => 'Mali',
				'MT' => 'Malta',
				'MQ' => 'Martinique',
				'MR' => 'Mauritania',
				'MU' => 'Mauritius',
				'YT' => 'Mayotte',
				'MX' => 'Mexico',
				'MD' => 'Moldova',
				'MC' => 'Monaco',
				'MN' => 'Mongolia',
				'ME' => 'Montenegro',
				'MS' => 'Montserrat',
				'MA' => 'Morocco',
				'MZ' => 'Mozambique',
				'MM' => 'Myanmar',
				'NA' => 'Namibia',
				'NR' => 'Nauru',
				'NP' => 'Nepal',
				'NL' => 'Netherlands',
				'NC' => 'New Caledonia',
				'NZ' => 'New Zealand',
				'NI' => 'Nicaragua',
				'NE' => 'Niger',
				'NG' => 'Nigeria',
				'NU' => 'Niue',
				'NF' => 'Norfolk Island',
				'MP' => 'Northern Mariana Islands',
				'KP' => 'North Korea',
				'NO' => 'Norway',
				'OM' => 'Oman',
				'PK' => 'Pakistan',
				'PS' => 'Israel', // Palestinian Territory, Occupied.
				'PA' => 'Panama',
				'PG' => 'Papua New Guinea',
				'PY' => 'Paraguay',
				'PE' => 'Peru',
				'PH' => 'Philippines',
				'PN' => 'Pitcairn Island',
				'PL' => 'Poland',
				'PT' => 'Portugal',
				'PR' => 'Puerto Rico',
				'QA' => 'Qatar',
				'RE' => 'Reunion',
				'RO' => 'Romania',
				'RU' => 'Russia',
				'RW' => 'Rwanda',
				'BL' => 'Saint Barthelemy (Guadeloupe)',
				'SH' => 'Saint Helena',
				'KN' => 'Saint Kitts and Nevis',
				'LC' => 'Saint Lucia',
				'MF' => 'Saint Martin (French) (Guadeloupe)',
				'SX' => 'Sint Maarten',
				'PM' => 'Saint Pierre and Miquelon',
				'VC' => 'Saint Vincent and the Grenadines',
				'SM' => 'San Marino',
				'ST' => 'Sao Tome and Principe',
				'SA' => 'Saudi Arabia',
				'SN' => 'Senegal',
				'RS' => 'Serbia',
				'SC' => 'Seychelles',
				'SL' => 'Sierra Leone',
				'SG' => 'Singapore',
				'SK' => 'Slovakia',
				'SI' => 'Slovenia',
				'SB' => 'Solomon Islands',
				'SO' => 'Somalia',
				'ZA' => 'South Africa',
				'GS' => 'Great Britain and Northern Ireland', // South Georgia and the South Sandwich Islands.
				'KR' => 'South Korea',
				'ES' => 'Spain',
				'LK' => 'Sri Lanka',
				'SD' => 'Sudan',
				'SR' => 'Suriname',
				'SJ' => 'Norway', // Svalbard and Jan Mayen.
				'SZ' => 'Swaziland',
				'SE' => 'Sweden',
				'CH' => 'Switzerland',
				'SY' => 'Syria',
				'TW' => 'Taiwan',
				'TJ' => 'Tajikistan',
				'TZ' => 'Tanzania',
				'TH' => 'Thailand',
				'TL' => 'Timor-Leste',
				'TG' => 'Togo',
				'TK' => 'Tokelau',
				'TO' => 'Tonga',
				'TT' => 'Trinidad and Tobago',
				'TN' => 'Tunisia',
				'TR' => 'Turkey',
				'TM' => 'Turkmenistan',
				'TC' => 'Turks and Caicos Islands',
				'TV' => 'Tuvalu',
				'UG' => 'Uganda',
				'UA' => 'Ukraine',
				'AE' => 'United Arab Emirates',
				'GB' => 'United Kingdom',
				'UM' => 'United States (US) Minor Outlying Islands',
				'VI' => 'United States (US) Virgin Islands',
				'UY' => 'Uruguay',
				'UZ' => 'Uzbekistan',
				'VU' => 'Vanuatu',
				'VA' => 'Vatican City',
				'VE' => 'Venezuela',
				'VN' => 'Vietnam',
				'WF' => 'Wallis and Futuna Islands',
				'EH' => 'Morocco', // Western Sahara.
				'WS' => 'Samoa',
				'YE' => 'Yemen',
				'ZM' => 'Zambia',
				'ZW' => 'Zimbabwe',
			)
		);

		if ( isset( $countries[ $code ] ) ) {
			return strtoupper( $countries[ $code ] );
		} else {
			return false;
		}
	}

	/**
	 * Get dafault API type.
	 *
	 * @return string soap|rest;
	 */
	public function get_default_api_type() {
		return ! empty( $this->get_option( 'user_id' ) ) ? 'soap' : 'rest';
	}

	/**
	 * Check if dimensions fall within "Letter" specs.
	 *
	 * @param float $package_length Length.
	 * @param float $package_width  Width.
	 * @param float $package_height Height.
	 *
	 * @return bool Whether or not package fit "Letter" specs.
	 */
	public function is_letter( $package_length, $package_width, $package_height ) {
		if ( $package_length > 11.5 || $package_length < 5 ) {
			return false;
		}
		if ( $package_width > 6.125 || $package_width < 3.5 ) {
			return false;
		}
		if ( $package_height > 0.25 || $package_height < 0.007 ) {
			return false;
		}

		return true;
	}


	/**
	 * Check if dimensions fall within "Large Envelope" specs.
	 *
	 * @param float $package_length Length.
	 * @param float $package_width  Width.
	 * @param float $package_height Height.
	 *
	 * @return bool Whether or not package fit "Large Envelope" specs.
	 */
	public function is_large_envelope( $package_length, $package_width, $package_height ) {
		if ( $package_length > 15 || $package_length < 11.5 ) {
			return false;
		}
		if ( $package_width > 12 || $package_width < 6.125 ) {
			return false;
		}
		if ( $package_height > 0.75 || $package_height < 0.25 ) {
			return false;
		}

		return true;
	}

	/**
	 * Check and return a boolean value to indicate if any package contain an item
	 * that's over 70lbs.
	 *
	 * @since 4.4.33
	 *
	 * @param array $package Package to ship.
	 *
	 * @return bool true if there is at least 1 object is > 70lbs.
	 */
	public function is_package_overweight( $package ) {
		foreach ( $package['contents'] as $item_id => $values ) {
			if ( $values['data']->get_weight() ) {
				$weight = wc_get_weight( $values['data']->get_weight(), 'lbs' );
				if ( $weight > 70 ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Calculates the girth of the package from dimensions.
	 *
	 * Ref: https://www.ups.com/us/en/help-center/packaging-and-supplies/prepare-overize.page
	 *
	 * @since 4.4.45
	 *
	 * @param array $dimensions The dimension to calculate from.
	 *
	 * @return int $girth
	 */
	public function get_girth( $dimensions = null ) {
		if ( is_null( $dimensions ) ) {
			return 0;
		}

		$girth = round( ( $dimensions[0] * 2 ) + ( $dimensions[1] * 2 ) );

		return $girth;
	}

	/**
	 * Loop through the default product dimensions
	 * for this instance, convert the values to
	 * inches and return as an array.
	 *
	 * @return array the default product dimensions
	 */
	public function get_default_product_dimensions(): array {
		$dimensions = array();
		foreach ( $this->product_dimensions as $dimension ) {
			$value        = ! empty( $dimension ) ? $dimension : '1';
			$dimensions[] = wc_get_dimension( $value, 'in' );
		}

		return $dimensions;
	}

	/**
	 * Return the default product weight converted to inches
	 *
	 * @return float|int|string the default product weight
	 */
	public function get_default_product_weight() {
		return ! empty( $this->product_weight ) ? wc_get_weight( $this->product_weight, 'lbs' ) : '1';
	}

	/**
	 * Return the empty box weight if box weights should
	 * be included in the calculations. Check for an override
	 * first, before returning the default.
	 *
	 * @param string $box_key        The key for the requested box.
	 * @param float  $default_weight The weight to use if the weight isn't overridden.
	 *
	 * @return float|mixed
	 */
	public function get_empty_box_weight( string $box_key, float $default_weight ) {
		// If empty box weights are disabled, return 0.0.
		if ( ! $this->enable_flat_rate_box_weights ) {
			return 0.0;
		}

		// If the setting isn't overridden, return the default.
		if ( empty( $this->flat_rate_box_weights[ $box_key ] ) ) {
			return $default_weight;
		}

		return $this->flat_rate_box_weights[ $box_key ];
	}

	/**
	 * Check if the package's dimensions match those of a USPS tube.
	 *
	 * @param string $length Package length.
	 * @param string $width  Package width.
	 * @param string $height Package height.
	 *
	 * @return bool
	 */
	public function package_has_usps_tube_dimensions( string $length, string $width, string $height ): bool {
		$usps_tube_dimensions = array(
			'small'  => '25.5625x6x5.25',
			'medium' => '38.0625x6.25x4.25',
		);

		$package_dimensions = $length . 'x' . $width . 'x' . $height;

		if ( in_array( $package_dimensions, $usps_tube_dimensions, true ) ) {
			return true;
		}

		return false;
	}
}
