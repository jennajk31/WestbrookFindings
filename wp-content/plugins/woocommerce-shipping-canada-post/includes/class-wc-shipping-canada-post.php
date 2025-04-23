<?php
/**
 * Main class file.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WC_CANADA_POST_ABSPATH . 'includes/class-logger.php';

use WooCommerce\CanadaPost\Logger;
use WooCommerce\BoxPacker\WC_Boxpack;

/**
 * WC_Shipping_Canada_Post class.
 *
 * @extends WC_Shipping_Method
 */
class WC_Shipping_Canada_Post extends WC_Shipping_Method {
	/**
	 * Canada post API endpoints.
	 *
	 * @var array
	 */
	private $endpoints = array(
		'development' => 'https://ct.soa-gw.canadapost.ca/rs/ship/price',
		'production'  => 'https://soa-gw.canadapost.ca/rs/ship/price',
	);

	/**
	 * Platform ID.
	 *
	 * @var string
	 */
	private $platform_id = '0008218084';

	/**
	 * List of found rates.
	 *
	 * @var array
	 */
	private $found_rates;

	/**
	 * List of Canada post services.
	 *
	 * @var array
	 */
	private $services = array();

	/**
	 * Liability additional coverage costs from https://www.canadapost-postescanada.ca/cpc/doc/en/support/LM-e.pdf.
	 *
	 * @var string|float
	 */
	private $ca_additional_liability_coverage = '2.75';

	/**
	 * Liability registered mail costs from https://www.canadapost-postescanada.ca/cpc/doc/en/support/LM-e.pdf.
	 *
	 * @var string|float
	 */
	private $ca_registered = '10.50';

	/**
	 * Liability international registered mail costs from https://www.canadapost-postescanada.ca/cpc/doc/en/support/prices/consumer-prices.pdf.
	 *
	 * @var string|float
	 */
	private $int_registered = '21.00';

	/**
	 * Lettermail boxes.
	 *
	 * @var array
	 */
	private $lettermail_boxes;

	/**
	 * Postcode origin.
	 *
	 * @var string.
	 */
	protected $origin;

	/**
	 * Packing method.
	 *
	 * @var string.
	 */
	protected $packing_method;

	/**
	 * List of boxes.
	 *
	 * @var array.
	 */
	public $boxes;

	/**
	 * List of services.
	 *
	 * @var string.
	 */
	protected $custom_services;

	/**
	 * Flag for using flat rates or not.
	 *
	 * @var boolean.
	 */
	protected $enable_flat_rates;

	/**
	 * Type of rate that will be offered.
	 *
	 * @var string.
	 */
	protected $offer_rates;

	/**
	 * Quotation type whether to "commercial" or "counter".
	 *
	 * @var string.
	 */
	protected $quote_type;

	/**
	 * Rate cost that will be used.
	 *
	 * @var string.
	 */
	protected $use_cost;

	/**
	 * Lettermail rates.
	 *
	 * @var string.
	 */
	protected $lettermail;

	/**
	 * Flag to use debug mode or not.
	 *
	 * @var boolean.
	 */
	public $debug;

	/**
	 * Logger instance.
	 *
	 * @var Logger
	 */
	public $logger;

	/**
	 * Canada post options.
	 *
	 * @var string.
	 */
	public $options;

	/**
	 * Flag for showing delivery time in front-end.
	 *
	 * @var boolean.
	 */
	protected $show_delivery_time;

	/**
	 * Delivery time delay value.
	 *
	 * @var int.
	 */
	protected $delivery_time_delay;

	/**
	 * Customer number.
	 *
	 * @var string.
	 */
	protected $customer_number;

	/**
	 * Username.
	 *
	 * @var string.
	 */
	protected $username;

	/**
	 * Password.
	 *
	 * @var string.
	 */
	protected $password;

	/**
	 * Contract ID.
	 *
	 * @var string.
	 */
	protected $contract_id;

	/**
	 * Endpoint.
	 *
	 * @var string.
	 */
	public $endpoint;

	/**
	 * Maximum weight.
	 *
	 * @var float.
	 */
	protected $max_weight;

	/**
	 * Domestic Canadian Flat Rate Boxes.
	 *
	 * @see https://www.canadapost-postescanada.ca/cpc/en/personal/stamps-and-packaging/flat-rate-box.page
	 *
	 * @var array
	 */
	private $ca_flat_rate_boxes = array(
		'extra-small' => array(
			'name'       => 'Extra Small Flat Rate Box',
			'length'     => '24.1',
			'width'      => '16.5',
			'height'     => '8.9',
			'max_weight' => '5',
			'cost'       => '17.99',
		),
		'small'       => array(
			'name'       => 'Small Flat Rate Box',
			'length'     => '35',
			'width'      => '26',
			'height'     => '5',
			'max_weight' => '5',
			'cost'       => '19.99',
		),
		'medium'      => array(
			'name'       => 'Medium Flat Rate Box',
			'length'     => '39',
			'width'      => '26',
			'height'     => '12',
			'max_weight' => '5',
			'cost'       => '24.99',
		),
		'large'       => array(
			'name'       => 'Large Flat Rate Box',
			'length'     => '40',
			'width'      => '30',
			'height'     => '19',
			'max_weight' => '5',
			'cost'       => '32.99',
		),
	);

	/**
	 * Domestic Canadian rates from https://www.canadapost-postescanada.ca/cpc/en/personal/stamp-prices.page
	 *
	 * @var array
	 */
	private $ca_lettermail_boxes = array(
		'standard'     => array(
			'name'   => 'Standard Lettermail',
			'length' => '24.5',
			'width'  => '15.6',
			'height' => '0.5',
			'weight' => '0.05',
			'costs'  => array(
				'0.03' => '1.15',
				'0.05' => '1.40',
			),
		),
		'medium'       => array(
			'name'   => 'Medium Lettermail',
			'length' => '23.5',
			'width'  => '16.5',
			'height' => '0.5',
			'weight' => '0.05',
			'costs'  => array(
				'0.03' => '1.15',
				'0.05' => '1.40',
			),
		),
		'non-standard' => array(
			'name'   => 'Non-standard Lettermail',
			'length' => '38',
			'width'  => '27',
			'height' => '2',
			'weight' => '0.5',
			'costs'  => array(
				'0.1' => '2.09',
				'0.2' => '3.43',
				'0.3' => '4.78',
				'0.4' => '5.48',
				'0.5' => '5.89',
			),
		),
	);

	/**
	 * Shipping to US rates from https://www.canadapost-postescanada.ca/cpc/en/personal/stamp-prices.page
	 *
	 * @var array
	 */
	private $us_lettermail_boxes = array(
		'standard'     => array(
			'name'   => 'Standard USA Letter-post',
			'length' => '24.5',
			'width'  => '15.6',
			'height' => '0.5',
			'weight' => '0.05',
			'costs'  => array(
				'0.03' => '1.40',
				'0.05' => '2.09',
			),
		),
		'medium'       => array(
			'name'   => 'Medium USA Letter-post',
			'length' => '23.5',
			'width'  => '16.5',
			'height' => '0.5',
			'weight' => '0.05',
			'costs'  => array(
				'0.03' => '1.40',
				'0.05' => '2.09',
			),
		),
		'non-standard' => array(
			'name'   => 'Non-standard USA Letter-post',
			'length' => '38',
			'width'  => '27',
			'height' => '2',
			'weight' => '0.5',
			'costs'  => array(
				'0.1' => '3.43',
				'0.2' => '5.99',
				'0.5' => '11.99',
			),
		),
	);

	/**
	 * Shipping international other than US.
	 *
	 * @var array
	 */
	private $int_lettermail_boxes = array(
		'standard'     => array(
			'name'   => 'Standard International Letter-post',
			'length' => '24.5',
			'width'  => '15.6',
			'height' => '0.5',
			'weight' => '0.05',
			'costs'  => array(
				'0.03' => '2.92',
				'0.05' => '4.17',
			),
		),
		'medium'       => array(
			'name'   => 'Medium International Letter-post',
			'length' => '23.5',
			'width'  => '16.5',
			'height' => '0.5',
			'weight' => '0.05',
			'costs'  => array(
				'0.03' => '2.92',
				'0.05' => '4.17',
			),
		),
		'non-standard' => array(
			'name'   => 'Non-standard International Letter-post',
			'length' => '38',
			'width'  => '27',
			'height' => '2',
			'weight' => '0.5',
			'costs'  => array(
				'0.1' => '6.88',
				'0.2' => '11.99',
				'0.5' => '23.97',
			),
		),
	);

