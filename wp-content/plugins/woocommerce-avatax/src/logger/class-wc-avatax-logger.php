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

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

/**
 * Plugin logger class.
 *
 * @since 2.8.1
 *
 * @method 
 */
class WC_AvaTax_Logger {

    /** Instance of AvaTax Plugin of type WC_AvaTax */
    protected $plugin;

    /** Instance of AvaTax logger API of type WC_AvaTax_Logger_API */
    protected $logger_api;
    protected $elr_log;

    public function __construct($elr_logger = false) {
        $this->elr_log = $elr_logger;
        $this->plugin = wc_avatax();
        $this->init_logger();
    }

    /** Initializes the logger
     * 
     * @since 2.8.1
     */
    protected function init_logger() {
        $this->logger_api = $this->elr_log ? $this->plugin->get_elr_logger_api() : $this->plugin->get_logger_api();

        if($this->elr_log){
            add_action( 'elr_event_log_info', array($this, 'post_log_to_connector_logger'), 10, 16 );
        }
        else{
            add_action( 'event_log_info', array($this, 'post_log_to_connector_logger'), 10, 16 );
        }
    }

    public function test(){
        wc_avatax()->log("Testing AvaLogger");
    }

    /** 
     * 
     * @since 2.8.1
     * 
     * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
     * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
     * @param string $message which give proper information about why this log is logged.
     */
    public function log_info($operation, $function_name, $message){
        $args = ["LogType" => LogType::ConfigAudit, "LogLevel" => LogLevel::Informational, "EventBlock" => EventBlock::InternalFunction];

        $this->log(
                    $operation, 
                    $function_name, 
                    $message, 
                    '', 
                    '', 
                    '', 
                    '', 
                    '',
                    [],
                    0,
                    $args,
                    "",
                    ""
                );
    }

    /** 
     * 
     * @since 2.8.1
     * 
     * @param string $event is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
     * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
     * @param string $message which give proper information about why this log is logged.
     */
    public function log_event($event, $function_name, $message){
        $args = ["LogType" => LogType::ConfigAudit, "LogLevel" => LogLevel::Informational, "EventBlock" => EventBlock::InternalFunction];

        $this->log(
                    $event, 
                    $function_name, 
                    $message, 
                    '', 
                    '', 
                    '', 
                    '', 
                    '',
                    [],
                    0,
                    $args,
                    "",
                    ""
                );
    }

    /** 
     * 
     * @since 2.8.1
     * 
     * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
     * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
     * @param string $message which give proper information about why this log is logged.
	 * @param string $stack_trace Trace the actual exception from where it is originated. Validation error may not have stacktrace.
     */
    public function log_error($operation, $function_name, $message, $stack_trace){
        $args = ["LogType" => LogType::Debug, "LogLevel" => LogLevel::Error, "EventBlock" => EventBlock::InternalFunction];

        $this->log(
                    $operation, 
                    $function_name, 
                    $message, 
                    '', 
                    '', 
                    '', 
                    '', 
                    $stack_trace,
                    [],
                    0,
                    $args,
                    "",
                    ""
                );
    }

    /** 
     * 
     * @since 3.0.0
     * 
     * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
     * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
     * @param string $message which give proper information about why this log is logged.
	 * @param string $stack_trace Trace the actual exception from where it is originated. Validation error may not have stacktrace.
     */
    public function log_exception($operation, $function_name, $message, $stack_trace){
        $args = ["LogType" => LogType::Debug, "LogLevel" => LogLevel::Exception, "EventBlock" => EventBlock::InternalFunction];

        $this->log(
                    $operation, 
                    $function_name, 
                    $message, 
                    '', 
                    '', 
                    '', 
                    '', 
                    $stack_trace,
                    [],
                    0,
                    $args,
                    "",
                    ""
                );
    }

