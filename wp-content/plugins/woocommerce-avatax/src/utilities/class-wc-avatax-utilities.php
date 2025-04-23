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

defined( 'ABSPATH' ) or exit;

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_HS_API;
use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * WooCommerce AvaTax main plugin class.
 *
 * @since 2.7.0
 */
class WC_AvaTax_Utilities {
	
	/* ECM Subscription names */
	const TYPE_AVATAX_ECMESSENTIALS = 'ECMEssentials';
	const TYPE_AVATAX_ECMPRO = 'ECMPro';
	const TYPE_AVATAX_ECMPREMIUM = 'ECMPremium';
	const TYPE_AVATAX_LANDED_COST = 'AvaLandedCost';
	const TABLE_TYPE_FLAT = 'flat';
    const TABLE_TYPE_EAV = 'eav';
    const TABLE_TYPE_VERTICAL = 'vertical';
    const ARR_ELR_DOCUMENT_TYPE = ['order' => 'ubl-invoice', 'refund' => 'ubl-creditnote'];
	protected $MAIN_MAPPER_TABLE = "";
    protected $query ="";
    
	public function __construct(){
		global $wpdb;
		$this->MAIN_MAPPER_TABLE = $wpdb->prefix . "wc_orders";
	}
/**
     * Clears the default setting fields.
     * 
     * @since 2.8.3
     *
     */
	public function clear_default_fields() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_enable_tax_calculation','wc_avatax_record_calculations', 'wc_avatax_calculate_on_cart', 'wc_avatax_sku_as_item_code', 'wc_avatax_company_code','wc_avatax_company_id','wc_avatax_company_name','wc_avatax_company_response', 'wc_avatax_enable_ecm', 'wc_avatax_enable_vat', 'wc_avatax_enable_cross_border_classification', 'wc_avatax_origin_address', 'wc_avatax_hs_api_username', 'wc_avatax_hs_api_password', 'wc_avatax_api_product_countries_sync', 'wc_avatax_debug', 'wc_avatax_enable_address_validation', 'wc_avatax_supported_countries_list', 'wc_avatax_landed_cost_products_with_sync_errors', 'wc_avatax_landed_cost_products_with_sync_resolutions', 'wc_avatax_shipping_code' )" );
		
		$this->clear_transient();