	/**
	 * Sets the box packer library to use.
	 *
	 * @var string
	 */
	public string $box_packer_library;
	/**
	 * Class constructor.
	 *
	 * @param int $instance_id Instance ID.
	 *
	 * @return void
	 */
	public function __construct( $instance_id = 0 ) {

		$this->services = array(
			'DOM.RP'         => __( 'Regular Parcel', 'woocommerce-shipping-canada-post' ),
			'DOM.EP'         => __( 'Expedited Parcel', 'woocommerce-shipping-canada-post' ),
			'DOM.XP'         => __( 'Xpresspost', 'woocommerce-shipping-canada-post' ),
			'DOM.PC'         => __( 'Priority', 'woocommerce-shipping-canada-post' ),
			'DOM.LIB'        => __( 'Library Books', 'woocommerce-shipping-canada-post' ),
			'USA.EP'         => __( 'Expedited Parcel USA', 'woocommerce-shipping-canada-post' ),
			'USA.TP'         => __( 'Tracked Packet USA', 'woocommerce-shipping-canada-post' ),
			'USA.TP.LVM'     => __( 'Tracked Packet USA (LVM)', 'woocommerce-shipping-canada-post' ),
			'USA.PW.ENV'     => __( 'Priority Worldwide Envelope USA', 'woocommerce-shipping-canada-post' ),
			'USA.PW.PAK'     => __( 'Priority Worldwide pak USA', 'woocommerce-shipping-canada-post' ),
			'USA.PW.PARCEL'  => __( 'Priority Worldwide Parcel USA', 'woocommerce-shipping-canada-post' ),
			'USA.SP.AIR'     => __( 'Small Packet USA Air', 'woocommerce-shipping-canada-post' ),
			'USA.SP.AIR.LVM' => __( 'Small Packet USA Air (LVM)', 'woocommerce-shipping-canada-post' ),
			'USA.SP.SURF'    => __( 'Small Packet USA Surface', 'woocommerce-shipping-canada-post' ),
			'USA.XP'         => __( 'Xpresspost USA', 'woocommerce-shipping-canada-post' ),
			'INT.XP'         => __( 'Xpresspost International', 'woocommerce-shipping-canada-post' ),
			'INT.TP'         => __( 'International Tracked Packet', 'woocommerce-shipping-canada-post' ),
			'INT.IP.AIR'     => __( 'International Parcel Air', 'woocommerce-shipping-canada-post' ),
			'INT.IP.SURF'    => __( 'International Parcel Surface', 'woocommerce-shipping-canada-post' ),
			'INT.PW.ENV'     => __( 'Priority Worldwide Envelope International', 'woocommerce-shipping-canada-post' ),
			'INT.PW.PAK'     => __( 'Priority Worldwide pak International', 'woocommerce-shipping-canada-post' ),
			'INT.PW.PARCEL'  => __( 'Priority Worldwide parcel International', 'woocommerce-shipping-canada-post' ),
			'INT.SP.AIR'     => __( 'Small Packet International Air', 'woocommerce-shipping-canada-post' ),
			'INT.SP.SURF'    => __( 'Small Packet International Surface', 'woocommerce-shipping-canada-post' ),
		);

		$this->instance_id        = absint( $instance_id );
		$this->id                 = 'canada_post';
		$this->method_title       = __( 'Canada Post', 'woocommerce-shipping-canada-post' );
		$this->method_description = __( 'The Canada Post extension obtains rates dynamically from the Canada Post API during cart/checkout.', 'woocommerce-shipping-canada-post' );
		$this->supports           = array(
			'shipping-zones',
			'instance-settings',
			'settings',
		);

		/**
		 * Filter the lettermail boxes to allow actors to alter prices, dimension, add taxes, etc.
		 *
		 * @param array Canada lettermail boxes.
		 *
		 * @since 2.5.0
		 */
		$this->ca_lettermail_boxes = apply_filters( 'wc_shipping_canada_post_ca_lettermail_boxes', $this->ca_lettermail_boxes );

		/**
		 * Filter to modify list of lettermail boxes.
		 *
		 * @param array Lettermail boxes.
		 *
		 * @since 2.5.0
		 */
		$this->us_lettermail_boxes = apply_filters( 'wc_shipping_canada_post_us_lettermail_boxes', $this->us_lettermail_boxes );

		/**
		 * Filter to modify list of international lettermail boxes.
		 *
		 * @param array International lettermail boxes.
		 *
		 * @since 2.5.0
		 */
		$this->int_lettermail_boxes = apply_filters( 'wc_shipping_canada_post_int_lettermail_boxes', $this->int_lettermail_boxes );

		$this->init();
	}

	/**
	 * Check if the package can use Canada post.
	 *
	 * @param array $package Cart package.
	 *
	 * @return bool
	 */
	public function is_available( $package ) {
		if ( empty( $package['destination']['country'] ) ) {
			return false;
		}

		/**
		 * Filter to modify shipping method availability.
		 *
		 * @param boolean Availability. `true` means available. `false` means not available.
		 * @param array  $package Cart package.
		 *
		 * @since 2.5.0
		 */
		return apply_filters( 'woocommerce_shipping_' . $this->id . '_is_available', true, $package );
	}

	/**
	 * Initialize settings
	 *
	 * @version 2.5.0
	 * @since 2.5.0
	 * @return void
	 */
	private function set_settings() {
		// Define user set variables.
		$this->title               = $this->get_option( 'title', $this->method_title );
		$this->origin              = $this->get_option( 'origin', '' );
		$this->packing_method      = $this->get_option( 'packing_method', 'per_item' );
		$this->boxes               = $this->get_option( 'boxes', array() );
		$this->custom_services     = $this->get_option( 'services', array() );
		$this->enable_flat_rates   = $this->get_option( 'enable_flat_rates', 'no' );
		$this->offer_rates         = $this->get_option( 'offer_rates', 'all' );
		$this->quote_type          = $this->get_option( 'quote_type', 'commercial' );
		$this->use_cost            = $this->get_option( 'use_cost', 'due' );
		$this->lettermail          = $this->get_option( 'lettermail', array() );
		$this->debug               = ( $this->get_option( 'debug' ) === 'yes' );
		$this->options             = $this->get_option( 'options', array() );
		$this->show_delivery_time  = ( $this->get_option( 'show_delivery_time' ) === 'yes' );
		$this->delivery_time_delay = $this->get_option( 'delivery_time_delay', 1 );

		// Get merchant credentials.
		$this->customer_number = get_option( 'wc_canada_post_customer_number' );
		$this->username        = get_option( 'wc_canada_post_merchant_username' );
		$this->password        = get_option( 'wc_canada_post_merchant_password' );
		$this->contract_id     = get_option( 'wc_canada_post_contract_number' );
		$this->endpoint        = $this->endpoints['production'];

		// Used for weight based packing only.
		$this->max_weight = floatval( $this->get_option( 'max_weight', '30' ) );

		$this->box_packer_library = $this->get_option( 'box_packer_library', $this->get_default_box_packer_library() );

		/**
		 * Set the logger.
		 */
		$this->logger = new Logger( $this->debug );
	}

