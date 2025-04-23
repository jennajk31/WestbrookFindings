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

use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Set up the admin settings.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Settings {

	/** @var string $id The settings page ID */
	protected $id = 'avatax';


	/**
	 * Constructs the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->add_hooks();

		//wc_avatax()->logger()->test();
	}

	/**
	 * Adds action and filter hooks.
	 *
	 * @since 1.13.0
	 */
	private function add_hooks() {

		// add the settings section to the WooCommerce Tax tab
		add_filter( 'woocommerce_get_sections_tax', [ $this, 'add_settings_section' ] );

		// output the settings
		add_action( 'woocommerce_get_settings_tax', [ $this, 'add_settings' ] );

		//display a custom license key field with landing message
		add_action( 'woocommerce_admin_field_wc_avatax_api_license_key_type', [ $this, 'display_api_license_key_field' ] );
		add_action( 'woocommerce_admin_field_wc_avatax_api_environment_type', [ $this, 'display_api_environment_field' ] );
		add_action( 'woocommerce_admin_field_wc_avatax_product_sync', [ $this, 'display_product_sync_field' ] );
		// save the settings
		add_action( 'woocommerce_settings_save_tax', [ $this, 'save_settings' ] );

		// clears the license key cache when the license key changes
		add_action( 'update_option_wc_avatax_api_license_key', [ $this, 'prune_account_number_cache' ], 9, 2 );

		// clears the API account number cache when the account number changes
		add_action( 'update_option_wc_avatax_api_account_number', [ $this, 'prune_account_number_cache' ], 9, 2 );
		add_action( 'update_option_wc_avatax_api_environment', [ $this, 'prune_account_number_cache' ], 9, 2 );

		// trigger a new sync when the cross-border countries list updates
		add_action( 'update_option_wc_avatax_api_product_countries_sync', [ $this, 'handle_countries_sync_update' ], 10, 2 );
	}

	/**
	 * Add the AvaTax section to the Tax tab.
	 *
	 * @since 1.0.0
	 * @param array $sections The existing Tax sections.
	 * @return array $sections The new Tax sections.
	 */
	public function add_settings_section( $sections ) {

		$sections[ $this->id ] = __( 'AvaTax', 'woocommerce-avatax' );

		return $sections;
	}

	/**
	 * Get the API settings.
	 *
	 * @since 1.0.0
	 * @return array $settings The API settings.
	 */
	public function get_api_settings() {

		$connection_status = get_transient( 'wc_avatax_connection_status' );

		$settings = array(

			array(
				'name' => __( 'Connect to Avalara', 'woocommerce-avatax' ),
				'type' => 'title'
			),
			array(
				'id'      => 'wc_avatax_api_environment',
				'name'    => __( 'Choose your account type', 'woocommerce-avatax' ),
				'options' => array(
					'production'  => __( 'Production', 'woocommerce-avatax' ),
					'development' => __( 'Development', 'woocommerce-avatax' ),
				),
				'desc'=>'Select your Production or Development account.',
				'default' => 'production',
				'type'    => 'wc_avatax_api_environment_type',
			),

			array(
				'id'                => 'wc_avatax_api_account_number',
				'name'              => __( 'Account ID', 'woocommerce-avatax' ),
				'type'              => 'text',
				'class'             => 'wc-avatax-connection-field',
				'css'               => 'min-width:300px;',
				'custom_attributes' => array(
					'data-wc-avatax-connection-status' => $connection_status,
				),
			),

			array(
				'id'                => 'wc_avatax_api_license_key',
				'name'              => __( 'License Key/Password', 'woocommerce-avatax' ),
				'type'              => 'wc_avatax_api_license_key_type',
				'class'             => 'wc-avatax-connection-field',
				'css'               => 'min-width:300px;',
				'custom_attributes' => array(
					'data-wc-avatax-connection-status' => $connection_status,
				),
			),

			array(
				'type' => 'sectionend',
			),
		);

		/**
		 * Filter the API settings.
		 *
		 * @since 1.0.0
		 * @param array $settings The API settings.
		 */
		return (array) apply_filters( 'woocommerce_get_settings_' . $this->id . '_api', $settings );
	}


	/**
	 * Get all of the combined settings.
	 *
	 * @since 1.0.0
	 * @return array $settings The combined settings.
	 */
	public function get_settings() {

		$settings = $this->get_api_settings();
		if ( $this->get_plugin()->check_api() ) {
			$settings = array_merge(
				$settings,
				$this->get_product_sync_settings()
			);
		}
		/**
		 * Filter the combined settings.
		 *
		 * @since 1.0.0
		 * @param array $settings The combined settings.
		 */
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Replace core Tax settings with our own when the AvaTax section is being viewed.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function add_settings( $settings ) {

		global $current_section;

		// Output the general settings
		if ( $this->id == $current_section ) {

			// Always display the API and ELR settings
			$settings = array_merge(
				$this->get_api_settings(),
			);
			if ( $this->get_plugin()->check_api() ) {
				$settings = array_merge(
					$settings,
					$this->get_product_sync_settings()
				);
			}
		}

		return $settings;
	}

	/**
	 * Saves the settings.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @global string $current_section The current settings section.
	 */
	public function save_settings() {
		global $current_section;
		$is_connecting = true;
		if ( $this->id === $current_section ) {

			// if the API credentials were good at last check, save the settings
			if ( 'connected' === get_transient( 'wc_avatax_connection_status' ) ) {
				$is_connecting = false;
			}
			else {
				$is_connecting = true;
			}

			// always save the API, product sync & misc settings
			$this->save_fields( $this->get_api_settings() );
			if($is_connecting)
			{
				wc_avatax()->get_company_details('defaultCompany');
			}
			$this->save_fields( $this->get_product_sync_settings() );

			// reset the API status transient
			delete_transient( 'wc_avatax_connection_status' );
			delete_transient( 'wc_avatax_subscribed' );

			// TODO: we should do the same for Item Classification API as well {IT 2022-01-11}
			// check the API again and display an error for bad credentials
			if ( $this->get_plugin()->check_api(false) ) {

				if($is_connecting){
					// save the settings
					add_action( 'woocommerce_settings_saved', [ $this, 'save_default_fields' ] );
				}
				$this->save_origin_address_field();
			}
			else
			{
				if($is_connecting){
					add_action( 'woocommerce_settings_saved', [ $this, 'clear_default_fields' ] );
				}
			}
			
			// stop cross-border product sync in case credentials were removed
			if ( ! $this->get_plugin()->has_hs_api_credentials_set() ) {
				$this->get_plugin()->get_landed_cost_sync_handler()->stop_syncing();
			}
		}
	}
	/**
	 * Save default fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_default_fields() {
		wc_avatax()->wc_avatax_utilities()->save_default_fields();
		$this->send_avatax_settings_to_cup();

		//Refresh logger instance:
		if(wc_avatax()->refresh_logger()){
			//Logging Connect event
			wc_avatax()->logger()->log_event("Connect", "save_default_fields", "Connecting to AvaTax");
		}
		else
		{
			wc_avatax()->log("log_event - Connect, function name - save_default_fields, message - Connecting to AvaTax");
		}
	}

	/**
	 * Clears default fields.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function clear_default_fields() {
		wc_avatax()->wc_avatax_utilities()->clear_default_fields();

		if(!$this->get_plugin()->has_api_credentials_set()){
			WC_Admin_Settings::add_error( __( 'Enter Account ID and License key.', 'woocommerce-avatax' ) );
		}
	}

	/**
	 * Save the settings fields.
	 *
	 * This is a simple wrapper for `WC_Admin_Settings::save_fields` to intercept our custom "address"
	 * field type for special handling. All other fields are saved as usual. This is being improved in WC 2.4+
	 * but for now this is easiest for older versions.
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields the settings fields to save
	 */
	private function save_fields( $fields ) {

		// Loop through each setting and look for an address field
		foreach ( $fields as $key => $field ) {

			// If found, save it our way and remove it from the settings to save the WooCommerce way
			if ( isset( $field['id'], $field['type'] ) && 'wc_avatax_address' === $field['type'] ) {
				$this->save_address_field( $field );
				unset( $fields[ $key ] );
			}
		}

		WC_Admin_Settings::save_fields( $fields );
	}

	/**
	 * Save the custom address field.
	 *
	 * @since 1.0.0
	 * @param array $field The field definition.
	 */
	private function save_address_field( $field ) {

		$address = isset( $_POST[ $field['id'] ] ) ? wp_unslash( $_POST[ $field['id'] ] ) : array();

		/**
		 * Filter the address values before the final save.
		 *
		 * @since 1.0.0
		 * @param array $address {
		 *     The address values.
		 *
		 * @type string @address_1 The street address.
		 * @type string @city      The city name.
		 * @type string @state     The state.
		 * @type string @country   The country code.
		 * @type string @postcode  The postal code.
		 * }
		 */
		$address = (array) apply_filters( 'wc_avatax_save_address_field', $address );

		$address = array_map( 'wc_clean', $address );

		if ( ! empty( $address ) ) {
			update_option( $field['id'], $address );
		}
	}

	/**
	 * Gets the product sync settings fields.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	protected function get_product_sync_settings() : array {

		$settings = [];
		if (wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api() && wc_avatax()->wc_avatax_utilities()->has_nexus_outside_countries(["US"]) ) {
			$settings = [
				[
					'type' => 'title',
					'name' => __( 'Transactions outside the US (HS code classification)', 'woocommerce-avatax' ),
				],
				[
					'id'       => 'wc_avatax_enable_cross_border_classification',
					'name'     => __( 'Enable sync', 'woocommerce-avatax' ),
					'desc_tip'     => __( "Calculate customs duties for orders outside the U.S. In Avalara, line items must have a fully qualified tariff code to calculate customs duties for the destination country.", 'woocommerce-avatax' ),
					'default'  => 'no',
					'type'     => 'checkbox',
					'class'	   => 'checkbox-toggle',
				],
				[
					'id'       => 'wc_avatax_hs_api_username',
					'name'     => __( 'Avalara username', 'woocommerce-avatax' ),
					'type'     => 'text',
					'css'      => 'min-width:300px;',
					'desc' => __( 'The username used for your Avalara login.', 'woocommerce-avatax' ),
				],
				[
					'id'       => 'wc_avatax_hs_api_password',
					'name'     => __( 'Avalara password', 'woocommerce-avatax' ),
					'type'     => 'password',
					'css'      => 'min-width:300px;',
					'desc' => __( 'The password used for your Avalara login.', 'woocommerce-avatax' ),
				],
				[
					'id'                => 'wc_avatax_api_product_countries_sync',
					'name'              => __( 'Select countries to assign HS codes', 'woocommerce-avatax' ),
					'type'              => 'multiselect',
					'class'             => 'wc-enhanced-select',
					'options'           => $this->get_normalized_supported_countries_options(),
					'custom_attributes' => [ 'multiple' => 'multiple' ],
					'desc'              => __( 'Select countries outside U.S. to calculate customs duties. Avalara automatically assigns HS codes for the countries you select.', 'woocommerce-avatax' ),
				],
				[
					'id'       => 'wc_avatax_api_product_sync',
					'name'     => __( 'Sync products with Avalara', 'woocommerce-avatax' ),
					'type'     => 'wc_avatax_product_sync'
				],
				[ 'type' => 'sectionend' ],
			];
		}

		/**
		 * Filters the product sync settings fields.
		 *
		 * @since 1.13.0
		 *
		 * @param array $settings product sync fields
		 */
		return (array) apply_filters( 'woocommerce_get_settings_' . $this->id . '_product_sync', $settings );
	}

	/**
	 * Displays the API License field with landing message and connect, disconnect, test connection button.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	public function display_api_license_key_field( $options ) {

		$id           = $options['id'];
		$label        = $options['title'];
		$value		  = $options['value'];
		$company_name = get_option('wc_avatax_company_name');
		$env 		  = get_option('wc_avatax_api_environment', 'production');
		$message = sprintf( __( 'If you are new to Avalara, %1$ssign up%2$s to generate your account information.', 'woocommerce-avatax' ),
			'<a href="https://www.avalara.com/us/en/get-started.html" target="_blank">',
			'</a>'
		);
		$application_id = wc_avatax()::CONNECTOR_ID;
		$website_id = get_option("wc_avatax_website_id");
		$edit_config_link = ($env === 'production' ? ('https://integrations.avalara.com/#/advance-configuration-settings/a/' . $application_id . '/c/' . $website_id) : ('https://sandbox.integrations.avalara.com/#/advance-configuration-settings/a/' . $application_id . '/c/' . $website_id));

		$connected_message = __('<p>Your WooCommerce store is connected to Company <b>'.$company_name.'</b> in Avalara. <br /><br /> Go <a href="'. ($env == 'production' ? 'https://integrations.avalara.com/' : 'https://sandbox.integrations.avalara.com/') . '" target="_blank"> back to Avalara</a> to finish setting up the tax profile for Company <b>'.$company_name.'</b>.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><span class="info-icon">i</span>', 'woocommerce-avatax');
		require_once( $this->get_plugin()->get_plugin_path() . '/src/admin/views/html-field-api-license-key.php' );
	}

	/**	
	 * Displays the API environment field.
	 *
	 * @since 2.7.0
	 */
	public function display_api_environment_field( $options ) {
		$value		  = get_option("wc_avatax_api_environment");
		require_once( $this->get_plugin()->get_plugin_path() . '/src/admin/views/html-field-api-environment.php' );
	}

	/**	
	 * Gets the Nexus enabled countries.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	private function get_supported_countries() : array {
		$supported_countries =[];
		$supported_countries =  get_transient( 'wc_avatax_supported_countries_list');
		if(empty( $supported_countries ))
		{
			$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
			$supported_countries = $this->get_plugin()->get_landed_cost_handler()->get_supported_countries();
			set_transient( 'wc_avatax_supported_countries_list', $supported_countries, $cache_expiration );
		}
		return $supported_countries;
	}

	/**	
	 * Fetches ECM enabled Countries from Nexus.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	public function get_ecm_enabled_countries(): array {
		$supported_countries =  get_transient( 'wc_avatax_supported_countries_list');
		if($supported_countries == null)
		{
			$supported_countries = $this->get_supported_countries();
		}
		$ecm_enabled_countries = [
			(in_array('US', $supported_countries) ? 'US' : ''),
			(in_array('CA', $supported_countries)  ? 'CA' : '')
		];
		array_filter($ecm_enabled_countries);
		return $ecm_enabled_countries;
	}

	/**
	 * Displays a custom product sync settings field.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param array $options
	 */
	public function display_product_sync_field( $options ) {

		$description = WC_Admin_Settings::get_field_description( $options );

		$id           = $options['id'];
		$label        = $options['title'];
		$tooltip_html = $description['tooltip_html'];
		$description  = $description['description'];
		$disabled     = ! $this->is_cross_border_product_sync_connection_allowed();
		$syncing      = $this->get_plugin()->get_landed_cost_sync_handler()->is_syncing_active();

		require_once( $this->get_plugin()->get_plugin_path() . '/src/admin/views/product-sync.php' );
	}


	/**
	 * Gets a list of countries that’s ready to be used as a setting option.
	 *
	 * @since 1.13.0
	 *
	 * @return array
	 */
	private function get_normalized_supported_countries_options() : array {
		$supported_countries  = array_map( 'mb_strtoupper', $this->get_supported_countries() );

		$normalized_countries = ( new WC_Countries() )->get_countries();

		foreach ( $normalized_countries as $country_code => $country_name ) {
			if ( ! in_array( $country_code, $supported_countries, true ) ) {
				unset( $normalized_countries[ $country_code ] );
			}
		}

		return $normalized_countries;
	}

	/**
	 * Gets the Cross Border Product Sync field description.
	 *
	 * @since 1.13.0
	 *
	 * @return string
	 */
	private function get_cross_border_product_sync_description() : string {


		$sync_error_items = '';

		if ( ! $this->get_plugin()->has_hs_api_credentials_set() ) {
			$sync_error_items .= '<li>' . __( 'Please fill in your Avalara username and password', 'woocommerce-avatax' ) . '</li>';
		}

		if ( ! $this->get_plugin()->get_landed_cost_handler()->has_countries_for_product_sync() ) {
			$sync_error_items .= '<li>' . __( 'Please select at least one supported country.', 'woocommerce-avatax' ) . '</li>';
		}

		return ! empty( $sync_error_items ) ? sprintf(
			/* translators: Placeholders: %1$s - opening <strong> HTML tag, %2$s - closing </strong> HTML tag, %3$s - unordered HTML list of sync errors */
			__( '%1$sUnable to sync. Please correct the following errors:%2$s %3$s', 'woocommerce-avatax' ),
			'<strong>',
			'</strong>',
			'<ul class="wc-avatax-producst-sync-errors ul-disc" style="color: #A94545;">' . $sync_error_items . '<ul>'
		) : '';
	}

	/**
	 * Handles the sync countries list update.
	 *
	 * This is a callback to be executed when the list of countries to sync is updated.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 *
	 * @param array $old_countries
	 * @param array $new_countries
	 */
	public function handle_countries_sync_update( $old_countries, $new_countries ) {

		$sync_handler = $this->get_plugin()->get_landed_cost_sync_handler();

		if ( $sync_handler->is_syncing_active() && ! empty( array_diff((array) $new_countries, (array) $old_countries ) ) ) {
			$sync_handler->enqueue_full_sync();
		}
	}

	/**
	 * Determines whether the API environment is set to production.
	 *
	 * @since 1.13.0
	 * @deprecated 1.16.0
	 *
	 * @return bool
	 */
	public function is_api_environment_production() : bool {

		wc_deprecated_function( __METHOD__, '1.16.0' );

		return 'production' === get_option( 'wc_avatax_api_environment' );
	}

	/**
	 * Determines whether the store may try to connect to Cross-Border product sync.
	 *
	 * @since 1.13.0
	 *
	 * @return bool
	 */
	public function is_cross_border_product_sync_connection_allowed() : bool {

		$connection_allowed =
			wc_avatax()->has_api_credentials_set()
			&& wc_avatax()->has_hs_api_credentials_set()
			&& wc_avatax()->get_landed_cost_handler()->has_countries_for_product_sync();

		/**
		 * Filters whether the user is allowed to connect Cross Border product sync.
		 *
		 * @since 1.13.0
		 *
		 * @param bool $connection_allowed whether the user is allowed to connect Cross Border product sync
		 */
		return (bool) apply_filters( 'wc_avatax_cross_border_product_sync_connection_allowed', $connection_allowed );
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 1.13.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax {

		return wc_avatax();
	}

	/**
	 * Caches the company ID to prevent race conditions with other requests that depend on it.
	 *
	 * @since 1.13.0
	 */
	protected function cache_company_id() {

		wc_avatax()->get_company_id();
	}

	/**
	 * Clears the company ID cache when updating the company code.
	 *
	 * Also stops sync in progress if the company code changes when saving settings.
	 *
	 * @internal
	 *
	 * @since 1.17.0
	 *
	 * @param string|mixed $old_company_code
	 * @param string|mixed $new_company_code
	 * @return void
	 */
	public function prune_company_id_cache( $old_company_code, $new_company_code ) {

		if ( $old_company_code === $new_company_code ) {
			return;
		}
		wc_avatax()->clear_company_id_cache();
		wc_avatax()->get_landed_cost_sync_handler()->stop_syncing();
		add_action( 'woocommerce_settings_saved', [ $this, 'save_company_default_fields' ] );
	}

	/**
	 * Save default fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_company_default_fields()
	{
		wc_avatax()->wc_avatax_utilities()->save_company_default_fields();
	}
	/**
	 * Clears the account number cache when updating the account number.
	 *
	 * Also stops sync in progress if the account number changes when saving settings.
	 *
	 * @internal
	 *
	 * @since 2.3.0
	 *
	 * @param string|mixed $old_account_number
	 * @param string|mixed $new_account_number
	 * @return void
	 */
	public function prune_account_number_cache( $old_account_number, $new_account_number ) {

	if ($old_account_number=== $new_account_number) {
			return;
		}
		wc_avatax()->get_landed_cost_sync_handler()->stop_syncing();
		wc_avatax()->clear_account_number_cache();
	}

	

	/**
	 * Saves origin Address Field
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 *
	 * @return void
	 */
	public function save_origin_address_field() {
		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			$response = wc_avatax()->get_api()->get_company_location();
			if ($response != null)
			{
				$applicable_address = array(
					'address_1' =>  $response->line1,
					'country'  => $response->country ,
					'state'    => $response->region ,
					'postcode' =>  $response->postalCode ,
					'city'     =>  $response->city
				);
				update_option('wc_avatax_origin_address', $applicable_address);
			}
			else {
				if(WC()->customer != null) {
					$applicable_address = array(
						'address_1' => WC()->customer->get_shipping_address(),
						'country'  => WC()->customer->get_shipping_country(),
						'state'    => WC()->customer->get_shipping_state(),
						'postcode' => WC()->customer->get_shipping_postcode(),
						'city'     => WC()->customer->get_shipping_city()
					);
					update_option('wc_avatax_origin_address', $applicable_address);
				}
			}
		}
	}

	/**
	 * Sends configuration setting to CUP
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	protected function send_avatax_settings_to_cup()
	{
		$integration_api = wc_avatax()->wc_avatax_utilities()->get_integration_api();
		$response = $integration_api->send_settings_to_cup('POST');
		if($response instanceof stdClass && empty(((array)$response))){
			WC_Admin_Settings::add_error( __( 'Unable to connect to Avalara API service, please try again after some time.', 'woocommerce-avatax' ) );
			wc_avatax()->wc_avatax_utilities()->disconnect_avatax();
			$set = $this->get_settings();
			set_transient("wc_avatax_ccs_error", 'yes', 60);
		}
	}
}
