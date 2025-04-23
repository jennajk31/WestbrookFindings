<?php
/**
 * WC_USPS class file.
 *
 * @package WC_Shipping_USPS
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once WC_USPS_ABSPATH . 'includes/trait-util.php';

use WooCommerce\USPS\Util;

/**
 * WC_USPS class
 */
class WC_USPS {

	use Util;

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
		add_action( 'before_woocommerce_init', array( $this, 'declare_product_editor_compatibility' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( WC_USPS_FILE ), array( $this, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( $this, 'plugin_row_meta' ), 10, 2 );
		add_action( 'woocommerce_shipping_init', array( $this, 'init' ) );
		add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		add_action( 'admin_notices', array( $this, 'environment_check' ) );
		add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
		add_action( 'wp_ajax_usps_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );

		include_once WC_USPS_ABSPATH . 'includes/class-wc-shipping-usps-admin.php';
		if ( is_admin() ) {
			new WC_Shipping_USPS_Admin();

		}

		include_once WC_USPS_ABSPATH . 'includes/class-product-editor.php';
	}

	/**
	 * Check the environment.
	 *
	 * @return void
	 */
	public function environment_check() {
		if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
			return;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown --- It's a capability from WooCommerce
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$admin_page              = 'wc-settings';
		$is_usps_gate_registered = isset( WC()->shipping->get_shipping_methods()['usps'] ) && is_a( WC()->shipping->get_shipping_methods()['usps'], 'WC_Shipping_USPS' ) ? true : false;

		if ( get_woocommerce_currency() !== 'USD' ) {
			echo '<div class="error">
				<p>' .
				// translators: %s is a link to WooCommerce general settings page.
				wp_kses_post( sprintf( __( 'USPS requires that the <a href="%s">currency</a> is set to US Dollars.', 'woocommerce-shipping-usps' ), esc_url( admin_url( 'admin.php?page=' . $admin_page . '&tab=general' ) ) ) ) .
				'</p>
			</div>';
		} elseif ( ! in_array( WC()->countries->get_base_country(), array( 'US', 'PR', 'VI', 'MH', 'FM' ), true ) ) {
			echo '<div class="error">
				<p>' .
				// translators: %s is a link to WooCommerce general settings page.
				wp_kses_post( sprintf( __( 'USPS requires that the <a href="%s">base country/region</a> is the United States.', 'woocommerce-shipping-usps' ), esc_url( admin_url( 'admin.php?page=' . $admin_page . '&tab=general' ) ) ) ) .
				'</p>
			</div>';
		} elseif ( $is_usps_gate_registered && '150WOOTH2143' === WC()->shipping->get_shipping_methods()['usps']->get_option( 'user_id' ) || ( $is_usps_gate_registered && empty( WC()->shipping->get_shipping_methods()['usps']->get_option( 'user_id' ) ) && $this->instances_exist() ) ) {
			echo '<div class="error">
				<p>' .
				wp_kses_post(
					sprintf(
						// translators: %1$s is a link to USPS API. %2$s is a anchor closer tag. %3$s is  a link to USPS settings page.
						__( 'The WooCommerce USPS User ID your site is currently using is no longer valid. Registering for an account at USPS is now required. <br />Please register for an %1$saccount at USPS%2$s and %3$senter your user ID here%2$s.', 'woocommerce-shipping-usps' ),
						'<a href="https://www.usps.com/business/web-tools-apis/welcome.htm" target="_blank">',
						'</a>',
						'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=usps' ) ) . '" target="_blank">'
					)
				) .
				'</p>
			</div>';
		} elseif ( $is_usps_gate_registered && empty( WC()->shipping->get_shipping_methods()['usps']->get_option( 'user_id' ) ) ) {
			echo '<div class="error">
				<p>' .
				wp_kses_post(
					sprintf(
						// translators: %1$s is a link to USPS API. %2$s is a anchor closer tag. %3$s is  a link to USPS settings page.
						__( 'WooCommerce USPS Shipping plugin requires you to %1$sregister for an account at USPS%2$s and %3$senter your user ID here%2$s.', 'woocommerce-shipping-usps' ),
						'<a href="https://www.usps.com/business/web-tools-apis/welcome.htm" target="_blank">',
						'</a>',
						'<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=usps' ) ) . '" target="_blank">'
					)
				) .
				'</p>
			</div>';
		}
	}

	/**
	 * Localisation
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'woocommerce-shipping-usps', false, dirname( plugin_basename( WC_USPS_FILE ) ) . '/languages/' );
	}

	/**
	 * Declare High-Performance Order Storage (HPOS) compatibility
	 *
	 * @see https://github.com/woocommerce/woocommerce/wiki/High-Performance-Order-Storage-Upgrade-Recipe-Book#declaring-extension-incompatibility
	 *
	 * @return void
	 */
	public function declare_hpos_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-shipping-usps/woocommerce-shipping-usps.php' );
		}
	}

	/**
	 * Declare Product Editor compatibility
	 *
	 * @see https://github.com/woocommerce/woocommerce/blob/trunk/docs/product-editor-development/product-editor.md#declaring-compatibility-with-the-product-editor
	 */
	public function declare_product_editor_compatibility() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'product_block_editor', 'woocommerce-shipping-usps/woocommerce-shipping-usps.php' );
		}
	}

	/**
	 * Settings page links.
	 *
	 * @param array $links List of plugin URLs.
	 */
	public function plugin_action_links( $links ) {
		$plugin_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=usps' ) . '">' . __( 'Settings', 'woocommerce-shipping-usps' ) . '</a>',
		);
		return array_merge( $plugin_links, $links );
	}

	/**
	 * Plugin page links to support and documentation
	 *
	 * @param  array  $links List of plugin links.
	 * @param  string $file Current file.
	 * @return array
	 */
	public function plugin_row_meta( $links, $file ) {
		if ( plugin_basename( WC_USPS_FILE ) === $file ) {
			$row_meta = array(
				/**
				 * Filter to modify USPS documentation URL.
				 *
				 * @var string USPS documentation URL.
				 *
				 * @since 4.4.25
				 */
				'docs'    => '<a href="' . esc_url( apply_filters( 'woocommerce_shipping_usps_docs_url', 'https://docs.woocommerce.com/document/usps-shipping-method/' ) ) . '" title="' . esc_attr( __( 'View Documentation', 'woocommerce-shipping-usps' ) ) . '">' . __( 'Docs', 'woocommerce-shipping-usps' ) . '</a>',

				/**
				 * Filter to modify USPS support URL.
				 *
				 * @var string USPS support URL.
				 *
				 * @since 4.4.25
				 */
				'support' => '<a href="' . esc_url( apply_filters( 'woocommerce_shipping_usps_support_url', 'https://woocommerce.com/my-account/create-a-ticket/?select=18657' ) ) . '" title="' . esc_attr( __( 'Open a support request at WooCommerce.com', 'woocommerce-shipping-usps' ) ) . '">' . __( 'Support', 'woocommerce-shipping-usps' ) . '</a>',
			);
			return array_merge( $links, $row_meta );
		}
		return (array) $links;
	}

	/**
	 * Load gateway class
	 */
	public function init() {
		require_once WC_USPS_ABSPATH . 'includes/class-wc-usps-privacy.php';
		include_once WC_USPS_ABSPATH . 'includes/class-wc-shipping-usps.php';
	}

	/**
	 * Add method to WC
	 *
	 * @param array $methods List of shipping methods.
	 */
	public function add_method( $methods ) {
		if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
			$methods[] = 'WC_Shipping_USPS';
		} else {
			$methods['usps'] = 'WC_Shipping_USPS';
		}

		return $methods;
	}

	/**
	 * Enqueue scripts
	 */
	public function assets() {
		if ( ! $this->is_usps_settings_page() && ! $this->is_usps_instance_settings_page() ) {
			return;
		}

        // Enqueue Css.
		wp_enqueue_style( 'usps-admin-css', plugin_dir_url( WC_USPS_FILE ) . 'assets/css/admin.css', '', WC_USPS_VERSION );

		// Enqueue scripts.
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'usps-admin', plugin_dir_url( WC_USPS_FILE ) . 'assets/js/admin.js', array( 'jquery' ), WC_USPS_VERSION, true );

		wp_localize_script(
			'usps-admin',
			'usps_settings',
			array(
				'woocommerce_usps_rest' => get_option( 'woocommerce_usps_rest' ),
				'is_instance_settings'  => $this->is_usps_instance_settings_page(),
				'is_usps_settings_page' => $this->is_usps_settings_page(),
			)
		);
	}

	/**
	 * Checks the plugin version
	 *
	 * @since 4.4.0
	 * @version 4.4.0
	 * @return bool
	 */
	public function maybe_install() {
		// Only need to do this for versions less than 4.4.0 to migrate
		// settings to shipping zone instance.
		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
		if ( ! $doing_ajax
			&& ! defined( 'IFRAME_REQUEST' )
			&& version_compare( WC_VERSION, '2.6.0', '>=' )
			&& version_compare( get_option( 'wc_usps_version' ), '4.4.0', '<' ) ) {

			$this->install();

		}

		return true;
	}

	/**
	 * Update/migration script
	 *
	 * @since 4.4.0
	 * @version 4.4.0
	 */
	public function install() {
		// get all saved settings and cache it.
		$usps_settings = get_option( 'woocommerce_usps_settings', false );

		// settings exists.
		if ( $usps_settings ) {
			global $wpdb;

			// unset un-needed settings.
			unset( $usps_settings['enabled'] );
			unset( $usps_settings['availability'] );
			unset( $usps_settings['countries'] );

			// first add it to the "rest of the world" zone when no usps
			// instance.
			if ( ! $this->is_zone_has_usps( 0 ) ) {
				// phpcs:ignore --- Need to user WPDB::query add a USPS method to rest of the world
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'usps', 1, 1 ) );
				// add settings to the newly created instance to options table.
				$instance = $wpdb->insert_id;
				add_option( 'woocommerce_usps_' . $instance . '_settings', $usps_settings );
			}

			update_option( 'woocommerce_usps_show_upgrade_notice', 'yes' );
		}

		update_option( 'wc_usps_version', WC_USPS_VERSION );
	}

	/**
	 * Show the user a notice for plugin updates
	 *
	 * @since 4.4.0
	 */
	public function upgrade_notice() {
		$show_notice = get_option( 'woocommerce_usps_show_upgrade_notice' );

		if ( 'yes' !== $show_notice ) {
			return;
		}

		// phpcs:ignore WordPress.WP.Capabilities.Unknown --- It's a capability from WooCommerce
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		$query_args      = array(
			'page' => 'wc-settings',
			'tab'  => 'shipping',
		);
		$zones_admin_url = add_query_arg( $query_args, get_admin_url() . 'admin.php' );
		?>
		<div class="notice notice-success is-dismissible wc-usps-notice">
			<p>
				<?php
				// translators: %1$s is a link to WooCommerce shipping zone settings page. %2$s is an anchor closer tag.
				echo wp_kses_post( sprintf( __( 'USPS now supports shipping zones. The zone settings were added to a new USPS method on the "Rest of the World" Zone. See the zones %1$shere%2$s ', 'woocommerce-shipping-usps' ), '<a href="' . esc_url( $zones_admin_url ) . '">', '</a>' ) );
				?>
			</p>
		</div>

		<script type="application/javascript">
			jQuery( '.notice.wc-usps-notice' ).on( 'click', '.notice-dismiss', function () {
				wp.ajax.post('usps_dismiss_upgrade_notice');
			});
		</script>
		<?php
	}

	/**
	 * Turn of the dismisable upgrade notice.
	 *
	 * @since 4.4.0
	 */
	public function dismiss_upgrade_notice() {
		update_option( 'woocommerce_usps_show_upgrade_notice', 'no' );
	}
}
