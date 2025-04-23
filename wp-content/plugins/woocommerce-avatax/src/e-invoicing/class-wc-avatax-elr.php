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
 * Handle the checkout-specific functionality.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr {


	/**
	 * Construct the class.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		$this->add_hooks();
	}

	/**
	 * Adds handler actions and filters.
	 *
	 * @since 3.0.0
	 */
	protected function add_hooks() {
		//Add new option to order action to send ivoice to ELR
		add_action( 'woocommerce_order_actions', array( $this, 'add_order_action' ) );

		// Add custom meta box to WooCommerce orders page
		add_action( 'add_meta_boxes', array( $this, 'elr_order_meta_box' ), 999, 1);

		// Send to Avalara E-invoicing and Live Reporting manually through the admin action
		add_action( 'woocommerce_order_action_wc_avatax_elr_send', array( $this, 'process_elr' ));

		// Send to Avalara E-invoicing and Live Reporting when status change
		add_action( 'woocommerce_order_status_on-hold_to_processing', array( $this, 'auto_process_elr' ), 11, 1 );
		add_action( 'woocommerce_order_status_on-hold_to_completed',  array( $this, 'auto_process_elr' ), 11, 1 );
		add_action( 'woocommerce_order_status_failed_to_processing',  array( $this, 'auto_process_elr' ), 11, 1 );
		add_action( 'woocommerce_order_status_failed_to_completed',   array( $this, 'auto_process_elr' ), 11, 1 );
		add_action( 'woocommerce_order_status_processing_to_completed',  array( $this, 'auto_process_elr' ), 11, 1 );

		// Process refund to ELR
		if(wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled()) {
			add_action( 'woocommerce_order_refunded', [ $this, 'process_refund_to_elr' ], 10, 2 );
		}
	}

	/**
	 * Add custom meta box.
	 *
	 * @since 3.0.0
	 */
	function elr_order_meta_box() {
		//If ELR not connected, bail out.
		if(!wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled()) return;

		// Get current screen
		if ( ! function_exists( 'wc_get_page_screen_id' ) ) {
			return false;
		}
		$screen = get_current_screen();
    
		// Check if we're on the shop_order edit page
		if ($screen && $screen->id === wc_get_page_screen_id( 'shop_order' )) {
			add_meta_box(
				'elr-order-meta-box',
				__( 'ELR Order', 'woocommerce-avatax' ),
				function() {
					// Get the saved value
					$order = $this->get_current_order();

					$status_details = $this->get_invoice_status_details($order);
					$invoice_status = $status_details['status'];
					$invoice_status_messages = $status_details['messages'];
					$processing_id = $status_details['processing_id'];
					
					// Output the input field
					echo wc_avatax()->wc_avatax_elr_utilities()->get_elr_status_html($order->get_id(), $invoice_status, $processing_id, $this->get_invoice_status_messages($invoice_status_messages));
				},
				null,
				'side',
				'core'
			);
			$this->elr_order_refund_meta_box();
		}
	}

	/**
	 * Add custom meta box for Refund.
	 *
	 * @since 3.0.0
	 */
	function elr_order_refund_meta_box() {
		// Get the saved value
		$order = $this->get_current_order();
		$order_refunds = $order->get_refunds();

		if(!empty($order_refunds)){
			add_meta_box(
				'elr-refund-meta-box',
				__( 'ELR Refund', 'woocommerce-avatax' ),
				function() {
					
						// Get the saved value
						$order = $this->get_current_order();
						$order_refunds = $order->get_refunds();
						// Loop through the order refunds array
						foreach( $order_refunds as $refund ){
							$status_details = $this->get_invoice_status_details($refund);
							$invoice_status = $status_details['status'];
							$invoice_status_messages = $status_details['messages'];
							$processing_id = $status_details['processing_id'];
							// Output the input field
							echo wc_avatax()->wc_avatax_elr_utilities()->get_elr_refund_status_html($refund->get_id(), $invoice_status, $processing_id, $this->get_invoice_status_messages($invoice_status_messages));
						}
				},
				null,
				'side',
				'core'
			);
		}
	}

	/**
	 * Gets invoice status details from an order.
	 * 
	 * Retrieves and processes the invoice status information for a given order.
	 * If the invoice is posted, gets the current status from Avalara and updates
	 * the order meta accordingly.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order $order The order object to get invoice status for
	 * @return array {
	 *     Array containing invoice status details
	 *     
	 *     @type string $status   The current status of the invoice
	 *     @type string $messages Any status messages associated with the invoice
	 * }
	 */
	public function get_invoice_status_details($order) {
		$invoice_status = "";
		$invoice_status_messages = "";
		$invoice_id="";
		
		$invoice_status_response = $this->get_invoice_status($order);
		
		if ($invoice_status_response) {
			$invoice_status = $invoice_status_response->get_status();
			$invoice_status_messages = $invoice_status_response->get_status_messages();
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_invoice_id');
			
			if ($invoice_status == "Complete") {
				wc_avatax()->wc_avatax_utilities()->update_order_meta(
					$order->get_id(), 
					"_wc_avatax_elr_status", 
					"complete"
				);
			} elseif ($invoice_status == "Error") {
				wc_avatax()->wc_avatax_utilities()->update_order_meta(
					$order->get_id(), 
					"_wc_avatax_elr_status", 
					"error"
				);
			}
			
			// Update invoice status message to order meta
			wc_avatax()->wc_avatax_utilities()->update_order_meta(
				$order->get_id(), 
				"_wc_avatax_elr_status_messages", 
				$invoice_status_messages
			);
		} elseif ($this->is_invoice_completed($order)) {
			$invoice_status = "Complete";
			$invoice_status_messages = wc_avatax()->wc_avatax_utilities()->get_order_meta(
				$order->get_id(), 
				"_wc_avatax_elr_status_messages"
			);
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_invoice_id');
		} elseif ($this->has_invoice_error($order)) {
			$invoice_status = "Error";
			$invoice_status_messages = wc_avatax()->wc_avatax_utilities()->get_order_meta(
				$order->get_id(), 
				"_wc_avatax_elr_status_messages"
			);
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), '_wc_avatax_invoice_id');
		}
		
		return [
			'status' => $invoice_status,
			'messages' => $invoice_status_messages,
			'processing_id' => $invoice_id
		];
	}

	/**
	 * Get the current order object.
	 * 
	 * Retrieves the current order object while maintaining compatibility with both
	 * traditional post-based storage and HPOS (High-Performance Order Storage).
	 * Checks for order in the following sequence:
	 * 1. Global $theorder variable (WC_Order object)
	 * 2. Global $theorder variable (WP_Post object for legacy storage)
	 * 3. Direct order ID
	 * 4. URL parameter 'id'
	 *
	 * @since 3.0.0
	 *
	 * @return WC_Order|false WC_Order object if found, false otherwise.
	 */
	function get_current_order() {
		global $theorder;
		$order = false;
	
		if (isset($theorder)) {
			if ($theorder instanceof WC_Order) {
				$order = $theorder;
			} elseif ($theorder instanceof WP_Post) {
				// Handle legacy post-based storage
				$order_id = $theorder->ID;
			if ('shop_order' === get_post_type($order_id)) {
					$order = wc_get_order($order_id);
			}
			} elseif (is_numeric($theorder)) {
				// Handle direct order ID
				$order = wc_get_order($theorder);
			}
		}
	
		// If order is still not found, try getting from request
		if (!$order && !empty($_GET['id'])) {
			$order = wc_get_order((int) $_GET['id']);
		}
	
		return $order;
	}

	/**
	 * Sends order to Avalara E-invoicing and Live Reporting after an order is marked complete.
	 *
	 * Handles the ELR processing for a completed order. The function checks if the invoice hasn't been posted
	 * or if there was a previous error, and if either condition is true, it triggers the ELR processing.
	 *
	 * @since 3.0.0
	 * @access public
	 *
	 * @param int|WC_Order $order The order ID or WC_Order object to process
	 * @return void
	 */
	public function auto_process_elr($order){
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		if ( ! $order ) {
			return;
		}
		$this->process_elr($order);
	}

	/**
	 * Sends invoice to Avalara.
	 *
	 * @since 3.0.0
	 * @param WC_Order $order The order object.
	 * @return \WC_Order|bool $order The processed order or false on failure.
	 */
	public function process_elr( WC_Order $order ) {
		try{
			if(!$this->can_send_invoice($order)) return;

			//Performance log variables
			$execution_start = hrtime(true);
			$api_time = $execution_end = 0.0;

			// Call the API
			$order_id = $order->get_id();
			wc_avatax()->wc_avatax_utilities()->delete_order_meta($order_id, "_wc_avatax_elr_status", wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, "_wc_avatax_elr_status"));
			wc_avatax()->wc_avatax_utilities()->delete_order_meta($order_id, "_wc_avatax_invoice_id", wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, "_wc_avatax_invoice_id"));
			$invoiceData = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($order_id, $entity_type = 'order');
			// Send the invoice
			$response = wc_avatax()->get_elr_api()->submit_invoice($order, $invoiceData);

			if($response && $invoice = $response->process_response()){
			$api_time = $response->get_response_time();
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, "_wc_avatax_elr_status", "invoice_sent");
				wc_avatax()->wc_avatax_utilities()->update_order_meta($order_id, "_wc_avatax_invoice_id", $invoice->id);
				$order->add_order_note( sprintf( __( 'Order #%s sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ), $order->get_id() ) );

				$execution_end = hrtime(true);
				$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
				$connector_time = $execution_time - $api_time;
				wc_avatax()->elr_logger()->log_performance_elr("SubmitInvoice", "process_elr", "Submitting order invoice.", $order->get_order_key('edit'), "SalesInvoice", $connector_time, $api_time, count($order->get_items()), $invoice->id,$invoice->mandate);
			}
		} catch ( \Exception $e ) {

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() );
			}

			return new Framework\SV_WC_API_Exception( $e->getMessage() );
		}
	}

	/**
	 * Process order refunds to ELR.
	 *
	 * @since 2.9.0
	 *
	 * @param int $order_id The order ID.
	 * @param int $refund_id The refund ID.
	 */
	public function process_refund_to_elr( $order_id, $refund_id ) {
		//Performance log variables
		$execution_start = hrtime(true);
		$api_time = $execution_end = 0.0;
		wc_avatax()->wc_avatax_utilities()->delete_order_meta($refund_id, "_wc_avatax_elr_status", wc_avatax()->wc_avatax_utilities()->get_order_meta($refund_id, "_wc_avatax_elr_status"));
		wc_avatax()->wc_avatax_utilities()->delete_order_meta($refund_id, "_wc_avatax_invoice_id", wc_avatax()->wc_avatax_utilities()->get_order_meta($refund_id, "_wc_avatax_invoice_id"));
		
		$order  = wc_get_order( $order_id );
		$refund = wc_get_order( $refund_id );

		if ( ! $order || ! $refund ) {
			wc_avatax()->log_elr( 'Order or refund not found' );
			return;
		}

		try {
			// Prepare refund payload data
			$payloadData = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($refund_id, $entity_type = 'refund');
			// Send the invoice
			$response = wc_avatax()->get_elr_api()->submit_invoice($order, $payloadData);

			if($response && $invoice = $response->process_response()){
				$api_time = $response->get_response_time();
				// Add the posted status to the refund
				wc_avatax()->wc_avatax_utilities()->update_order_meta($refund_id, "_wc_avatax_elr_status", "invoice_sent");
				wc_avatax()->wc_avatax_utilities()->update_order_meta($refund_id, "_wc_avatax_invoice_id", $invoice->id);

				$order->add_order_note( sprintf( __( 'Refund #%s sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ), $refund->get_id() ) );

				$execution_end = hrtime(true);
				$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
				$connector_time = $execution_time - $api_time;
				wc_avatax()->elr_logger()->log_performance_elr("SubmitInvoice", "process_refund_to_elr", "Submitting refund invoice.", $order->get_order_key('edit'), "SalesInvoice", $connector_time, $api_time, count($refund->get_items()), $invoice->id,$invoice->mandate);
			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() );
			}

			$message  = '<strong>' . __( 'Invoice not sent to Avalara E-invoicing and Live Reporting.', 'woocommerce-avatax' ) . '</strong> ';
			$message .= $e->getMessage();

			$order->add_order_note( $message );
		}
	}

	/**
	 * Gets invoice status.
	 *
	 * @since 3.0.0
	 * @param WC_Order $order The order object.
	 * @return \WC_AvaTax_Elr_API_Invoice_Status_Response invoice status.
	 */
	protected function get_invoice_status($order){
		
		if($this->can_fetch_invoice_status($order)){
			$order_id = $order->get_id();
			$invoice_id = wc_avatax()->wc_avatax_utilities()->get_order_meta($order_id, '_wc_avatax_invoice_id');
		
			return wc_avatax()->get_elr_api()->get_invoice_status($invoice_id);
		}
		else
			return false;
	}

	/**
	 * Gets invoice status messages.
	 *
	 * @since 3.0.0
	 * @param WC_Order $order The order object.
	 * @return \string invoice status messages.
	 */
	public function get_invoice_status_messages($messages){
		$message_string = '<ul class="order_notes">';
		if(!empty($messages)){
			foreach((array)$messages as $message){
				$date = new DateTime($this->get_message_date($message->eventDateTime));
				$formated_dt =  date_format($date, wc_date_format()).' at '.date_format($date, wc_time_format());
				$message_string = $message_string . '<li rel="243" class="note">
														<div class="note_content">
															<p>'. $message->message .'</p>
														</div>
														<p class="meta">
															<abbr class="exact-date" title="2024-02-17 20:14:32"> '.
															$formated_dt
															.'</abbr>
														</p>
													</li>';
			}
		}
		$message_string = $message_string . '</ul>';
		
		return $message_string;
	}

	/**
	 * Determine if an order invoice has already been sent to AvaTax.
	 *
	 * @since 3.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order invoice has already been sent to AvaTax.
	 */
	public function is_invoice_posted( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}
		return (!empty(wc_avatax()->wc_avatax_utilities()->get_order_meta($order->get_id(), "_wc_avatax_invoice_id", true)));
	}


	/**
	 * Determines if an invoice can be sent for the given order.
	 * 
	 * @since 3.0.0
	 * 
	 * @param mixed $order Order ID (integer) or WC_Order object
	 * @return boolean Returns true if the invoice hasn't been posted yet or if there was a previous error,
	 *                 false otherwise
	 */
	public function can_send_invoice($order){
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return (!$this->is_invoice_posted($order) || $this->has_invoice_error($order));
	}

	/**
	 * Determines if the invoice status can be fetched for the given order.
	 * 
	 * @since 3.0.0
	 * 
	 * @param mixed $order Order ID (integer) or WC_Order object
	 * @return boolean Returns true if the invoice has been posted AND (has an error OR is not completed),
	 *                 false otherwise. This indicates whether the status needs to be checked/updated.
	 */
	public function can_fetch_invoice_status($order){
		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ($this->is_invoice_posted($order) && ($this->has_invoice_error($order) || !$this->is_invoice_completed($order)));
	}

	/**
	 * Checks if the order has an invoice error status.
	 *
	 * @since 3.0.0
	 *
	 * @param WC_Order|int $order Order object or order ID.
	 * @return bool Returns true if the order has an invoice error status, false otherwise.
	 */
	public function has_invoice_error( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( wc_avatax()->wc_avatax_elr_utilities()->order_has_elr_status( $order->get_id(), 'error' ) );
	}

	/**
	 * Determine if an order invoice submission has completed or not.
	 *
	 * @since 3.0.0
	 * @param \WC_Order|int $order The order object or ID.
	 * @return bool Whether the order invoice has already been sent to AvaTax.
	 */
	public function is_invoice_completed( $order ) {

		if ( is_numeric( $order ) ) {
			$order = wc_get_order( $order );
		}

		return ( wc_avatax()->wc_avatax_elr_utilities()->order_has_elr_status( $order->get_id(), 'complete' ) );
	}

	/**
	 * Formats a date string into a standardized datetime format.
	 * 
	 * @param string $date_string The input date string to be formatted
	 * @return string Formatted date in 'Y-m-d H:i:s' format
	 */
	protected function get_message_date($date_string){
		$lastIndex = strripos($date_string, ':');

		return date('Y-m-d H:i:s', strtotime(substr($date_string, 0, $lastIndex )));
	}

	/**
	 * Add a "Send to Avalara E-invoicing and Live Reporting" action to the order action options.
	 *
	 * @since 3.0.0
	 * @global WC_Order $theorder The current order object.
	 * @param array $actions The available order actions.
	 * @return array $actions
	 */
	public function add_order_action( $actions ) {
		global $theorder;
		
		$status = $theorder->get_status();
		if($status !== "completed" && $status !== "processing") return $actions;

		if(wc_avatax()->wc_avatax_elr_utilities()->is_elr_enabled() && $this->can_send_invoice($theorder)){
			$actions['wc_avatax_elr_send'] = __( 'Send to Avalara E-invoicing and Live Reporting', 'woocommerce-avatax' );
		}

		return $actions;
	}
}