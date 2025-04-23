<?php
/**
 * WooCommerce AvaTax
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce AvaTax to newer
 * versions in the future. If you wish to customize WooCommerce AvaTax for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-avatax/
 *
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

namespace SkyVerge\WooCommerce\AvaTax;

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * Plugin lifecycle handler.
 *
 * @since 1.7.0
 *
 * @method \WC_AvaTax get_plugin()
 */
class Lifecycle extends Framework\Plugin\Lifecycle {


	/**
	 * Lifecycle constructor.
	 *
	 * @since 1.7.1
	 *
	 * @param \WC_AvaTax $plugin
	 */
	public function __construct( $plugin ) {

		parent::__construct( $plugin );
		$this->upgrade_versions = [
			'1.3.0',
			'1.7.2',
			'2.6.0',
			'2.7.0',
			'2.7.1',
			'2.7.2',
			'3.0.0'
		];
	}


	/**
	 * Installs default settings & pages.
	 *
	 * @since 1.7.0
	 */
	protected function install() {

		require_once( $this->get_plugin()->get_plugin_path() . '/src/admin/class-wc-avatax-settings.php' );
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/class-wc-avatax-elr-settings.php' );

		// include settings so we can install defaults
		$settings = new \WC_AvaTax_Settings();

		// install default settings for each section
		foreach ( $settings->get_settings() as $setting ) {

			if ( isset( $setting['default'], $setting['id'] ) ) {
				update_option( $setting['id'], $setting['default'] );
			}
		}

		$this->maybe_migrate();
	}

	/**
	 * Handles upgrade routines.
	 *
	 * @since 1.7.0
	 *
	 * @param string $installed_version currently installed version
	 */
	public function upgrade( $installed_version ) {

		// forces logging enabled during upgrade routines
		add_filter( 'wc_avatax_logging_enabled', [ $this, 'enable_logging' ] );

		parent::upgrade( $installed_version );

		// restore normal logging behavior
		remove_filter( 'wc_avatax_logging_enabled', [ $this, 'enable_logging' ] );
	}


	/**
	 * Force enables logging while performing upgrade routines.
	 *
	 * @internal
	 *
	 * @since 1.7.1
	 *
	 * @return bool
	 */
	public function enable_logging() {

		return true;
	}


	/**
	 * Determines if the legacy AvaTax plugin's settings exist and migrates them if so.
	 *
	 * @since 1.7.0
	 */
	protected function maybe_migrate() {
		global $wpdb;

		if ( 'yes' === get_option( 'wc_avatax_migrated' ) ) {
			return;
		}

		$this->get_plugin()->log( 'Starting migration from legacy extension' );

		/**
		 * Process settings
		 */

		$legacy_settings = get_option( 'woocommerce_avatax_settings', [] );

		if ( ! empty( $legacy_settings ) ) {

			$settings = [
				'wc_avatax_origin_address' => [],
			];

			// These options can be copied to ours directly
			$direct_options = [
				'account'              => 'wc_avatax_api_account_number',
				'license'              => 'wc_avatax_api_license_key',
				'company_code'         => 'wc_avatax_company_code',
				'default_tax_code'     => 'wc_avatax_default_product_code',
				'default_freight_code' => 'wc_avatax_shipping_code'
			];

			foreach ( $legacy_settings as $name => $value ) {

				switch ( $name ) {

					case 'avalara_url':
						$settings['wc_avatax_api_environment'] = ( Framework\SV_WC_Helper::str_starts_with( $value, 'https://development' ) ) ? 'development' : 'production';
					break;

					case 'disable_tax_calc':

						if ( 'yes' !== $value ) {
							$settings['wc_avatax_enable_tax_calculation'] = 'yes';

							// Enable WC taxes as the legacy plugin required them to be disabled
							update_option( 'woocommerce_calc_taxes', 'yes' );
						}

					break;

					case 'disable_addr_validation':
						$settings['wc_avatax_enable_address_validation'] = ( 'yes' !== $value ) ? 'yes' : 'no';
					break;

					case 'commit_action':
						$settings['wc_avatax_commit'] = ( 'c' === $value ) ? 'yes' : 'no';
					break;

					case 'enable_exempt_id':
						$settings['wc_avatax_enable_vat'] = ( 'b' === $value ) ? 'yes' : 'no';
					break;

					// Rebuild the origin address
					case 'origin_street':
						$settings['wc_avatax_origin_address']['address_1'] = $value;
					break;

					case 'origin_city':
						$settings['wc_avatax_origin_address']['city'] = $value;
					break;

					case 'origin_state':
						$settings['wc_avatax_origin_address']['state'] = $value;
					break;

					case 'origin_zip':
						$settings['wc_avatax_origin_address']['postcode'] = $value;
					break;

					case 'origin_country':
						$settings['wc_avatax_origin_address']['country'] = $value;
					break;

					default:

						if ( isset( $direct_options[ $name ] ) ) {
							$settings[ $direct_options[ $name ] ] = $value;
						}

					break;
				}
			}

			// Update the settings with the migrated values
			foreach ( $settings as $name => $value ) {

				if ( '' !== $value ) {
					update_option( $name, $value );
				}
			}

			// Remove the legacy settings
			delete_option( 'woocommerce_avatax_settings' );
		}

		/**
		 * Process orders
		 */
		if (  wc_avatax()->wc_avatax_utilities()->is_hpos_enabled() ) {
			$this->MigrateOrderWithHPOS();
		}
		else{
			$this->MigrateLegacyOrders();
		}

		// Migrate the product tax codes
		// legacy key: _taxnow_taxcode
		// new key: _wc_avatax_code
		$wpdb->update( $wpdb->postmeta,
			[
				'meta_key' => '_wc_avatax_code',
			],
			[
				'meta_key' => '_taxnow_taxcode',
			]
		);

		// Migration complete
		update_option( 'wc_avatax_migrated', 'yes' );

		$this->get_plugin()->log( 'Migration complete' );
	}

