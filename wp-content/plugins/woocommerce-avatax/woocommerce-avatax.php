<?php
/**
 * Plugin Name: Avalara AvaTax
 * Plugin URI: http://www.woocommerce.com/products/woocommerce-avatax/
 * Description: Seamless integration with Avalara's tax calculation and management services.
 * Author: Avalara
 * Author URI: https://avlr.co/3I8hQgs
 * Version: 3.0.0
 * Text Domain: woocommerce-avatax
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2016-2022, SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package   AvaTax
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 *
 * Woo: 1389326:57077a4b28ba71cacf692bcf4a1a7f60
 * WC requires at least: 3.9.4
 * WC tested up to: 8.9.3
 */

defined( 'ABSPATH' ) or exit;

use Automattic\WooCommerce\StoreApi\StoreApi;
use Automattic\WooCommerce\StoreApi\Schemas\ExtendSchema;
use Automattic\WooCommerce\StoreApi\Exceptions\RouteException;


/**
 * The plugin loader class.
 *
 * @since 1.7.0
 */
class WC_AvaTax_Loader {


	/** minimum PHP version required by this plugin */
	const MINIMUM_PHP_VERSION = '7.4';

	/** minimum WordPress version required by this plugin */
	const MINIMUM_WP_VERSION = '5.6';

	/** minimum WooCommerce version required by this plugin */
	const MINIMUM_WC_VERSION = '3.9.4';

	/** SkyVerge plugin framework version used by this plugin */
	const FRAMEWORK_VERSION = '5.10.14';

	/** the plugin name, for displaying notices */
	const PLUGIN_NAME = 'Avalara AvaTax';


	/** @var WC_AvaTax_Loader single instance of this class */
	protected static $instance;

	/** @var array the admin notices to add */
	protected $notices = array();


	/**
	 * Constructs the class.
	 *
	 * @since 1.7.0
	 */
	protected function __construct() {

		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init', array( $this, 'check_environment' ) );
		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );

		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		/**
		 * Declaring compatibility for HPOS.
		 *
		 * @since 2.5.0
		 */
		add_action( 'before_woocommerce_init', array($this, 'declare_hpos_compatibility'));

		// if the environment check fails, initialize the plugin
		if ( $this->is_environment_compatible() ) {

			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}

		/**
		 * Include the dependencies needed to instantiate the block.
		 */
		add_action('woocommerce_blocks_loaded', function() {
			require_once __DIR__ . '/src/blocks/IntegrationFiles/avatax-blocks-integration.php';
			require_once __DIR__ . '/src/blocks/IntegrationFiles/avatax-VAT-validation-integration.php';
			require_once __DIR__ . '/src/blocks/IntegrationFiles/avatax-checkout-messages-integration.php';
			add_action(
				'woocommerce_blocks_checkout_block_registration',
				function( $integration_registry ) {
					$integration_registry->register( new VAT_Blocks_Integration() );
					$integration_registry->register( new Checkout_Messages_Blocks_Integration() );
					if( 'yes' === get_option( 'wc_avatax_enable_ecm', 'no' ))
					{
						$integration_registry->register( new Avatax_Blocks_Integration() );
					}
					if( 'yes' === get_option( 'wc_avatax_enable_address_validation', 'no' ))
					{
						$tax_based_on = get_option( 'woocommerce_tax_based_on', '' );
						require_once __DIR__ . '/src/blocks/IntegrationFiles/avatax-shipping-address-validation-integration.php';
						$integration_registry->register( new Shipping_Address_Blocks_Integration() );
						if ('billing' === $tax_based_on) {
							require_once __DIR__ . '/src/blocks/IntegrationFiles/avatax-billing-address-validation-integration.php';
							$integration_registry->register( new Billing_Address_Blocks_Integration() );
						}
					}
				}
			);
		});
		add_filter( 'http_request_host_is_external', array($this,'allow_external_host'), 1, 3 );

		register_deactivation_hook( __FILE__, array( $this, 'deactivating_avatax') );

