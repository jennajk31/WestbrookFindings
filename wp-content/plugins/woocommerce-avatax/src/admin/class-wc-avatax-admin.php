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
 * Set up the AvaTax admin.
 *
 * @since 1.0.0
 */
class WC_AvaTax_Admin {


	/** @var \WC_AvaTax_Settings settings handler */
	public $settings;

	/** @var \WC_AvaTax_Elr_Settings settings handler */
	public $elr_settings;

	/** @var \WC_AvaTax_Product_Admin product handler */
	public $product;


	/**
	 * Construct the class.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		$this->includes();

		// load admin scripts and styles
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ) );

		// load modal template
		add_action( 'admin_footer', [ $this, 'output_sync_modal_template' ] );
		add_action( 'admin_footer', [ $this, 'output_disconnect_modal_template' ] );
		add_action( 'admin_footer', [ $this, 'output_confirmation_modal_template' ] );

		// add the product category tax code fields
		add_action( 'product_cat_add_form_fields',  array( $this, 'add_category_code_fields' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'edit_category_code_fields' ) );

		// save the product category tax code fields
		// the same is done when creating a new category from WC_AvaTax_AJAX::save_category_tax_code_field
		add_action( 'edit_product_cat', array( $this, 'save_category_code_fields' ) );

		// Add product category tax code column
		add_filter( 'manage_edit-product_cat_columns',  array( $this, 'add_category_code_columns' ) );
		add_filter( 'manage_product_cat_custom_column', array( $this, 'display_category_code_columns' ), 10, 3 );

		// Add the VAT ID information to the order billing information
		add_action( 'woocommerce_admin_billing_fields', array( $this, 'add_admin_order_vat_id' ) );

		// Add the Order Invoice message information to the order billing information section
		add_action( 'woocommerce_admin_billing_fields', array( $this, 'add_admin_order_invoice_messages' ) );

		// Hide our custom line item meta from the order admin
		add_filter( 'woocommerce_hidden_order_itemmeta', array( $this, 'hide_order_item_meta' ) );

		// Add the item tax rate input to the order admin
		add_action( 'woocommerce_admin_order_item_values', array( $this, 'add_order_item_tax_rate' ), 10, 3 );

		// add a hidden input to the order items form to indicate landed cost for an order
		add_action( 'woocommerce_order_item_add_action_buttons', array( $this, 'add_order_calculated_field' ) );

		// Add a "Send to Avalara" action to the order action options if calculation is enabled
		if ( wc_avatax()->get_tax_handler()->is_available() ) {
			add_action( 'woocommerce_order_actions', array( $this, 'add_order_action' ) );
		}

		// Add and save the customer tax settings fields
		add_action( 'show_user_profile',        array( $this, 'add_tax_meta_fields' ), 15, 1 );
		add_action( 'edit_user_profile',        array( $this, 'add_tax_meta_fields' ), 15, 1 );
		
		if(get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes' )
		{
			add_action( 'show_user_profile',        array( $this, 'add_certificate_table' ), 15, 1 );
			add_action( 'edit_user_profile',        array( $this, 'add_certificate_table' ), 15, 1 );
		}
		add_action( 'personal_options_update',  array( $this, 'save_tax_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_tax_meta_fields' ) );
		add_action( 'edit_user_profile_update', array( $this, 'save_customer_avatax' ) );
		add_action( 'personal_options_update', array( $this, 'save_customer_avatax' ) );

		//Adds custom styles to match toggle button color with admin button colors
		add_action('admin_head', array( $this, 'custom_styles' ), 100);
	}


	/**
	 * Include the admin files.
	 *
	 * @since 1.0.0
	 */
	public function includes() {

		// settings handler
		require_once( wc_avatax()->get_plugin_path() . '/src/admin/class-wc-avatax-settings.php' );
		$this->settings = new WC_AvaTax_Settings;

		// elr settings handler
		require_once( $this->get_plugin()->get_plugin_path() . '/src/e-invoicing/class-wc-avatax-elr-settings.php' );
		$elr_settings = new WC_AvaTax_Elr_Settings();

		// product handler
		require_once( wc_avatax()->get_plugin_path() . '/src/admin/class-wc-avatax-product-admin.php' );
		$this->product = new WC_AvaTax_Product_Admin;

	}


