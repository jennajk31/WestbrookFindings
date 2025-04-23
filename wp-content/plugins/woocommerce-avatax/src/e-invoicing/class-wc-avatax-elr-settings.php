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

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Set up the admin settings.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr_Settings {

	/** @var string $id The settings page ID */
	protected $id = 'avatax-elr';


	/**
	 * Constructs the class.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {

		$this->add_hooks();

		wc_avatax()->check_elr_api(false);
	}

	/**
	 * Adds action and filter hooks.
	 *
	 * @since 3.0.0
	 */
	private function add_hooks() {

		// add the settings section to the WooCommerce Tax tab
		add_filter( 'woocommerce_get_sections_tax', [ $this, 'add_settings_section' ] );

		// output the settings
		add_action( 'woocommerce_get_settings_tax', [ $this, 'add_settings' ] );

		//display a custom license key field with landing message
		add_action( 'woocommerce_admin_field_wc_avatax_elr_client_secret_type', [ $this, 'display_client_secret_field' ] );
		add_action( 'woocommerce_admin_field_wc_avatax_elr_company_type', [ $this, 'display_company_field' ] );
		add_action( 'woocommerce_admin_field_wc_avatax_elr_environment_type', [ $this, 'display_api_environment_field' ] );
		add_action( 'woocommerce_admin_field_wc_avatax_elr_custom_tab_type', [ $this, 'display_wc_avatax_elr_custom_tab_type' ] );

		//Update tenant details
		add_action( 'update_option_wc_avatax_elr_company', [ $this, 'updating_elr_company' ], 9, 2 );
		add_action( 'add_option_wc_avatax_elr_company', [ $this, 'updating_elr_company' ], 9, 2 );

		// save the settings
		add_action( 'woocommerce_settings_save_tax', [ $this, 'save_settings' ] );
		$this->compatibility_notice();

	}

	public function updating_elr_company( $old_elr_company, $new_elr_company ) {

		if ($old_elr_company === $new_elr_company) {
			return;
		}
		
		$this->register_elr_tenant_aplication();
	}

	/**
	 * Displays a compatibility notice for HPOS (High-Performance Order Storage) requirement.
	 * 
	 * Checks if HPOS is enabled when ELR settings are active. If HPOS is not enabled,
	 * displays an error notice in the WordPress admin panel informing users that
	 * Avalara E-Invoicing and Live Reporting requires HPOS to be enabled.
	 * 
	 * @since 3.0.0
	 * @access protected
	 * 
	 * @return void
	 */
	protected function compatibility_notice(){
		if(!wc_avatax()->wc_avatax_utilities()->is_hpos_enabled()){
			if (wc_avatax()->is_plugin_elr_settings() ) {
				wc_avatax()->get_admin_notice_handler()->add_admin_notice(sprintf(
					__( '%1$sAvalara E-Invoicing and Live Reporting (ELR) requires HPOS feature enabled!%2$s', 'woocommerce-avatax' ),
					'<strong class="error">',
					'</strong>',
				), 'wc-elr-hpos', array(
					'always_show_on_settings' => false,
					'dismissible'             => false,
					'notice_class'			  => "error"
				)  );
			}
		}
	}

	/**
	 * Add the AvaTax section to the Tax tab.
	 *
	 * @since 3.0.0
	 * @param array $sections The existing Tax sections.
	 * @return array $sections The new Tax sections.
	 */
	public function add_settings_section( $sections ) {

		$sections[ $this->id ] = __( 'E-Invoicing and Live Reporting', 'woocommerce-avatax' );

		return $sections;
	}

	/**
	 * Get ELR settings.
	 *
	 * @since 3.0.0
	 * @return array $settings ELR settings.
	 */
	public function get_elr_settings() {

		$connection_status = get_transient( 'wc_avatax_elr_connection_status', "not-connected" );

		$settings = array(
			array(
				'type' => 'title',
				'name' => __( 'Avalara E-Invoicing and Live Reporting', 'woocommerce-avatax' ),
			),
			array(
				'id'      => 'wc_avatax_elr_environment',
				'name'    => __( 'Choose your account type', 'woocommerce-avatax' ),
				'options' => array(
					'production'  => __( 'Production', 'woocommerce-avatax' ),
					'development' => __( 'Development', 'woocommerce-avatax' ),
				),
				'desc'=>'Select your Production or Development account.',
				'default' => 'production',
				'type'    => 'wc_avatax_elr_environment_type',
			),
			array(
				'id'                => 'wc_avatax_elr_client_id',
				'name'              => __( 'Client ID', 'woocommerce-avatax' ),
				'type'              => 'text',
				'desc'              => $connection_status !== 'connected' ? '<a href="https://knowledge.avalara.com/bundle/nsz1680268923536_nsz1680268923536/page/uev1682056823407.html" target="_blank">Learn how to find Client ID and Client Secret</a>' : '',
				'css'               => 'min-width:300px;',
				'custom_attributes' => array(
					'data-wc-avatax-elr-connection-status' => $connection_status,
				),
			),
			array(
				'id'                => 'wc_avatax_elr_client_secret',
				'name'              => __( 'Client secret', 'woocommerce-avatax' ),
				'type'              => 'text',
				'css'               => 'min-width:300px;',
				'type'    => 'wc_avatax_elr_client_secret_type',
			),
			array(
				'id'                => 'wc_avatax_elr_company',
				'name'              => __( 'Company', 'woocommerce-avatax' ),
				'type'              => 'text',
				'css'               => 'min-width:300px;',
				'type'    => 'wc_avatax_elr_company_type',
			)
		);
		$LoggingField = array(array(
			'id'      => 'wc_elr_debug',
			'name'    => __( 'Enable logging', 'woocommerce-avatax' ),
			'desc_tip'    => __( 'Creates a log of API requests, responses, and errors for transactions. The log is useful in troubleshooting issues with transactions and is saved in WooCommerce.', 'woocommerce-avatax' ),
			'default' => 'no',
			'type'    => 'checkbox',
			'class'	   => 'checkbox-toggle',
		));
		if ( $this->get_plugin()->check_elr_api(false) ) {
			$custom_field_settings = $this->get_custom_fields();
			if(!empty($custom_field_settings)){
				$settings = array_merge(
					$settings, 
					$LoggingField,
					$custom_field_settings
				);
			}
			else{
				$settings = array_merge(
					$settings,
					$LoggingField
				);
			}
		}
		if ( $this->get_plugin()->check_elr_api(false) ) {
			$settings = array_merge(
				$settings,
				array(array('type' => 'sectionend')));
		}
			
		return (array) apply_filters( 'woocommerce_get_settings_' . $this->id , $settings );
	}

	/**
	 * Get custom fields for settings
	 * 
	 */
	public function get_custom_fields() {
		$custom_field_settings = array();
		$field_list = get_option('wc_avatax_elr_custom_fields', array());
		
		if(!empty($field_list)) {
			
            foreach((array)$field_list->company as $field){
				$custom_field_settings = array_merge($custom_field_settings, array(array(
						'id'                => $field->field_id,
						'name'              => $field->field_name,
						'type'              => ($field->data_type == 'string' ? 'text' : ($field->data_type == 'date' ? 'date' : 'number')),
						'css'               => 'min-width:400px;',
						'custom_attributes' => ($field->data_type == 'string' ? array('maxlength' => 100) : []),
				)));
			}
		}
		if(!empty($custom_field_settings)){
			//Adding additional setting field to display the tabs for custom setting, mapping, conditional fields and view sample data.
			$custom_field_settings = array_merge($custom_field_settings, array(array(
				'type'    => 'wc_avatax_elr_custom_tab_type',
			),));
		}
		else{
			return array(array(
				'type'    => 'wc_avatax_elr_custom_tab_type',
			));
		}
		return $custom_field_settings;
	}

	/**
	 * Get all of the combined settings.
	 *
	 * @since 3.0.0
	 * @return array $settings The combined settings.
	 */
	public function get_settings() {
		$settings = $this->get_elr_settings();
		
		/**
		 * Filter the combined settings.
		 *
		 * @since 3.0.0
		 * @param array $settings The combined settings.
		 */
		return apply_filters( 'woocommerce_get_settings_' . $this->id, $settings );
	}

	/**
	 * Replace core Tax settings with our own when the ELR section is being viewed.
	 *
	 * @since 3.0.0
	 * @return array
	 */
	public function add_settings( $settings ) {

		global $current_section;

		// Output the general settings
		if ( $this->id == $current_section ) {

			// Always display the API and ELR settings
			$settings = $this->get_elr_settings();
		}

		return $settings;
	}

	/**
	 * Saves the settings.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 *
	 * @global string $current_section The current settings section.
	 */
	public function save_settings() {
		global $current_section;
		$is_connecting = true;
		if ( $this->id === $current_section ) {

			// if the API credentials were good at last check, save the settings
			if ( 'connected' === get_transient( 'wc_avatax_elr_connection_status' ) ) {
				$is_connecting = false;
			}
			else {
				$is_connecting = true;
			}

			// always save the API, product sync & misc settings
			$this->save_fields( $this->get_elr_settings() );

			// reset the API status transient
			delete_transient( 'wc_avatax_elr_connection_status' );

			if ( !$this->get_plugin()->check_elr_api(false) ) {
				if(!$this->get_plugin()->has_elr_api_credentials_set()){
					WC_Admin_Settings::add_error( __( 'Enter client ID and client secret.', 'woocommerce-avatax' ) );
				}
				else{
					WC_Admin_Settings::add_error( __( 'Either your client ID or client secret is incorrect.', 'woocommerce-avatax' ) );
				}
				if($is_connecting){
					add_action( 'woocommerce_settings_saved', [ $this, 'clear_default_fields' ] );
				}
			} else {
				if($is_connecting){
					// save the settings
					add_action( 'woocommerce_settings_saved', [ $this, 'register_elr_tenant_aplication' ] );
				}
			}
		}
	}

	/**
	 * Clears default fields.
	 *
	 * @internal
	 *
	 * @since 3.0.0
	 * 
	 */
	public function clear_default_fields() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_elr_client_id','wc_avatax_elr_client_secret')" );
		wc_avatax()->wc_avatax_elr_utilities()->clear_transient();
	}


	/**
	 * Register ELR app for sending schema to CCS API.
	 *
	 * @since 2.9.0
	 */
	public function register_elr_tenant_aplication() {
		wc_avatax()->wc_avatax_elr_utilities()->register_or_update_elr_tenant('POST');

		//Refresh logger instance:
		if(wc_avatax()->refresh_elr_logger()){
			//Logging Connect event
			wc_avatax()->elr_logger()->log_event("ConnectELR", "register_elr_tenant_aplication", "Connecting to AvaTax ELR");
		}
		else
		{
			wc_avatax()->log_elr("log_event - ConnectELR, function name - register_elr_tenant_aplication, message - Connecting to AvaTax ELR");
		}
	}

	/**
	 * Save the settings fields.
	 *
	 * This is a simple wrapper for `WC_Admin_Settings::save_fields` to intercept our custom "address"
	 * field type for special handling. All other fields are saved as usual. This is being improved in WC 2.4+
	 * but for now this is easiest for older versions.
	 *
	 * @since 3.0.0
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
	 * Displays the ELR client secret field with landing message and connect, disconnect, test connection button.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function display_client_secret_field( $options ) {

		$id           = $options['id'];
		$label        = $options['title'];
		$value		  = $options['value'];
		$company_name = get_transient("wc_avatax_company_name");
		$env 		  = get_option('wc_avatax_elr_environment', 'production');
		$message = "";// sprintf( __( 'If you are new to Avalara, %1$ssign up%2$s to generate your account information.', 'woocommerce-avatax' ),'<a href="https://admin-avatax.avalara.net" target="_blank">','</a>');
		
		$connected_message = __('<p>Your WooCommerce store is connected to <b>'.$company_name.'</b> in Avalara. <br /><br /> Go <a href="'. ($env == 'production' ? 'https://integrations.avalara.com/' : 'https://sandbox.integrations.avalara.com/') . '" target="_blank"> back to Avalara</a> to finish setting up the tax profile for <b>'.$company_name.'</b>.</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button><span class="info-icon">i</span>', 'woocommerce-avatax');
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/views/html-field-client_secret.php' );
	}
	

	/**
	 * Displays the ELR company field with disconnect.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function display_company_field( $options ) {

		$id           = $options['id'];
		$label        = $options['title'];
		$company_id   = get_option("wc_avatax_elr_company");
		$env 		  = get_option('wc_avatax_elr_environment', 'production');

		$company_code_list = [];
		if(wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api())
		{
			$companies = wc_avatax()->get_elr_api()->get_elr_companies();

			if ($companies !== null) {
			    $company_code_list = $companies->get_elr_company_code_list();
			}
			
		}
		
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/views/html-field-elr_company.php' );
		$this->get_condition_payload_fields();
	}

	// code for getting condition payload fields 
	/**
	 * Displays the ELR environment field.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function get_condition_payload_fields() {
		if(wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api())
		{
			//In get condition pyaload check first if present transient else call get condition payload api
			$payloadCondition = wc_avatax()->get_elr_api()->get_condition_payload();
		}

		//$records = wc_avatax()->wc_avatax_utilities()->getMapperTableRows();
	}

	/**	
	 * Displays the ELR environment field.
	 *
	 * @since 3.0.0
	 */
	public function display_api_environment_field( $options ) {
		$value		  = get_option("wc_avatax_elr_environment");
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/views/html-field-api-environment.php' );
	}
	/**	
	 * Displays the ELR custom tabs.
	 *
	 * @since 3.0.0
	 */
	public function display_wc_avatax_elr_custom_tab_type( $options ) {
		$env = get_option("wc_avatax_elr_environment");
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/views/html-field-elr_custom_tab.php' );
	}
	

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 3.0.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax {

		return wc_avatax();
	}

}