		add_action('woocommerce_blocks_enqueue_checkout_block_scripts_before', function() {
			$exposure_zones = wc_avatax()->get_exposure_zones();
			include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details-popup.php' );
		
		});

		add_action(
			'woocommerce_blocks_loaded',
			function() {
				woocommerce_store_api_register_update_callback(
					[
						'namespace' => 'address-validation-block',
						'callback'  => function( $data ) {
							if ( 'VAT_ID' === $data['action'] ) {
								WC()->session->set( 'wc_avatax_vat_id', $data['vatid'] );
								wc_avatax()->wc_avatax_utilities()->update_order_meta( $data['orderid'], '_billing_wc_avatax_vat_id', $data['vatid']);
								return;
							}
							$response = wc_avatax()->get_api()->validate_address( array(
								'address_1' => $data['address']['address_1'],
								'address_2' => $data['address']['address_2'],
								'city'      => $data['address']['city'] ,
								'state'     => $data['address']['state'] ,
								'country'   => $data['address']['country'],
								'postcode'  => $data['address']['postcode'],
							) );

							if($response->has_errors())
							{
								throw new RouteException( 'avatax_address_validation_error', wc_avatax()->wc_avatax_utilities()->get_address_error_messages($response) , 404 );
							}
							else {
								$address = $response->get_normalized_address();
								if ( 'shipping_validate_address' === $data['action'] ) {
									WC()->customer->set_shipping_address( $address['address_1'] );
									WC()->customer->set_shipping_address_2( $address['address_2'] );
									WC()->customer->set_shipping_city( $address['city'] );
									WC()->customer->set_shipping_state( $address['state'] );
									WC()->customer->set_shipping_country( $address['country'] );
									WC()->customer->set_shipping_postcode( $address['postcode'] );
								}
								else {
									WC()->customer->set_billing_address( $address['address_1'] );
									WC()->customer->set_billing_address_2( $address['address_2'] );
									WC()->customer->set_billing_city( $address['city'] );
									WC()->customer->set_billing_state( $address['state'] );
									WC()->customer->set_billing_country( $address['country'] );
									WC()->customer->set_billing_postcode( $address['postcode'] );
								}
							}
							return $response;
						},
					]
				);
			}
		);
	}
	/**
	 * Function to call when plugin is getting deactivated.
	 *
	 * @since 2.8.0
	 */
	public function deactivating_avatax($network_deactivating = false ){
		wc_avatax()->log("Deactivating the plugin");
		wc_avatax()->wc_avatax_utilities()->disconnect_avatax();
		wc_avatax()->wc_avatax_elr_utilities()->disconnect_elr();
	}

	//Added this function to allow FQA environment url api.qa.avalara.io as external host
	function allow_external_host( $allow, $host, $url ) {
		$allow = false; 
		if ( $host == 'api.qa.avalara.io' || $host == 'config.connector.avalara.com' || $host == 'config.connector.sbx.avalara.com' || $host = 'ccs.gamma.qa.us-west-2.aws.avalara.io') {
			$allow = true; 
		}
		return $allow; 
	}

	/**
	 * Cloning instances is forbidden due to singleton pattern.
	 *
	 * @since 1.7.0
	 */
	public function __clone() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot clone instances of %s.', get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Unserializing instances is forbidden due to singleton pattern.
	 *
	 * @since 1.7.0
	 */
	public function __wakeup() {

		_doing_it_wrong( __FUNCTION__, sprintf( 'You cannot unserialize instances of %s.', get_class( $this ) ), '1.0.0' );
	}


	/**
	 * Initializes the plugin.
	 *
	 * @since 1.7.0
	 */
	public function init_plugin() {

		if ( ! $this->plugins_compatible() ) {
			return;
		}

		$this->load_framework();

		// load the main plugin class
		require_once( plugin_dir_path( __FILE__ ) . 'class-wc-avatax.php' );

		// fire it up!
		wc_avatax();

		// Create elr table with default data and custom fields in the database while install
		wc_avatax()->wc_avatax_elr_utilities()->set_elr_default_schema();
	}


	/**
	 * Loads the base framework classes.
	 *
	 * @since 1.7.0
	 */
	protected function load_framework() {

		if ( ! class_exists( '\\SkyVerge\\WooCommerce\\PluginFramework\\' . $this->get_framework_version_namespace() . '\\SV_WC_Plugin' ) ) {
			require_once( plugin_dir_path( __FILE__ ) . 'vendor/skyverge/wc-plugin-framework/woocommerce/class-sv-wc-plugin.php' );
		}
	}


	/**
	 * Gets the framework version in namespace form.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	protected function get_framework_version_namespace() {

		return 'v' . str_replace( '.', '_', $this->get_framework_version() );
	}


	/**
	 * Gets the framework version used by this plugin.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	protected function get_framework_version() {

		return self::FRAMEWORK_VERSION;
	}


	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * Based on http://wptavern.com/how-to-prevent-wordpress-plugins-from-activating-on-sites-with-incompatible-hosting-environments
	 *
	 * @since 1.7.0
	 */
	public function activation_check() {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( self::PLUGIN_NAME . ' could not be activated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @since 1.7.0
	 */
	public function check_environment() {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', self::PLUGIN_NAME . ' has been deactivated. ' . $this->get_environment_message() );
		}
	}


	/**
	 * Adds notices for out-of-date WordPress and/or WooCommerce versions.
	 *
	 * @since 1.7.0
	 */
	public function add_plugin_notices() {

		if ( ! $this->is_wp_compatible() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WP_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! $this->is_wc_compatible() ) {

			$this->add_admin_notice( 'update_woocommerce', 'error', sprintf(
				'%1$s requires WooCommerce version %2$s or higher. Please %3$supdate WooCommerce%4$s to the latest version, or %5$sdownload the minimum required version &raquo;%6$s',
				'<strong>' . self::PLUGIN_NAME . '</strong>',
				self::MINIMUM_WC_VERSION,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>',
				'<a href="' . esc_url( 'https://downloads.wordpress.org/plugin/woocommerce.' . self::MINIMUM_WC_VERSION . '.zip' ) . '">', '</a>'
			) );
		}
	}


	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {

		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}


	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		if ( ! self::MINIMUM_WP_VERSION ) {
			return true;
		}

		return version_compare( get_bloginfo( 'version' ), self::MINIMUM_WP_VERSION, '>=' );
	}


	/**
	 * Determines if the WooCommerce compatible.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {

		if ( ! self::MINIMUM_WC_VERSION ) {
			return true;
		}

		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, self::MINIMUM_WC_VERSION, '>=' );
	}


	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.7.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.7.0
	 *
	 * @param string $slug the notice message slug
	 * @param string $class the notice message class
	 * @param string $message the notice message body
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message,
		);
	}


	/**
	 * Displays any admin notices added with \WC_AvaTax_Loader::add_admin_notice()
	 *
	 * @since 1.7.0
	 */
	public function admin_notices() {

		foreach ( (array) $this->notices as $notice_key => $notice ) :

			?>
			<div class="<?php echo esc_attr( $notice['class'] ); ?>">
				<p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
			</div>
			<?php

		endforeach;
	}


	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * Override this method to add checks for more than just the PHP version.
	 *
	 * @since 1.7.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {

		return version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '>=' );
	}


	/**
	 * Gets the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.7.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', self::MINIMUM_PHP_VERSION, PHP_VERSION );
	}


	/**
	 * Gets the main \WC_AvaTax_Loader instance.
	 *
	 * Ensures only one instance can be loaded.
	 *
	 * @since 1.7.0
	 *
	 * @return \WC_AvaTax_Loader
	 */
	public static function instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Declares compatibility for HPOS.
	 *
	 *
	 * @since 2.5.0
	 *
	 */
	public function declare_hpos_compatibility(){
		if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__, true );
		}
	}

}

// fire it up!
WC_AvaTax_Loader::instance();