		$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
		set_transient( 'wc_avatax_connection_status', 'not-connected', $cache_expiration );
	}
    /**
     * Disconnects the connection to AvaTax.
     * 
     * @since 2.7.0
     *
     */
	public function disconnect_avatax($is_update = false) {
		$integration_api = $this->get_integration_api();
		$integration_api->delete_configuration_settings();

		global $wpdb;
		if(!$is_update) {
			update_option("wc_avatax_api_environment", '' );
			update_option("wc_avatax_api_account_number", '');
			update_option("wc_avatax_api_license_key", '');
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_enable_tax_calculation','wc_avatax_record_calculations', 'wc_avatax_calculate_on_cart', 'wc_avatax_sku_as_item_code', 'wc_avatax_company_code','wc_avatax_company_id','wc_avatax_company_response','wc_avatax_company_name','wc_avatax_company_response', 'wc_avatax_enable_ecm', 'wc_avatax_enable_vat', 'wc_avatax_enable_cross_border_classification', 'wc_avatax_origin_address', 'wc_avatax_hs_api_username', 'wc_avatax_hs_api_password', 'wc_avatax_api_product_countries_sync', 'wc_avatax_debug', 'wc_avatax_enable_address_validation', 'wc_avatax_supported_countries_list', 'wc_avatax_landed_cost_products_with_sync_errors', 'wc_avatax_landed_cost_products_with_sync_resolutions', 'wc_avatax_shipping_code' )" );
		}
		else {
			$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name in ('wc_avatax_api_account_number','wc_avatax_api_license_key', 'wc_avatax_company_code','wc_avatax_company_id','wc_avatax_company_name','wc_avatax_company_response', 'wc_avatax_enable_ecm', 'wc_avatax_enable_vat', 'wc_avatax_enable_cross_border_classification', 'wc_avatax_origin_address', 'wc_avatax_hs_api_username', 'wc_avatax_hs_api_password', 'wc_avatax_api_product_countries_sync', 'wc_avatax_enable_address_validation', 'wc_avatax_supported_countries_list', 'wc_avatax_landed_cost_products_with_sync_errors', 'wc_avatax_landed_cost_products_with_sync_resolutions'  )" );
		}
		
		$this->clear_transient();

		$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
		set_transient( 'wc_avatax_connection_status', 'not-connected', $cache_expiration );
	}

	/**
     * Clears transient data.
     * 
     * @since 2.8.0
     *
     */
	public function clear_transient() {
		global $wpdb;
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_avatax_%'" );
		$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_wc_avatax_%'" );
	}

	/**
     * Checks whether the Colorado nexus is present in AvaTax.
     * 
     * @since 2.7.0
     *
     */
	public function is_colorado_nexus_present(){
		$supported_states = wc_avatax()->get_landed_cost_handler()->get_supported_states('US');
			return in_array('CO', $supported_states);
	}

	/**
     * Get the subscriptions for current account from AvaTax.
     * 
     * @since 2.7.0
     *
     */
	protected function get_subscriptions() {
		$subscriptions_list = [];
		$subscriptions_list = get_transient( 'wc_avatax_subscriptions_list' );
		if($subscriptions_list == null)
		{
			$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
			$subscriptions_list = wc_avatax()->get_api()->get_subscriptions()->get_subscriptions();
			set_transient( 'wc_avatax_subscriptions_list', $subscriptions_list, $cache_expiration );
		}
		return $subscriptions_list;
	}

	/**
     * Checks whether the subscription is present or not.
     * 
     * @since 2.7.0
	 * @param string[] array of subscription names
	 * @return bool
     */
	public function has_subscription(array $type ) : bool {
		$subscriptions =  wp_list_pluck( get_transient( 'wc_avatax_subscriptions_list'), 'subscriptionDescription' );
		if($subscriptions == null && wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			$subscriptions = $this->get_subscriptions();
			$subscriptions = wp_list_pluck( $subscriptions , 'subscriptionDescription' );
		}
		return count(array_intersect($subscriptions, $type)) ? true : false;
	}
    
	/**
	 * Checks wheter account has ECM subscription or not.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function has_ecm_subscription() {
		return $this->has_subscription( array(self::TYPE_AVATAX_ECMPRO, self::TYPE_AVATAX_ECMESSENTIALS, self::TYPE_AVATAX_ECMPREMIUM));
	}

    /**
	 * Checks wheter account has ECM subscription or not.
	 *
	 * @since 2.8.1
	 *
	 * @return bool
	 */
	public function has_landed_cost_subscription() {
		return $this->has_subscription( array(self::TYPE_AVATAX_LANDED_COST));
	}

	/**
	 * Checks wheter account has ELR subscription or not.
	 *
	 * @since 3.0.0
	 *
	 * @return bool
	 */
	public function has_elr_subscription() {
		return true;
		//return $this->has_subscription( array(self::TYPE_AVATAX_ECMPRO, self::TYPE_AVATAX_ECMESSENTIALS, self::TYPE_AVATAX_ECMPREMIUM));
	}	/**	
	 * Gets the Nexus enabled countries.
	 *
	 * @since 2.7.0
	 *
	 * @return array
	 */
	private function get_supported_countries() : array {
		$supported_countries =[];
		$supported_countries =  get_transient( 'wc_avatax_supported_countries_list',[]);
		if(empty($supported_countries) )
		{
			$cache_expiration = apply_filters( 'wc_avatax_connection_status_cache_expiration', HOUR_IN_SECONDS * 1 );
			$supported_countries = wc_avatax()->get_landed_cost_handler()->get_supported_countries();
			set_transient( 'wc_avatax_supported_countries_list', $supported_countries, $cache_expiration );
		}
		return $supported_countries;
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

		wc_avatax()->get_company_details('defaultCompany');

		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			update_option('wc_avatax_default_product_code', 'P0000000');
			update_option('wc_avatax_shipping_code', 'FR');
			update_option('wc_avatax_enable_tax_calculation', 'yes');
			update_option('wc_avatax_record_calculations', 'yes');
			update_option('wc_avatax_calculate_on_cart', 'no');
			update_option('wc_avatax_sku_as_item_code', 'no');
			update_option('wc_avatax_debug', 'yes');
			$this->save_company_default_fields();
			if(wc_avatax()->wc_avatax_utilities()->has_ecm_subscription()) {
				update_option('wc_avatax_enable_ecm', 'yes');
			}

			if($this->has_nexus_outside_countries( ["US"])){
				update_option('wc_avatax_api_product_countries_sync',  $this->get_supported_countries());
			}
		}
	}

	/**	
	 * Checks whether the nexus is outside US/Canada.
	 *
	 * @since 2.7.0
	 *
	 * @return bool
	 */
	public function has_nexus_outside_countries($countries = ["US","CA"]) : bool {
		$supported_countries =  get_transient( 'wc_avatax_supported_countries_list', []);
		if(empty($supported_countries)) 
		{
			$supported_countries = $this->get_supported_countries();
		}
		return count($supported_countries) > count(array_intersect($supported_countries, $countries)) ? true : false;
	}


	/**
	 * Save default company fields. Enables all visible settings when connecting to AvaTAx.
	 *
	 * @internal
	 *
	 * @since 2.7.0
	 * 
	 */
	public function save_company_default_fields()
	{
		$this->update_origin_address();

		if(!$this->has_nexus_outside_countries()) {
			update_option('wc_avatax_enable_address_validation', 'yes');
		}
		else {
				update_option('wc_avatax_api_product_countries_sync',  $this->get_supported_countries());
				update_option('wc_avatax_enable_vat', 'yes');
				update_option('wc_avatax_enable_cross_border_classification', 'no');
				update_option('wc_avatax_hs_api_username', '');
				update_option('wc_avatax_hs_api_password', '');
				update_option('wc_avatax_api_product_countries_sync',  $this->get_supported_countries());
				update_option('wc_avatax_landed_cost_syncing_state', 'off');
				update_option('wc_avatax_landed_cost_full_sync', 'no');
		}

	}

	/**
	 * Updates the origin address
	 * 
	 * @since 2.8.0
	 */
	public function update_origin_address(){
		$this->clear_transient();
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
				wc_avatax()->log("applicable_address" . json_encode(get_option('wc_avatax_origin_address')));
			}
	}

	/**
	 * Checks if HPOS feature is enabled or not.
	 *
	 * @internal
	 *
	 * @since 2.7.1
	 * 
	 */
	public function is_hpos_enabled(){
		return (class_exists( Automattic\WooCommerce\Utilities\OrderUtil::class ) && Automattic\WooCommerce\Utilities\OrderUtil::custom_orders_table_usage_is_enabled());
	}

	/**
	 * Gets the order meta.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @return string the found value for provided key
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function get_order_meta($order_id, $meta_key, $single = true){
		if($this->is_hpos_enabled()) {
			$order = wc_get_order( $order_id );
			return $order->get_meta($meta_key, $single );
		}
		else{
			return get_post_meta( $order_id, $meta_key, $single );
		}

	}

	/**
	 * Adds the order meta data.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @param string $meta_value a metadata value
	 * @return bool 
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function add_order_meta($order_id, $meta_key, $meta_value){

		if($this->is_hpos_enabled()) {
			$order = wc_get_order( $order_id );
			$order->add_meta_data($meta_key, $meta_value);
			$order->save();
			return true;
		} else {
			// Traditional CPT-based orders are in use.
			return add_post_meta( $order_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Updates the order meta data.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @param string $meta_value a metadata value
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function update_order_meta($order_id, $meta_key, $meta_value){

		if($this->is_hpos_enabled()) {
			$order = wc_get_order( $order_id );
			$order->update_meta_data($meta_key, $meta_value );
			$order->save();
		}
		else{
			update_post_meta( $order_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Deletes the order meta data.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @param string $meta_key a metadata key
	 * @param string $meta_value a metadata value
	 * @return bool 
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function delete_order_meta($order_id, $meta_key, $meta_value){
		if ( $this->is_hpos_enabled() ) {
			// HPOS usage is enabled.
			$order = wc_get_order( $order_id );
			$order->delete_meta_data($meta_key, $meta_value );
			$order->save();
			return true;
		} else {
			// Traditional CPT-based orders are in use.
			return delete_post_meta( $order_id, $meta_key, $meta_value );
		}
	}

	/**
	 * Gets the order type.
	 *
	 * @internal
	 *
	 * @param string $order_id the order id
	 * @return string 
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function get_post_type($order_id){
		if($this->is_hpos_enabled()) {
			return Automattic\WooCommerce\Utilities\OrderUtil::get_order_type( $order_id );
		}
		else{
			return get_post_type( $order_id );
		}
	}

	/**
	 * Hide our custom line item meta from the order admin.
	 *
	 * @internal
	 *
	 * @since 2.7.1
	 *
	 * @param array $hidden_meta The hidden line item keys.
	 * @return array $hidden_meta
	 */
	public function hide_order_item_meta( $hidden_meta ) {

		$hidden_meta[] = '_wc_avatax_code';
		$hidden_meta[] = '_wc_avatax_rate';
		$hidden_meta[] = '_wc_avatax_hs_code';
		$hidden_meta[] = '_wc_avatax_vat_code';

		return $hidden_meta;
	}

	/**
	 * Syncs the settings to CCS 
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	public function sync_confic_settings() {
		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
			$this->get_configuration_settings();
		}
	}

	/**
	 * Gets the integration API
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */

	public function get_integration_api($generateElrToken = false)
	{
		$api_account_number  = get_option('wc_avatax_api_account_number');
		$api_license_key     = get_option('wc_avatax_api_license_key');
		$api_environment     = get_option('wc_avatax_api_environment');
		

		return wc_avatax()->get_integration_api($api_account_number, $api_license_key, $api_environment, $generateElrToken);
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
	public function send_avatax_settings_to_cup($type)
	{
		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
			$integration_api = $this->get_integration_api();
			$response = $integration_api->send_settings_to_cup($type);
		}
	}

	/**
	 * Gets configuration setting from CUP
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 *
	 * @return void
	 */
	protected function get_configuration_settings()
	{
		$is_company_updated = false;
		$integration_api = $this->get_integration_api();
		$configuration = $integration_api->get_configuration_settings();
		
		if(!empty((array) $configuration)){
			if(get_option('wc_avatax_company_code') !== $configuration->tax_calculation->wc_avatax_company_code) {
				wc_avatax()->clear_company_id_cache();
				wc_avatax()->get_landed_cost_sync_handler()->stop_syncing();
				update_option('wc_avatax_company_code', $configuration->tax_calculation->wc_avatax_company_code);
				wc_avatax()->get_company_details('companyByCode');
				
				$this->save_company_default_fields();
				$is_company_updated = true;
			}

			update_option('wc_avatax_shipping_code', (empty(trim($configuration->tax_calculation->wc_avatax_shipping_code)) ? "FR" : trim($configuration->tax_calculation->wc_avatax_shipping_code)));
			update_option('wc_avatax_enable_tax_calculation', ($configuration->tax_calculation->wc_avatax_enable_tax_calculation ? 'yes' : 'no'));
			update_option('wc_avatax_record_calculations', ($configuration->tax_calculation->wc_avatax_record_calculations ? 'yes' : 'no'));
			update_option('wc_avatax_calculate_on_cart', ($configuration->tax_calculation->wc_avatax_calculate_on_cart ? 'yes' : 'no'));
			update_option('wc_avatax_sku_as_item_code', ($configuration->tax_calculation->wc_avatax_sku_as_item_code ? 'yes' : 'no'));
			update_option('wc_avatax_debug', ($configuration->logs->wc_avatax_debug ? 'yes' : 'no'));

			if($is_company_updated){
				$this->send_avatax_settings_to_cup('PUT');
			}
			else {
				if(wc_avatax()->wc_avatax_utilities()->has_ecm_subscription()) {
					update_option('wc_avatax_enable_ecm', ($configuration->exemption_certificate_management->wc_avatax_enable_ecm ? 'yes' : 'no'));
				}
		
				if($this->has_nexus_outside_countries(["US"])){
					update_option('wc_avatax_enable_vat', ($configuration->transactions_outside_the_us->wc_avatax_enable_vat ? 'yes' : 'no'));
				}
				if(!$this->has_nexus_outside_countries()){
					update_option('wc_avatax_enable_address_validation', ($configuration->address_validation->wc_avatax_enable_address_validation ? "yes" : "no"));
				}
			}
		}
		else{
			$this->send_avatax_settings_to_cup('POST');
		}
	}
	/**
	 * Gets the error message from address request response.
	 * 
	 * @since 2.7.1
	 *
	 */
	public function get_address_error_messages($response){
		$message = '';
		foreach($response->messages as $msg){
			
			$message = $message . '<div class="wc-avatax-address-validation-result wc-avatax-address-validation-error">' . $msg->summary . '</div></br>';
		}
		return $message;
	}
	/**
	 * Checks if the VAT field is applicable for the origin address.
	 * 
	 * @since 2.8.1
	 *
	 */
	public function is_VAT_Field_Applicable()
	{
		$origin_address = get_option( 'wc_avatax_origin_address', [] );
		$vat_countries  = Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt( '4.0.0' ) ? WC()->countries->get_european_union_countries( 'eu_vat' ) : WC()->countries->get_vat_countries();

		// Only output the VAT if applicable to the shop's origin address
		if (!empty($origin_address) && ! in_array( $origin_address['country'], $vat_countries, true ) ) {
			return false;
		}
		return true;
	}
	public function add_checkout_messages(){
		if ( ! empty( WC()->cart->avatax_messages ) && is_array( WC()->cart->avatax_messages ) ) {

			$has_missing_hs_code_warnings = false;

			foreach ( WC()->cart->avatax_messages as $message ) {

				if ( ! empty( $message->summary ) && ! empty( $message->refersTo ) && 'LandedCost' === $message->refersTo ) {
					return '<p class="wc-avatax-message">' . esc_html( $message->summary ) . '</p>';
				} elseif ( 'MissingHSCodeWarning' === $message->summary ) {
					$has_missing_hs_code_warnings = true;
				}
			}
			foreach ( WC()->cart->avatax_invoice_messages as $message ) {

				if ( ! empty( $message->content && 'No applicable messaging for this line.' != $message->content) ) {
					return '<p class="wc-avatax-message">' . esc_html( $message->content ) . '</p>';
				}
			}

			if ( $has_missing_hs_code_warnings ) {

				$country_code = WC()->customer->get_shipping_country();
				$country = ( new WC_Countries() )->get_countries()[$country_code] ?? $country_code;

				/* translators: Placeholders: %s - country name */
				return '<p class="wc-avatax-message">' . sprintf( esc_html__( "We cannot calculate import duties for %s for some of the products in your cart. By placing the order, you'll need to settle any applicable customs duties and fees with the shipment carrier. You can also contact us to complete your order.", 'woocommerce-avatax' ), $country ) . '</p>';
			}
		}
	}

	/**
	 * Calculate the time difference between two microtime values.
	 *
	 * @since 2.8.1
	 * 
	 * @param float|null $start The starting microtime value
	 * @param float|null $end   The ending microtime value (defaults to current microtime if null)
	 * 
	 * @return float Time difference in seconds with microsecond precision
	 *               Returns 0.0 if start time is not provided
	 */
	function microtime_diff($start, $end = null)
	{
		if(!$start){
			return 0.0;
		}

		if (!$end) {
			$end = microtime();
		}

		return ($end - $start)/1e+6;
	}

	
	/**
	 * Get an array of European countries.
	 *
	 * @since 2.8.1
	 * 
	 * This function returns an array of European countries based on the installed
	 * version of WooCommerce.
	 *
	 * For WooCommerce versions prior to 4.0.0, it returns the list of countries
	 * that are part of the European Union, specifically for VAT (Value Added Tax)
	 * purposes.
	 *
	 * For WooCommerce versions 4.0.0 and later, it returns the list of all
	 * countries that have VAT regulations.
	 *
	 * @return array An array of European countries or VAT countries.
	 */
	public function get_european_countries()
	{
		return Framework\SV_WC_Plugin_Compatibility::is_wc_version_lt('4.0.0')
			? WC()->countries->get_european_union_countries('eu_vat')
			: WC()->countries->get_vat_countries();
	}
}
