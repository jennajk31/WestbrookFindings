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
 * The AvaLogger API logger request class.
 *
 * @since 2.8.1
 */
class WC_AvaTax_Logger_API_Request extends WC_AvaTax_API_Request {

	/**
	 * Constructs the class.
	 *
	 * @since 2.8.1
	 *
	 */
	public function __construct() {
		$this->path = "";
		$this->method = 'POST';
	}

	/** Prepares the logger API request object 
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
	 */
	public function prepare_request( $log_type, $log_level, $operation, $function_name, $message, $doc_code, $event_block, $doc_type, $connector_time, $connector_latency, $stack_trace, $transaction_type, $line_count, $elr_log, $elr_process_id, $elr_mandate_id)	{

		$connector_version =		wc_avatax()::VERSION;
		$client_string = 			wc_avatax()::CLIENT_STRING;
		$connector_id = 			($elr_log ? wc_avatax()::ELR_CONNECTOR_ID : wc_avatax()::CONNECTOR_ID);
		$connector_name = 			"AvaTax For WooCommerce || " . $connector_version . "v2; " . $client_string;
		$connector_environment = 	(($elr_log ? wc_avatax()->get_elr_api_environment() : wc_avatax()->get_api_environment()) === "development" ? "Sandbox" : "Production" );
		
		//TODO: Read the ELR account number from options.
		$account_id = 				($elr_log ? get_option( 'wc_avatax_elr_tenant_id', '' ) : get_option( 'wc_avatax_api_account_number', '' ));
		$wc_version = 				"WooCommerce " . (( defined( 'WC_VERSION' ) && WC_VERSION ) ? WC_VERSION : "");
		$wp_version_string =		"WordPress " . (( defined( 'ABSPATH' ) && isset( $GLOBALS['wp_version'] ) ) ? $GLOBALS['wp_version'] : '');
		

		// Set the base request params
		$data = array(
			'CallerAccuNum'			=> $account_id, 			//Account for which we are logging
			'LogType'				=> $log_type, 				//Integer (0 = Performance, 1 = Debug, 2 = ConfigAudit) - Numbering is for dll versions and string is for SaaS / directly accessing log entries API
			'LogLevel'				=> $log_level, 				//Integer (0 = Error, 1 = Exception, 2 = Informational) - Numbering is for dll versions and string is for SaaS / directly accessing log entries API
			'ConnectorName'			=> $connector_name,
			'ConnectorVersion'		=> $connector_version,
			'ERPDetails'			=> $wc_version . ", " . $wp_version_string,
			'ClientString'			=> $client_string, 			//Client string associated to each major version
			'Operation'				=> $operation, 				//ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode
			'AvaTaxEnvironment'		=> $connector_environment, 
			'Source'				=> "Backend_Service", 		//This should capture the source where this call is triggered from. This is important to identify the source of operation to debug issues, as we may trigger certain operations at many integration points. Sample values : Backend hook, Backend service, SalesOrder , SalesReturnOrder, SalesInvoice, CreditMemo etc
			'FunctionName'			=> $function_name, 			//Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
			'Message'				=> $message, 				//Message which give proper information about why this log is logged
			'DocCode'				=> $doc_code, 				//Document code for transaction ( Applicable only for Usage and performance logs)
			'EventBlock'			=> $event_block, 			//In case where performance metrics is not under expected range : Integer (0 = InternalFunction, 1 = PreGetTax, 2 = PostGetTax, 3 = PrePostTax, 4 = PostPostTax, 5 = PreCommitTax, 6 = PostCommitTax, 7 = PreAdjustTax, 8 = PostAdjustTax, 9 = PreCancelTax, 10 = PostCancelTax, 11 = PreGetTaxHistory, 12 = PostGetTaxHistory, 13 = PreReconcileTaxHistory, 14 = PostReconcileTaxHistory, 15 = PreBatchTax, 16 = PostBatchTax)
			'DocType'				=> $doc_type, 				//Document type passed in transaction, for ex - SalesOrder , SalesInvoice , PurchaseInvoice etc. Required when performing tax operations.
			'ConnectorTime'			=> $connector_time, 		//Time required by connector code for processing a transaction which includes request formation and updating values from response in ERP. Required when writing performance log.
			'ConnectorLatency'		=> $connector_latency, 		//Time required to get response from service call from initiation of request. Required when writing performance log
			'StackTrace'			=> $stack_trace,			//Trace the actual exception from where it is originated. Validation error may not have stacktrace.
			'TransactionType'		=> $transaction_type,       //Type of transaction which is being performed. Required when writing performance log.
			'LogMessageType'		=> "Info",
			'LineCount'				=> $line_count
		);

		if($elr_log){
			$data['ELRProcessID'] = $elr_process_id;
			$data['ELRCompanyID'] = get_option( 'wc_avatax_elr_company', '' );
			$data['ELRMandateID'] = $elr_mandate_id;
		}

		$this->data = $data;
	}
}
