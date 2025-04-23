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
use SkyVerge\WooCommerce\AvaTax\API\Requests\WC_AvaTax_API_Company_Filter_Default_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\WC_AvaTax_API_Company_Filter_Company_Code_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\Companies_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\Nexus_List_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\Product_Classification_Systems_List_By_Company_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\Query_Items_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\WC_Avatax_API_Tax_Code_Request;
use SkyVerge\WooCommerce\AvaTax\API\Requests\WC_Avatax_API_Company_Tax_Code_Request;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Companies_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Nexus_List_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Product_Classification_Systems_List_By_Company_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\Query_Items_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\WC_Avatax_API_Tax_Code_Response;
use SkyVerge\WooCommerce\AvaTax\API\Responses\WC_Avatax_API_Company_Tax_Code_Response;
use SkyVerge\WooCommerce\AvaTax\Api\WC_AvaTax_Abstract_API;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API.
 *
 * @since 1.0.0
 */
class WC_AvaTax_API extends WC_AvaTax_Abstract_API {


	/** @var string Avalara account ID */
	protected $account_id;

	/** @var string Avalara license key */
	protected $license_key;

	/** @var string Avalara company code */
	protected $company_code;


	/**
	 * Construct the API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $account_id Avalara account ID
	 * @param string $license_key Avalara license key
	 * @param string $company_code Avalara company code
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function __construct( $account_id, $license_key, $company_code, $environment ) {

		$this->account_id   = $account_id;
		$this->license_key  = $license_key;
		$this->company_code = $company_code;

		$this->request_uri = ( 'production' === $environment ) ? 'https://rest.avatax.com/api/' : 'https://sandbox-rest.avatax.com/api/';
		$this->request_uri .= static::VERSION;

		// Set basic auth creds
		$this->set_http_basic_auth( $this->account_id, $this->license_key );

		parent::__construct();
	}


	/**
	 * Gets the companies.
	 *
	 * @since 1.13.0
	 *
	 * @return Companies_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_companies(string $paginated_url = '') : Companies_Response {

			$request = $this->get_new_request('companies', $paginated_url);

			$response = $this->perform_request( $request );
			
			return $response;
	}
	/**
	 * Gets the companies by company code.
	 *
	 * @since 2.10.0
	 *
	 * @return Companies_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_company_by_companyCode(string $paginated_url = '' ) : Companies_Response {
			
		$request = $this->get_new_request('company-by-code', $paginated_url);

		$response = $this->perform_request( $request );
		
		return $response;
	}

	/**
	 * Gets the default company.
	 *
	 * @since 2.10.0
	 *
	 * @return Companies_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_default_company(string $paginated_url = '' ) : Companies_Response {
			
		$request = $this->get_new_request('default-company', $paginated_url);

		$response = $this->perform_request( $request );
		
		return $response;
	}

	/**
	 * Gets the Product tax codes.
	 *
	 * @since 2.6.1
	 *
	 * @return WC_Avatax_API_Product_Tax_Code_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_tax_codes(string $args = '') : bool {

		$request = $this->get_new_request( 'taxCodes',  $args);

		$response = $this->perform_request( $request );

		$response_tax_codes = $response->get_tax_code_list();

		$response_company_tax_codes = $this->get_company_tax_codes();

		return $response_company_tax_codes && $response_tax_codes;
	}

	/**
	 * Gets the Product tax codes.
	 *
	 * @since 2.6.1
	 *
	 * @return WC_Avatax_API_Company_Tax_Code_Response
	 * @param string $paginated_url
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_company_tax_codes(string $args = '') : bool {

		$request = $this->get_new_request( 'companyTaxCodes',  $args);

		$response = $this->perform_request( $request );

		return $response->get_company_tax_code_list();
	}

	/**
	 * Gets the transport parameter list.
	 *
	 * @since 2.4.0
	 *
	 * @return array of transport parameter values
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_transport_parmeter_list() : array {

		try{
			$request = $this->get_new_request( 'transport');

			$response = $this->perform_request( $request );

			return $response->get_transport_list();
		}
		catch(\Exception $e){

			//Logging error
			wc_avatax()->logger()->log_exception("AvaTaxAPI", "get_transport_parmeter_list", $e->getMessage(), $e->getTraceAsString());

			return array();
		}
	}

	/**
	 * Gets the ecommerce token.
	 *
	 * @since 2.6.0
	 *
	 * @return array of transport parameter values
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_ecommerce_token(string $custid = '') : array {
		$request = $this->get_new_request( 'ecommerce-token', $custid );
		$response = $this->perform_request( $request );
		return $response->get_ecommerce_token_response();
	}
	/**
	 * Gets the exposure zones.
	 *
	 * @since 2.6.0
	 *
	 * @return array of exposure zones
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_exposure_zones() : array {
		$request = $this->get_new_request( 'exposure-zones');
		$response = $this->perform_request( $request );
		return $response->get_exposure_zones_response();
	}
	/**
	 * Gets the certificates list.
	 *
	 * @since 2.6.0
	 *
	 * @return array of certificate details
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_certificate_list(string $customercode, string $alternateid){

		$request = $this->get_new_request( 'certificates');

		$request->get_certificates_from_avatax_using_customer_alternateId_or_customer_code($customercode,$alternateid);

		$response = $this->perform_request( $request );

		return $response->get_certificates_list();
	}
	/**
	 * Invite customer to add certificate
	 *
	 * @since 2.6.0
	 *
	 * @return array of invite sent response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function invite_customer_to_add_certificate(string $customercode, string $useremail) : array {

		$request = $this->get_new_request( 'invitecustomer' );

		$request->invite_customer_to_add_certificate($customercode,$useremail);

		$response = $this->perform_request( $request );

		return $response->get_certificates_invite_response();
	}
	/**
	 * Add customer to Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return array of added customer response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function add_customer_to_avatax(string $userid) : array {

		$request = $this->get_new_request( 'addcustomer');
		
		$request->add_customer_to_avatax($userid);
		
		$response = $this->perform_request( $request );
		return $response->get_add_customer_to_avatax_response();
	}

	/**
	 * Update customer in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return array of updated customer in Avatax
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function update_customer_alternate_Id($args) : array {

		$request = $this->get_new_request( 'updatecustomerobject');
		$request->update_alternate_id_customer_object_to_avatax($args);

		$response = $this->perform_request( $request );

		return $response->get_update_customer_to_avatax_response();
	}

	/**
	 * Update customer object in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return array of updated customer in Avatax
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function update_customer_object_to_avatax(string $existing_customer_code,array $user_data) : array {

		$request = $this->get_new_request( 'updatecustomerobject');
		
		$request->update_customer_object_to_avatax($existing_customer_code, $user_data);
		
		$response = $this->perform_request( $request );

		return $response->get_update_customer_to_avatax_response();
	}
	/**
	 * Get customer details from Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return object of customer in Avatax
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_customer_details(string $customerCode) : object {

		$request = $this->get_new_request( 'getcustomer');
		
		$request->get_customer_details($customerCode);

		$response = $this->perform_request( $request );

		return $response->get_customers_details();
	}

	/**
	 * Unlink certificate from customer account in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return bool
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function unlink_certificate( string $certificateid , string $customerCode ) {

		$request = $this->get_new_request( 'unlinkcertificate' );

		$request->unlink_certificate( $certificateid , $customerCode);

		$response = $this->perform_request( $request, true);
		if($response['Status_Code'] == 200)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Gets the Nexus list.
	 *
	 * @since 1.13.0
	 *
	 * @return Nexus_List_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_nexus_list(string $paginated_url = '') : Nexus_List_Response {

		$request = $this->get_new_request( 'nexus-list',  $paginated_url );

		//The response handler is getting set to Companies_Response::class due to wc_avatax()->get_company_id() in Nexus_List_Request. Again setting it to Nexus_List_Response::class
		$this->set_response_handler( Nexus_List_Response::class );

		return $this->perform_request( $request );
	}


	/**
	 * Gets the product classification systems list.
	 *
	 * @since 1.16.0
	 *
	 * @return Product_Classification_Systems_List_By_Company_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_product_classification_systems_list(string $paginated_url = '') : Product_Classification_Systems_List_By_Company_Response {

		return $this->perform_request( $this->get_new_request( 'product-classification-systems-list',  $paginated_url ) );
	}


	/**
	 * Queries the items list
	 *
	 * @since 1.16.0
	 *
	 * @param string $filter
	 * @param array|string $include
	 * @param null $limit
	 * @return Query_Items_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function query_items( string $filter = '', $include = '', $limit = null ) : Query_Items_Response {

		$request = $this->get_new_request( 'query-items' );
		$request->filter( $filter )->include( $include )->limit( $limit );

		return $this->perform_request( $request );
	}

	/**
	 * Get the calculated tax for a cart instance.
	 *
	 * @since 1.5.0
	 *
	 * @param \WC_Cart $cart cart object
	 * @return WC_AvaTax_API_Tax_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function calculate_cart_tax( WC_Cart $cart ) {

		$request = $this->get_new_request( 'tax' );

		$request->process_cart( $cart );
		$transaction_type = $request->get_transaction_type();
		$this->set_response_handler( 'WC_AvaTax_API_Tax_Response' );
		$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);

		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
		$response->set_transaction_type($transaction_type);

		return $response;
	}


	/**
	 * Get the calculated tax for a specific order.
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Order $order order object
	 * @param bool $commit Whether to commit the transaction to Avalara
	 * @return WC_AvaTax_API_Tax_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function calculate_order_tax( WC_Order $order, bool $commit ) {

		$request = $this->get_new_request( 'tax' );
		$request->process_order( $order, $commit );
		$transaction_type = $request->get_transaction_type();
		$this->set_response_handler( 'WC_AvaTax_API_Tax_Response' );
		$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);

		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
		$response->set_transaction_type($transaction_type);

		return $response;
	}


	/**
	 * Get the calculated tax for a refunded order.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order_Refund $refund order refund object
	 * @return WC_AvaTax_API_Tax_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function calculate_refund_tax( WC_Order_Refund $refund ) {

		$request = $this->get_new_request( 'tax' );
		$request->process_refund( $refund );
		$transaction_type = $request->get_transaction_type();

		$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);

		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
		$response->set_transaction_type($transaction_type);

		return $response;
	}


	/**
	 * Refund an order.
	 *
	 * @since 1.15.0
	 *
	 * @param WC_Order_Refund $refund order refund object
	 * @param string|null $type the type of refund, leave empty for a Full refund
	 * @return WC_AvaTax_API_Response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function refund_order( WC_Order_Refund $refund, string $type = null ) {

		$request = $this->get_new_request( 'refund' );

		$request->process_refund( $refund, $type );

		return $this->perform_request( $request );$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);
		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));

		return $response;
	}


	/**
	 * Validate an address.
	 *
	 * @since 1.0.0
	 *
	 * @param array $address {
	 *     The address details.
	 *
	 * @type string $address_1 Line 1 of the street address.
	 * @type string $address_2 Line 2 of the street address.
	 * @type string $city The city name.
	 * @type string $state The state or region.
	 * @type string $country The country code.
	 * @type string $postcode The zip or postcode.
	 * }
	 * @return object The validated and normalized address.
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function validate_address( array $address ) {

		$request = $this->get_new_request( 'address' );

		$request->validate_address( $address );

		$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);
		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));

		return $response;
	}


	/**
	 * Void a document in Avalara based on a WooCommerce order.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id The associated order ID.
	 * @return WC_AvaTax_API_Tax_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function void_order( $order_id ) {

		$request = $this->get_new_request( 'void' );

		$request->void_order( $order_id );
		$transaction_type = $request->get_transaction_type();

		$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);

		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
		$response->set_transaction_type($transaction_type);

		return $response;
	}


	/**
	 * Void a document in Avalara based on a WooCommerce refund.
	 *
	 * @since 1.0.0
	 *
	 * @param WC_Order_Refund $refund order refund object
	 * @return WC_AvaTax_API_Tax_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function void_refund( WC_Order_Refund $refund ) {

		$request = $this->get_new_request( 'void' );
		$request->void_refund( $refund );
		$transaction_type = $request->get_transaction_type();

		$api_start = hrtime(true);
		$response = $this->perform_request( $request );
		$api_end = hrtime(true);
		
		$response->set_response_time(wc_avatax()->wc_avatax_utilities()->microtime_diff($api_start, $api_end));
		$response->set_transaction_type($transaction_type);

		return $response;
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

		$request = $this->get_new_request( 'utility' );

		$request->set_ping_data();

		return $this->perform_request( $request );
	}


	/**
	 * Gets the configured account subscriptions.
	 *
	 * TODO: since 1.16.0 this method is no longer called, as there are no subscriptions to check. Consider removing {IT 2022-01-11}
	 *
	 * @since 1.5.0
	 *
	 * @return WC_AvaTax_API_Subscriptions_Response $response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_subscriptions() {

		$request = $this->get_new_request( 'subscriptions' );

		return $this->perform_request( $request );
	}


	/**
	 * Gets the available Entity/Use codes.
	 *
	 * @since 1.6.2
	 *
	 * @return WC_AvaTax_API_Entity_Use_Code_Response $response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_entity_use_codes() {

		$request = $this->get_new_request( 'entity-use-code' );

		return $this->perform_request( $request );
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
	 * @since 1.0.0
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
	 * @since 1.5.0
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

			//throw new Framework\SV_WC_API_Exception( $message );
			wc_avatax()->log($message);
			
			if( ($this->response_code !== 401) ||  (get_transient( 'wc_avatax_connection_status', '' ) === 'connected' && !str_contains(strtolower($message), 'authentication failed'))){
				wc_avatax()->logger()->log_error("API_Response_Validation", "do_post_parse_response_validation", $message, "");
			}

		}

		return true;
	}


	/**
	 * Builds and returns a new API request object
	 *
	 * @see Framework\SV_WC_API_Base::get_new_request()
	 *
	 * @since 1.0.0
	 *
	 * @param string $type the desired request type
	 * @param mixed $args optional argument(s) to be passed to the request
	 * @return Companies_Request|Nexus_List_Request|Product_Classification_Systems_List_By_Company_Request|Query_Items_Request|WC_AvaTax_API_Address_Request|WC_AvaTax_API_Entity_Use_Code_Request|WC_Avatax_API_Rate_Request|WC_Avatax_API_Subscriptions_Request|WC_AvaTax_API_Tax_Request|WC_Avatax_API_Utility_Request|WC_AvaTax_API_Void_Request|WC_AvaTax_API_Company_Filter_Company_Code_Request|WC_AvaTax_API_Company_Filter_Default_Request
	 * @throws Framework\SV_WC_API_Exception for invalid request types
	 */
	protected function get_new_request( $type = '', $args = null ) {

		switch ( $type ) {

			case 'companies' :
				$this->set_response_handler( Companies_Response::class );
				return new Companies_Request($args);

			case 'company-by-code' :
				$this->set_response_handler( Companies_Response::class );
				return new WC_AvaTax_API_Company_Filter_Company_Code_Request($args);

			case 'default-company' :
				$this->set_response_handler( Companies_Response::class );
				return new WC_AvaTax_API_Company_Filter_Default_Request($args);

			case 'taxCodes' :
				$this->set_response_handler( WC_Avatax_API_Tax_Code_Response::class );
				return new WC_Avatax_API_Tax_Code_Request($args);

			case 'companyTaxCodes' :
				$this->set_response_handler( WC_Avatax_API_Company_Tax_Code_Response::class );
				return new WC_Avatax_API_Company_Tax_Code_Request($args);
				
			case 'nexus-list' :
				$this->set_response_handler( Nexus_List_Response::class );
				return new Nexus_List_Request($args);

			case 'product-classification-systems-list' :
				$this->set_response_handler( Product_Classification_Systems_List_By_Company_Response::class );
				return new Product_Classification_Systems_List_By_Company_Request( $this->get_company_code() );

			case 'query-items' :
				$this->set_response_handler( Query_Items_Response::class );
				return new Query_Items_Request();

			case 'utility':
				$this->set_response_handler( 'WC_Avatax_API_Utility_Response' );
				return new WC_Avatax_API_Utility_Request();

			case 'subscriptions':
				$this->set_response_handler( 'WC_Avatax_API_Subscriptions_Response' );
				return new WC_Avatax_API_Subscriptions_Request( $this->account_id );

			case 'entity-use-code':
				$this->set_response_handler( 'WC_AvaTax_API_Entity_Use_Code_Response' );
				return new WC_AvaTax_API_Entity_Use_Code_Request();

			case 'tax':
				$this->set_response_handler( 'WC_AvaTax_API_Tax_Response' );
				return new WC_AvaTax_API_Tax_Request( $this->get_company_code() );

			case 'void':
				$this->set_response_handler( 'WC_AvaTax_API_Response' );
				return new WC_AvaTax_API_Void_Request( $this->get_company_code() );

			case 'address':
				$this->set_response_handler( 'WC_AvaTax_API_Address_Response' );
				return new WC_AvaTax_API_Address_Request();
			case 'transport':
				$this->set_response_handler( 'WC_AvaTax_API_Transport_Response' );
				return new WC_AvaTax_API_Transport_Request();
			case 'certificates':
				$this->set_response_handler( 'WC_AvaTax_API_Get_Certificates_Response' );
				return new WC_AvaTax_API_Get_Certificates_Request();
			case 'invitecustomer':
				$this->set_response_handler( 'WC_AvaTax_API_Invite_Customer_To_Add_Certificate_Response' );
				return new WC_AvaTax_API_Invite_Customer_To_Add_Certificate_Request();
			case 'getcustomer':
				$this->set_response_handler( 'WC_AvaTax_API_Get_Customer_Details_Response' );
				return new WC_AvaTax_API_Get_Customer_Details_Request();
			case 'addcustomer':
				$this->set_response_handler( 'WC_AvaTax_API_Add_Customer_To_Avatax_Response' );
				return new WC_AvaTax_API_Add_Customer_To_Avatax_Request();
			case 'unlinkcertificate':
				$this->set_response_handler( 'WC_AvaTax_API_Unlink_Certificate_Response' );
				return new WC_AvaTax_API_Unlink_Certificate_Request();
			case 'updatecustomerobject':
				$this->set_response_handler( 'WC_AvaTax_API_Update_Customer_To_Avatax_Response' );
				return new WC_AvaTax_API_Update_Customer_To_Avatax_Request();
			case 'ecommerce-token':
				$this->set_response_handler( 'WC_AvaTax_API_Get_Ecommerce_Token_Response' );
				return new WC_AvaTax_API_Get_Ecommerce_Token_Request($args);
			case 'exposure-zones':
				$this->set_response_handler( 'WC_AvaTax_API_Get_Exposure_Zones_Response' );
				return new WC_AvaTax_API_Get_Exposure_Zones_Request();
			case 'location':
				$this->set_response_handler( 'WC_AvaTax_API_Company_Location_Response' );
				return new WC_AvaTax_API_Company_Location_Request();
			default:
				throw new Framework\SV_WC_API_Exception( 'Invalid request type' );
		}
	}


	/**
	 * Gets the configured company code.
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	public function get_company_code() {

		return $this->company_code;
	}

	/**
	 * Gets the company location for the selected company
	 *
	 * @since 2.7.0
	 *
	 * @return WC_AvaTax_API_Entity_Use_Code_Response $response response object
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function get_company_location() {

		if(wc_avatax()->get_company_id() !== ''){
			$request = $this->get_new_request( 'location' );
			$request->get_company_location();
			$this->set_response_handler( 'WC_AvaTax_API_Company_Location_Response' );
			$response = $this->perform_request( $request );
			return $response->get_location_address();
		}
		else{
			return null;
		}
	}


}