	/**
	 * Processes orders with HPOS.
	 *
	 * @since 2.5.0
	 */
	protected function MigrateOrderWithHPOS()
	{
		global $wp_version;
		// Get order that have been processed by AvaTax but haven't been migrated yet
		$orders = wc_get_orders( [
			'post_type'   => 'shop_order',
			'post_status' => 'any',
			'meta_query'  => [
				'relation' => 'AND',
				[
					'key'     => '_taxnow_avalaracommit',
					'compare' => 'EXISTS',
				],
				$wp_version >= 3.9 ?
				[
					'key'     => '_wc_avatax_status',
					'compare' => 'NOT EXISTS',
				] : 
				[
					'key'     => '_wc_avatax_status',
					'value'   => 'The tops of UPS trucks are not brown :( (bug #23268)',
					'compare' => 'NOT EXISTS',
				],
			],
		] );

		// Convert to our custom order statuses
		foreach ( $orders as $order ) {

			$order = wc_get_order( $order->ID );

			$order_id = $order->get_id();

			$order->add_meta_data('_wc_avatax_status', 'posted');

			if ( 'return' === get_post_meta( $order_id, '_taxnow_avalaracommit', true ) ) {
				$order->add_meta_data( '_wc_avatax_status', 'refunded' );
			}

			if ( $order->has_status( 'cancelled' ) ) {
				$order->add_meta_data( '_wc_avatax_status', 'voided' );
			}

			// Don't process this one again
			add_post_meta('_wc_avatax_status', 'migrated' );
		}
	}

	/**
	 * Process legacy orders.
	 *
	 * @since 1.7.0
	 */
	protected function MigrateLegacyOrders()
	{
		// Get order that have been processed by AvaTax but haven't been migrated yet
		$legacy_orders = get_posts( [
			'post_type'   => 'shop_order',
			'post_status' => 'any',
			'meta_query'  => [
				'relation' => 'AND',
				[
					'key'     => '_taxnow_avalaracommit',
					'compare' => 'EXISTS',
				],
				[
					'key'     => '_wc_avatax_status',
					'value'   => 'The tops of UPS trucks are not brown :( (bug #23268)',
					'compare' => 'NOT EXISTS',
				],
			],
		] );

		// Convert to our custom order statuses
		foreach ( $legacy_orders as $order ) {

			$order = wc_get_order( $order->ID );

			$order_id = $order->get_id();

			add_post_meta( $order_id, '_wc_avatax_status', 'posted' );

			if ( 'return' === get_post_meta( $order_id, '_taxnow_avalaracommit', true ) ) {
				add_post_meta( $order_id, '_wc_avatax_status', 'refunded' );
			}

			if ( $order->has_status( 'cancelled' ) ) {
				add_post_meta( $order_id, '_wc_avatax_status', 'voided' );
			}

			// Don't process this one again
			add_post_meta( $order_id, '_wc_avatax_status', 'migrated' );
		}
	}

