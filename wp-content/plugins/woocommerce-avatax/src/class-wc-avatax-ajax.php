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
 * Handle the AJAX-specific functionality.
 *
 * @since 1.0.0
 */
class WC_AvaTax_AJAX {


	/** @var bool $reload_order_notes_after_calculating_taxes whether order notes should be reloaded after calculating order taxes in admin */
	protected $reload_order_notes_after_calculating_taxes = false;


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->add_hooks();
	}


	/**
	 * Adds handler actions and filters.
	 *
	 * @since 1.13.0
	 */
	protected function add_hooks() {

		// validate the Origin Address settings fields
		add_action( 'wp_ajax_wc_avatax_validate_origin_address', [ $this, 'validate_origin_address' ] );

		// validate the customer address at checkout
		add_action( 'wp_ajax_wc_avatax_validate_customer_address',        [ $this, 'validate_customer_address' ] );
		add_action( 'wp_ajax_nopriv_wc_avatax_validate_customer_address', [ $this, 'validate_customer_address' ] );
		add_action( 'wp_ajax_wc_avatax_revalidate_customer_address_on_addresschange',        [ $this, 'revalidate_customer_address' ] );

		// Cross Border ajax callback methods
		add_action( 'wp_ajax_wc_avatax_resync_error_products',    [ $this, 'resync_products_with_errors' ] );
		add_action( 'wp_ajax_wc_avatax_toggle_cross_border_sync', [ $this, 'toggle_cross_border_sync' ] );

		add_action( 'wp_ajax_wc_avatax_sync_tax_codes', [ $this, 'tax_code_sync' ] );
		add_action( 'wp_ajax_wc_avatax_tax_code_lookp', [ $this, 'tax_code_lookup' ]);
		
		// display and save the product variation tax code field
		add_action( 'woocommerce_product_after_variable_attributes', [ $this, 'display_product_variation_code_fields' ], 15, 3 );
		add_action( 'woocommerce_save_product_variation',            [ $this, 'save_product_variation_code_fields' ] );

		// save the product tax code quick edit field
		add_action( 'woocommerce_product_quick_edit_save', [ $this, 'save_product_tax_code_quick_edit' ] );

		// save the tax code field when a new product category is created
		add_action( 'created_product_cat', [ $this, 'save_category_code_fields' ], 10, 2 );

		// add estimated AvaTax calculations to orders when "Calculate Taxes" is run from the admin
		add_action( 'woocommerce_saved_order_items', [ $this, 'estimate_order_tax' ] );

		// check for Landed Cost warnings after calculating taxes in admin and possibly reload order notes
		add_action( 'wc_avatax_after_order_tax_calculated', [ $this, 'check_for_landed_cost_warnings' ], 10, 2 );
		add_action( 'woocommerce_order_item_add_action_buttons', [ $this, 'maybe_trigger_order_notes_reload'] );
		add_action( 'wp_ajax_wc_avatax_get_order_notes', [ $this, 'get_order_notes' ] );
		add_action( 'wp_ajax_wc_avatax_get_ecommerce_token', [ $this, 'get_ecommerce_token' ] );
		add_action( 'wp_ajax_wc_avatax_submit_map_perform', [ $this, 'submit_map_perform' ] );

		add_action('wp_ajax_wc_avatax_download_certificate',[$this,'download_certificate']);
		add_action( 'wp_ajax_wc_avatax_invite_customer_certificate', [ $this, 'invite_customer_certificate' ] );
		add_action( 'wp_ajax_wc_avatax_unlink_customer_certificate', [ $this, 'unlink_customer_certificate' ] );
		add_action( 'wp_ajax_wc_avatax_manage_certificate_link', [ $this, 'manage_certificate_link' ] );
		add_action( 'wp_ajax_wc_avatax_update_alternate_id', [ $this, 'update_alternate_id' ] );
		add_action( 'wp_ajax_wc_avatax_update_caching_transient_for_customer', [ $this, 'update_caching_transient_for_customer' ] );

		add_action( 'wp_ajax_wc_avatax_disconnect', [ $this, 'disconnect_avatax' ] );
		add_action( 'wp_ajax_wc_avatax_update_connection', [ $this, 'update_connection' ] );
		add_action( 'wp_ajax_wc_avatax_refresh_config', [ $this, 'refresh_config' ] );

		//Hide our custom line item meta from the order admin
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );

		add_action( 'wp_ajax_wc_avatax_elr_disconnect', [ $this, 'disconnect_elr' ] );
		add_action( 'wp_ajax_wc_avatax_elr_save_custom_fields', [ $this, 'save_custom_fields' ] );
		add_action( 'wp_ajax_wc_avatax_refresh_elr_status', [ $this, 'refresh_elr_status' ] );
		add_action( 'wp_ajax_wc_avatax_send_refund_to_avalara', [ $this, 'send_refund_to_avalara' ] );
		add_action( 'wp_ajax_wc_avatax_send_order_to_avalara', [ $this, 'send_order_to_avalara' ] );
		
	}

	/**
	 * Checks for landed cost warnings in the tax calculation response and sets a flag for later use.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @param int $order_id order ID (unused)
	 * @param WC_AvaTax_API_Tax_Response $response tax calculation response object
	 * @return void
	 */
	public function check_for_landed_cost_warnings( $order_id, $response ) {

		foreach ( $response->get_messages() as $message ) {

			if ( 'MissingHSCodeWarning' === $message->summary ) {

				$this->reload_order_notes_after_calculating_taxes = true;
				break;
			}
		}
	}


	/**
	 * Triggers admin JS to reload order notes.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @return void
	 */
	public function maybe_trigger_order_notes_reload() {

		if ( $this->reload_order_notes_after_calculating_taxes ) {
			echo '<script>window.wc_avatax_admin.reload_order_notes("' . wp_create_nonce( 'wc_avatax_get_order_notes' ) .  '")</script>';
		}
	}


	/**
	 * Gets the order notes & sends an AJAX success response with teh rendered notes HTML.
	 *
	 * @internal
	 *
	 * @since 1.16.0
	 *
	 * @see \WC_AJAX::save_order_items() - based on this method
	 *
	 * @return void
	 */
	public function get_order_notes() {

		check_ajax_referer( 'wc_avatax_get_order_notes', 'security' );

		if ( ! isset( $_REQUEST['order_id'] ) || ! current_user_can( 'edit_shop_orders' )) {
			wp_die( -1 );
		}

		wp_send_json_success( [ 'notes_html' => $this->get_order_notes_html( absint( $_REQUEST['order_id'] ) ) ] );
	}


	/**
	 * Gets the order notes HTML for the given order ID.
	 *
	 * @since 1.16.0
	 *
	 * @param int $order_id
	 * @return false|string
	 */
	protected function get_order_notes_html( int $order_id ) {

		ob_start();
		$notes = wc_get_order_notes( [ 'order_id' => $order_id ] );

		if ( defined('WC_ABSPATH') ) {
			include WC_ABSPATH . '/includes/admin/meta-boxes/views/html-order-notes.php';
		}

		return ob_get_clean();
	}


	/**
	 * Validate the Origin Address settings fields.
	 *
	 * @since 1.0.0
	 */
	public function validate_origin_address() {

		//Performance log variables
		$execution_start = hrtime(true);
		$api_time = $execution_end = 0.0;
		$response_string = "";

		// No nonce? No go
		check_ajax_referer( 'wc_avatax_validate_origin_address', 'nonce' );

		try {

			/**
			 * Fire before validating the origin address.
			 *
			 * @since 1.0.0
			 */
			do_action( 'wc_avatax_before_origin_address_validated' );

			$response = wc_avatax()->get_api()->validate_address( array(
				'address_1' => Framework\SV_WC_Helper::get_requested_value( 'line1' ),
				'city'      => Framework\SV_WC_Helper::get_requested_value( 'city' ),
				'state'     => Framework\SV_WC_Helper::get_requested_value( 'region' ),
				'country'   => Framework\SV_WC_Helper::get_requested_value( 'country' ),
				'postcode'  => Framework\SV_WC_Helper::get_requested_value( 'postcode' ),
			) );

			$api_time = $response->get_response_time();
			$response_string = json_encode($response);

			// Documented in `WC_AvaTax_Settings::save_address_field`
			$address = (array) apply_filters( 'wc_avatax_save_address_field', $response->get_normalized_address() );

			// Save the validated address
			update_option( 'wc_avatax_origin_address', $address );

			/**
			 * Fire after validating the origin address.
			 *
			 * @since 1.0.0
			 * @param array $address The validated and normalized address.
			 */
			do_action( 'wc_avatax_after_origin_address_validated', $address );

			$execution_end = hrtime(true);
			$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
			$connector_time = $execution_time - $api_time;
			wc_avatax()->logger()->log_performance("ValidateAddress", "validate_origin_address", "Validating the address.", "", "", $connector_time, $api_time, [], 0);

			wp_send_json( array(
				'code'    => 200,
				'address' => $address,
			) );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "validate_origin_address", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 2.6.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax {

		return wc_avatax();
	}

	/**
	 * Invite customer to add certificate
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function invite_customer_certificate() {

		try {
			$userid		= $_POST['userId'];
			$user_data =wc_avatax()->get_user_data($userid);
			if(!empty((array) $user_data['customerCode'])){

				if(!empty((array) wc_avatax()->check_if_customer_exists_return($user_data['customerCode'])))
			{
				$certificateInviteResponse = $this->get_plugin()->get_api()->invite_customer_to_add_certificate($user_data['customerCode'],$user_data['emailAddress']);
			}
			else
			{
				$isUserAdded = wc_avatax()->add_customer_to_avatax($userid);
				if($isUserAdded === true){
					$certificateInviteResponse = $this->get_plugin()->get_api()->invite_customer_to_add_certificate($user_data['customerCode'],$user_data['emailAddress']);
				}
			}
			if(empty($certificateInviteResponse)){
				wp_send_json( array(
					'code'    => 0,
					'message' => "Failed to send invite to ".$user_data['emailAddress'].".",
				) );
			}
			else{
				wp_send_json( array(
					'code'    => 200,
					'message' => "Invite sent successfully to ".$user_data['emailAddress'].".",
				) );
			}
			}
			else{
				wp_send_json( array(
					'code'    => 0,
					'message' => "Please entry billing details and save the customer to sent invite.",
				) );
			}
			

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "invite_customer_certificate", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
				'message' => "Failed to send invite."
			) );
		}
	}
	/**
	 * Update alternateId of the customer in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function update_alternate_id()
	{
		$customerCode		= $_POST['customerCode'];
		$userId		= $_POST['userId'];
			
		$current_user = wp_get_current_user();
		$user_data = wc_avatax()->get_user_data($userId);
		$alternateId = $user_data['alternateId']!=null  ? $user_data['alternateId'] : $current_user->user_email . "_" . $userId;
		$args = array(
			"customerCode" =>$customerCode,
			"alternateId" =>$alternateId,
		);
		$customer_update_response = wc_avatax()->get_api()->update_customer_alternate_Id($args);

		if(empty($customer_update_response)){
			wp_send_json( array(
				'code'    => 0,
				'message' => "AlternateId Not Updated.",
			) );
		}
		else{
			wp_send_json( array(
				'code'    => 200,
				'message' => "AlternateId Updated.",
			) );
		}
	}
	/**
	 * Update caching transient of the customer in Avatax
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function update_caching_transient_for_customer()
	{
		
		$customerCode		= $_POST['customerCode'];
		set_transient( "wc_avatax_api_" . $customerCode, true, DAY_IN_SECONDS );
	}
	/**
	 * Unlink certificate from customer Avatax account
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function unlink_customer_certificate() {
		try {

			$certid     = $_POST['certificateId'];
			$userid		= $_POST['userId'];
			$response = wc_avatax()->get_api()->unlink_certificate($certid,$userid);
			if(!$response){
				wp_send_json( array(
					'code'    => 0,
					'message' => "Failed to unlink certificate.",
				) );
			}
			else{
				wp_send_json( array(
					'code'    => 200,
					'message' => "Certificate unlinked successfully.",
				) );
			}
			

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}
			
			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "unlink_customer_certificate", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'message' => 'Failed to unlink certificate.',
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}

	public function manage_certificate_link() {
		$url = (get_permalink( get_option('woocommerce_myaccount_page_id') ). 'tax-certificate');
		wc_avatax()->log("manage_certificate_link called-> " . $url );
		wp_send_json( array(
			'code'  => 200,
			'data' => $url,
		));
	}

	/**
	 * Download customer certificate
	 *
	 * @since 2.6.0
	 *
	 * @return json
	 */
	public function download_certificate()
	{
		try {
			$certid     = $_POST['certid'];

			$company_id = (int)wc_avatax()->get_company_id();
			$environment = wc_avatax()->get_api_environment();
			$request_uri = ( 'production' === $environment ) ? 'https://rest.avatax.com/api/v2/' : 'https://sandbox-rest.avatax.com/api/v2/';
			$download_request_uri = $request_uri . "companies/$company_id/certificates/$certid/attachment";
			
			$account_number = get_option( 'wc_avatax_api_account_number' );
			$license_key    = get_option( 'wc_avatax_api_license_key' );
			$authToken = sprintf( 'Basic %s', base64_encode( "{$account_number}:{$license_key}" ) );

			$opts = array(
				'http'=>array(
				  'method'=>"GET",
				  'header'=>"Accept-language: en\r\n" .
							"authorization: $authToken\r\n"
				)
			  );
			  
			  $context = stream_context_create($opts);
			  $file_content=file_get_contents($download_request_uri, false, $context); 
			  $data = base64_encode($file_content);
			  wp_send_json( array(
				'code'  => 200,
				'data' => $data,
			) );
		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "download_certificate", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}
	/**
	 * Validate the customer address at checkout.
	 *
	 * @since 1.0.0
	 */
	public function revalidate_customer_address() {
		WC()->session->set( 'wc_avatax_address_validated', false );
		wp_send_json( array(
			'code'    => 200
		) );
	}
	/**
	 * Validate the customer address at checkout.
	 *
	 * @since 1.0.0
	 */
	public function validate_customer_address() {

		//Performance log variables
		$execution_start = hrtime(true);
		$api_time = $execution_end = 0.0;
		$response_string = "";

		// No nonce? No go
		if ( ! wp_verify_nonce( Framework\SV_WC_Helper::get_requested_value( 'nonce' ), 'wc_avatax_validate_customer_address' ) ) {
			wp_die();
		}

		try {

			/**
			 * Fire before validating a customer address.
			 *
			 * @since 1.0.0
			 * @param array $address The validated and normalized address.
			 */
			do_action( 'wc_avatax_before_customer_address_validated' );

			$response = wc_avatax()->get_api()->validate_address( array(
				'address_1' => Framework\SV_WC_Helper::get_posted_value( 'address_1' ),
				'address_2' => Framework\SV_WC_Helper::get_posted_value( 'address_2' ),
				'city'      => Framework\SV_WC_Helper::get_posted_value( 'city' ),
				'state'     => Framework\SV_WC_Helper::get_posted_value( 'state' ),
				'country'   => Framework\SV_WC_Helper::get_posted_value( 'country' ),
				'postcode'  => Framework\SV_WC_Helper::get_posted_value( 'postcode' ),
			) );

			$api_time = $response->get_response_time();
			$response_string = json_encode($response);
			
			if($response->has_errors())
			{
				wp_send_json( array(
					'code'    => 404,
					'error' =>  wc_avatax()->wc_avatax_utilities()->get_address_error_messages($response),
				) );
			}
			
			$address = $response->get_normalized_address();

			// Set the shipping address values to the normalized address
			WC()->customer->set_shipping_address( $address['address_1'] );
			WC()->customer->set_shipping_address_2( $address['address_2'] );
			WC()->customer->set_shipping_city( $address['city'] );
			WC()->customer->set_shipping_state( $address['state'] );
			WC()->customer->set_shipping_country( $address['country'] );
			WC()->customer->set_shipping_postcode( $address['postcode'] );

			$type = Framework\SV_WC_Helper::get_posted_value( 'type' );

			// If validating a billing address, set those values too
			if ( 'billing' === $type ) {

				WC()->customer->set_billing_address( $address['address_1'] );
				WC()->customer->set_billing_address_2( $address['address_2'] );
				WC()->customer->set_billing_city( $address['city'] );
				WC()->customer->set_billing_state( $address['state'] );
				WC()->customer->set_billing_country( $address['country'] );
				WC()->customer->set_billing_postcode( $address['postcode'] );
			}

			// Prepend the address type (billing or shipping) to the keys
			foreach ( $address as $key => $value ) {
				$address[ $type . '_' . $key ] = $value;
				unset( $address[ $key ] );
			}

			/**
			 * Fire after validating a customer address.
			 *
			 * @since 1.0.0
			 * @param array $address The validated and normalized address.
			 */
			do_action( 'wc_avatax_after_customer_address_validated', $address );

			WC()->session->set( 'wc_avatax_address_validated', true );

			$execution_end = hrtime(true);
			$execution_time = wc_avatax()->wc_avatax_utilities()->microtime_diff($execution_start, $execution_end);
			$connector_time = $execution_time - $api_time;
			wc_avatax()->logger()->log_performance("ValidateAddress", "validate_customer_address", "Validating the address call by AJAX", "", "", $connector_time, $api_time, [], 0);

			// Off you go
			wp_send_json( array(
				'code'    => 200,
				'address' => $address,
			) );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "validate_customer_address", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}


	/**
	 * Display the product variation tax code field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $loop the variation loop key
	 * @param array $variation_data the variation data
	 * @param \WC_Product_Variation $variation the variation object
	 */
	public function display_product_variation_code_fields( $loop, $variation_data, $variation ) {

		$default  = get_post_meta( $variation->post_parent, '_wc_avatax_code', true );
		$tax_code = get_post_meta( $variation->ID, '_wc_avatax_code', true );

		include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-product-variation-tax-code.php' );
	}


	/**
	 * Save a product variation tax code.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $variation_id the varation ID
	 */
	public function save_product_variation_code_fields( $variation_id ) {

		$tax_code = '';

		if ( isset( $_POST['variable_post_id'] ) && ( false !== ( $i = array_search( $variation_id, $_POST['variable_post_id'] ) ) ) ) {

			if ( isset( $_POST['variable_wc_avatax_code'] ) ) {
				$tax_code = $_POST['variable_wc_avatax_code'][ $i ];
			}
		}

		if ( '' !== $tax_code ) {
			update_post_meta( $variation_id, '_wc_avatax_code', wc_clean( $tax_code ) );
		} else {
			delete_post_meta( $variation_id, '_wc_avatax_code' );
		}
	}


	/**
	 * Save the product tax code quick edit field.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param \WC_Product $product the product object
	 */
	public function save_product_tax_code_quick_edit( $product ) {

		if ( isset( $_REQUEST['_wc_avatax_code'] ) ) {
			update_post_meta( $product->get_id(), '_wc_avatax_code', sanitize_text_field( $_REQUEST['_wc_avatax_code'] ) );
		}
	}


	/**
	 * Saves the tax code fields when a new product category is created.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id new term ID
	 * @param int $tt_id new term taxonomy ID
	 */
	public function save_category_code_fields( $term_id, $tt_id ) {

		$tax_code = sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'wc_avatax_category_tax_code' ) );

		update_term_meta( $term_id, 'wc_avatax_tax_code', $tax_code );
	}


	/**
	 * Add estimated AvaTax calculations to orders when "Calculate Taxes" is run from the admin.
	 *
	 * @since 1.0.0
	 *
	 * @param int $order_id the order ID
	 * @throws WC_Data_Exception
	 */
	public function estimate_order_tax( $order_id ) {

		// If not otherwise calculating taxes, bail
		if ( ! doing_action( 'wp_ajax_woocommerce_calc_line_taxes' ) ) {
			return;
		}

		// If tax calculation is turned off, bail
		if ( ! wc_avatax()->get_tax_handler()->is_available() ) {
			return;
		}

		$order = wc_get_order( $order_id );
		$avatax_tax_included = wc_avatax()->wc_avatax_utilities()->get_order_meta( $order->get_id(), '_wc_avatax_tax_included', true);
		if($avatax_tax_included == "")
		{
			
			$tax_included =  get_option( 'woocommerce_prices_include_tax', 'no' );
			wc_avatax()->wc_avatax_utilities()->add_order_meta($order->get_id(), '_wc_avatax_tax_included', $tax_included );
		}
		// If order couldn't be fetched, bail
		if ( ! $order ) {
			return;
		}

		// Estimate taxes for the address provided in the request, if available. When an order is not yet saved, address
		// fields won't be set, making tax calculation not possible. The following will ensure we're using the address
		// given in the AJAX request (which is the address entered on the customer billing/shipping address form in admin).
		if ( isset( $_POST['country'], $_POST['state'] ) ) {

			/** @see \WC_AJAX::calc_line_taxes() */
			$country_code = wc_strtoupper( wc_clean( wp_unslash( $_POST['country'] ) ) );
			$state        = wc_strtoupper( wc_clean( wp_unslash( $_POST['state'] ) ) );
			$tax_based_on = get_option( 'woocommerce_tax_based_on ', '');
			// temporarily set address fields on order object so that the tax request class can access them
			if ( 'shipping' === $tax_based_on ) {
				$order->set_shipping_country( $country_code );
				$order->set_shipping_state( $state );
				$order->set_shipping_city( wc_strtoupper( wc_clean( wp_unslash( $_POST['city'] ?? '' ) ) ) );
				$order->set_shipping_postcode( wc_strtoupper( wc_clean( wp_unslash( $_POST['postcode'] ?? '' ) ) ) );
			}
			elseif ( 'billing' === $tax_based_on) {
				$order->set_billing_country( $country_code );
				$order->set_billing_state( $state );
				$order->set_billing_city( wc_strtoupper( wc_clean( wp_unslash( $_POST['city'] ?? '' ) ) ) );
				$order->set_billing_postcode( wc_strtoupper( wc_clean( wp_unslash( $_POST['postcode'] ?? '' ) ) ) );
			}

		}  elseif ( $order->has_shipping_address() ) {
			$country_code = $order->get_shipping_country( 'edit' );
			$state        = $order->get_shipping_state( 'edit' );
		} else {
			$country_code = $order->get_billing_country( 'edit' );
			$state        = $order->get_billing_state( 'edit' );
		}

		// check that the destination is taxable
		if ( ! wc_avatax()->get_tax_handler()->is_location_taxable( $country_code, $state ) ) {
			return;
		}

		wc_avatax()->get_order_handler()->estimate_tax( $order );
	}


	/**
	 * Process order refunds and get accurate tax refund rates from the AvaTax API.
	 *
	 * Totals passed around this method are mostly negative floats that will _subtract_ from an order's total.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @deprecated 1.15.0
	 *
	 * @param int $order_id The order ID.
	 * @param int $refund_id The refund ID.
	 */
	public function process_refund( $order_id, $refund_id ) {

		wc_deprecated_function( __METHOD__, '1.15.0', 'WC_AvaTax_Order_Handler::process_refund' );
	}


	/**
	 * Attempts to sync again all the products that had sync errors in a previous run.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function resync_products_with_errors() {

		check_ajax_referer( 'wc_avatax_resync_error_products', 'nonce' );

		wc_avatax()->get_landed_cost_sync_handler()->resync_products_with_errors();
	}


	/**
	 * Toggles the Cross-Border item classification sync state.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function toggle_cross_border_sync() {

		$user_name = $_REQUEST['wc_avatax_hs_api_username'];
		$password = $_REQUEST['wc_avatax_hs_api_password'];
		$countries = $_REQUEST['wc_avatax_api_product_countries_sync'];
		$classification = $_REQUEST['wc_avatax_enable_cross_border_classification'];
		
		update_option('wc_avatax_hs_api_username', $user_name);
		update_option('wc_avatax_hs_api_password', $password);
		update_option('wc_avatax_api_product_countries_sync', $countries);
		update_option('wc_avatax_enable_cross_border_classification', $classification == "1" ? "yes" : "no");

		check_ajax_referer( 'wc_avatax_toggle_cross_border_sync', 'nonce' );

		$sync_handler = wc_avatax()->get_landed_cost_sync_handler();

		if ( $can_toggle = ( $sync_handler->is_syncing_active() || wc_avatax()->get_landed_cost_handler()->can_connect_to_hs_api() ) ) {
			$sync_handler->toggle_syncing();
		}

		wp_send_json_success( [
			'toggled' => wc_bool_to_string( $can_toggle ),
		] );
	}
	/**
	 * Makes an api call for getiing ecommerce token.
	 *
	 * @since 2.6.0
	 */
	public function get_ecommerce_token() {

		try {
			$custid     = $_POST['custid'];
			if($custid==null)
			{
				$custid = wc_avatax()->get_user_email();
			}
			$response = wc_avatax()->get_api()->get_ecommerce_token($custid);
			wp_send_json( array(
				'code'    => 200,
				'data' => $response,
			) );

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "get_ecommerce_token", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}
	/**
	 * Map perform function for ELR field mapper UI.
	 *
	 * @since 2.7.2
	 */
	public function submit_map_perform() {
		try {
			if (isset($_POST['param']) && !empty($_POST['param'])) {
				switch ($_POST['param']) {
					case 'table_dependency';
						$response  = wc_avatax()->wc_avatax_elr_utilities()->getTableRefferenceFields($_POST['tablename']);
						break;
					case 'save_mapping';
						$any_error = wc_avatax()->wc_avatax_elr_utilities()->saveEinvoiceMapping($_POST['filterInfo']);
						$response  = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($_POST['entity']);
						$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema($_POST['entity']));
						$savedSchema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema($_POST['entity']));
						$mapperTables = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($_POST['entity']);
						break;
					case 'save_schema';
						$response  = json_encode(wc_avatax()->wc_avatax_elr_utilities()->save_and_send_schema($_POST['columns'], $_POST['entity']));
						break;
					case 'document_ready';
						$response  = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($_POST['entity']);
						$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema($_POST['entity']));
						$savedSchema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema($_POST['entity']));
						$mapperTables = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($_POST['entity']);
						break;
					case 'delete_mapper_record';
						wc_avatax()->wc_avatax_elr_utilities()->deleteMapperRecord($_POST['mapperid']);
						$response  = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($_POST['entity']);
						$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema($_POST['entity']));
						$savedSchema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema($_POST['entity']));
						$mapperTables = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList($_POST['entity']);
						break;
					case 'delete_conditional_record';
						$response  = wc_avatax()->wc_avatax_elr_utilities()->deleteConditionalRecord($_POST['conditionalId'], $_POST['filterId']);
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getConditionalMapperTableRows());
						break;
					case 'ELRData';
						$order_number = $_POST['order_number'];
						$response  = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceCollectionByInvoiceId($order_number, $_POST['entity_type']));
						break;
					case 'InvoiceMapper';
						$response  = wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceConditionalMapperRecords();
						break;
					case 'save_filter_data':
						$response  = wc_avatax()->wc_avatax_elr_utilities()->InsertFilterData($_POST['filterInfo']);
						$schema = json_encode(wc_avatax()->wc_avatax_elr_utilities()->getConditionalMapperTableRows());
						break;
					default:
						break;

					
				}
				wp_send_json( array(
					'code'    => 200,
					'data' => $response,
					'records' => $records,
					'schema' => $schema,
					'savedSchema' => $savedSchema,
					'mapperTables' => $mapperTables,
					'error_save' => $any_error,
				) );

			}

		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->elr_logging_enabled() ) {
				wc_avatax()->log_elr( $e->getMessage() );
			}

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}
	/**
	 * Gets the tax codes from AvaTax.
	 * 
	 * @since 2.6.1
	 *
	 */
	public function tax_code_sync()
	{
		$response = wc_avatax()->get_api()->get_tax_codes();

		wp_send_json_success( [
			'isSuccess' => $response,
		] );
	}

	/**
	 * Search for the tax codes in database.
	 * 
	 * @since 2.6.1
	 *
	 */
	public function tax_code_lookup()
	{
		global $wpdb;

		$type = $_REQUEST['type'];
		$key = filter_var(sanitize_key($_REQUEST['key']), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_HIGH);
		$sql = "";

		$table_name = $wpdb->prefix . "wc_avatax_tax_codes";
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
		$records = "";

		if ( $wpdb->get_var( $query ) === $table_name ) {
			
			$sql = $wpdb->prepare( "SELECT * FROM ". $table_name . " WHERE taxCode LIKE  %s OR description LIKE  %s", ("%" . $wpdb->esc_like($key) . "%"), ("%" . $wpdb->esc_like($key) . "%"));
			$results = $wpdb->get_results($sql);
			if(!empty($results)){
				$records = "<ul>";
				foreach( $results as $result ) {
					$records = $records . "<li class='lookupItem' data-val='" . $result->taxCode . "'>" .$result->taxCode. " " . $result->description."</li>";
				}
				$records = $records . "</ul>";
			}
		}

		wp_send_json_success( [
			'records' => $records,
		] );
	}

	/**
	 * Disconnects the connection to AvaTax.
	 * 
	 * @since 2.7.0
	 *
	 */
	public function disconnect_avatax() {
		//Logging Dissconnect event
		wc_avatax()->logger()->log_event("Disconnect", "disconnect_avatax", "Successfully disconnected the AvaTax account.");

		wc_avatax()->wc_avatax_utilities()->disconnect_avatax(false);

		wp_send_json( array(
			'code'    => 200,
			'data' => $response,
		) );
	}

	/**
	 * Updates the connection to AvaTax.
	 * 
	 * @since 2.7.0
	 *
	 */
	public function update_connection() {
		$account_number = $_REQUEST['wc_avatax_api_account_number'];
		$license_key = $_REQUEST['wc_avatax_api_license_key'];
		$environment = $_REQUEST['wc_avatax_api_environment'];

		$api = new WC_AvaTax_API( $account_number, $license_key, $company_code, $environment );

		$response = $api->test();

		if ( ! $response->is_authenticated() ) {
			//Logging Update Connection event
			wc_avatax()->logger()->log_event("UpdateConnection", "update_connection", "Account ID (". $account_number .") or License key is incorrect. Restored old credentials.");

			wp_send_json( array(
				'code'    => 401,
				'data' => $response,
				'message' => 'Account ID or License key is incorrect. Restored old credentials'
			) );
		}
		else {
			wc_avatax()->wc_avatax_utilities()->disconnect_avatax(true);

			//Logging Update Connection event
			wc_avatax()->logger()->log_event("UpdateConnection", "update_connection", "Successfully updated connection");

			wp_send_json( array(
				'code'    => 200,
				'data' => $response,
			) );
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
		return wc_avatax()->wc_avatax_utilities()->hide_order_item_meta($hidden_meta);
	}

	/**
	 * Syncs the configuration settings with CUP.
	 *
	 * @since 2.8.0
	 *
	 */
	public function refresh_config() {
		try {
			
			wc_avatax()->wc_avatax_utilities()->sync_confic_settings();
			
			//Logging Refresh Configuration event
			wc_avatax()->logger()->log_event("SynchronizeConfig", "refresh_config", "Synchronizing the configuration by clicking Synchronize config button on UI.");

			wp_send_json( array(
				'code'    => 200,
				'message' => 'Connected to AvaTax, Refreshing settings.'
			) );
		} catch ( Framework\SV_WC_API_Exception $e ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Ajax", "refresh_config", $e->getMessage(), $e->getTraceAsString());

			wp_send_json( array(
				'code'  => (int) $e->getCode(),
				'error' => esc_html( $e->getMessage() ),
			) );
		}
	}

	/**
	 * Disconnects the connection to ELR.
	 * 
	 * @since 3.0.0
	 *
	 */
	public function disconnect_elr() {

		// No nonce? No go
		check_ajax_referer( 'wc_avatax_elr_disconnect', 'nonce' );

		//Logging Dissconnect event
		wc_avatax()->elr_logger()->log_event("Disconnect", "disconnect_elr", "Successfully disconnected the AvaTax ELR.");

		wc_avatax()->wc_avatax_elr_utilities()->disconnect_elr(); 

		wp_send_json( array(
			'code'    => 200
		) );
	}

	/**
	 * Saves the custom fields
	 * 
	 * @since 3.0.0
	 *
	 */
	public function save_custom_fields() {
		$response = ['code' => 200];
	
		try {
			check_ajax_referer('wc_avatax_elr_disconnect', 'nonce');
	
			// Get and decode new fields from AJAX request
			$wc_avatax_elr_custom_fields = html_entity_decode(stripslashes($_REQUEST['wc_avatax_elr_custom_fields']));
			$new_fields = json_decode($wc_avatax_elr_custom_fields);
	
			// Get existing fields
			$already_present_fields = get_option("wc_avatax_elr_custom_fields");
	
			if ($new_fields && $already_present_fields) {
				// Get counts for comparison
				$existing_count = count((array)$already_present_fields->company) + count((array)$already_present_fields->customer);
				$new_count = count((array)$new_fields->company) + count((array)$new_fields->customer);
	
				// Only proceed if we have fewer new fields than existing fields
				if ($new_count < $existing_count) {
					// Get all existing field IDs
					$existing_field_ids = [];
					if (!empty($already_present_fields->company)) {
						foreach ($already_present_fields->company as $field) {
							$existing_field_ids[] = $field->field_id;
						}
					}
					if (!empty($already_present_fields->customer)) {
						foreach ($already_present_fields->customer as $field) {
							$existing_field_ids[] = $field->field_id;
						}
					}
	
					// Get all new field IDs
					$new_field_ids = [];
					if (!empty($new_fields->company)) {
						foreach ($new_fields->company as $field) {
							$new_field_ids[] = $field->field_id;
						}
					}
					if (!empty($new_fields->customer)) {
						foreach ($new_fields->customer as $field) {
							$new_field_ids[] = $field->field_id;
						}
					}
	
					// Find removed field IDs
					$removed_field_ids = array_diff($existing_field_ids, $new_field_ids);
	
					// If we found removed fields, process them
					foreach ($removed_field_ids as $field_id) {
						delete_option($field_id);
					}
				}
			}
	
			// Update with new fields
			update_option("wc_avatax_elr_custom_fields", $new_fields);
	
		} catch (Exception $e) {
			$response = [
				'code' => 500,
				'message' => $e->getMessage()
			];
		}
	
		wp_send_json($response);
	}
	
	/**
	 * Saves the custom fields
	 * 
	 * @since 3.0.0
	 *
	 */
	public function refresh_elr_status() {
		// No nonce? No go
		check_ajax_referer( 'wc_avatax_elr_disconnect', 'nonce' );

		$order = wc_get_order($_POST['order_id']);
		$status_details = wc_avatax()->get_elr_handler()->get_invoice_status_details($order);
		$invoice_status = $status_details['status'];
		$invoice_status_messages = wc_avatax()->get_elr_handler()->get_invoice_status_messages($status_details['messages']);
		$processing_id = $status_details['processing_id'];
		$html = '';
		if($order instanceof WC_Order_Refund){
			$html = wc_avatax()->wc_avatax_elr_utilities()->get_elr_refund_status_html($order->get_id(), $invoice_status, $processing_id, $invoice_status_messages);
		}else {
			$html = wc_avatax()->wc_avatax_elr_utilities()->get_elr_status_html($order->get_id(), $invoice_status, $processing_id, $invoice_status_messages);
		}
		wp_send_json( array(
			'code'	=> 200,
			'data'	=> $html,
			'element_identifier' => (($order instanceof WC_Refund) ? "refund-" : "order-"). $order->get_id()
		) );
	}

	/**
	 * Send order to Avalara E-invoicing and Live Reporting
	 * 
	 * @since 3.0.0
	 *
	 */
	public function send_order_to_avalara(){
		// No nonce? No go
		check_ajax_referer( 'wc_avatax_elr_disconnect', 'nonce' );

		$order =  new WC_Order($_POST['order_id']);
		if ( ! $order ) {
			wp_send_json( array(
				'code'	=> 400,
				'error'	=> 'Order not found',
			) );
		}

		wc_avatax()->get_elr_handler()->process_elr($order);

		wp_send_json( array(
			'code'	=> 200,
		) );

	}

	/**
	 * Send refund to Avalara E-invoicing and Live Reporting
	 * 
	 * @since 3.0.0
	 *
	 */
	public function send_refund_to_avalara(){
		// No nonce? No go
		check_ajax_referer( 'wc_avatax_elr_disconnect', 'nonce' );

		$refund =  new WC_Order_Refund($_POST['order_id']);
		$order = $refund->get_parent_id();
		 if ( ! $refund ) {
			wp_send_json( array(
				'code'	=> 400,
				'error'	=> 'Refund not found',
			) );
		}
		if ( ! $order ) {
			wp_send_json( array(
				'code'	=> 400,
				'error'	=> 'Order not found',
			) );
		}

		wc_avatax()->get_elr_handler()->process_refund_to_elr($refund->get_parent_id(), $_POST['order_id']);

		wp_send_json( array(
			'code'	=> 200,
		) );

	}
}
