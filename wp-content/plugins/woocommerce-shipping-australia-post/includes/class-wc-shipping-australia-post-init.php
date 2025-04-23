<?php
/**
 * Australia Post Init.
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WC_Shipping_Australia_Post_Init class.
 */
class WC_Shipping_Australia_Post_Init {
	/**
	 * Plugin's version.
	 *
	 * @since 2.4.0
	 *
	 * @var string
	 */
	public $version = WC_SHIPPING_AUSTRALIA_POST_VERSION;

	/**
	 * Class Instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Get the class instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Initialize the plugin's public actions
	 */
	public function __construct() {
		if ( class_exists( 'WC_Shipping_Method' ) ) {
			add_action( 'admin_init', array( $this, 'maybe_install' ), 5 );
			add_action( 'after_setup_theme', array( $this, 'load_textdomain' ) );
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( WC_SHIPPING_AUSTRALIA_POST_PLUGIN_FILE ), array( $this, 'plugin_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'includes' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
			add_action( 'admin_notices', array( $this, 'environment_check' ) );
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
			add_action( 'wp_ajax_australia_post_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'wc_deactivated' ) );
		}
	}

	/**
	 * Environment_check function.
	 *
	 * @return void
	 */
	public function environment_check() {
		if ( version_compare( WC_VERSION, '2.6.0', '<' ) ) {
			return;
		}

		if ( ! wc_shipping_enabled() ) {
			return;
		}

		$general_tab_link = admin_url(
			add_query_arg(
				array(
					'page' => 'wc-settings',
					'tab'  => 'general',
				),
				'admin.php'
			)
		);

		/**
		 * Allow 3rd parties to skip the check against the store currency. This check is irrelevant in multi-currency scenarios.
		 *
		 * @since 2.4.12
		 */
		if ( apply_filters( 'woocommerce_shipping_australia_post_check_store_currency', true ) && ( 'AUD' !== get_woocommerce_currency() ) ) {
			/* translators: %1$s Link to General Tab, %2$s end of link */
			echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'Australia Post requires that the %1$scurrency%2$s is set to Australian Dollars.', 'woocommerce-shipping-australia-post' ), '<a href="' . esc_url( $general_tab_link ) . '">', '</a>' ) ) . '</p></div>';
		}