	/**
	 * Updates to version 1.3.0
	 *
	 * @since 1.7.1
	 */
	protected function upgrade_to_1_3_0() {

		if (  'yes' === get_option( 'wc_avatax_migrated' )  ) {

			// for users we've previously migrated, delete the old settings
			delete_option( 'woocommerce_avatax_settings' );
		}
	}


	/**
	 * Updates to version 1.7.2
	 *
	 * @since 1.7.2
	 */
	protected function upgrade_to_1_7_2() {

		// previously this setting was just for intl customers, and always "yes" for US customers
		update_option( 'wc_avatax_calculate_on_cart_international_customers', 'yes' === get_option( 'wc_avatax_calculate_on_cart' ) ? 'yes' : 'no' );

		// always update to "yes", as it now controls US customers
		update_option( 'wc_avatax_calculate_on_cart', 'yes' );
	}

	/**
	 * Updates to version 2.6.0
	 *
	 * @since 2.6.0
	 */
	protected function upgrade_to_2_6_0() {

		// previously this setting was just for intl customers, and always "yes" for US customers
		if('force' === get_option( 'wc_avatax_calculate_on_cart' ))
		{
			$this->get_plugin()->log("Updating Cart Calculations from: ". get_option( 'wc_avatax_calculate_on_cart' ) );
			update_option( 'wc_avatax_calculate_on_cart',  'yes' );
			$this->get_plugin()->log("Updated Cart Calculations to: ". get_option( 'wc_avatax_calculate_on_cart' ) );
		}

	}
	/**
	 * Updates to version 2.7.0
	 *
	 * @since 2.7.0
	 */
	protected function upgrade_to_2_7_0() {
		wc_avatax()->log("upgrade_to_2_7_0");

		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			delete_transient('wc_avatax_supported_countries_list');
			global $wpdb;
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_avatax_%'" );
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_wc_avatax_%'" );
			wc_avatax()->get_company_id();

			$supported_states = wc_avatax()->get_landed_cost_handler()->get_supported_states('US');
			delete_option('wc_avatax_record_calculations');
			
			if (!wc_avatax()->wc_avatax_utilities()->is_colorado_nexus_present())
			{
				delete_option('wc_avatax_enable_co_rdf');
			}

			if(wc_avatax()->wc_avatax_utilities()->has_nexus_outside_countries())
			{
				delete_option('wc_avatax_enable_address_validation');
				delete_option('wc_avatax_require_address_validation');
				delete_option('wc_avatax_address_validation_countries');
			}
			else {
				delete_option('wc_avatax_enable_cross_border_classification');
				delete_option('wc_avatax_enable_vat');
				delete_option('wc_avatax_hs_api_password');
				delete_option('wc_avatax_hs_api_username');
				delete_option('wc_avatax_landed_cost_products_pending_sync');
				delete_option('wc_avatax_landed_cost_syncing_state');

			}
			if(!wc_avatax()->wc_avatax_utilities()->has_ecm_subscription())
			{
				delete_option('wc_avatax_ecm_autovalidation');
				delete_option('wc_avatax_ecm_countries');
				delete_option('wc_avatax_enable_ecm');
			}
		}


	}

	/**
	 * Updates to version 2.7.1
	 *
	 * @since 2.7.1
	 */
	protected function upgrade_to_2_7_1() {
		// Removing dependency for wc_avatax_commit and instead using wc_avatax_record_calculations setting
		update_option('wc_avatax_record_calculations',get_option( 'wc_avatax_commit', 'yes'));
		delete_option('wc_avatax_commit');
	}

	/**
	 * Updates to version 2.7.2
	 *
	 * @since 2.7.2
	 */
	protected function upgrade_to_2_7_2() {
		// Removing dependency for wc_avatax_enable_co_rdf.
		delete_option('wc_avatax_enable_co_rdf');
	}

	/**
	 * Updates to version 3.0.0
	 *
	 * @since 3.0.0
	 */
	protected function upgrade_to_3_0_0() {
		//Sending configuration settings to CCS in AvaTax
		if(get_option('wc_avatax_company_code'))
		{
			wc_avatax()->get_company_details('companyByCode');
		}
		wc_avatax()->wc_avatax_utilities()->send_avatax_settings_to_cup('POST');
		
		if(wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled()){
			wc_avatax()->wc_avatax_elr_utilities()->register_or_update_elr_tenant('PUT');
		}
	}

	/**
	 * Updates to version 2.11.0
	 *
	 * @since 2.11.0
	 */
	protected function upgrade_to_2_11_0() {
		// Create elr table with default data and custom fields in the database while upgrade
		wc_avatax()->wc_avatax_elr_utilities()->set_elr_default_schema();
	}
}
