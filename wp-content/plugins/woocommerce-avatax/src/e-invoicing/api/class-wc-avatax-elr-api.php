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

use SkyVerge\WooCommerce\AvaTax\API\Requests\Companies_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\Nexus_List_Request;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Companies_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Nexus_List_Response;

use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_Abstract_API;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr_API extends WC_AvaTax_Abstract_API {


	/** @var string Avalara E-invoicing and Live Reporting client ID */
	protected $client_id;

	/** @var string Avalara E-invoicing and Live Reporting client secret */
	protected $client_secret;

	/** @var string Avalara Environment */
	protected $environment;
	// protected $sbx_env_token_url = 'https://ai-awsfqa.avlr.sh/';
	// protected $prd_env_token_url = 'https://ai-awsfqa.avlr.sh/';
	protected $sbx_env_token_url = 'https://ai-sbx.avlr.sh/';
	protected $prd_env_token_url = 'https://identity.avalara.com/';
	protected $token_expiry_seconds = 3540;
	// protected $sbx_elr_api_url = 'https://router.studio.stg.us-west-2.avalara.io/studio-router/apps/1aaaf9a1-65b9-4b68-9660-dbb40148e25d';
	protected $sbx_elr_api_url = 'https://router.studio.sbx.us-west-2.avalara.com/studio-router/apps/1aaaf9a1-65b9-4b68-9660-dbb40148e25d';
	protected $prd_elr_api_url = 'https://router.studio.us-west-2.avalara.com/studio-router/apps/1aaaf9a1-65b9-4b68-9660-dbb40148e25d';

	/**
	 * Construct the API.
	 *
	 * @since 3.0.0
	 *
	 * @param string $client_id ELR client ID
	 * @param string $client_secret ELR client secret
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function __construct( $client_id, $client_secret, $environment ) {

		$this->client_id   = $client_id;
		$this->client_secret  = $client_secret;
		$this->environment  = $environment;

		$this->request_uri = ( 'production' === $environment ) ? $this->prd_elr_api_url : $this->sbx_elr_api_url;

		$this->set_request_headers( [
			'avalara-version' => '1.0.0'
		] );
		
		parent::__construct(wc_avatax()::ELR_CONNECTOR_ID);
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
	 * @since 3.0.0
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
	 * @since 3.0.0
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
			//wc_avatax()->log($message);
			//return false;
		}

		return true;
	}


	/**
	 * Builds and returns a new API request object
	 *
	 * @see Framework\SV_WC_API_Base::get_new_request()
	 *
	 * @since 3.0.0
	 *
	 * @param string $type the desired request type
	 * @param mixed $args optional argument(s) to be passed to the request
	 * @return WC_AvaTax_Elr_API_Get_Companies_Request
	 * @throws Framework\SV_WC_API_Exception for invalid request types
	 */
	protected function get_new_request( $type = '', $args = null ) {
		$this->set_bearer_token_auth($this->get_elr_auth_token());
		switch ( $type ) {

			case 'submit_invoice' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Submit_Invoice_Response::class );
				return new WC_Avatax_Elr_API_Submit_Invoice_Request($args);
			case 'invoice_status' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Invoice_Status_Response::class );
				return new WC_Avatax_Elr_API_Invoice_Status_Request($args);
			case 'download_invoice' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Download_Invoice_Response::class );
				return new WC_Avatax_Elr_API_Download_Invoice_Request($args);
			case 'companies' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Get_Companies_Response::class );
				return new WC_AvaTax_Elr_API_Get_Companies_Request($args);
			case 'invoice_condition_payload' :
				$this->set_response_handler( WC_AvaTax_Elr_API_Condition_Payload_Response::class );
				return new WC_AvaTax_Elr_API_Condition_Payload_Request($args);
			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid request type' );
		}
	}

	/**
	 * Pings the AvaTax API.
	 *
	 * Primarily used to test for a valid connection.
	 *
	 * @since 1.0.0
	 *
	 * @return WC_AvaTax_API_Utility_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function test() {

		return $this->get_elr_auth_token();
	}

	/**
	 * Sets the authentication for E-Invoicing APIs.
	 *
	 * @since 3.0.0
	 *
	 * @param string $client_id ELR client ID
	 * @param string $client_secret ELR client secret
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function get_elr_auth_token() {
		try{
			$token = get_transient('wc_avatax_elr_token');

			if(! get_transient('wc_avatax_elr_token')){

				$api_url = ( 'production' === $this->environment  ? $this->prd_env_token_url : $this->sbx_env_token_url) . "connect/token";
				$options = array(
					'body' => array( 
										'useragent' => $_SERVER['HTTP_USER_AGENT'], 
										'grant_type' => 'client_credentials', 
										'client_id' => $this->client_id, 
										'client_secret' => $this->client_secret 
									),
				);

				if ( wp_http_supports( array( 'ssl' ) ) ) {
					$api_url = set_url_scheme( $api_url, 'https' );
				}

				$response       = wp_remote_post( $api_url, $options );

				$response_code  = wp_remote_retrieve_response_code( $response );
				$response_body  = json_decode( wp_remote_retrieve_body( $response ), true );
				$response_error = null;

				if ( (is_wp_error( $response ) && 200 !== wp_remote_retrieve_response_code( $response )) || isset($response_body['error'])) {
					$response_error = $response;
					if ( wc_avatax()->elr_logging_enabled()) {
						wc_avatax()->log_elr( sprintf( '%1$s: %2$s', $response_code ?? 'Error', is_array($response_error) ? json_encode($response_error) : $response_error->get_error_message() ) );
					}
					return false;
				} else {
					$token = $response_body["access_token"];
					$exp_time = ($response_body["expires_in"] - 300);

					set_transient('wc_avatax_elr_token', $token, $exp_time);
					return $token;
				}
			}
			else {
				return $token;
			}
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( sprintf( '%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage() ) );
			}
			return false;
		}
	}

	/**
	 * Submits invoice to Avalara.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return WC_AvaTax_Elr_API_Submit_Invoice_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function submit_invoice($order, $data) {

		$request = $this->get_new_request( 'submit_invoice' );
		$request->prepare_request( $data);

		try{
			$api_start = hrtime(true);
			$response =  $this->perform_request( $request );
			$api_end = hrtime(true);
			$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
			
			if($response->has_error_code()){
				$message  = '<strong>' . __( (($order instanceof WC_Refund ? "Refund" : "Order"). ' not sent to Avalara E-invoicing and Live Reporting.'), 'woocommerce-avatax' ) . '</strong> </br> ';
				$message .= $response->get_invoice_error_message();

				$order->add_order_note( $message );
			}
			
			return $response;
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr(sprintf('%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage()));
			}

			$message  = '<strong>' . __( (($order instanceof WC_Refund ? "Refund" : "Order"). ' not sent to Avalara E-invoicing and Live Reporting.'), 'woocommerce-avatax' ) . '</strong> </br> ';
			$message .= $e->getMessage();

			$order->add_order_note( $message );

			wc_avatax()->elr_logger()->log_exception("SubmitInvoice", "submit_invoice", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
	}

	/**
	 * Gets invoice Status.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return WC_AvaTax_Elr_API_Invoice_Status_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_invoice_status( $order_id) {
		try{
			$request = $this->get_new_request( 'invoice_status', $order_id );

			$api_start = hrtime(true);
			$response =  $this->perform_request( $request );
			$api_end = hrtime(true);
			$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
			
			if($response->has_error_code()){
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $response->get_invoice_error_message() );
				}
				return false;
			}
			return $response;
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr(sprintf('%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage()));
			}

			wc_avatax()->elr_logger()->log_exception("GetInvoiceStatus", "get_invoice_status", $e->getMessage(), $e->getTraceAsString());

			return false;
		}
	}

	/**
	 * Download invoice.
	 *
	 * @since 3.0.0
	 *
	 * @param \WC_Order $order order object
	 * @return WC_AvaTax_Elr_API_Invoice_Status_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function download_invoice( $invoice_id, $entity_type) {
		try{
			$request = $this->get_new_request( 'download_invoice', $invoice_id );
			$request->prepare_request($entity_type);

			$api_start = hrtime(true);
			$response =  $this->perform_request( $request );
			$api_end = hrtime(true);
			$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));

			if($response->has_error_code()){
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $response->get_invoice_error_message() );
				}
				return "";
			}

			return $response->get_invoice();
		}
		catch( Exception $e){
			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr(sprintf('%1$s: %2$s', $e->getCode() ?? 'Error', $e->getMessage()));
			}

			wc_avatax()->elr_logger()->log_exception("DownloadInvoice", "download_invoice", $e->getMessage(), $e->getTraceAsString());

			return "";
		}
	}


	/**
	 * Gets the einvoice companies.
	 *
	 * @since 2.9.0
	 *
	 * @return bool | WC_AvaTax_Elr_API_Get_Companies_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_elr_companies(string $paginated_url = '') : ?WC_AvaTax_Elr_API_Get_Companies_Response {

		$response = get_transient('wc_avatax_elr_company_response', '');
		if(!$response || !isset($response) || $response === ''){
			try {
				$request = $this->get_new_request( 'companies',  $paginated_url );
	
				$api_start = hrtime(true);
				$response = $this->perform_request( $request );
				$api_end = hrtime(true);
				$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
				
				if(!$response->has_error_code()){
					set_transient( 'wc_avatax_elr_company_response', $response, 1 * DAY_IN_SECONDS );
				}

				if(get_option('wc_avatax_elr_tenant_id', '') === ''){
					update_option("wc_avatax_elr_tenant_id", $response->get_tenant_id());
				}
	
				return $response;
			} 
			catch ( Exception $e ) {
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $e->getMessage() );
					
					// Display error message immediately
					echo '<div class="error inline" style="margin: 10px 0;">
                	<p><strong>' . esc_html__('Error:', 'woocommerce-avatax') . '</strong> ' 
                	. esc_html__('Unable to retrieve company information. Please try again later.', 'woocommerce-avatax') . '</p>';
					}

					wc_avatax()->elr_logger()->log_exception("GetCompanies", "get_elr_companies", $e->getMessage(), $e->getTraceAsString());

				return null;
			}
		} 
		return $response;
	}
	
	/**
	 * Get condition payload
	 *
	 * @since 3.0.0
	 *
	 * @return WC_AvaTax_Elr_API_Condition_Payload_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_condition_payload() {
		// add condition for transient entry
		if (empty(get_transient( 'wc_avatax_elr_condition_payload'))) {
			$request = $this->get_new_request('invoice_condition_payload');

			$response =  $this->perform_request( $request );

			if($response->has_errors()){
				if ( wc_avatax()->elr_logging_enabled() ) {
					wc_avatax()->log_elr( $e->getMessage() );
				}
				return "";
			}
			if ($response->get_condition_payload_response()) {
				set_transient( 'wc_avatax_elr_condition_payload', $response->get_condition_payload_response(), DAY_IN_SECONDS);
			}
			return $response->get_condition_payload_response();
		}
	}
}