		if ( 'AU' !== WC()->countries->get_base_country() ) {
			/* translators: %1$s Link to General Tab, %2$s end of link */
			echo '<div class="error"><p>' . wp_kses( sprintf( __( 'Australia Post requires that the <a href="%s">base country/region</a> is set to Australia.', 'woocommerce-shipping-australia-post' ), esc_url( $general_tab_link ) ), array( 'a' => array( 'href' => array() ) ) ) . '</p></div>';
		}
	}

	/**
	 * Include classes.
	 *
	 * @since 2.4.0
	 * @version 2.4.0
	 * @return void
	 */
	public function includes() {
		require_once __DIR__ . '/class-wc-australia-post-privacy.php';

		require_once __DIR__ . '/class-wc-shipping-australia-post.php';
	}

	/**
	 * Add Australia Post shipping method to WC
	 *
	 * @param mixed $methods List of shipping methods.
	 * @return array
	 */
	public function add_method( $methods ) {

		$methods['australia_post'] = 'WC_Shipping_Australia_Post';

		return $methods;
	}

	/**
	 * Localisation
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-shipping-australia-post', false, plugin_basename( WC_SHIPPING_AUSTRALIA_POST_ABSPATH ) . '/languages/' );
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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-shipping-australia-post/woocommerce-shipping-australia-post.php' );
		}
	}

	/**
	 * Plugin page links
	 *
	 * @param array $links List of plugin action links.
	 */
	public function plugin_links( $links ) {
		$plugin_links = array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section=australia_post' ) . '">' . __( 'Settings', 'woocommerce-shipping-australia-post' ) . '</a>',
			'<a href="https://woocommerce.com/my-account/tickets/">' . __( 'Support', 'woocommerce-shipping-australia-post' ) . '</a>',
			'<a href="http://docs.woocommerce.com/document/australia-post/">' . __( 'Docs', 'woocommerce-shipping-australia-post' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * WooCommerce not installed notice
	 */
	public function wc_deactivated() {
		/* translators: %s Link to WooCommerce */
		echo '<div class="error"><p>' . wp_kses_post( sprintf( __( 'WooCommerce Australia Post Shipping requires %s to be installed and active.', 'woocommerce-shipping-australia-post' ), '<a href="https://woocommerce.com" target="_blank">WooCommerce</a>' ) ) . '</p></div>';
	}

	/**
	 * Checks the plugin version
	 *
	 * @since 2.4.0
	 * @version 2.4.0
	 * @return bool
	 */
	public function maybe_install() {
		// only need to do this for versions less than 2.4.0 to migrate.
		// settings to shipping zone instance.
		$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
		if ( ! $doing_ajax
			&& ! defined( 'IFRAME_REQUEST' )
			&& version_compare( get_option( 'wc_australia_post_version' ), '2.4.0', '<' ) ) {

			$this->install();

		}

		return true;
	}

	/**
	 * Update/migration script
	 *
	 * @since 2.4.0
	 * @version 2.4.0
	 */
	public function install() {
		// Get all saved settings and cache it.
		$australia_post_settings = get_option( 'woocommerce_australia_post_settings', false );

		// Settings exists.
		if ( $australia_post_settings ) {
			global $wpdb;

			// Unset un-needed settings.
			unset( $australia_post_settings['enabled'] );
			unset( $australia_post_settings['availability'] );
			unset( $australia_post_settings['countries'] );

			// Add it to the "rest of the world" zone when no Australia Post instance.
			if ( ! $this->is_zone_has_australia_post( 0 ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'australia_post', 1, 1 ) );
				// Add settings to the newly created instance to options table.
				$instance = $wpdb->insert_id;
				add_option( 'woocommerce_australia_post_' . $instance . '_settings', $australia_post_settings );
			}
			update_option( 'woocommerce_australia_post_show_upgrade_notice', 'yes' );
		}
		update_option( 'wc_australia_post_version', $this->version );
	}

	/**
	 * Show the user a notice for plugin updates
	 *
	 * @since 2.4.0
	 */
	public function upgrade_notice() {
		$show_notice = get_option( 'woocommerce_australia_post_show_upgrade_notice' );

		if ( 'yes' !== $show_notice ) {
			return;
		}

		$query_args = array(
			'page' => 'wc-settings',
			'tab'  => 'shipping',
		);

		$zones_admin_url = add_query_arg( $query_args, get_admin_url() . 'admin.php' );
		?>
		<div class="notice notice-success is-dismissible wc-australia-post-notice">
			<p>
				<?php
				/* translators: %s Admin link start and end */
				echo wp_kses_post( sprintf( __( 'Australia Post now supports shipping zones. The zone settings were added to a new Australia Post method on the "Rest of the World" Zone. See the zones %1$shere%2$s ', 'woocommerce-shipping-australia-post' ), '<a href="' . esc_url( $zones_admin_url ) . '">', '</a>' ) );
				?>
			</p>
		</div>

		<script type="application/javascript">
			jQuery( '.notice.wc-australia-post-notice' ).on( 'click', '.notice-dismiss', function () {
				wp.ajax.post('australia_post_dismiss_upgrade_notice');
			});
		</script>
		<?php
	}

	/**
	 * Turn of the dismisable upgrade notice.
	 *
	 * @since 2.4.0
	 */
	public function dismiss_upgrade_notice() {
		update_option( 'woocommerce_australia_post_show_upgrade_notice', 'no' );
	}

	/**
	 * Helper method to check whether given zone_id has australia_post method instance.
	 *
	 * @since 2.4.0
	 *
	 * @param int $zone_id Zone ID.
	 *
	 * @return bool True if given zone_id has australia_post method instance
	 */
	public function is_zone_has_australia_post( $zone_id ) {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'australia_post' AND zone_id = %d", $zone_id ) ) > 0;
	}
}
