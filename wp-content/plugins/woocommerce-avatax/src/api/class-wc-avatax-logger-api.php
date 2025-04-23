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
 * The AvaTax AvaLogger API.
 *
 * @since 2.8.1
 */
class WC_AvaTax_Logger_API extends WC_AvaTax_Abstract_API {


	/** @var string Avalara account ID */
	protected $account_id;

	/** @var string Avalara license key */
	protected $license_key;

	/** @var string Avalara company code */
	protected $company_code;

	protected $client_string;
	protected $connector_version;
	protected $use_bearer_token = false;

	/**
	 * Construct the API.
	 *
	 * @since 2.8.1
	 *
	 * @param string $account_id Avalara account ID
	 * @param string $license_key Avalara license key
	 * @param string $environment The current API environment, either `production` or `development`.
	 */
	public function __construct( $account_id, $license_key, $environment, $token = false ) {

		$this->account_id   		= $account_id;
		$this->license_key  		= $license_key;
		$connector_id 				= $token ? wc_avatax()::ELR_CONNECTOR_ID : wc_avatax()::CONNECTOR_ID;

		$this->request_uri = ( 'production' === $environment ) ? ('https://ceplogger.avalara.com/api/logger/' . $connector_id) : ('https://ceplogger.sbx.avalara.com/api/logger/'. $connector_id);

		if($token){
			$this->use_bearer_token = true;
			$this->set_bearer_token_auth($token);
			parent::__construct(wc_avatax()::ELR_CONNECTOR_ID);
		}
		else{
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
	 * Logs the information to AvaTax.
	 *
	 * @since 2.8.1
	 *
	 * @param string $log_type 0 = Performance, 1 = Debug, 2 = ConfigAudit
	 * @param string $log_level of the log. 0 = Error, 1 = Exception, 2 = Informational
	 * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
	 * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
	 * @param string $message Message which give proper information about why this log is logged.
	 * @param string $doc_code Document code for transaction ( Applicable only for Usage and performance logs)
	 * @param string $event_block In case where performance metrics is not under expected range : Integer (0 = InternalFunction, 1 = PreGetTax, 2 = PostGetTax, 3 = PrePostTax, 4 = PostPostTax, 5 = PreCommitTax, 6 = PostCommitTax, 7 = PreAdjustTax, 8 = PostAdjustTax, 9 = PreCancelTax, 10 = PostCancelTax, 11 = PreGetTaxHistory, 12 = PostGetTaxHistory, 13 = PreReconcileTaxHistory, 14 = PostReconcileTaxHistory, 15 = PreBatchTax, 16 = PostBatchTax)
	 * @param string $doc_type Document type passed in transaction, for ex - SalesOrder , SalesInvoice , PurchaseInvoice etc. Required when performing tax operations.
	 * @param string $connector_time Time required by connector code for processing a transaction which includes request formation and updating values from response in ERP. Required when writing performance log.
	 * @param string $connector_latency Time required to get response from service call from initiation of request. Required when writing performance log
	 * @param string $stack_trace Trace the actual exception from where it is originated. Validation error may not have stacktrace.
	 * @param array $transaction_type Type of transaction which is being performed. Required when writing performance log.
	 * @param int $line_count Number if lines in get tax/create transaction call.
	 * @param bool $elr_log type of log. 0 = AvaTax, 1 = ELR
	 * @param string $elr_process_id Process Id of the transaction. Required when writing performance log.
     * @param string $elr_mandate_id Mandate Id of the transaction. Required when writing performance log.
	 * @return WC_AvaTax_Logger_API_Response
	 * @throws Framework\SV_WC_API_Exception
	 */
	public function log($log_type, $log_level, $operation, $function_name, $message, $doc_code, $event_block, $doc_type, $connector_time, $connector_latency, $stack_trace, $transaction_type, $line_count, $elr_log, $elr_process_id, $elr_mandate_id) : WC_AvaTax_Logger_API_Response {
		$request = $this->get_new_request();
		$request->prepare_request($log_type, $log_level, $operation, $function_name, $message, $doc_code, $event_block, $doc_type, $connector_time, $connector_latency, $stack_trace, $transaction_type, $line_count, $elr_log, $elr_process_id, $elr_mandate_id);
		return $this->perform_request( $request );
	}

	/**
	 * Builds and returns a new API request object
	 *
	 * @see Framework\SV_WC_API_Base::get_new_request()
	 *
	 * @since 2.8.1
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
		$this->set_response_handler( WC_AvaTax_Logger_API_Response::class );
		return new WC_AvaTax_Logger_API_Request();
	}

	/**
	 * Validate the parsed response data.
	 *
	 * Primarily checks for http response returned by the logger API. If not 200 then logs the error locally.
	 *
	 * @since 2.8.3
	 *
	 */
	protected function do_post_parse_response_validation() {
		if($this->response_code !== 200){
			wc_avatax()->log(
                "\nFailed to log error :". json_encode($this->get_request()->get_data()) );
		}
	}

}