	/**
	 * Initiation function.
	 *
	 * @return void
	 */
	private function init() {
		// Load the settings.
		$this->init_form_fields();
		$this->set_settings();

		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'clear_transients' ) );
	}

	/**
	 * Process settings on save
	 *
	 * @since 2.5.0
	 * @version 2.5.0
	 * @return void
	 */
	public function process_admin_options() {
		parent::process_admin_options();

		$this->set_settings();
	}

	/**
	 * Output a message or error.
	 *
	 * @param string $message Debug message.
	 * @param array  $data Additional data for debugging.
	 */
	public function debug( $message, $data = array() ) {
		$this->logger->debug( $message, $data );
	}

	/**
	 * Handle CP registration.
	 */
	public function revoke_registration() {
		$security_nonce = isset( $_GET['security_nonce'] ) ? sanitize_text_field( wp_unslash( $_GET['security_nonce'] ) ) : '';
		if ( ! empty( $_GET['disconnect_canada_post'] ) && wp_verify_nonce( $security_nonce, 'canada_disconnect_security' ) ) {
			update_option( 'wc_canada_post_customer_number', '' );
			update_option( 'wc_canada_post_contract_number', '' );
			update_option( 'wc_canada_post_merchant_username', '' );
			update_option( 'wc_canada_post_merchant_password', '' );
		}
	}

	/**
	 * Display a notice when the account is connected.
	 *
	 * @return void
	 */
	public function admin_options() {
		if ( isset( $_GET['tab'] ) && 'shipping' === $_GET['tab'] && isset( $_GET['section'] ) && 'canada_post' === $_GET['section'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended --- No need to verify as no DB operation.
			// Handle registration.
			$this->revoke_registration();

			if ( get_option( 'wc_canada_post_customer_number' ) ) {
				echo wp_kses_post(
					wpautop(
						sprintf(
							// translators: %1$s is a Canada post customer number and %2$s is a URL to disconnect the account.
							__( 'The account <code>%1$s</code> is currently connected - to disconnect this account <a href="%2$s">click here</a>.', 'woocommerce-shipping-canada-post' ),
							esc_html( get_option( 'wc_canada_post_customer_number' ) ),
							esc_url(
								add_query_arg(
									array(
										'disconnect_canada_post' => 'true',
										'security_nonce' => wp_create_nonce( 'canada_disconnect_security' ),
									)
								)
							)
						)
					)
				);
			}
		}

		parent::admin_options();
	}

	/**
	 * Generate services table HTML.
	 *
	 * @return string
	 */
	public function generate_services_html() {
		ob_start();
		?>
		<tr valign="top" id="service_options">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Services', 'woocommerce-shipping-canada-post' ); ?></th>
			<td class="forminp">
				<table class="canada_post_services widefat">
					<thead>
						<th class="sort">&nbsp;</th>
						<th><?php esc_html_e( 'Service Code', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Name', 'woocommerce-shipping-canada-post' ); ?></th>
						<th><?php esc_html_e( 'Enabled', 'woocommerce-shipping-canada-post' ); ?></th>
						<th>
							<?php
							// translators: %s is a WooCommerce currency symbol.
							printf( esc_html__( 'Price Adjustment (%s)', 'woocommerce-shipping-canada-post' ), esc_html( get_woocommerce_currency_symbol() ) );
							?>
						</th>
						<th><?php esc_html_e( 'Price Adjustment (%)', 'woocommerce-shipping-canada-post' ); ?></th>
					</thead>
					<tbody>
						<?php
						$sort             = 0;
						$ordered_services = array();

						foreach ( $this->services as $code => $name ) {

							if ( isset( $this->custom_services[ $code ]['order'] ) ) {
								$sort = absint( $this->custom_services[ $code ]['order'] );
							}

							while ( isset( $ordered_services[ $sort ] ) ) {
								++$sort;
							}

							$ordered_services[ $sort ] = array( $code, $name );

							++$sort;
						}

						ksort( $ordered_services );

						foreach ( $ordered_services as $value ) {
							$code = $value[0];
							$name = $value[1];
							?>
							<tr>
							<td class="sort"><input type="hidden" class="order" name="<?php echo esc_attr( 'canada_post_service[' . $code . '][order]' ); ?>" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['order'] ) ? $this->custom_services[ $code ]['order'] : '' ); ?>" /></td>
								<td><strong><?php echo esc_html( $code ); ?></strong></td>
								<td><input type="text" name="<?php echo esc_attr( 'canada_post_service[' . $code . '][name]' ); ?>" placeholder="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['name'] ) ? $this->custom_services[ $code ]['name'] : '' ); ?>" size="50" /></td>
								<td><input type="checkbox" name="<?php echo esc_attr( "canada_post_service[$code][enabled]" ); ?>" <?php checked( ( ! isset( $this->custom_services[ $code ]['enabled'] ) || ! empty( $this->custom_services[ $code ]['enabled'] ) ), true ); ?> /></td>
								<td><input type="text" name="<?php echo esc_attr( "canada_post_service[$code][adjustment]" ); ?>" placeholder="N/A" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['adjustment'] ) ? $this->custom_services[ $code ]['adjustment'] : '' ); ?>" size="4" /></td>
								<td><input type="text" name="<?php echo esc_attr( "canada_post_service[$code][adjustment_percent]" ); ?>" placeholder="N/A" value="<?php echo esc_attr( isset( $this->custom_services[ $code ]['adjustment_percent'] ) ? $this->custom_services[ $code ]['adjustment_percent'] : '' ); ?>" size="4" /></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Generate Box packing HTML.
	 *
	 * @return string
	 */
	public function generate_box_packing_html() {
		ob_start();
		?>
		<tr valign="top" id="packing_options">
			<th scope="row" class="titledesc"><?php esc_html_e( 'Box Sizes', 'woocommerce-shipping-canada-post' ); ?></th>
			<td class="forminp">
				<style type="text/css">
					.canada_post_boxes td, .canada_post_services td {
						vertical-align: middle;
							padding: 4px 7px;
					}
					.canada_post_boxes th, .canada_post_services th {
						padding: 9px 7px;
					}
					.canada_post_boxes td input {
						margin-right: 4px;
					}
					.canada_post_boxes .check-column {
						vertical-align: middle;
						text-align: left;
						padding: 0 7px;
					}
					.canada_post_boxes .help_tip {
						float: none !important;
						margin-right: 24px !important;
					}
					.canada_post_services th.sort {
						width: 16px;
					}
					.canada_post_services td.sort {
						cursor: move;
						width: 16px;
						padding: 0 16px;
						cursor: move;
						background: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAAHUlEQVQYV2O8f//+fwY8gJGgAny6QXKETRgEVgAAXxAVsa5Xr3QAAAAASUVORK5CYII=) no-repeat center;
					}
				</style>
				<table class="canada_post_boxes widefat">
					<thead>
						<tr>
							<th class="check-column"><input type="checkbox" /></th>
							<th><?php esc_html_e( 'Name', 'woocommerce-shipping-canada-post' ); ?></th>
							<th><?php esc_html_e( 'Outer Length', 'woocommerce-shipping-canada-post' ); ?></th>
							<th><?php esc_html_e( 'Outer Width', 'woocommerce-shipping-canada-post' ); ?></th>
							<th><?php esc_html_e( 'Outer Height', 'woocommerce-shipping-canada-post' ); ?></th>
							<th><?php esc_html_e( 'Inner Length', 'woocommerce-shipping-canada-post' ); ?></th>
							<th><?php esc_html_e( 'Inner Width', 'woocommerce-shipping-canada-post' ); ?></th>
							<th><?php esc_html_e( 'Inner Height', 'woocommerce-shipping-canada-post' ); ?></th>
							<th>
								<?php esc_html_e( 'Weight of Box', 'woocommerce-shipping-canada-post' ); ?>
								<img class="help_tip" width="16" height="16" data-tip="<?php esc_attr_e( 'Weight of the actual box and will be added to the weight of the contents. This will increase the cost of shipping.', 'woocommerce-shipping-canada-post' ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" />
							</th>
							<th>
								<?php esc_html_e( 'Max Weight', 'woocommerce-shipping-canada-post' ); ?>
								<img class="help_tip" width="16" height="16" data-tip="<?php esc_attr_e( 'Maximum weight your box can hold. This includes contents weight and box weight.', 'woocommerce-shipping-canada-post' ); ?>" src="<?php echo esc_url( WC()->plugin_url() . '/assets/images/help.png' ); ?>" />
							</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th colspan="3">
								<a href="#" class="button plus insert"><?php esc_html_e( 'Add Box', 'woocommerce-shipping-canada-post' ); ?></a>
								<a href="#" class="button minus remove"><?php esc_html_e( 'Remove selected box(es)', 'woocommerce-shipping-canada-post' ); ?></a>
							</th>
							<th colspan="7">
								<small class="description"><?php esc_html_e( 'Items will be packed into these boxes depending based on item dimensions and volume. Outer dimensions will be passed to Canada Post, whereas inner dimensions will be used for packing. Items not fitting into boxes will be packed individually.', 'woocommerce-shipping-canada-post' ); ?></small>
							</th>
						</tr>
					</tfoot>
					<tbody id="rates">
						<?php
						if ( $this->boxes ) {
							foreach ( $this->boxes as $key => $box ) {
								?>
								<tr>
									<td class="check-column"><input type="checkbox" /></td>
									<td><input type="text" size="10" name="<?php echo esc_attr( "boxes_name[$key]" ); ?>" value="<?php echo isset( $box['name'] ) ? esc_attr( $box['name'] ) : ''; ?>" /></td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_outer_length[$key]" ); ?>" value="<?php echo esc_attr( $box['outer_length'] ); ?>" />cm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_outer_width[$key]" ); ?>" value="<?php echo esc_attr( $box['outer_width'] ); ?>" />cm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_outer_height[$key]" ); ?>" value="<?php echo esc_attr( $box['outer_height'] ); ?>" />cm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_inner_length[$key]" ); ?>" value="<?php echo esc_attr( $box['inner_length'] ); ?>" />cm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_inner_width[$key]" ); ?>" value="<?php echo esc_attr( $box['inner_width'] ); ?>" />cm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_inner_height[$key]" ); ?>" value="<?php echo esc_attr( $box['inner_height'] ); ?>" />cm</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_box_weight[$key]" ); ?>" value="<?php echo esc_attr( $box['box_weight'] ); ?>" />kg</td>
									<td><input type="text" size="5" name="<?php echo esc_attr( "boxes_max_weight[$key]" ); ?>" value="<?php echo esc_attr( $box['max_weight'] ); ?>" />kg</td>
								</tr>
								<?php
							}
						}
						?>
					</tbody>
				</table>
				<script type="text/javascript">

					jQuery().ready(function(){

						jQuery('#woocommerce_canada_post_packing_method').change(function(){

							if ( jQuery(this).val() == 'box_packing' )
								jQuery('#packing_options').show();
							else
								jQuery('#packing_options').hide();

							if ( jQuery(this).val() == 'weight' )
								jQuery('#woocommerce_canada_post_max_weight').closest('tr').show();
							else
								jQuery('#woocommerce_canada_post_max_weight').closest('tr').hide();

						}).change();

						jQuery('#woocommerce_canada_post_show_delivery_time').change(function(){

							if ( jQuery(this).is(':checked') )
								jQuery('#woocommerce_canada_post_delivery_time_delay').closest('tr').show();
							else
								jQuery('#woocommerce_canada_post_delivery_time_delay').closest('tr').hide();

						}).change();

						jQuery('.canada_post_boxes .insert').click( function() {
							var $tbody = jQuery('.canada_post_boxes').find('tbody');
							var size = $tbody.find('tr').length;
							var code = '<tr class="new">\
									<td class="check-column"><input type="checkbox" /></td>\
									<td><input type="text" size="10" name="boxes_name[' + size + ']" /></td>\
									<td><input type="text" size="5" name="boxes_outer_length[' + size + ']" />cm</td>\
									<td><input type="text" size="5" name="boxes_outer_width[' + size + ']" />cm</td>\
									<td><input type="text" size="5" name="boxes_outer_height[' + size + ']" />cm</td>\
									<td><input type="text" size="5" name="boxes_inner_length[' + size + ']" />cm</td>\
									<td><input type="text" size="5" name="boxes_inner_width[' + size + ']" />cm</td>\
									<td><input type="text" size="5" name="boxes_inner_height[' + size + ']" />cm</td>\
									<td><input type="text" size="5" name="boxes_box_weight[' + size + ']" />kg</td>\
									<td><input type="text" size="5" name="boxes_max_weight[' + size + ']" />kg</td>\
								</tr>';

							$tbody.append( code );

							return false;
						} );

						jQuery('.canada_post_boxes .remove').click(function() {
							var $tbody = jQuery('.canada_post_boxes').find('tbody');

							$tbody.find('.check-column input:checked').each(function() {
								jQuery(this).closest('tr').hide().find('input').val('');
							});

							return false;
						});

						// Ordering
						jQuery('.canada_post_services tbody').sortable({
							items:'tr',
							cursor:'move',
							axis:'y',
							handle: '.sort',
							scrollSensitivity:40,
							forcePlaceholderSize: true,
							helper: 'clone',
							opacity: 0.65,
							placeholder: 'wc-metabox-sortable-placeholder',
							start:function(event,ui){
								ui.item.css('background-color','#f6f6f6');
							},
							stop:function(event,ui){
								ui.item.removeAttr('style');
								canada_post_services_row_indexes();
							}
						});

						function canada_post_services_row_indexes() {
							jQuery('.canada_post_services tbody tr').each(function(index, el){
								jQuery('input.order', el).val( parseInt( jQuery(el).index('.canada_post_services tr') ) );
							});
						};

					});

				</script>
			</td>
		</tr>
		<?php
		return ob_get_clean();
	}

	/**
	 * Validating Box packing value.
	 *
	 * @param mixed $key Field name or key.
	 *
	 * @return array
	 */
	public function validate_box_packing_field( $key ) {
		// All inputs below are sanitized in the loop below.
		//phpcs:disable WordPress.Security.NonceVerification.Missing --- Nonce verification already handled the method caller.
		$boxes_name         = isset( $_POST['boxes_name'] ) ? wc_clean( wp_unslash( $_POST['boxes_name'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_outer_length = isset( $_POST['boxes_outer_length'] ) ? wc_clean( wp_unslash( $_POST['boxes_outer_length'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_outer_width  = isset( $_POST['boxes_outer_width'] ) ? wc_clean( wp_unslash( $_POST['boxes_outer_width'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_outer_height = isset( $_POST['boxes_outer_height'] ) ? wc_clean( wp_unslash( $_POST['boxes_outer_height'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_inner_length = isset( $_POST['boxes_inner_length'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_length'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_inner_width  = isset( $_POST['boxes_inner_width'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_width'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_inner_height = isset( $_POST['boxes_inner_height'] ) ? wc_clean( wp_unslash( $_POST['boxes_inner_height'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_box_weight   = isset( $_POST['boxes_box_weight'] ) ? wc_clean( wp_unslash( $_POST['boxes_box_weight'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$boxes_max_weight   = isset( $_POST['boxes_max_weight'] ) ? wc_clean( wp_unslash( $_POST['boxes_max_weight'] ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		//phpcs:enable WordPress.Security.NonceVerification.Missing

		$boxes           = array();
		$number_of_boxes = count( $boxes_outer_length );

		for ( $i = 0; $i < $number_of_boxes; $i++ ) {
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
				);
			}
		}

		return $boxes;
	}

	/**
	 * Validating services field value function.
	 *
	 * @param mixed $key Field name or key.
	 *
	 * @return array
	 */
	public function validate_services_field( $key ) {
		$services = array();
		//phpcs:disable WordPress.Security.NonceVerification.Missing --- Nonce verification already handled the method caller.
		$posted_services = isset( $_POST['canada_post_service'] ) ? wp_unslash( $_POST['canada_post_service'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, sanitized in the loop below.
		//phpcs:enable WordPress.Security.NonceVerification.Missing

		foreach ( $posted_services as $code => $settings ) {

			$services[ $code ] = array(
				'name'               => wc_clean( $settings['name'] ),
				'order'              => wc_clean( $settings['order'] ),
				'enabled'            => isset( $settings['enabled'] ) ? true : false,
				'adjustment'         => wc_clean( $settings['adjustment'] ),
				'adjustment_percent' => str_replace( '%', '', wc_clean( $settings['adjustment_percent'] ) ),
			);

		}

		return $services;
	}

	/**
	 * Clear transient.
	 *
	 * @return void
	 */
	public function clear_transients() {
		global $wpdb;

		$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_cp_quote_%') OR `option_name` LIKE ('_transient_timeout_cp_quote_%')" ); //phpcs:ignore --- Need to use WPDB::query to remove transient.
	}

	/**
	 * Initialize form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'               => array(
				'title'       => __( 'Method Title', 'woocommerce-shipping-canada-post' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-shipping-canada-post' ),
				'default'     => __( 'Canada Post', 'woocommerce-shipping-canada-post' ),
				'desc_tip'    => true,
			),
			'origin'              => array(
				'title'       => __( 'Origin Postcode', 'woocommerce-shipping-canada-post' ),
				'type'        => 'text',
				'description' => __( 'Enter the postcode for the <strong>sender</strong>.', 'woocommerce-shipping-canada-post' ),
				'default'     => '',
				'desc_tip'    => true,
			),
			'quote_type'          => array(
				'title'   => __( 'Quote Type', 'woocommerce-shipping-canada-post' ),
				'type'    => 'select',
				'default' => '',
				'options' => array(
					'commercial' => __( 'Commercial - Discounted customer or VentureOne member rates.', 'woocommerce-shipping-canada-post' ),
					'counter'    => __( 'Counter -  Regular price paid by consumers.', 'woocommerce-shipping-canada-post' ),
				),
			),
			'use_cost'            => array(
				'title'   => __( 'Rate Cost', 'woocommerce-shipping-canada-post' ),
				'type'    => 'select',
				'default' => 'due',
				'options' => array(
					'due'  => __( 'Use "Due" cost - cost after taxes', 'woocommerce-shipping-canada-post' ),
					'base' => __( 'Use "Base" cost - base cost for the rate', 'woocommerce-shipping-canada-post' ),
				),
			),
			'enable_flat_rates'   => array(
				'title'       => __( 'Flat Rates', 'woocommerce-shipping-canada-post' ),
				'type'        => 'select',
				'default'     => 'no',
				'options'     => array(
					'yes' => __( 'Yes - Enable flat rate services', 'woocommerce-shipping-canada-post' ),
					'no'  => __( 'No - Disable flat rate services', 'woocommerce-shipping-canada-post' ),
				),
				'description' => sprintf(
					// translators: %1$s is an anchor element opener, %2$s is a anchor element closer, %3$s is a break element.
					__( 'Enable this option to offer shipping using %1$sCanada Post Flat Rate services%2$s. %3$sItems will be packed into flat rate boxes and the customer will be offered a single rate from these.', 'woocommerce-shipping-canada-post' ),
					'<a href="https://www.canadapost-postescanada.ca/cpc/en/personal/sending/parcels/flat-rate-box.page" target="_blank">',
					'</a>',
					'<br>'
				),
				'desc_tip'    => false,
			),
			'flat_rate_title'     => array(
				'title'       => __( 'Flat Rate Title', 'woocommerce-shipping-canada-post' ),
				'type'        => 'text',
				'description' => '',
				'default'     => '',
				'placeholder' => __( 'Flat Rate', 'woocommerce-shipping-canada-post' ),
			),
			'lettermail'          => array(
				'title'       => __( 'Lettermail Rates', 'woocommerce-shipping-canada-post' ),
				'type'        => 'multiselect',
				'class'       => 'chosen_select',
				'css'         => 'width: 450px;',
				'default'     => '',
				'description' => __( 'Lettermail rates are hardcoded into the plugin as they are not part of the API.', 'woocommerce-shipping-canada-post' ),
				'options'     => array(
					'standard'   => __( 'Standard Lettermail', 'woocommerce-shipping-canada-post' ),
					'registered' => __( 'Registered Lettermail', 'woocommerce-shipping-canada-post' ),
				),
				'desc_tip'    => true,
			),
			'options'             => array(
				'title'       => __( 'Additional Options', 'woothemes' ),
				'type'        => 'multiselect',
				'class'       => 'chosen_select',
				'css'         => 'width: 450px;',
				'default'     => '',
				'options'     => array(
					'COV'  => __( 'Coverage', 'woocommerce-shipping-canada-post' ),
					'PA18' => __( 'Proof of Age Required', 'woocommerce-shipping-canada-post' ),
					'SO'   => __( 'Signature', 'woocommerce-shipping-canada-post' ),
					'CYL'  => __( 'Mailing Tube', 'woocommerce-shipping-canada-post' ),
				),
				'description' => __( 'Additional options affect all rates.', 'woocommerce-shipping-canada-post' ),
				'desc_tip'    => true,
			),
			'show_delivery_time'  => array(
				'title'       => __( 'Delivery time', 'woocommerce-shipping-canada-post' ),
				'label'       => __( 'Show estimated delivery time next to rate name.', 'woocommerce-shipping-canada-post' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'description' => __( 'Rates will be labelled, for example, Rate Name - approx. 2 days.', 'woocommerce-shipping-canada-post' ),
			),
			'delivery_time_delay' => array(
				'title'       => __( 'Delivery Delay', 'woocommerce-shipping-canada-post' ),
				'type'        => 'text',
				'default'     => '1',
				'description' => __( 'If showing delivery time, allow for a delay. e.g. a delay of 1 day for a method which ships in 2 days would be labelled: approx. 2-3 days', 'woocommerce-shipping-canada-post' ),
				'desc_tip'    => true,
			),
			'packing_method'      => array(
				'title'   => __( 'Parcel Packing Method', 'woocommerce-shipping-canada-post' ),
				'type'    => 'select',
				'default' => '',
				'class'   => 'packing_method',
				'options' => array(
					'per_item'    => __( 'Default: Pack items individually', 'woocommerce-shipping-canada-post' ),
					'weight'      => __( 'Weight of all items', 'woocommerce-shipping-canada-post' ),
					'box_packing' => __( 'Recommended: Pack into boxes with weights and dimensions', 'woocommerce-shipping-canada-post' ),
				),
			),
			'max_weight'          => array(
				'title'             => __( 'Maximum weight', 'woocommerce-shipping-canada-post' ),
				'type'              => 'number',
				'default'           => '30',
				'description'       => __( 'Maximum weight per package.', 'woocommerce-shipping-canada-post' ),
				'custom_attributes' => array(
					'step' => '0.01',
					'min'  => '0',
				),
			),
			'boxes'               => array(
				'type' => 'box_packing',
			),
			'offer_rates'         => array(
				'title'       => __( 'Offer Rates', 'woocommerce-shipping-canada-post' ),
				'type'        => 'select',
				'description' => '',
				'default'     => 'all',
				'options'     => array(
					'all'      => __( 'Offer the customer all returned rates', 'woocommerce-shipping-canada-post' ),
					'cheapest' => __( 'Offer the customer the cheapest rate only, anonymously', 'woocommerce-shipping-canada-post' ),
				),
			),
			'services'            => array(
				'type' => 'services',
			),
		);

		$this->form_fields = array(
			'box_packer_library' => array(
				'title'       => __( 'Box Packer Library', 'woocommerce-shipping-canada-post' ),
				'type'        => 'select',
				'default'     => '',
				'class'       => 'box_packer_library',
				'options'     => array(
					'original' => __( 'Speed Packer', 'woocommerce-shipping-canada-post' ),
					'dvdoug'   => __( 'Accurate Packer', 'woocommerce-shipping-canada-post' ),
				),
				'description' => __( 'Speed Packer packs items by volume, Accurate Packer check each dimension allowing more accurate packing but might be slow when you sell items in large quantities.', 'woocommerce-shipping-canada-post' ),
			),
			'debug' => array(
				'title'       => __( 'Debug Mode', 'woocommerce-shipping-canada-post' ),
				'label'       => __( 'Enable debug mode', 'woocommerce-shipping-canada-post' ),
				'type'        => 'checkbox',
				'default'     => 'no',
				'description' => __( 'Enable debug mode to show debugging information on the cart/checkout.', 'woocommerce-shipping-canada-post' ),
			),
		);
	}

	/**
	 * Convert XML string to an array.
	 *
	 * @param string $xml_string XML string to convert.
	 * @return array Converted array.
	 */
	private function convert_xml_string_to_array( $xml_string ) {
		$xml_object = simplexml_load_string( $xml_string );

		if ( false === $xml_object ) {
			return array();
		}

		$json  = wp_json_encode( $xml_object );
		$array = json_decode( $json, true );

		return is_array( $array ) ? $array : array( $xml_string );
	}

	/**
	 * Calculate shipping rate function.
	 *
	 * @param array $package Cart package.
	 * @return void
	 */
	public function calculate_shipping( $package = array() ) {
		$this->rates      = array();
		$headers          = $this->get_request_header();
		$package_requests = $this->get_package_requests( $package );

		libxml_use_internal_errors( true );

		$this->debug( __( 'Canada Post debug mode is enabled. To hide these messages, turn off debug mode in the settings.', 'woocommerce-shipping-canada-post' ) );

		// Abort the request if country field is empty or postcode is empty with destination country to US or CA.
		// Canada Post API need to have those informations to run the request.
		if ( empty( $package['destination']['country'] ) || ( in_array( $package['destination']['country'], array( 'CA', 'US' ) ) && empty( $package['destination']['postcode'] ) ) ) {
			$this->debug( __( 'Postal code or country field is empty. Canada Post shipping calculation is aborted.', 'woocommerce-shipping-canada-post' ) );

			return;
		}

		if ( $package_requests ) {

			foreach ( $package_requests as $key => $package_request ) {

				$request  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
				$request .= '<mailing-scenario xmlns="http://www.canadapost.ca/ws/ship/rate">' . "\n";
				$request .= $package_request;
				$request .= $this->get_request( $package );
				$request .= '</mailing-scenario>' . "\n";

				$transient       = 'cp_quote_' . md5( $request );
				$cached_response = get_transient( $transient );

				if ( false !== $cached_response ) {

					$response = $cached_response;

					$request_data = $this->convert_xml_string_to_array( $request );
					$this->debug( 'Canada Post CACHED REQUEST:', $request_data );
					$this->debug( 'Canada Post CACHED RESPONSE:', $this->convert_xml_string_to_array( $response ) );

				} else {

					$response = wp_remote_post(
						$this->endpoint,
						array(
							'method'    => 'POST',
							'timeout'   => 70,
							'sslverify' => 0,
							'headers'   => $headers,
							'body'      => $request,
						)
					);

					// Do not process if it's WP_Error.
					if ( is_wp_error( $response ) ) {
						$this->debug( $response->get_error_message() );
						continue;
					}

					$response = wp_remote_retrieve_body( $response );

					// Store result in case the request is made again.
					if ( ! empty( $response ) ) {

						/**
						 * If the API returns an error message, we don't want to cache
						 * that. So, we'll load the string response into a SimpleXMLElement
						 * object and check that object for a message property, which will
						 * indicate that the response was an error message.
						 */
						$response_xml = simplexml_load_string( $response );
						if ( $response_xml instanceof SimpleXMLElement && ! property_exists( $response_xml, 'message' ) ) {
							set_transient( $transient, $response, YEAR_IN_SECONDS );
						}
					} else {
						$response = '';
					}

					$request_data = $this->convert_xml_string_to_array( $request );
					$this->debug( 'Canada Post REQUEST:', $request_data );
					$this->debug( 'Canada Post RESPONSE:', $this->convert_xml_string_to_array( $response ) );
				}

				$xml = simplexml_load_string( '<root>' . preg_replace( '/<\?xml.*\?>/', '', $response ) . '</root>' );

				if ( ! $xml ) {
					$this->debug( 'Failed loading XML' );
				}

				if ( $xml instanceof SimpleXMLElement && $xml->{'price-quotes'} ) {
					$price_quotes = $xml->{'price-quotes'}->children( 'http://www.canadapost.ca/ws/ship/rate' );

					if ( $price_quotes->{'price-quote'} ) {
						foreach ( $price_quotes as $quote ) {

							$rate_code = strval( $quote->{'service-code'} );
							$rate_id   = $this->id . ':' . $rate_code;

							/**
							 * Get 'base' or 'due' cost.
							 *
							 * 'due' cost includes all taxes and adjustments.
							 * 'base' cost excludes taxes and adjustments.
							 */
							$rate_cost = (float) $quote->{'price-details'}->{$this->use_cost};

							// Add adjustments to 'base' cost so it will include all costs except taxes.
							if ( 'base' === $this->use_cost ) {
								$adjustments = (array) $quote->{'price-details'}->{'adjustments'};
								if ( $adjustments && isset( $adjustments['adjustment'] ) ) {
									// If there are multiple <adjustment> nodes, they are packed into an array.
									if ( is_array( $adjustments['adjustment'] ) ) {
										foreach ( $adjustments['adjustment'] as $adjustment ) {
											if ( ! empty( $adjustment->{'adjustment-cost'} ) ) {
												$rate_cost += $adjustment->{'adjustment-cost'};
											}
										}
									} else { // Handle single adjustment case.
										$adjustment = $adjustments['adjustment'];
										if ( ! empty( $adjustment->{'adjustment-cost'} ) ) {
											$rate_cost += $adjustment->{'adjustment-cost'};
										}
									}
								}
							}

							if ( ! empty( $this->custom_services[ $rate_code ]['name'] ) ) {
								$rate_name = (string) $this->custom_services[ $rate_code ]['name'];
							} elseif ( ! empty( $this->services[ $rate_code ] ) ) {
								$rate_name = (string) $this->services[ $rate_code ];
							} else {
								$rate_name = (string) $quote->{'service-name'};
							}

							// Get time.
							if ( $this->show_delivery_time ) {
								$transmit_time = $quote->{'service-standard'}->{'expected-transit-time'};

								if ( $transmit_time ) {
									if ( $this->delivery_time_delay ) {
										// translators: %1$d is a transmit time, %2$d is a transmit time with delivery time delay.
										$rate_name = $rate_name . ' - ' . sprintf( __( 'approx. %1$d&ndash;%2$d days', 'woocommerce-shipping-canada-post' ), $transmit_time, $transmit_time + $this->delivery_time_delay );
									} else {
										// translators: %d is a transmit time.
										$rate_name = $rate_name . ' - ' . sprintf( _n( 'approx. %d day', 'approx. %d days', $transmit_time, 'woocommerce-shipping-canada-post' ), $transmit_time );
									}
								}
							}

							$this->prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost );
						}
					}
				} else {
					// No rates.
					$debug_info = esc_html__( 'Invalid request. Ensure a valid shipping destination has been chosen on the cart/checkout page.', 'woocommerce-shipping-canada-post' );
					$this->debug( $debug_info );
					wc_add_notice( $debug_info, 'notice' );
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

		if ( ! empty( $this->lettermail ) ) {
			if ( in_array( 'standard', $this->lettermail, true ) ) {
				$lettermail_rate = $this->calculate_lettermail_rate( $package );

				if ( $lettermail_rate ) {
					$this->found_rates[ $lettermail_rate['id'] ] = $lettermail_rate;
				}
			}
			if ( in_array( 'registered', $this->lettermail, true ) ) {
				$lettermail_rate = $this->calculate_lettermail_rate( $package, true );

				if ( $lettermail_rate ) {
					$this->found_rates[ $lettermail_rate['id'] ] = $lettermail_rate;
				}
			}
		}

		// Maybe calculate flat rate box costs.
		if ( 'yes' === $this->enable_flat_rates && 'CA' === $package['destination']['country'] ) {
			$flat_rate = $this->calculate_flat_rate( $package );
			if ( $flat_rate ) {
				$this->found_rates[ $flat_rate['id'] ] = $flat_rate;
			}
		}

		// Add package request dimensions and weight to the rate meta data if available.
		$meta_data = $this->maybe_get_packed_box_details( $package_requests );
		if ( $this->found_rates && $meta_data ) {
			foreach ( $this->found_rates as $key => $rate ) {
				if ( ! empty( $meta_data ) ) {
					$this->found_rates[ $key ]['meta_data'] = $meta_data;
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

				$cheapest_rate = '';

				foreach ( $this->found_rates as $key => $rate ) {
					if ( ! $cheapest_rate || $cheapest_rate['cost'] > $rate['cost'] ) {
						$cheapest_rate = $rate;
					}
				}

				$cheapest_rate['label'] = $this->title;

				$this->add_rate( $cheapest_rate );

			}
		}
	}

	/**
	 * Calculate lettermail rates.
	 *
	 * @param array   $package Cart package.
	 * @param boolean $registered Registered service or not.
	 *
	 * @return array
	 */
	public function calculate_lettermail_rate( $package, $registered = false ) {
		$this->debug( 'Calculating Lettermail Rates' );

		$lettermail_cost = 0;

		$boxpack = ( new WC_Boxpack( 'cm', 'kg', $this->box_packer_library ) )->get_packer();

		if ( 'CA' === $package['destination']['country'] ) {
			$this->lettermail_boxes = $this->ca_lettermail_boxes;
		} elseif ( 'US' === $package['destination']['country'] ) {
			$this->lettermail_boxes = $this->us_lettermail_boxes;
		} else {
			$this->lettermail_boxes = $this->int_lettermail_boxes;
		}

		// Define boxes.
		foreach ( $this->lettermail_boxes as $service_code => $box ) {

			$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'] );
			$newbox->set_max_weight( $box['weight'] );
			$newbox->set_id( $service_code );

			$this->debug( 'Adding box: ' . $service_code . ' ' . $box['name'] . ' - ' . $box['length'] . 'x' . $box['width'] . 'x' . $box['height'] );
		}

		// Add items.
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				continue;
			}

			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() && $values['data']->get_weight() ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

			} else {
				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( sprintf( __( 'Product # is missing dimensions! Using 1x1x1.', 'woocommerce-shipping-canada-post' ), $item_id ), 'error' );
				} else {
					WC()->add_error( sprintf( __( 'Product # is missing dimensions! Using 1x1x1.', 'woocommerce-shipping-canada-post' ), $item_id ) );
				}

				$dimensions = array( 1, 1, 1 );
			}

			$boxpack->add_item(
				wc_get_dimension( $dimensions[2], 'cm' ),
				wc_get_dimension( $dimensions[1], 'cm' ),
				wc_get_dimension( $dimensions[0], 'cm' ),
				wc_get_weight( $values['data']->get_weight(), 'kg' ),
				$values['data']->get_price(),
				array(),
				$values['quantity']
			);
		}

		// Pack it.
		$boxpack->pack();

		// Get packages.
		$flat_packages = $boxpack->get_packages();

		if ( $flat_packages ) {
			foreach ( $flat_packages as $flat_package ) {
				if ( isset( $this->lettermail_boxes[ $flat_package->id ] ) ) {

					$this->debug( 'Packed ' . $flat_package->id );

					$this_box = $this->lettermail_boxes[ $flat_package->id ];
					$costs    = $this_box['costs'];

					foreach ( $costs as $weight => $cost ) {
						if ( $flat_package->weight <= $weight ) {
							$lettermail_cost += $cost;
							break;
						}
					}

					if ( isset( $this->options['COV'] ) && 'CA' === $package['destination']['country'] ) {
						$lettermail_cost += $this->ca_additional_liability_coverage * min( round( $this_box['value'] / 100, 2 ), 50 );
					}

					if ( $registered && 'CA' === $package['destination']['country'] ) {
						$lettermail_cost += $this->ca_registered;
					} elseif ( $registered ) {
						$lettermail_cost += $this->int_registered;
					}
				} else {
					return; // No match.
				}
			}

			return array(
				'id'    => $this->id . ':' . ( $registered ? 'registered_' : '' ) . 'lettermail',
				'label' => __( 'Lettermail&trade;', 'woocommerce-shipping-canada-post' ) . ( $registered ? ' (' . __( 'Registered', 'woocommerce-shipping-canada-post' ) . ')' : '' ),
				'cost'  => $lettermail_cost,
				'sort'  => -1,
			);
		}
	}

	/**
	 * Calculate flat rate.
	 *
	 * @param array $package Cart package.
	 *
	 * @return array
	 */
	public function calculate_flat_rate( $package ) {
		$this->debug( 'Calculating Flat Rates' );

		$flat_rate_cost = 0;

		$boxpack = ( new WC_Boxpack( 'cm', 'kg', $this->box_packer_library ) )->get_packer();

		// Define boxes.
		foreach ( $this->ca_flat_rate_boxes as $service_code => $box ) {

			$newbox = $boxpack->add_box( $box['length'], $box['width'], $box['height'] );
			$newbox->set_max_weight( $box['max_weight'] );
			$newbox->set_id( $service_code );

			$this->debug( 'Adding box: ' . $box['name'] . ' - ' . $box['length'] . 'x' . $box['width'] . 'x' . $box['height'] );
		}

		// Add items.
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				continue;
			}

			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() && $values['data']->get_weight() ) {

				$dimensions = array(
					$values['data']->get_length(),
					$values['data']->get_height(),
					$values['data']->get_width(),
				);
			} else {
				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( sprintf( __( 'Product # is missing dimensions! Using 1x1x1.', 'woocommerce-shipping-canada-post' ), $item_id ), 'error' );
				} else {
					WC()->add_error( sprintf( __( 'Product # is missing dimensions! Using 1x1x1.', 'woocommerce-shipping-canada-post' ), $item_id ) );
				}

				$dimensions = array( 1, 1, 1 );
			}

			$boxpack->add_item(
				wc_get_dimension( $dimensions[2], 'cm' ),
				wc_get_dimension( $dimensions[1], 'cm' ),
				wc_get_dimension( $dimensions[0], 'cm' ),
				wc_get_weight( $values['data']->get_weight(), 'kg' ),
				$values['data']->get_price(),
				array(),
				$values['quantity']
			);
		}

		// Pack it.
		$boxpack->pack();

		// Get packages.
		$flat_packages = $boxpack->get_packages();

		if ( $flat_packages ) {
			foreach ( $flat_packages as $flat_package ) {
				if ( isset( $this->ca_flat_rate_boxes[ $flat_package->id ] ) ) {

					$this->debug( 'Packed ' . $this->ca_flat_rate_boxes[ $flat_package->id ]['name'] );

					$this_box        = $this->ca_flat_rate_boxes[ $flat_package->id ];
					$flat_rate_cost += $this_box['cost'];
				} else {
					return; // No match.
				}
			}

			return array(
				'id'    => $this->id . ':flat_rate',
				'label' => $this->get_option( 'flat_rate_title', 'Flat Rate' ),
				'cost'  => $flat_rate_cost,
				'sort'  => - 1,
			);
		}
	}

	/**
	 * Prepare the shipping rate.
	 *
	 * @param mixed $rate_code Rate code.
	 * @param mixed $rate_id Rate ID.
	 * @param mixed $rate_name Rate name.
	 * @param mixed $rate_cost Rate cost.
	 *
	 * @return void
	 */
	private function prepare_rate( $rate_code, $rate_id, $rate_name, $rate_cost ) {

		// Cost adjustment in %.
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment_percent'] ) ) {
			$sign = substr( $this->custom_services[ $rate_code ]['adjustment_percent'], 0, 1 );

			if ( '-' === $sign ) {
				$rate_cost = $rate_cost - ( $rate_cost * ( floatval( substr( $this->custom_services[ $rate_code ]['adjustment_percent'], 1 ) ) / 100 ) );
			} else {
				$rate_cost = $rate_cost + ( $rate_cost * ( floatval( $this->custom_services[ $rate_code ]['adjustment_percent'] ) / 100 ) );
			}

			if ( $rate_cost < 0 ) {
				$rate_cost = 0;
			}
		}

		// Cost adjustment.
		if ( ! empty( $this->custom_services[ $rate_code ]['adjustment'] ) ) {
			$sign = substr( $this->custom_services[ $rate_code ]['adjustment'], 0, 1 );

			if ( '-' === $sign ) {
				$rate_cost = $rate_cost - floatval( substr( $this->custom_services[ $rate_code ]['adjustment'], 1 ) );
			} else {
				$rate_cost = $rate_cost + floatval( $this->custom_services[ $rate_code ]['adjustment'] );
			}

			if ( $rate_cost < 0 ) {
				$rate_cost = 0;
			}
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

		$this->found_rates[ $rate_id ] = array(
			'id'       => $rate_id,
			'label'    => $rate_name,
			'cost'     => $rate_cost,
			'sort'     => $sort,
			'packages' => $packages,
		);
	}

	/**
	 * Sorting the rates.
	 *
	 * @param mixed $a First rate to be sorted.
	 * @param mixed $b Second rate to be sorted.
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
	 * Get request header for calling API.
	 *
	 * @return array
	 */
	private function get_request_header() {
		return array(
			'Accept'          => 'application/vnd.cpc.ship.rate+xml',
			'Content-Type'    => 'application/vnd.cpc.ship.rate+xml',
			'Authorization'   => 'Basic ' . base64_encode( $this->username . ':' . $this->password ), // phpcs:ignore --- need to encode using base64
			'Accept-language' => 'en-CA',
			'Platform-id'     => $this->platform_id,
		);
	}

	/**
	 * Generate request from cart package.
	 *
	 * @param mixed $package Cart package.
	 *
	 * @return string XML request.
	 */
	private function get_request( $package ) {

		/**
		 * Filter to modify the postal code origin.
		 *
		 * @param string Origin postal code.
		 * @param array  $package Cart package.
		 * @param WC_Shipping_Canada_Post Canada post object.
		 *
		 * @since 2.1.3
		 */
		$request = ' <origin-postal-code>' . apply_filters( 'wc_shipping_canada_post_origin', str_replace( ' ', '', strtoupper( $this->origin ) ), $package, $this ) . '</origin-postal-code>' . "\n";

		if ( 'counter' === $this->quote_type ) {
			$request .= '	<quote-type>' . $this->quote_type . '</quote-type>' . "\n";
		} else {
			$request .= '	<customer-number>' . $this->customer_number . '</customer-number>' . "\n";

			if ( $this->contract_id ) {
				$request .= '	<contract-id>' . $this->contract_id . '</contract-id>' . "\n";
			}
		}

		$request .= '	<destination>' . "\n";

		// The destination.
		switch ( $package['destination']['country'] ) {
			case 'CA':
				$request .= '		<domestic>' . "\n";
				$request .= '			<postal-code>' . $this->ca_postal_code_replacement( $package['destination']['postcode'] ) . '</postal-code>' . "\n";
				$request .= '		</domestic>' . "\n";
				break;
			case 'US':
				// Remove space and asterisk in postal code for compatibility with Apple Pay and Google Pay.
				$request .= '		<united-states>' . "\n";
				$request .= '			<zip-code>' . str_replace( array( ' ', '*' ), '', strtoupper( $package['destination']['postcode'] ) ) . '</zip-code>' . "\n";
				$request .= '		</united-states>' . "\n";
				break;
			default:
				$request .= '		<international>' . "\n";
				$request .= '			<country-code>' . $package['destination']['country'] . '</country-code>' . "\n";
				$request .= '		</international>' . "\n";
				break;
		}

		$request .= '	</destination>' . "\n";
		// End destination.

		return $request;
	}

	/**
	 * Since Apple/Google Pay are using asterisk (*) to replace some of the postal code for security reason,
	 * we will need to replace the missing/replaced character.
	 * And Canada postal code is using an alphabet-numeric-alphabet-numeric-alphabet-numeric (A1A1A1) format
	 * in order to be validated.
	 * e.g. we need to change M4C *** into M4C 1A1 in order to be valid.
	 * This method will try to replace the missing/replaced character in canada post code into something valid.
	 *
	 * @param string $postcode Canada postal code.
	 */
	public function ca_postal_code_replacement( $postcode ) {
		// Initial check to make sure it has 6 digits.
		if ( 6 !== strlen( str_replace( ' ', '', strtoupper( $postcode ) ) ) ) {
			return strtoupper( $postcode ); // Bail as it's already invalid.
		}

		// Bail immediately if asterisk not found.
		// It means that the site doesn't use Apple Pay or Google Pay.
		if ( false === strpos( $postcode, '*' ) ) {
			return str_replace( ' ', '', strtoupper( $postcode ) );
		}

		// Canada postal code is using an alphabet-numeric-alphabet-numeric-alphabet-numeric (A1A1A1) format in order to be validated.
		// We need to replace the asterisk (*) with something valid.
		$stripped_postcode = str_replace( array( ' ', '*' ), '', strtoupper( $postcode ) ); // We need to remove the blank space and asterisk from the postalcode.
		$missing_chars     = 6 - strlen( $stripped_postcode ); // Count the missing chars.

		// If the postcode doesn't have 6 characters, add the missing characters with the correct format.
		return ( $missing_chars <= 0 ) ? $stripped_postcode : $stripped_postcode . substr( 'A1A1A1', ( -1 * $missing_chars ) );
	}

	/**
	 * Get request based on the packing method.
	 *
	 * @param array $package Cart package.
	 *
	 * @return string
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
	 * Generate XML per package item.
	 *
	 * @param array $package Cart package.
	 *
	 * @return string
	 */
	private function per_item_shipping( $package ) {
		$requests = array();

		// Get weight of order.
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d is cart item ID.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-canada-post' ), $item_id ) );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				// translators: %d is cart item ID.
				$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'woocommerce-shipping-canada-post' ), $item_id ) );
				return;
			}

			$weight = round( wc_get_weight( $values['data']->get_weight(), 'kg' ), 3 );
			// See issue #21.
			if ( $weight < 0.01 ) {
				$weight = 0.01;
			}

			$parcel  = '<parcel-characteristics>' . "\n";
			$parcel .= '	<weight>' . $weight . '</weight>' . "\n";

			if ( $values['data']->get_length() && $values['data']->get_height() && $values['data']->get_width() ) {

				$dimensions = array( $values['data']->get_length(), $values['data']->get_height(), $values['data']->get_width() );

				sort( $dimensions );

				$parcel .= '	<dimensions>' . "\n";
				$parcel .= '		<height>' . round( wc_get_dimension( $dimensions[0], 'cm' ), 1 ) . '</height>' . "\n";
				$parcel .= '		<width>' . round( wc_get_dimension( $dimensions[1], 'cm' ), 1 ) . '</width>' . "\n";
				$parcel .= '		<length>' . round( wc_get_dimension( $dimensions[2], 'cm' ), 1 ) . '</length>' . "\n";
				$parcel .= '	</dimensions>' . "\n";
			}

			$parcel .= '</parcel-characteristics>' . "\n";

			// Package options.
			if ( ! empty( $this->options ) ) {
				$option_request = '';
				foreach ( $this->options as $option ) {
					if ( 'CA' !== $package['destination']['country'] && 'PA18' === $option ) {
						continue;
					}
					$option_request .= '		<option>' . "\n";
					$option_request .= '			<option-code>' . $option . '</option-code>' . "\n";
					if ( 'COV' === $option ) {
						$option_request .= '			<option-amount>' . round( $values['data']->get_price(), 2 ) . '</option-amount>' . "\n";
					}
					$option_request .= '		</option>' . "\n";
				}
				if ( $option_request ) {
					$parcel .= '<options>' . "\n" . $option_request . '</options>' . "\n";
				}
			}

			for ( $i = 0; $i < $values['quantity']; $i++ ) {
				$requests[] = $parcel;
			}
		}

		return $requests;
	}

	/**
	 * Generate XML for "weight only" packing method.
	 *
	 * @param array $package Cart package.
	 *
	 * @return string
	 */
	private function weight_only_shipping( $package ) {
		$requests = array();
		$weight   = 0;
		$value    = 0;

		// Get weight of order.
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d is cart item ID.
				$this->debug( sprintf( __( 'Product #%d is missing virtual. Aborting.', 'woocommerce-shipping-canada-post' ), $item_id ) );
				continue;
			}

			if ( ! $values['data']->get_weight() ) {
				// translators: %d is cart item ID.
				$this->debug( sprintf( __( 'Product #%d is missing weight. Aborting.', 'woocommerce-shipping-canada-post' ), $item_id ) );
				return;
			}

			$weight += wc_get_weight( $values['data']->get_weight(), 'kg' ) * $values['quantity'];
			$value  += $values['data']->get_price() * $values['quantity'];
		}

		// Adjust weight if it's below the minimum threshold.
		if ( $weight < 0.01 ) {
			$weight = 0.01;
		}

		// Package options.
		$options_request = '';

		// Package options.
		if ( ! empty( $this->options ) ) {
			$option_request = '';
			foreach ( $this->options as $option ) {
				if ( 'CA' !== $package['destination']['country'] && 'PA18' === $option ) {
					continue;
				}
				$option_request .= '		<option>' . "\n";
				$option_request .= '			<option-code>' . $option . '</option-code>' . "\n";
				if ( 'COV' === $option ) {
					$option_request .= '			<option-amount>' . round( $value, 2 ) . '</option-amount>' . "\n";
				}
				$option_request .= '		</option>' . "\n";
			}
			if ( $option_request ) {
				$options_request .= '<options>' . "\n" . $option_request . '</options>' . "\n";
			}
		}

		if ( $weight > $this->max_weight ) {
			$this->debug( __( 'Package is too heavy. Splitting.', 'woocommerce-shipping-canada-post' ) );

			for ( $i = 0; $i < ( $weight / $this->max_weight ); $i++ ) {
				$request    = '<parcel-characteristics>' . "\n";
				$request   .= '	<weight>' . round( $this->max_weight, 3 ) . '</weight>' . "\n";
				$request   .= '</parcel-characteristics>' . "\n";
				$request   .= $options_request;
				$requests[] = $request;
			}

			if ( ( $weight % $this->max_weight ) ) {
				$request    = '<parcel-characteristics>' . "\n";
				$request   .= '	<weight>' . round( $weight % $this->max_weight, 3 ) . '</weight>' . "\n";
				$request   .= '</parcel-characteristics>' . "\n";
				$request   .= $options_request;
				$requests[] = $request;
			}
		} else {
			$request    = '<parcel-characteristics>' . "\n";
			$request   .= '	<weight>' . round( $weight, 3 ) . '</weight>' . "\n";
			$request   .= '</parcel-characteristics>' . "\n";
			$request   .= $options_request;
			$requests[] = $request;
		}

		return $requests;
	}

	/**
	 * Generate XML when using Box packing method.
	 *
	 * @param array $package Cart package.
	 *
	 * @return string
	 */
	private function box_shipping( $package ) {
		$requests = array();

		$boxpack = ( new WC_Boxpack( 'cm', 'kg', $this->box_packer_library ) )->get_packer();

		// Define boxes.
		foreach ( $this->boxes as $box ) {

			$newbox = $boxpack->add_box( $box['outer_length'], $box['outer_width'], $box['outer_height'], $box['box_weight'] );

			$newbox->set_inner_dimensions( $box['inner_length'], $box['inner_width'], $box['inner_height'] );

			if ( $box['max_weight'] ) {
				$newbox->set_max_weight( $box['max_weight'] );
			}

			if ( $box['name'] ) {
				$newbox->set_id( $box['name'] );
			}
		}

		// Add items.
		foreach ( $package['contents'] as $item_id => $values ) {

			if ( ! $values['data']->needs_shipping() ) {
				// translators: %d is cart item ID.
				$this->debug( sprintf( __( 'Product #%d is virtual. Skipping.', 'woocommerce-shipping-canada-post' ), $item_id ) );
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
				$this->debug( __( 'Canada post error: Product missing dimensions or weight. Abort box packing calculation.', 'woocommerce-shipping-canada-post' ) );
				return;
			}
		}

		// Pack it.
		$boxpack->pack();

		// Get packages.
		$flat_packages = $boxpack->get_packages();

		foreach ( $flat_packages as $flat_package ) {

			$dimensions = array( $flat_package->length, $flat_package->width, $flat_package->height );

			sort( $dimensions );

			$weight = round( $flat_package->weight, 3 );
			// See issue #21.
			if ( $weight < 0.01 ) {
				$weight = 0.01;
			}

			$request  = '<parcel-characteristics>' . "\n";
			$request .= '	<weight>' . $weight . '</weight>' . "\n";
			$request .= '	<dimensions>' . "\n";
			$request .= '		<height>' . round( $dimensions[0], 1 ) . '</height>' . "\n";
			$request .= '		<width>' . round( $dimensions[1], 1 ) . '</width>' . "\n";
			$request .= '		<length>' . round( $dimensions[2], 1 ) . '</length>' . "\n";
			$request .= '	</dimensions>' . "\n";
			$request .= '</parcel-characteristics>' . "\n";

			// Package options.
			if ( ! empty( $this->options ) ) {
				$option_request = '';
				foreach ( $this->options as $option ) {
					if ( 'CA' !== $package['destination']['country'] && 'PA18' === $option ) {
						continue;
					}
					$option_request .= '		<option>' . "\n";
					$option_request .= '			<option-code>' . $option . '</option-code>' . "\n";
					if ( 'COV' === $option ) {
						$option_request .= '			<option-amount>' . round( $flat_package->value, 2 ) . '</option-amount>' . "\n";
					}
					$option_request .= '		</option>' . "\n";
				}
				if ( $option_request ) {
					$request .= '<options>' . "\n" . $option_request . '</options>' . "\n";
				}
			}

			$requests[] = $request;
		}

		return $requests;
	}

	/**
	 * Extract the packed box dimensions and weights if available and return in an array.
	 *
	 * @param array $package_requests The package requests.
	 *
	 * @return array|false
	 */
	private function maybe_get_packed_box_details( $package_requests ) {
		// Package request must be an array.
		if ( ! is_array( $package_requests ) ) {
			return false;
		}

		$meta_data = array();
		foreach ( $package_requests as $index => $request ) {
			try {
				++$index;

				/*
				 * Wrap the request XML in another tag (<request-data>) to avoid an error being
				 * thrown when using "Additional Options" such as "signature" in the
				 * Canada Post shipping zone settings.
				 */
				$request_data = (array) simplexml_load_string( '<request-data>' . $request . '</request-data>' );

				// Make sure the parcel-characteristics element is set and that it is an instance of SimpleXMLElement.
				if ( ! isset( $request_data['parcel-characteristics'] ) || ! $request_data['parcel-characteristics'] instanceof SimpleXMLElement ) {
					continue;
				}

				$parcel = $request_data['parcel-characteristics'];

				// Make sure we have length, width, height and weight, or don't add to the meta data.
				if ( empty( $parcel->dimensions->length ) || empty( $parcel->dimensions->width ) || empty( $parcel->dimensions->height ) || empty( $parcel->weight ) ) {
					continue;
				}

				// translators: %1$s is package index.
				$package_number = sprintf( __( 'Package %1$s', 'woocommerce-shipping-canada-post' ), $index );

				// Combine length, width, height into a string.
				$dimensions = implode(
					' x ',
					array(
						$parcel->dimensions->length,
						$parcel->dimensions->width,
						$parcel->dimensions->height,
					)
				);

				// translators: %1$s is a box dimension, %2$s is a parcel weight.
				$meta_data[ $package_number ] = sprintf( __( '%1$s (cm) %2$skg', 'woocommerce-shipping-canada-post' ), $dimensions, $parcel->weight );
			} catch ( Exception $e ) {
				$this->debug( 'Failed generating SimpleXMLElement from package request XML string.' );
			}
		}

		return ! empty( $meta_data ) ? $meta_data : false;
	}

	/**
	 * If the box packer library option is not yet set and there are existing
	 * Canada Post shipping method instances, we can assume that this is not a
	 * new/fresh installation of the Canada Post plugin,
	 * so we should default to 'original'
	 *
	 * If the box packer library option is not set and there are no
	 * Canada Post shipping method instances, then this is likely a new
	 * installation of the Canada Post plugin,
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
	 * Helper method to get the number of Canada Post method instances.
	 *
	 * @return int The number of Canada Post method instances
	 */
	public function instance_count(): int {
		global $wpdb;

		// phpcs:ignore --- Need to use WPDB::get_var() to count the existing Canada Post in the shipping zone
		return absint( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'canada_post'" ) );
	}

	/**
	 * Helper method to check if there are existing Canada Post method instances.
	 *
	 * @return bool
	 */
	public function instances_exist(): bool {
		return $this->instance_count() > 0;
	}
}
