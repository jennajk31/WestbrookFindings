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
require_once( wc_avatax()->get_plugin_path() . '/src/admin/class-wc-avatax-settings.php' );

defined( 'ABSPATH' ) or exit;

/**
 * Set up the AvaTax front-end.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Frontend {


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// load front end assets
		add_action( 'wp_enqueue_scripts', [ $this, 'load_scripts' ] );
		add_action( 'woocommerce_edit_account_form', [ $this, 'display_custom_fields'] );
		add_action( 'woocommerce_save_account_details', [ $this, 'save_custom_fields'] );

		if ( $this->address_validation_enabled() ) {

			// Add an address validation button below each address form at checkout.
			add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_validate_address_button' ) );
			add_action( 'woocommerce_after_checkout_shipping_form', array( $this, 'add_shipping_validate_address_button' ) );

			// Validate the customer address at checkout when JavaScript is disabled.
			add_action( 'woocommerce_checkout_process', array( $this, 'validate_address' ) );
		}

		if ( wc_avatax()->get_tax_handler()->is_available() ) {

			// Display a "pending calculation" message on the cart page
			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				add_action( 'woocommerce_cart_totals_before_order_total', array( $this, 'display_cart_calculation_message' ) );
			} else {
				add_filter( 'woocommerce_cart_totals_taxes_total_html', array( $this, 'adjust_single_tax_total_html' ) );
			}

			// Add the VAT field if enabled
			if ( apply_filters( 'wc_avatax_enable_vat', ( 'yes' === get_option( 'wc_avatax_enable_vat' ) ) ) ) {
				add_filter( 'woocommerce_billing_fields', array( $this, 'add_checkout_vat_field' ) );
			}
			if(get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes')
			{
				add_filter( 'woocommerce_checkout_order_review', array( $this, 'display_ecm_links' ) );
				add_filter( 'woocommerce_checkout_shipping', array( $this, 'display_ecm_message' ) );
				$this->display_ecm_table();
			}
		}	
	}

	/**
	 * Display the custom fields.
	 *
	 * @since 3.0.0
	 */
	function display_custom_fields() {
		$user = wp_get_current_user();
		$custom_field_settings = array();
		$field_list = get_option('wc_avatax_elr_custom_fields', array());
		
		if(isset($field_list)) {
            foreach((array)$field_list->customer as $field){
				$type = ($field->data_type == 'string' ? 'text' : ($field->data_type == 'date' ? 'date' : 'number'));
				?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="favorite_color"><?php _e( $field->field_name, 'woocommerce' ); ?></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" <?php echo ($type == 'text' ? 'maxlength="100"' : '' ) ?> type="<?php echo $type ?>" name="<?php echo $field->field_id ?>" id="<?php echo $field->field_id ?>" value="<?php echo esc_attr( $user->{$field->field_id} ); ?>" />
				</p>
				<?php
			}
		}
	}

	/**
	 * Save the custom fields.
	 *
	 * @since 3.0.0
	 */
	function save_custom_fields( $user_id ) {
		$user = wp_get_current_user();
		$custom_field_settings = array();
		$field_list = get_option('wc_avatax_elr_custom_fields', array());
		
		if(isset($field_list)) {
            foreach((array)$field_list->customer as $field){
				
				if ( isset( $_POST[$field->field_id] ) ) {
					update_user_meta( $user_id, $field->field_id, sanitize_text_field( $_POST[$field->field_id] ) );
				}
			}
		}
	}

	public function display_ecm_message() {
			echo '<tr class="ecm-exemptmessage">';
				echo '<th>' . "AvaTax uses this email ID for tax exemption. To receive tax exemption for the order, ensure that the email ID you enter here is applicable for tax exemption." . '</th>';
			echo '</tr>';
	}

	/**
	 * Loads front-end assets.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function load_scripts() {
		
		$current_user = wp_get_current_user();
		if((is_account_page() || is_checkout()) && get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes')
		{
			$user_meta_data = get_metadata_raw('user',get_current_user_id());
			$email = empty($user_meta_data['billing_email'])?"":$user_meta_data['billing_email'][0];
			wp_enqueue_script( 'wc-avatax-frontend-misc', wc_avatax()->get_plugin_url() . '/assets/js/frontend/wc-avatax-frontend-misc.min.js', array( 'jquery' ) );
			wp_localize_script( 'wc-avatax-frontend-misc', 'wc_avatax_frontend_misc', [
	
				'ajax_url'                     		=> admin_url( 'admin-ajax.php' ),
				'user_email'				   		=> $email!=null ? $email : $current_user->user_email,
				'user_id'					   		=> get_current_user_id(),
				'is_checkout'				   		=>is_checkout(),
				'select_zone'                       => __( 'Please select exposure zone.', 'woocommerce' ),
				'enter_billing_address'				=> __( 'Please enter billing email address and save the details.', 'woocommerce' ),
				'gencert_generic_error'				=> __( "The page you're looking for couldn't be found. Please contact Avalara Support.", 'woocommerce' ),
				'confirm_invalidate_certificate'	=> __( "Are you sure you’d like to invalidate this certificate?", 'woocommerce' ),
				'is_checkout_block'					=>  $this->is_checkout_block(),

			] );
			wp_enqueue_script( 'wc-avatax-gencert', get_option('wc_avatax_api_environment') === 'development' ? "https://sbx.certcapture.com/gencert2/js":"https://app.certcapture.com/gencert2/js", array( 'jquery' ));
			
		}
		// load styles
		wp_enqueue_style( 'wc-avatax-frontend', wc_avatax()->get_plugin_url() . '/assets/css/frontend/wc-avatax-frontend.min.css', [], WC_AvaTax::VERSION );
		// the frontend JS is also needed apart from address validation
		wp_enqueue_script( 'wc-avatax-frontend', wc_avatax()->get_plugin_url() . '/assets/js/frontend/wc-avatax-frontend.min.js', array( 'jquery' ), WC_AvaTax::VERSION, true );

		
		

		wp_localize_script( 'wc-avatax-frontend', 'wc_avatax_frontend', [

			'ajax_url'                     => admin_url( 'admin-ajax.php' ),
			'address_validation_nonce'     => wp_create_nonce( 'wc_avatax_validate_customer_address' ),
			'address_validation_countries' => $this->address_validation_enabled() ? $this->get_address_validation_countries() : "",
			'is_checkout'					  => is_checkout(),
			'i18n' => [
				'address_validated' => __( 'Address validated.', 'woocommerce-avatax' ),
			],
			'tax_based_on'						=> get_option( 'woocommerce_tax_based_on', '' ),
			'vat_field_applicable'				=> wc_avatax()->wc_avatax_utilities()->is_VAT_Field_Applicable()
		] );
		
		if ( !(is_checkout() || is_cart())) {
			return;
		}
	}


	/**
	 * Add an address validation button at checkout.
	 *
	 * @since 1.0.0
	 */
	public function add_validate_address_button() {

		echo $this->get_validate_address_button();
	}


	/**
	 * Add an address validation button at checkout.
	 *
	 * @since 1.1.1
	 */
	public function add_shipping_validate_address_button() {

		echo $this->get_validate_address_button( 'shipping' );
	}


	/**
	 * Gets the address validation button markup.
	 *
	 * @since 1.1.1
	 *
	 * @param string $type button type, either shipping or billing
	 * @return string button HTML
	 */
	protected function get_validate_address_button( $type = 'billing' ) {

		/**
		 * Filters the address validation button label.
		 *
		 * @since 1.0.0
		 *
		 * @param string $label the address validation button label
		 */
		$label = (string) apply_filters( 'wc_avatax_validate_address_button_label', __( 'Validate Address', 'woocommerce-avatax' ) );

		return '<button class="wc_avatax_validate_address button" data-address-type="' . esc_attr( $type ) . '">' . esc_html( $label ) . '</button>';
	}


	/**
	 * Validate the customer address at checkout when JavaScript is disabled.
	 *
	 * @since 1.0.0
	 */
	public function validate_address() {

		// If the address validation button was not pressed, bail
		if ( ! Framework\SV_WC_Helper::get_posted_value( 'woocommerce_checkout_update_totals' ) ) {
			return;
		}

		// Skip shipping if not needed
		if ( Framework\SV_WC_Helper::get_posted_value( 'ship_to_different_address' ) ) {
			$type = 'shipping';
		} else {
			$type = 'billing';
		}

		$response = wc_avatax()->get_api()->validate_address( array(
			'address_1' => Framework\SV_WC_Helper::get_posted_value( $type . '_address_1' ),
			'address_2' => Framework\SV_WC_Helper::get_posted_value( $type . '_address_2' ),
			'city'      => Framework\SV_WC_Helper::get_posted_value( $type . '_city' ),
			'state'     => Framework\SV_WC_Helper::get_posted_value( $type . '_state' ),
			'country'   => Framework\SV_WC_Helper::get_posted_value( $type . '_country' ),
			'postcode'  => Framework\SV_WC_Helper::get_posted_value( $type . '_postcode' ),
		) );

		$address = $response->get_normalized_address();

		// Set the shipping address values to the normalized address
		$_POST[ $type . '_address_1' ] = $address['address_1'];
		$_POST[ $type . '_address_2' ] = $address['address_2'];
		$_POST[ $type . '_city' ]      = $address['city'];
		$_POST[ $type . '_state' ]     = $address['state'];
		$_POST[ $type . '_country' ]   = $address['country'];
		$_POST[ $type . '_postcode' ]  = $address['postcode'];

		wc_add_notice( __( 'Address validated.', 'woocommerce-avatax' ), 'success' );
	}


	/**
	 * Display a "pending calculation" message on the cart page when displaying a single tax total.
	 *
	 * @since 1.2.1
	 * @param string $html the tax total HTML
	 * @return string
	 */
	public function adjust_single_tax_total_html( $html ) {

		$cart  = WC()->cart;
		$taxes = $cart->get_cart_contents_taxes();

		if ( empty( $taxes ) && wc_avatax()->get_tax_handler()->override_wc_rates() ) {

			if ( is_cart() ) {
				$html = esc_html( $this->get_cart_calculation_message() );
			} elseif ( is_checkout() && $this->address_validation_required() && ! WC()->session->get( 'wc_avatax_address_validated', false ) ) {
				$html = esc_html__( 'Taxes will be calculated after you validate your address', 'woocommerce-avatax' );
			}
		}

		return $html;
	}


	/**
	 * Display a "pending calculation" message on the cart page when taxes are itemized.
	 *
	 * @since 1.2.1
	 */
	public function display_cart_calculation_message() {

		$taxes = Framework\SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.2' ) ? WC()->cart->get_cart_contents_taxes() : WC()->cart->taxes;

		if ( ! is_cart() || ! wc_avatax()->get_tax_handler()->override_wc_rates() || ! empty( $taxes ) ) {
			return;
		}

		/** This filter is documented in woocommerce-avatax/woocommerce-avatax.php */
		$title = apply_filters( 'wc_avatax_tax_label', WC()->countries->tax_or_vat() );

		echo '<tr class="tax-total">';
			echo '<th>' . esc_html( $title ) . '</th>';
			echo '<td data-title="' . esc_attr( $title ) . '">' . esc_html( $this->get_cart_calculation_message() ) . '</td>';
		echo '</tr>';
	}


	/**
	 * Get the "pending calculation" message for the cart page.
	 *
	 * @since 1.2.1
	 * @return string
	 */
	protected function get_cart_calculation_message() {

		/**
		 * Filter the cart pending tax calculation message.
		 *
		 * @since 1.2.1
		 * @param string $message
		 */
		return apply_filters( 'wc_avatax_cart_message', __( 'Taxes will be calculated at checkout', 'woocommerce-avatax' ) );
	}


	/**
	 * Add the VAT field to the checkout billing fields.
	 *
	 * @since 1.0.0
	 * @param array $fields The existing checkout fields.
	 * @return array $fields The checkout fields.
	 */
	public function add_checkout_vat_field( $fields ) {

		if(!wc_avatax()->wc_avatax_utilities()->is_VAT_Field_Applicable())
		{
			return $fields;
		}

		/**
		 * Filter the VAT ID checkout field label.
		 *
		 * @since 1.0.0
		 * @param string $label The VAT ID checkout field label.
		 */
		$label = apply_filters( 'wc_avatax_vat_id_field_label', __( 'VAT ID', 'woocommerce-avatax' ) );

		$fields['billing_wc_avatax_vat_id'] = [
			'label' => $label,
			'class' => [ 'form-row-wide' ],
		];

		return $fields;
	}


	/**
	 * Determines if address validation is required.
	 *
	 * @since 1.6.4
	 *
	 * @return bool
	 */
	public function address_validation_required() {

		/**
		 * Filters whether address validation is required.
		 *
		 * @since 1.6.4
		 *
		 * @param bool $required whether address validation is required
		 */
		return $this->address_validation_available() && (bool) apply_filters( 'wc_avatax_address_validation_required', ( 'yes' === get_option( 'wc_avatax_enable_address_validation' ) ) );
	}


	/**
	 * Determine if address validation is available at checkout.
	 *
	 * @since 1.0.0
	 *
	 * @return bool $enabled Whether address validation is available at checkout.
	 */
	public function address_validation_available() {

		$countries = $this->get_address_validation_countries();

		/**
		 * Filters whether address validation is available.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $required whether address validation is available
		 */
		return $this->address_validation_enabled() && (bool) apply_filters( 'wc_avatax_address_validation_available', in_array( WC()->customer->get_shipping_country(), $countries ) );
	}


	/**
	 * Determine if address validation is enabled.
	 *
	 * @since 1.0.0
	 * @return bool $enabled Whether address validation is enabled.
	 */
	public function address_validation_enabled() {

		/**
		 * Filter whether address validation is enabled.
		 *
		 * @since 1.0.0
		 * @param bool $enabled Whether address validation is enabled.
		 */
		return (bool) apply_filters( 'wc_avatax_enable_address_validation', ( 'yes' === get_option( 'wc_avatax_enable_address_validation' ) ) );
	}


	/**
	 * Determine if address validation is available at checkout.
	 *
	 * @since 1.0.0
	 * @return bool $enabled Whether address validation is available at checkout.
	 */
	public function get_address_validation_countries() 
	{
		$class_WC_AvaTax_Settings = new WC_AvaTax_Settings();
		$countries = $class_WC_AvaTax_Settings->get_ecm_enabled_countries();

		/**
		 * Filter the countries that support address validation.
		 *
		 * @since 1.0.0
		 * @param array $countries The countries that support address validation.
		 */
		return (array) apply_filters( 'wc_avatax_address_validation_countries', $countries );
	}

	/**
	 * Display ECM links
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */

	public function display_ecm_links() {
		echo '<tr class="ecm-purchase-link">';
		echo '<th>' .'<b>'. "Exemption" . '</b>' . '</th>';
		echo '</tr>';
		echo '<tr class="ecm-purchase-link">';
		echo '<th>' . $this->get_purchase_tax_exmpt_link() . '</th>';
		echo '</tr>';
		echo '<tr class="ecm-tax-exemptlink">';
		echo '<th>' . $this->get_manage_certificate_link() . '</th>';
		echo '</tr>';
	}
	
	protected function get_purchase_tax_exmpt_link() {

		/**
		 * Get Purchase tax exempt link.
		 *
		 * @since 2.6.0
		 * @param string $message
		 */
		$user_id = get_current_user_id();
		$exposure_zones = wc_avatax()->get_exposure_zones();
		if($user_id ==0)
		{
			$myaccounturl = rtrim(get_permalink(get_option('woocommerce_myaccount_page_id') ), "/");
			$checkouturl = rtrim(get_permalink(get_option('woocommerce_checkout_page_id') ), "/");
			$url = $myaccounturl."?redirect_to=".$checkouturl;
			return apply_filters( 'wc_avatax_ecm_links', sprintf( __( '%1$sAdd Certificates%2$s', 'woocommerce-avatax' ),
			'<a style="display:block;" href='.$url.'>',
			'</a>'
			) );
		}
		else
		{
			include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details-popup.php' );
			return apply_filters( 'wc_avatax_ecm_links', sprintf( __( '%1$sAdd Certificates%2$s', 'woocommerce-avatax' ),
			'<a style="display:block;" id ="cert_link" href="#">',
			'</a>'
			) );
	
		}
		
	}
	/**
	 * Get manage certificate link
	 *
	 * @since 2.6.0
	 *
	 * @return string
	 */
	protected function get_manage_certificate_link() {

		/**
		 * Get Purchase manage certificates link.
		 *
		 * @since  2.6.0
		 * @param string $message
		 */
		$url = (get_permalink( get_option('woocommerce_myaccount_page_id') ). 'tax-certificate');
		return apply_filters( 'wc_avatax_ecm_links', sprintf( __( '%1$sManage existing certificates%2$s', 'woocommerce-avatax' ),
		'<a href='.$url.'>',
		'</a>','<br>'
	) );
	}

	/**
	 * Display certificate table
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function display_ecm_table()
	{
		add_action( 'init', 'register_tax_certificate_endpoint');
			/**
			 * Register New Endpoint.
			 *
			 * @return void.
			 */
			function register_tax_certificate_endpoint() {
				add_rewrite_endpoint( 'tax-certificate', EP_ROOT | EP_PAGES );

				$rules = get_option( 'rewrite_rules' );
				
				//Check if rewrite rule exists
				$rule_exists = false;
				foreach ($rules AS $key => $value) {
					if (stristr($value, 'tax-certificate') === FALSE) {
						continue;
					} else {
						$rule_exists = true;
					}
				}
				
				//if rule not exists flush the rewrite rules
				if ( !$rule_exists) { 
					flush_rewrite_rules();
				}
			}
			add_filter( 'query_vars', 'tax_certificate_query_vars' );
			/**
			 * Add new query var.
			 *
			 * @param array $vars vars.
			 *
			 * @return array An array of items.
			 */
			function tax_certificate_query_vars( $vars ) {

				$vars[] = 'tax-certificate';
				return $vars;
			}
			add_filter( 'woocommerce_account_menu_items', 'add_tax_certificate_tab' );
			/**
			 * Add New tab in my account page.
			 *
			 * @param array $items myaccount Items.
			 *
			 * @return array Items including New tab.
			 */
			function add_tax_certificate_tab( $items ) {

				$items['tax-certificate'] = 'Tax Certificate';
				return $items;
			}

			if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI']!=null && str_contains($_SERVER['REQUEST_URI'], 'tax-certificate'))
			{
				$userId = get_current_user_id();
				$user_data = wc_avatax()->get_user_data($userId);
				$customer_certificates = wc_avatax()->get_certificate_options($user_data);
				$has_certificates =!empty($customer_certificates)? true:false;
				$exposure_zones = wc_avatax()->get_exposure_zones();
				add_action( 'woocommerce_account_tax-certificate_endpoint', function() use ($customer_certificates,$has_certificates,$exposure_zones,$userId ) {
					include( wc_avatax()->get_plugin_path() . '/src/frontend/templates/certificates.php' );
					include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details-popup.php' );
				} );

				function wc_get_account_certificates_columns() {
					/**
					 * Filters the array of My Account > Certificates columns.
					 *
					 * @since 2.6.0
					 * @param array $columns Array of column labels keyed by column IDs.
					 */
					return apply_filters(
						'wc_get_account_certificates_columns',
						array(
							'certificate-state'  => __( 'State', 'woocommerce' ),
							'certificate-signedDate'    => __( 'SignedDate', 'woocommerce' ),
							'certificate-expirationDate'  => __( 'ExpirationDate', 'woocommerce' ),
							'certificate-status'   => __( 'Status', 'woocommerce' ),
							'certificate-view' => __( 'View', 'woocommerce' ),
							'certificate-invalidate' => __( 'Invalidate', 'woocommerce' ),
						)
					);
				}
			}
	}
	/**
	 * Determines if classic checkout or checkout block  is required.
	 *
	 * @since 2.8.0
	 *
	 * @return bool
	 */
	public function is_checkout_block() {
		return WC_Blocks_Utils::has_block_in_page( wc_get_page_id('checkout'), 'woocommerce/checkout');
	}

	/**
	 * Determines if classic cart or cart block  is required.
	 *
	 * @since 2.9.0
	 *
	 * @return bool
	 */
	public function is_cart_block() {
		return WC_Blocks_Utils::has_block_in_page(wc_get_page_id('cart'), 'woocommerce/cart');
	}
		
}
