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


use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_Abstract_API;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API.
 *
 * @since 2.8.0
 */
class WC_AvaTax_Integration_API extends WC_AvaTax_Abstract_API {
	/** @var string Avalara account ID */
	protected $account_id;

	/** @var string Avalara license key */
	protected $license_key;

	/**@var bool Use bearer token */
	protected $use_bearer_token = false;

	/**
	 * Construct the API.
	 *
	 * @since 2.8.0
	 *
	 * @param string $account_id Avalara account ID
	 * @param string $license_key Avalara license key
	 * @param string $company_code Avalara company code
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function __construct( $account_id, $license_key, $environment, $generateElrToken = false) {
		$this->account_id   = $account_id;
		$this->license_key  = $license_key;

		//TODO : Update this later when application will be available on SBX
		$this->request_uri = ( 'production' === $environment ) ? 'https://config.connector.avalara.com/api' : 'https://config.connector.sbx.avalara.com/api';
		// $this->request_uri = ( 'production' === $environment ) ? 'https://config.connector.avalara.com/api' : 'https://ccs.gamma.qa.us-west-2.aws.avalara.io/api';
		

		if ($generateElrToken) {
			// header for elr auth token
			$this->set_request_headers( [
				'avalara-version' => '1.0.0'
			] );
			$this->use_bearer_token = true;
			parent::__construct(wc_avatax()::ELR_CONNECTOR_ID);
		} else {
			// Set basic auth creds
			$this->set_http_basic_auth( $this->account_id, $this->license_key );
			parent::__construct();
		}

		
	}

	/**
	 * Sets the authentication for E-Invoicing APIs.
	 *
	 * @since 3.0.0
	 */
	public function get_token() {
		try{
			$token = get_transient('wc_avatax_elr_token');

			if(! get_transient('wc_avatax_elr_token')){
				return wc_avatax()->get_elr_api()->get_elr_auth_token();
			} 

			return $token;
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}
			return false;
		}
	}

	/**
	 * Gets the configuration settings from CCS.
	 *
	 * @since 2.8.0
	 *
	 */
	public function get_configuration_settings() : object {
		$request = $this->get_new_request( 'getconfigurationsettings');
		$settings = null;
		try{
			$response = $this->perform_request( $request );	

			$settings = $response->get_configuration_settings();
			$response_code = $this->get_response_code();
			
			if($response_code == "404" ){
				return new stdClass();
			}
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			wc_avatax()->log( $e->getMessage() . "--" . json_encode(array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			)) );

			//Logging error
			wc_avatax()->logger()->log_exception("Integration", "get_configuration_settings", $e->getMessage(), $e->getTraceAsString());

			return new stdClass();
		}
		return $settings;	
	}
		
	/**
	 * Sends settingsto CCS API.
	 *
	 * @since 2.8.0
	 *
	 * @param string $type API type POST/PUT
	 */
	public function send_settings_to_cup($type) {
		try{
			$tenant_app_config_request = $this->get_new_request( 'set_tenant_app_config', $type );
			$tenant_app_config_response = $this->perform_request( $tenant_app_config_request );	
			$response_code = $this->get_response_code();
			
			if($response_code == "409" ){
				$tenant_app_config_request = $this->get_new_request( 'set_tenant_app_config', 'PUT' );
				$tenant_app_config_response = $this->perform_request( $tenant_app_config_request );	
			}
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			wc_avatax()->log( $e->getMessage() . "--" . json_encode(array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			)) );

			//Logging error
			wc_avatax()->logger()->log_exception("Integration", "send_settings_to_cup", $e->getMessage(), $e->getTraceAsString());
		}
		$request = $this->get_new_request( 'put_settings', $type );
		$request->prepare_settings_data();

		try{
			$response = $this->perform_request( $request );	
			$response_code = $this->get_response_code();
			
			if($response_code == "409" ){
				$this->send_settings_to_cup("PUT");
			}
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			wc_avatax()->log( $e->getMessage() . "--" . json_encode(array(
					'code'  => (int) $e->getCode(),
					'error' => esc_html( $e->getMessage() ),
				)) );

			//Logging error
			wc_avatax()->logger()->log_exception("Integration", "send_settings_to_cup", $e->getMessage(), $e->getTraceAsString());

			return new stdClass();
		}	
	}

	/**
	 * Sends ELR schema to CCS API.
	 *
	 * @since 2.9.0
	 *
	 * @param string $type API type POST/PUT
	 * @param array $data ELR schema data
	 * @param string $entityType Sales entity type
	 * 
	 */
	public function send_elr_schema_to_ccs($type, $data, $doctype) {
		//Register application for ELR schema on CCS
		//$this->register_elr_app_on_ccs($type);
		$args = array(
			'type' => $type,
			'doctype' => $doctype
		);
		
		$request = $this->get_new_request( 'post_payload_schema', $args );
		$request->set_payload_data($data, $doctype); //TODO : prepare data

		try{
			$response = $this->perform_request( $request );	
			$response_code = $this->get_response_code();
			
			if($response_code == "409" ){
				$this->send_elr_schema_to_ccs("PUT", $data, $doctype);
			}

			return $response;
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() . "--" . json_encode(array(
					'code'  => (int) $e->getCode(),
					'error' => esc_html( $e->getMessage() ),
				)) );
			}
			return new stdClass();
		}	
	}

	/**
	 * Register ELR app for sending schema to CCS API.
	 *
	 * @since 2.9.0
	 *
	 * @param string $type API type POST/PUT
	 */
	public function register_elr_app_on_ccs($type) {
		//Registering seperate application for ELR schema
		try{
			$elr_app_config_request = $this->get_new_request( 'set_elr_app_config', $type );
			$elr_app_config_response = $this->perform_request( $elr_app_config_request );	
			$response_code = $this->get_response_code();

			if($response_code == "409" ){
				$this->register_elr_app_on_ccs("PUT");
			}
			return $response_code;
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() . "--" . json_encode(array(
					'code'  => (int) $e->getCode(),
					'error' => esc_html( $e->getMessage() ),
				)) );
			}
			return false;
		}
	}

	/**
	 * Deletes the ELR payload and configuration from CCS.
	 *
	 * @since 3.0.0
	 *
	 */
	public function delete_elr_configuration() {
		$request = $this->get_new_request( 'delete_elr_configuration');
		try{
			$response = $this->perform_request( $request );	
			$response_code = $this->get_response_code();
			if($response_code != "200" ){
				wc_avatax()->log_elr("Could not delete the e-invoicing tenant configuration");
			}
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() . "--" . json_encode(array(
					'code'  => (int) $e->getCode(),
					'error' => esc_html( $e->getMessage() ),
				)) );
			}

			return new stdClass();
		}
		return true;
	}
	
	/**
	 * Deletes the configuration settings from CCS.
	 *
	 * @since 2.8.0
	 *
	 */
	public function delete_configuration_settings() {
		$request = $this->get_new_request( 'delete_settings');
		try{
			$response = $this->perform_request( $request );	
			$response_code = $this->get_response_code();
			if($response_code == "200" ){
				$tenant_request = $this->get_new_request( 'delete_tenant_config');
				$tenant_response = $this->perform_request( $tenant_request );	
				$tenant_response_code = $this->get_response_code();

				if($tenant_response_code != "200" ){
					wc_avatax()->log("Could not delete the tenant configuration");
				}
			}
		}
		catch(Framework\SV_WC_API_Exception $e ) {
			wc_avatax()->log( $e->getMessage() . "--" . json_encode(array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			)) );

			//Logging error
			wc_avatax()->logger()->log_exception("Integration", "delete_configuration_settings", $e->getMessage(), $e->getTraceAsString());

			return new stdClass();
		}
		return $settings;
	}

	/**
	 * Allow child classes to validate a response prior to instantiating the
	 * response object. Useful for checking response codes or messages, e.g.
	 * throw an exception if the response code is not 200.
	 *
	 * A child class implementing this method should simply return true if the response
	 * processing should continue, or throw a Framework\SV_WC_API_Exception with a
	 * relevant error message & code to stop processing.
	 *
	 * Note: Child classes *must* sanitize the raw response body before throwing
	 * an exception, as it will be included in the broadcast_request() method
	 * which is typically used to log requests.
	 *
	 * @since 2.8.0
	 */
	protected function do_pre_parse_response_validation() {
		// TODO
		return true;
	}


	/**
	 * Validate the parsed response data.
	 *
	 * Primarily checks for errors returned by the AvaTax API.
	 *
	 * @since 2.8.0
	 *
	 * @throws Framework\SV_WC_API_Exception
	 * @return bool
	 */
	protected function do_post_parse_response_validation() {

		$response = $this->get_response();

		if ( $response->has_errors() ) {

			$messages = array();
			$errors   = $response->get_errors();

			foreach ( $errors->get_error_codes() as $code ) {
				$messages[] = '[' . $code . '] ' . $errors->get_error_message( $code );
			}

			$message = implode( ' ', $messages );

			throw new Framework\SV_WC_API_Exception( $message );
		}
		return true;
	}

	/**
	 * Builds and returns a new API request object
	 *
	 * @see Framework\SV_WC_API_Base::get_new_request()
	 *
	 * @since 2.8.0
	 *
	 * @param string $type the desired request type
	 * @param mixed $args optional argument(s) to be passed to the request
	 * @return Companies_Request|Nexus_List_Request|Product_Classification_Systems_List_By_Company_Request|Query_Items_Request|WC_AvaTax_API_Address_Request|WC_AvaTax_API_Entity_Use_Code_Request|WC_Avatax_API_Rate_Request|WC_Avatax_API_Subscriptions_Request|WC_AvaTax_API_Tax_Request|WC_Avatax_API_Utility_Request|WC_AvaTax_API_Void_Request
	 * @throws Framework\SV_WC_API_Exception for invalid request types
	 */
	protected function get_new_request( $type = '', $args = null ) {

		if($this->use_bearer_token){
			$this->set_bearer_token_auth($this->get_token());
		}

		switch ( $type ) {
			case 'set_tenant_app_config':
				$this->set_response_handler('WC_AvaTax_API_Response');
				return new WC_AvaTax_API_Post_Tenant_App_Config($args);
				break;
			case 'getconfigurationsettings':
				$this->set_response_handler( 'WC_AvaTax_API_Get_Settings_Response' );
				return new WC_AvaTax_API_Get_Settings_Request($args);
				break;
			case 'complete_sync':
				$this->set_response_handler( 'WC_AvaTax_API_Response' );
				return new WC_AvaTax_API_Complete_Sync_Request($args);
				break;
			case 'put_settings':
				$this->set_response_handler( 'WC_AvaTax_API_Response' );
				return new WC_AvaTax_API_Put_Settings_Request($args);
				break;
			case 'delete_settings':
				$this->set_response_handler( 'WC_AvaTax_API_Response' );
				return new WC_AvaTax_API_Delete_Setting_Request($args);
				break;
			case 'delete_tenant_config':
				$this->set_response_handler( 'WC_AvaTax_API_Response' );
				return new WC_AvaTax_API_Delete_Tenant_App_Config($args);
				break;
			case 'post_payload_schema':
				$this->set_response_handler( 'WC_AvaTax_API_Response' );
				return new WC_AvaTax_API_Post_Payload_Schema_Request($args);
				break;
			case 'set_elr_app_config':
				$this->set_response_handler('WC_AvaTax_API_Response');
				return new WC_AvaTax_API_Post_Elr_App_Config($args);
				break;
			case 'delete_elr_configuration':
				$this->set_response_handler('WC_AvaTax_API_Response');
				return new WC_AvaTax_API_Delete_Elr_App_Config($args);
				break;
			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid request type' );
				break;
		}
	}

	/**
	 * Gets the configured company code.
	 *
	 * @since 2.8.0
	 *
	 * @return string
	 */
	public function get_company_code() {

		return $this->company_code;
	}
}