    /** 
     * 
     * @since 2.8.1
     * 
     * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
     * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
     * @param string $message which give proper information about why this log is logged.
	 * @param string $doc_code Document code for transaction ( Applicable only for Usage and performance logs)
	 * @param string $doc_type Document type passed in transaction, for ex - SalesOrder , SalesInvoice , PurchaseInvoice etc. Required when performing tax operations.
     * @param string $connector_time Time required by connector code for processing a transaction which includes request formation and updating values from response in ERP. Required when writing performance log.
	 * @param string $connector_latency Time required to get response from service call from initiation of request. Required when writing performance log	 
     * @param array $transaction_type Type of transaction which is being performed. Required when writing performance log.
     * @param int $line_count Number if lines in get tax/create transaction call.
     */
    public function log_performance($operation, $function_name, $message, $doc_code, $doc_type, $connector_time, $connector_latency, $transaction_type, $line_count){
        $args = ["LogType" => LogType::Performance,  "LogLevel" => LogLevel::Informational, "EventBlock" => EventBlock::InternalFunction];
        
        $this->log(
                    $operation, 
                    $function_name, 
                    $message, 
                    $doc_code, 
                    $doc_type, 
                    $connector_time, 
                    $connector_latency,
                    "",
                    $transaction_type,
                    $line_count,
                    $args,
                    "",
                    ""
                );
    }

     /** 
     * 
     * @since 2.8.1
     * 
     * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
     * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
     * @param string $message which give proper information about why this log is logged.
	 * @param string $doc_code Document code for transaction ( Applicable only for Usage and performance logs)
	 * @param string $doc_type Document type passed in transaction, for ex - SalesOrder , SalesInvoice , PurchaseInvoice etc. Required when performing tax operations.
     * @param string $connector_time Time required by connector code for processing a transaction which includes request formation and updating values from response in ERP. Required when writing performance log.
	 * @param string $connector_latency Time required to get response from service call from initiation of request. Required when writing performance log	 
     * @param int $line_count Number if lines in get tax/create transaction call.
	 * @param string $elr_process_id Process Id of the transaction. Required when writing performance log.
     * @param string $elr_mandate_id Mandate Id of the transaction. Required when writing performance log.
     */
    public function log_performance_elr($operation, $function_name, $message, $doc_code, $doc_type, $connector_time, $connector_latency, $line_count, $elr_process_id, $elr_mandate_id ){
        $args = ["LogType" => LogType::Performance,  "LogLevel" => LogLevel::Informational, "EventBlock" => EventBlock::InternalFunction];
        
        $this->log(
                    $operation, 
                    $function_name, 
                    $message, 
                    $doc_code, 
                    $doc_type, 
                    $connector_time, 
                    $connector_latency, 
                    "",
                    [],
                    $line_count,
                    $args,
                    $elr_process_id,
                    $elr_mandate_id
                );
    }