	/**
	 * Load the admin scripts and styles.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current screen suffix
	 */
	public function enqueue_scripts_styles( $hook_suffix ) {

		// only enqueue the scripts and styles on the settings screen or edit/new order screens
		if ( wc_avatax()->is_plugin_settings() || ('user-edit.php' === $hook_suffix) || ('profile.php' === $hook_suffix) || ( 'product' === get_post_type() && 'edit.php' === $hook_suffix ) || ( 'shop_order' === get_post_type() && ( 'post.php' === $hook_suffix || 'post-new.php' === $hook_suffix ) ) || ( 'product' === get_post_type() && 'post.php' === $hook_suffix ) ) {
			parse_str((string) parse_url( wp_get_referer(), PHP_URL_QUERY ), $args );
			parse_str((string) $_SERVER['QUERY_STRING'], $array );

			wp_enqueue_script( 'wc-backbone-modal', null, [ 'backbone' ] );
			wp_enqueue_script( 'wc-avatax-admin', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			wp_enqueue_script( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin-elr.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );

			wp_enqueue_script( 'wc-avatax-admin-jsontree', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin-jsontree.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			wp_enqueue_style( 'wc-avatax-admin-jsontree', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin-jsontree.min.css', WC_AvaTax::VERSION );
			wp_localize_script( 'wc-avatax-admin-jsontree', 'wc_avatax_admin_jsontree', [
				'schema' => json_encode(wc_avatax()->wc_avatax_elr_utilities()->getMapperSchema()),
				'savedSchema' => json_encode(wc_avatax()->wc_avatax_elr_utilities()->getEinvoiceSelectedFieldsSchema())
			]);


			if ( get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes')
			{
				wp_enqueue_script( 'wc-avatax-admin-gencert', get_option('wc_avatax_api_environment') === 'development' ? "https://sbx.certcapture.com/gencert2/js":"https://app.certcapture.com/gencert2/js", [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
				wp_enqueue_script( 'wc-avatax-admin-misc', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin-misc.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
				$params = array(
					'select_zone'                         => __( 'Please select exposure zone.', 'woocommerce' ),
					'enter_billing_address'				  => __( 'Please enter billing email address and save the details.', 'woocommerce' ),
					'enter_billing_address_different'	  => __( 'Entered billing email address is different from the billing email currently saved in DB. Press OK to proceed or Cancel to change the billing email address.', 'woocommerce' ),
					'gencert_generic_error'				  => __( "The page you're looking for couldn't be found. Please contact Avalara Support.", 'woocommerce' ),
					'enter_billing_address_confirmation'  => __( 'Please enter billing address details and save. There might be possibility that you have entered the details but not it is not saved.', 'woocommerce' ),
					'confirm_invalidate_certificate'	  => __( "Are you sure you’d like to invalidate this certificate?", 'woocommerce' )
				);
				wp_localize_script( 'wc-avatax-admin-misc', 'wc_avatax_admin_misc', $params );
			}
			wp_localize_script( 'wc-avatax-admin', 'wc_avatax_admin', [
				'address_nonce'         => wp_create_nonce( 'wc_avatax_validate_origin_address' ),
				'certificate_nonce'         => wp_create_nonce( 'wc_avatax_invite_customer_certificate' ),
				'assets_url'            => esc_url( wc_avatax()->get_framework_assets_url() ),
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'sync_state'            => wc_avatax()->get_landed_cost_sync_handler()->is_syncing_active() ? 'on' : 'off',
				'sync_nonce'            => wp_create_nonce( 'wc_avatax_toggle_cross_border_sync' ),
				'resync_nonce'          => wp_create_nonce( 'wc_avatax_resync_products_with_errors' ),
				'refund_ays'            => __( 'Heads up! AvaTax does not support tax-rate-based refunds. If taxes are refunded partially, the amount will be distributed across all tax rates.', 'woocommerce-avatax' ),
				'product_sync_modal'    => [
					'disconnect_title'  => __( 'Are you sure?', 'woocommerce-avatax' ),
					'disconnect_body'   => __( 'Disconnecting now will immediately end the product sync between your store and Avalara. Any new or updated products will not be synced with Avalara, which may impact your cross-border tax calculations. Do you still want to disconnect?', 'woocommerce-avatax' ),
					'disconnect_action' => __( 'Yes', 'woocommerce-avatax' ),
					'disconnect_cancel' => __( 'No', 'woocommerce-avatax' ),
					'connect_title'     => __( 'Syncing...', 'woocommerce-avatax' ),
					'connect_body'      => __( 'Sync is in process, but you can safely leave this screen.', 'woocommerce-avatax' ),
					'exposure_zones'		=> (get_option( 'wc_avatax_enable_ecm', 'no' ) == 'yes' && (('user-edit.php' === $hook_suffix) || ('profile.php' === $hook_suffix))) ? wc_avatax()->get_exposure_zones() : [],
				],
				'to_disable' => [
					'stop_current_sync' => __( 'Please stop the current sync to disable cross-border classification.', 'woocommerce-avatax' ),
				],
				'configuration_badge' =>[
					'is_tax_calculation_configured' 		=> wc_avatax()->is_section_configured('tax_calculation') ? 'yes' : 'no', 
					'is_address_validation_configured' 		=> wc_avatax()->is_section_configured('address_validation') ? 'yes' : 'no',
					'is_ecm_configured' 					=> wc_avatax()->is_section_configured('ecm') ? 'yes' : 'no',
					'is_transaction_outside_us_configured' 	=> wc_avatax()->is_section_configured('outside_us') ? 'yes' : 'no',
					'is_log_configured' 					=> wc_avatax()->is_section_configured('logs') ? 'yes' : 'no',
					'is_elr_configured'						=> wc_avatax()->is_section_configured('elr') ? 'yes' : 'no',
				]
			] );

			wp_localize_script( 'wc-avatax-admin-elr', 'wc_avatax_admin_elr', [
				'ajax_url'              => admin_url( 'admin-ajax.php' )
			]);


			wp_enqueue_style( 'wc-avatax-admin', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin.min.css', WC_AvaTax::VERSION );
			
		}
		if ( wc_avatax()->is_plugin_elr_settings()){
			wp_enqueue_script( 'wc-avatax-admin-jsontree', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin-jsontree.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			wp_enqueue_style( 'wc-avatax-admin-jsontree', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin-jsontree.min.css', WC_AvaTax::VERSION );
			
			wp_enqueue_script( 'wc-backbone-modal', null, [ 'backbone' ] );
			wp_enqueue_script( 'wc-avatax-admin', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			wp_enqueue_script( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-admin-elr.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			
			wp_enqueue_script( 'wc-avatax-admin-elr1', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-elr.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			
			wp_localize_script( 'wc-avatax-admin-elr', 'wc_avatax_admin_elr', [
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'disconnect_nonce'            => wp_create_nonce( 'wc_avatax_elr_disconnect' ),
			]);
			
			wp_enqueue_style( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin-elr.min.css', WC_AvaTax::VERSION );
			wp_enqueue_style( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin.min.css', WC_AvaTax::VERSION );
			
		}

		if(wc_get_page_screen_id( 'shop_order' ) === get_current_screen()->id){
			wp_enqueue_script( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/js/admin/wc-avatax-elr.min.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true );
			
			wp_localize_script( 'wc-avatax-admin-elr', 'wc_avatax_admin_elr', [
				'ajax_url'              => admin_url( 'admin-ajax.php' ),
				'disconnect_nonce'      => wp_create_nonce( 'wc_avatax_elr_disconnect' ),
				'order_id'				=> isset($_REQUEST['id']) ? $_REQUEST['id'] : ''
			]);
			
			wp_enqueue_style( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin-elr.min.css', WC_AvaTax::VERSION );
			wp_enqueue_style( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin.min.css', WC_AvaTax::VERSION );
		}
		
		// Check if we're on the plugins page and ELR enabled
		if ($hook_suffix === 'plugins.php' && wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api()) {
			// Enqueue the custom script
			wp_enqueue_script( 'wc-backbone-modal', null, [ 'backbone' ] );
			wp_enqueue_script('deactivate-alert', wc_avatax()->get_plugin_url() . '/assets/js/admin/deactivate-alert.js', [ 'jquery', 'wc-backbone-modal' ], \WC_AvaTax::VERSION, true);
			wp_enqueue_style( 'wc-avatax-admin-elr', wc_avatax()->get_plugin_url() . '/assets/css/admin/wc-avatax-admin-elr.min.css', WC_AvaTax::VERSION );
		}
	}

	/**
	 * Adds custom styles to match toggle button color with admin button colors
	 * 
	 * @since 2.7.1
	 * 
	 */
	public function custom_styles()
	{
		global $_wp_admin_css_colors;
		$color_scheme = get_user_option( 'admin_color' );
		$color = $_wp_admin_css_colors[ $color_scheme ]->colors[2];
		echo '<style>  :root {--wc-avatax-color: '.$color.';}</style>';
	}

	/**
	 * Includes an export modal template in the plugin settings pages.
	 *
	 * @internal
	 *
	 * @since 1.13.0
	 */
	public function output_sync_modal_template() {

		if ( ! wc_avatax()->is_plugin_settings() ) {
			return;
		}

		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-sync-modal.php' );
	}

	/**
	 * Includes an export modal template in the plugin settings pages.
	 *
	 * @internal
	 *
	 * @since 2.10.0
	 */
	public function output_disconnect_modal_template() {
		// Get the current screen
		$screen = get_current_screen();

		if ( wc_avatax()->is_plugin_elr_settings() || ($screen->id === 'plugins' && wc_avatax()->has_elr_api_credentials_set() && wc_avatax()->check_elr_api())) {
			include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-elr-disconnect-confirm-modal.php' );
		}
	}

	/**
	 * Includes an elr schema send confirmation modal template in the elr config pages.
	 *
	 * @internal
	 *
	 * @since 2.10.0
	 */
	public function output_confirmation_modal_template() {

		if ( ! wc_avatax()->is_plugin_elr_settings() ) {
			return;
		}

		include_once( wc_avatax()->get_plugin_path() . '/src/admin/views/html-elr-confirmation-modal.php' );
	}


	/**
	 * Adds landed costs settings.
	 *
	 * @internal
	 *
	 * @since 1.5.0
	 * @deprecated 1.16.0
	 *
	 * @param array|mixed $settings
	 * @return array|mixed
	 */
	public function add_settings_pages( $settings ) {

		wc_deprecated_function( __METHOD__, '1.16.0' );

		return $settings;
	}


	/**
	 * Display the tax code fields on the add product category screen.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_category_code_fields() {

		// tax code
		include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-add-category-tax-code.php' );
	}


	/**
	 * Display the tax code fields on the edit product category screen.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 * @param object $term current term object
	 */
	public function edit_category_code_fields( $term ) {

		$tax_code = get_term_meta( $term->term_id, 'wc_avatax_tax_code', true );

		include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-field-edit-category-tax-code.php' );
	}


	/**
	 * Save the category tax code fields.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id current term ID
	 */
	public function save_category_code_fields( $term_id ) {

		$tax_code = sanitize_text_field( Framework\SV_WC_Helper::get_posted_value( 'wc_avatax_category_tax_code' ) );

		update_term_meta( $term_id, 'wc_avatax_tax_code', $tax_code );
	}


	/**
	 * Add the tax code columns to category admin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns existing category columns
	 * @return array $columns
	 */
	public function add_category_code_columns( $columns ) {

		$columns['tax_code'] = __( 'Tax Code', 'woocommerce-avatax' );

		return $columns;
	}


	/**
	 * Display the tax code in its column.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param string $content column content
	 * @param string $column current column slug
	 * @param int $id category ID
	 * @return string $columns amended column content
	 */
	public function display_category_code_columns( $content, $column, $id ) {

		if ( 'tax_code' === $column ) {
			$content .= get_term_meta( $id, 'wc_avatax_tax_code', true );
		}

		return $content;
	}


	/**
	 * Add the VAT ID information to the order billing information.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $fields The existing billing fields
	 * @return array
	 */
	public function add_admin_order_vat_id( $fields ) {

		$fields['wc_avatax_vat_id'] = array(
			'label' => __( 'VAT ID', 'woocommerce-avatax' ),
		);

		return $fields;
	}


	/**
	 * Add the invoice messages to the order billing information.
	 *
	 * @internal
	 *
	 * @since 2.4.0
	 *
	 * @param array $fields The existing billing fields
	 * @return array
	 */
	public function add_admin_order_invoice_messages( $fields ) {
		$fields['wc_avatax_order_messages'] = array(
			'label' => __( 'VAT message', 'woocommerce-avatax' ),
			'custom_attributes' => array( 'disabled' => true)
		);
		return $fields;
	}

	/**
	 * Hide our custom line item meta from the order admin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 *
	 * @param array $hidden_meta The hidden line item keys.
	 * @return array $hidden_meta
	 */
	public function hide_order_item_meta( $hidden_meta ) {

		return wc_avatax()->wc_avatax_utilities()->hide_order_item_meta($hidden_meta);
	}


	/**
	 * Add the item tax rate input to the order admin.
	 *
	 * @since 1.0.0
	 * @param WC_Product $product The product object.
	 * @param array $item The item meta.
	 * @param int $item_id The item ID.
	 */
	public function add_order_item_tax_rate( $product, $item, $item_id ) {

		// Only add this value if a tax rate was set for the item
		if ( ( ! is_array( $item ) && ! $item instanceof WC_Order_Item_Tax ) || empty( $item['wc_avatax_rate'] ) ) {
			return;
		}

		echo '<input
				class="wc_avatax_refund_line_rate"
				name="wc_avatax_refund_line_rate[' . absint( $item_id ) . ']"
				value="' . (float) $item['wc_avatax_rate'] . '"
				type="hidden"
			/>';
	}


	/**
	 * Adds a hidden input to the order items form to indicate AvaTax calculation for an order.
	 *
	 * This primarily used to display a warning to users trying to partially refund AvaTax transactions, as that's currently not supported.
	 *
	 * @internal
	 *
	 * @since 1.6.4
	 *
	 * @param \WC_Order $order order object
	 */
	public function add_order_calculated_field( $order ) {

		?>
		<input name="wc_avatax_calculated" type="hidden" value="<?php echo wc_avatax()->get_order_handler()->is_order_posted( $order ) ? 'yes' : 'no'; ?>"/>
		<?php
	}


	/**
	 * Add a "Send to Avalara" action to the order action options.
	 *
	 * @since 1.0.0
	 * @global WC_Order $theorder The current order object.
	 * @param array $actions The available order actions.
	 * @return array $actions
	 */
	public function add_order_action( $actions ) {
		global $theorder;

		// Only add the action if the order is ready for sending
		if ( wc_avatax()->get_order_handler()->is_order_ready( $theorder ) ) {
			$actions['wc_avatax_send'] = __( 'Send to Avalara', 'woocommerce-avatax' );
		}

		return $actions;
	}


	/**
	 * Adds the customer tax settings fields.
	 *
	 * @since 1.0.0
	 *
	 * @param \WP_User $user user object
	 */
	public function add_tax_meta_fields( $user ) {

		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		// base entity/use codes and their descriptions
		// below we try and get the same codes from the API, but this acts as a fallback in case there is a failure
		// note: O is intentionally absent
		$entity_use_codes = array(
			'A' => __( 'Federal government', 'woocommerce-avatax' ),
			'B' => __( 'State government', 'woocommerce-avatax' ),
			'C' => __( 'Tribe / Status Indian / Indian Band', 'woocommerce-avatax' ),
			'D' => __( 'Foreign diplomat', 'woocommerce-avatax' ),
			'E' => __( 'Charitable or benevolent organization', 'woocommerce-avatax' ),
			'F' => __( 'Religious organization', 'woocommerce-avatax' ),
			'G' => __( 'Resale', 'woocommerce-avatax' ),
			'H' => __( 'Commercial agricultural production', 'woocommerce-avatax' ),
			'I' => __( 'Industrial production / manufacturer', 'woocommerce-avatax' ),
			'J' => __( 'Direct pay permit', 'woocommerce-avatax' ),
			'K' => __( 'Direct mail', 'woocommerce-avatax' ),
			'L' => __( 'Other', 'woocommerce-avatax' ),
			'M' => __( 'Educational organization', 'woocommerce-avatax' ),
			'N' => __( 'Local government', 'woocommerce-avatax' ),
			'P' => __( 'Commercial aquaculture', 'woocommerce-avatax' ),
			'Q' => __( 'Commercial Fishery', 'woocommerce-avatax' ),
			'R' => __( 'Non-resident', 'woocommerce-avatax' ),
			'MED1' => __( 'US MDET with exempt sales tax', 'woocommerce-avatax' ),
			'MED2' => __( 'US MDET with taxable sales tax', 'woocommerce-avatax' ),
		);

		try {

			$response = wc_avatax()->get_api()->get_entity_use_codes();

			// append the official code name to the nice label if found, otherwise just add to the list as-is
			foreach ( $response->get_codes() as $code => $name ) {

				$label = isset( $entity_use_codes[ $code ] ) ? "{$entity_use_codes[ $code ]} ({$name})" : $name;

				$entity_use_codes[ $code ] = $label;
			}

		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $exception->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Admin", "add_tax_meta_fields", $exception->getMessage(), $exception->getTraceAsString());
		}

		/**
		 * Filters the customer usage types.
		 *
		 * @since 1.0.0
		 *
		 * @param array $entity_use_codes entity/use codes, formatted as $code => $description
		 */
		$entity_use_codes = apply_filters( 'wc_avatax_customer_usage_types', $entity_use_codes );

		$selected_code = get_user_meta( $user->ID, 'wc_avatax_tax_exemption', true );

		include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-edit-user-tax-fields.php' );

		/**
		 * Field wc_avatax_user_ior added hold seller importer of record data for user
		 *
		 * @since 2.3.0
		 *
		 */

		
		$selected_ior = get_user_meta( $user->ID, 'wc_avatax_user_ior', true );
		include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-edit-user-ior-fields.php' );
	}

	/**
	 *Add certificate table
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */
	public function add_certificate_table( $user ) {
		if (! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		try {
			$userId = $user->ID;
			$user_data = wc_avatax()->get_user_data($userId);
			if(!empty($userId)){
				$certificateslist = wc_avatax()->get_certificate_options($user_data);
				$db_billing_email = $user_data['customerCode'];
				$isAdmin = "true";
				include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-certificate-details.php' );
				include( wc_avatax()->get_plugin_path() . '/src/admin/views/Exemption/html-account-add-exemption.php' );
				include( wc_avatax()->get_plugin_path() . '/src/admin/views/html-sync-modal.php' );
			}
		} catch ( Framework\SV_WC_Plugin_Exception $exception ) {

			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $exception->getMessage() );
			}

			//Logging error
			wc_avatax()->logger()->log_exception("Admin", "add_certificate_table", $exception->getMessage(), $exception->getTraceAsString());
		}
	}
	/**
	 * Validate custoemer data
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function validation_of_fields($user_id){
		$user     = new stdClass();
		$user_id  = (int) $user_id;
		if ( $user_id ) {
			$update           = true;
			$user->ID         = $user_id;
			$userdata         = get_userdata( $user_id );
			$user->user_login = wp_slash( $userdata->user_login );
		} else {
			$update = false;
		}

		if ( ! $update && isset( $_POST['user_login'] ) ) {
			$user->user_login = sanitize_user( wp_unslash( $_POST['user_login'] ), true );
		}

		$pass1 = '';
		$pass2 = '';
		if ( isset( $_POST['pass1'] ) ) {
			$pass1 = trim( $_POST['pass1'] );
		}
		if ( isset( $_POST['pass2'] ) ) {
			$pass2 = trim( $_POST['pass2'] );
		}

		if ( isset( $_POST['role'] ) && current_user_can( 'promote_users' ) && ( ! $user_id || current_user_can( 'promote_user', $user_id ) ) ) {
			$new_role = sanitize_text_field( $_POST['role'] );

			// If the new role isn't editable by the logged-in user die with error.
			$editable_roles = get_editable_roles();
			if ( ! empty( $new_role ) && empty( $editable_roles[ $new_role ] ) ) {
				wp_die( __( 'Sorry, you are not allowed to give users that role.' ), 403 );
			}
		}

		if ( isset( $_POST['email'] ) ) {
			$user->user_email = sanitize_text_field( wp_unslash( $_POST['email'] ) );
		}
		if ( isset( $_POST['nickname'] ) ) {
			$user->nickname = sanitize_text_field( $_POST['nickname'] );
		}

		//add_action('user_profile_update_errors',array( $this, 'get_user_errrors' ));
		$errors = new WP_Error();

		/* checking that username has been typed */
		if ( '' === $user->user_login ) {
			$errors->add( 'user_login', __( '<strong>Error:</strong> Please enter a username.' ) );
		}

		/* checking that nickname has been typed */
		if ( $update && empty( $user->nickname ) ) {
			$errors->add( 'nickname', __( '<strong>Error:</strong> Please enter a nickname.' ) );
		}

		// Check for blank password when adding a user.
		if ( ! $update && empty( $pass1 ) ) {
			$errors->add( 'pass', __( '<strong>Error:</strong> Please enter a password.' ), array( 'form-field' => 'pass1' ) );
		}

		// Check for "\" in password.
		if ( false !== strpos( wp_unslash( $pass1 ), '\\' ) ) {
			$errors->add( 'pass', __( '<strong>Error:</strong> Passwords may not contain the character "\\".' ), array( 'form-field' => 'pass1' ) );
		}

		// Checking the password has been typed twice the same.
		if ( ( $update || ! empty( $pass1 ) ) && $pass1 != $pass2 ) {
			$errors->add( 'pass', __( '<strong>Error:</strong> Passwords do not match. Please enter the same password in both password fields.' ), array( 'form-field' => 'pass1' ) );
		}

		if ( ! empty( $pass1 ) ) {
			$user->user_pass = $pass1;
		}

		if ( ! $update && isset( $_POST['user_login'] ) && ! validate_username( $_POST['user_login'] ) ) {
			$errors->add( 'user_login', __( '<strong>Error:</strong> This username is invalid because it uses illegal characters. Please enter a valid username.' ) );
		}

		if ( ! $update && username_exists( $user->user_login ) ) {
			$errors->add( 'user_login', __( '<strong>Error:</strong> This username is already registered. Please choose another one.' ) );
		}

		/** This filter is documented in wp-includes/user.php */
		$illegal_logins = (array) apply_filters( 'illegal_user_logins', array() );

		if ( in_array( strtolower( $user->user_login ), array_map( 'strtolower', $illegal_logins ), true ) ) {
			$errors->add( 'invalid_username', __( '<strong>Error:</strong> Sorry, that username is not allowed.' ) );
		}

		/* checking email address */
		if ( empty( $user->user_email ) ) {
			$errors->add( 'empty_email', __( '<strong>Error:</strong> Please enter an email address.' ), array( 'form-field' => 'email' ) );
		} elseif ( ! is_email( $user->user_email ) ) {
			$errors->add( 'invalid_email', __( '<strong>Error:</strong> The email address is not correct.' ), array( 'form-field' => 'email' ) );
		} else {
			$owner_id = email_exists( $user->user_email );
			if ( $owner_id && ( ! $update || ( $owner_id != $user->ID ) ) ) {
				$errors->add( 'email_exists', __( '<strong>Error:</strong> This email is already registered. Please choose another one.' ), array( 'form-field' => 'email' ) );
			}
		}
		if ( $errors->has_errors() ) {
			return $errors;
		}
	}

	/**
	 * Save customer in Avtax
	 *
	 * @since 2.6.0
	 *
	 * @return void
	 */
	public function save_customer_avatax( $user_id ) 
	{
		try 
		{
			$errors = $this->validation_of_fields($user_id);
			if(empty($errors))
			{
				if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api()) {
					$user_data = wc_avatax()->get_user_data($user_id);
					if(!empty($user_data['customerCode']))
					{
						$customer = wc_avatax()->check_if_customer_exists_return($user_data['customerCode']);
						if(!empty((array) $customer))
						{
							if(strval($_POST['email'])==strval($customer->emailAddress) &&
							strval($_POST['first_name']. " " . $_POST['last_name']) == strval($customer->name) &&
							strval($_POST['billing_address_1'])==strval($customer->line1) &&
							strval($_POST['billing_address_2'])==strval($customer->line2) &&
							strval($_POST['billing_city'])==strval($customer->city) &&
							strval($_POST['billing_postcode'])==strval($customer->postalCode) &&
							strval($_POST['billing_country'])==strval($customer->country) &&
							strval($_POST['billing_state'])==strval($customer->region) &&
							strval($_POST['billing_email'])==strval($customer->customerCode) &&
							strval($_POST['email']."_".$user_id)==strval($customer->alternateId)){
							wc_avatax()->log("All values are similar");
							}
						}
						else
						{
							$this->get_plugin()->get_api()->update_customer_object_to_avatax($user_data['customerCode'],array(
								'id' => $user_id,
								'customerCode' => strval($_POST['billing_email']),
								'emailAddress' => strval($_POST['email']),
								'name' => strval($_POST['first_name']. " " . $_POST['last_name']),
								'line1'      => strval($_POST['billing_address_1']),
								'line2'     => strval($_POST['billing_address_2']),
								'city'   => strval($_POST['billing_city']),
								'postalCode'  => strval($_POST['billing_postcode']),
								'country'  => strval($_POST['billing_country']),
								'region'  => strval($_POST['billing_state']),
								'alternateId'  =>strval($_POST['email'])."_".$user_id,
							));
							wc_avatax()->log("All values are not similar");
						}
					}
				}
			}
		}
		catch ( Framework\SV_WC_API_Exception $e ) 
		{

			//Logging error
			wc_avatax()->logger()->log_exception("Admin", "save_customer_avatax", $e->getMessage(), $e->getTraceAsString());

			wp_die( __( 'Error while updating data in avatax.' ), 403 );
			if ( wc_avatax()->logging_enabled() ) {
				wc_avatax()->log( $e->getMessage() );
			}
		}
	}

	/**
	 * Save the customer tax settings.
	 *
	 * @since 1.0.0
	 * @param int $user_id The user ID.
	 */
	public function save_tax_meta_fields( $user_id ) {

		// Save the tax exemption code
		update_user_meta( $user_id, 'wc_avatax_tax_exemption', wc_clean( $_POST['wc_avatax_user_exemption'] ) );

		/**
		 * Field wc_avatax_user_ior added hold seller importer of record data for user.
		 *
		 * @since 2.3.0
		 *
		 */
		update_user_meta( $user_id, 'wc_avatax_user_ior', wc_clean( $_POST['wc_avatax_user_ior'] ) );
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
}