    /** Logs the message/information to AvaTax
     * 
     * @since 2.8.1
     * 
     * @param string $args["LogType"] 0 = Performance, 1 = Debug, 2 = ConfigAudit
	 * @param string $args["LogLevel"] of the log. 0 = Error, 1 = Exception, 2 = Informational
	 * @param string $operation is what kind of AvaTax operation for which loggin is done. ConfigChanges,Window Opened,Window Closed, IsAuthorized , GetTax / CreateTransaction / CreateOrAdjustTransaction , PostTax / SettleTransaction, Commit/CommitTransaction, AddressValidation/Resolveaddress , CertCapture , Reconcile,ListTransactionBycode 
	 * @param string $function_name Update this tag with exact function name being utilised in the code to perform this operation. This will differ for different systems and different operations.
	 * @param string $message Message which give proper information about why this log is logged.
	 * @param string $doc_code Document code for transaction ( Applicable only for Usage and performance logs)
	 * @param string $args["EventBlock"] In case where performance metrics is not under expected range : Integer (0 = InternalFunction, 1 = PreGetTax, 2 = PostGetTax, 3 = PrePostTax, 4 = PostPostTax, 5 = PreCommitTax, 6 = PostCommitTax, 7 = PreAdjustTax, 8 = PostAdjustTax, 9 = PreCancelTax, 10 = PostCancelTax, 11 = PreGetTaxHistory, 12 = PostGetTaxHistory, 13 = PreReconcileTaxHistory, 14 = PostReconcileTaxHistory, 15 = PreBatchTax, 16 = PostBatchTax)
	 * @param string $doc_type Document type passed in transaction, for ex - SalesOrder , SalesInvoice , PurchaseInvoice etc. Required when performing tax operations.
	 * @param string $connector_time Time required by connector code for processing a transaction which includes request formation and updating values from response in ERP. Required when writing performance log.
	 * @param string $connector_latency Time required to get response from service call from initiation of request. Required when writing performance log
	 * @param string $stack_trace Trace the actual exception from where it is originated. Validation error may not have stacktrace.
     * @param array $transaction_type Type of transaction which is being performed. Required when writing performance log.
     * @param int $line_count Number if lines in get tax/create transaction call.
	 * @param string $elr_process_id Process Id of the transaction. Required when writing performance log.
     * @param string $elr_mandate_id Mandate Id of the transaction. Required when writing performance log.
	 */
    protected function log( $operation, $function_name, $message, $doc_code, $doc_type, $connector_time, $connector_latency, $stack_trace, $transaction_type, $line_count, $args, $elr_process_id, $elr_mandate_id) {
        
        if($operation !== "Disconnect") {
            $scheduled_time = time() + 10;
            
            wp_schedule_single_event( $scheduled_time, ($this->elr_log ? 'elr_event_log_info' : 'event_log_info'), array( $args["LogType"], 
                $args["LogLevel"], 
                $operation, 
                $function_name, 
                $message, 
                $doc_code, 
                $args["EventBlock"], 
                $doc_type,
                $connector_time, 
                $connector_latency, 
                $stack_trace,
                $transaction_type,
                $line_count,
                $this->elr_log,
                $elr_process_id,
                $elr_mandate_id
            ) );
        }
        else{
            if(is_object($this->logger_api)){
                $response = $this->logger_api->log(
                                                    $args["LogType"], 
                                                    $args["LogLevel"], 
                                                    $operation, 
                                                    $function_name, 
                                                    $message, 
                                                    $doc_code, 
                                                    $args["EventBlock"], 
                                                    $doc_type,
                                                    $connector_time, 
                                                    $connector_latency, 
                                                    $stack_trace,
                                                    $transaction_type,
                                                    $line_count,
                                                    $this->elr_log,
                                                    $elr_process_id,
                                                    $elr_mandate_id
                                                );
            }
    
        }
       
        
    }

    /**
	 * Logs the information to Connector logger.
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
	 * @param string $elr_process_id Process Id of the transaction. Required when writing performance log.
     * @param string $elr_mandate_id Mandate Id of the transaction. Required when writing performance log.
	 */
	public function post_log_to_connector_logger($log_type, $log_level, $operation, $function_name, $message, $doc_code, $event_block, $doc_type, $connector_time, $connector_latency, $stack_trace, $transaction_type, $line_count, $elr_log, $elr_process_id, $elr_mandate_id) {
		if(is_object($this->logger_api)){
            $response = $this->logger_api->log(
                $log_type,
                $log_level,
                $operation,
                $function_name,
                $message,
                $doc_code,
                $event_block,
                $doc_type,
                $connector_time,
                $connector_latency,
                $stack_trace,
                $transaction_type,
                $line_count,
                $elr_log,
                $elr_process_id,
                $elr_mandate_id
            );
        }
	}
}

/** 
 * 
 * @since 2.8.1
 * 
 */
class LogType {
    const Performance = "Performance";
    const Debug = "Debug";
    const ConfigAudit = "ConfigAudit";
}

/** 
 * 
 * @since 2.8.1
 * 
 */
class LogLevel {
    const Error = "Error";
    const Exception = "Exception";
    const Informational = "Informational";
}

/** 
 * 
 * @since 2.8.1
 * 
 */
class EventBlock {
    const InternalFunction = 0;
    const PreGetTax = 1;
    const PostGetTax = 2;
    const PrePostTax = 3;
    const PostPostTax = 4;
    const PreCommitTax = 5;
    const PostCommitTax = 6;
    const PreAdjustTax = 7;
    const PostAdjustTax = 8;
    const PreCancelTax = 9;
    const PostCancelTax = 10;
    const PreGetTaxHistory = 11;
    const PostGetTaxHistory = 12;
    const PreReconcileTaxHistory = 13;
    const PostReconcileTaxHistory = 14;
    const PreBatchTax = 15;
    const PostBatchTax = 16;
}