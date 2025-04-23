<?php
/**
 * Initiation class file.
 *
 * @package woocommerce-shipping-canada-post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin initiation class.
 */
class WC_Shipping_Canada_Post_Init {
	/**
	 * Plugin's version.
	 *
	 * @since 2.5.0
	 *
	 * @var string
	 */
	public $version = WC_CANADA_POST_VERSION;

	/**
	 * Class instance.
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Get the class instance.
	 *
	 * @return WC_Shipping_Canada_Post_Init
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
			define( 'WC_CANADA_POST_REGISTRATION_ENDPOINT', 'https://woocommerce.com/wc-api/canada_post_registration' );

			add_action( 'init', array( $this, 'load_textdomain' ) );
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( WC_CANADA_POST_FILE ), array( $this, 'plugin_links' ) );
			add_action( 'woocommerce_shipping_init', array( $this, 'includes' ) );
			add_filter( 'woocommerce_shipping_methods', array( $this, 'add_method' ) );
			add_action( 'admin_notices', array( $this, 'connect_canada_post' ) );
			add_action( 'woocommerce_api_canada_post_return', array( $this, 'wc_canada_post_api_canada_post_return' ) );
			add_action( 'admin_notices', array( $this, 'environment_check' ) );
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
			add_action( 'wp_ajax_canada_post_dismiss_upgrade_notice', array( $this, 'dismiss_upgrade_notice' ) );
			add_action( 'wp_ajax_wc_canada_post_create_registration_nonce', array( $this, 'wc_canada_post_create_registration_nonce' ) );
		} else {
			add_action( 'admin_notices', array( $this, 'wc_deactivated' ) );
		}
	}

	/**
	 * Environment check.
	 *
	 * @return void
	 */
	public function environment_check() {
		if ( version_compare( WC_VERSION, '3.0.0', '<' ) ) {
			return;
		}

		$general_setting = add_query_arg(
			array(
				'page' => 'wc-settings',
				'tab'  => 'general',
			),
			admin_url( 'admin.php' )
		);

		if ( 'CAD' !== get_woocommerce_currency() ) {
			printf(
				'<div class="error"><p>%s</p></div>',
				// translators: %s is a WooCommerce general settings URL.
				wp_kses( sprintf( __( 'Canada Post requires that the <a href="%s">currency</a> is set to Canadian Dollars.', 'woocommerce-shipping-canada-post' ), esc_url( $general_setting ) ), array( 'a' => array( 'href' => array() ) ) )
			);
		}

		if ( 'CA' !== WC()->countries->get_base_country() ) {
			printf(
				'<div class="error"><p>%s</p></div>',
				// translators: %s is a WooCommerce general settings URL.
				wp_kses( sprintf( __( 'Canada Post requires that the <a href="%s">base country/region</a> is set to Canada.', 'woocommerce-shipping-canada-post' ), esc_url( $general_setting ) ), array( 'a' => array( 'href' => array() ) ) )
			);
		}
	}

	/**
	 * Connects to Canada Post
	 *
	 * @version 2.5.3
	 */
	public function connect_canada_post() {
		/**
		 * If shipping is disabled, no need to prompt for connection.
		 *
		 * @see https://github.com/woocommerce/woocommerce-shipping-canada-post/issues/44
		 */
		if ( 'disabled' === get_option( 'woocommerce_ship_to_countries' ) ) {
			return;
		}

		if ( empty( $_GET['token-id'] ) && empty( $_GET['registration-status'] ) && ! get_option( 'wc_canada_post_customer_number' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended --- No need to use security nonce as no DB operation.
			?>
			<div id="message" class="updated">
				<p>
					<strong><?php esc_html_e( 'Connect your Canada Post Account', 'woocommerce-shipping-canada-post' ); ?></strong>
					&ndash; <?php esc_html_e( 'Before you can start using Canada Post you need to register for an account, or connect an existing one.', 'woocommerce-shipping-canada-post' ); ?>
				</p>
				<p class="submit">
					<a class="wc-canada-post-registration-link" href="<?php echo esc_url( add_query_arg( 'return_url', WC()->api_request_url( 'canada_post_return' ), WC_CANADA_POST_REGISTRATION_ENDPOINT ) ); ?>" class="button-primary"><?php esc_html_e( 'Register/Connect', 'woocommerce-shipping-canada-post' ); ?></a>
				</p>
			</div>
			<script type="application/javascript">
				jQuery( document ).ready( function ( $ ) {
					$( '.wc-canada-post-registration-link' ).on( 'click', function ( e ) {
						e.preventDefault();

						/**
						 * Send AJAX request to create a new nonce we'll use to secure the
						 * wc_canada_post_api_canada_post_return method.
						 */
						$.ajax( {
							url: '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>',
							type: 'POST',
							data: {
								action: 'wc_canada_post_create_registration_nonce',
								security: '<?php echo esc_js( wp_create_nonce( 'wc_canada_post_create_registration_nonce' ) ); ?>'
							},
							success: function ( response ) {
								if ( response.success ) {
									window.location.href = $( '.wc-canada-post-registration-link' ).attr( 'href' );
								}

								if ( response.data.message ) {
									alert( response.data.message );
								}
							}
						} );
					} );
				} );
			</script>
			<?php
		}
	}

	/**
	 * Create a new nonce to verify in the wc_canada_post_api_canada_post_return method.
	 *
	 * @version 2.5.3
	 */
	public function wc_canada_post_create_registration_nonce() {
		if ( ! check_ajax_referer( 'wc_canada_post_create_registration_nonce', 'security', false ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'The link you followed has expired. Please refresh the page and try again.', 'woocommerce-shipping-canada-post' ) ) );
		}

		// Create a new nonce.
		$nonce = wp_create_nonce( 'canada-post-registration_user-' . get_current_user_id() );

		// Save the nonce to the database.
		update_option( 'wc_canada_post_registration_nonce', $nonce );

		wp_send_json_success();
	}

	/**
	 * When returning from CP, redirect to settings
	 */
	public function wc_canada_post_api_canada_post_return() {
		if ( ! wp_verify_nonce( get_option( 'wc_canada_post_registration_nonce', '' ), 'canada-post-registration_user-' . get_current_user_id() ) ) {
			wp_die( esc_html__( 'The link you followed has expired.', 'woocommerce-shipping-canada-post' ), 403 );
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( esc_html__( 'Insufficient privileges to change plugin settings.', 'woocommerce-shipping-canada-post' ), 403 );
		}

		if ( isset( $_GET['token-id'] ) && isset( $_GET['registration-status'] ) ) {
			switch ( $_GET['registration-status'] ) {
				case 'CANCELLED':
					wp_die( esc_html__( 'The Canada Post extension will be unable to get quotes on your behalf until you accept the terms and conditons.', 'woocommerce-shipping-canada-post' ) );
					break;
				case 'SUCCESS':
					// Get details.
					$details = wp_remote_get(
						add_query_arg( 'token', sanitize_text_field( wp_unslash( $_GET['token-id'] ) ), WC_CANADA_POST_REGISTRATION_ENDPOINT ),
						array(
							'method'      => 'GET',
							'timeout'     => 45,
							'redirection' => 5,
							'httpversion' => '1.0',
							'blocking'    => true,
							'headers'     => array( 'user-agent' => 'WCAPI/1.0.0' ),
							'cookies'     => array(),
							'sslverify'   => false,
						)
					);
					$details = (array) json_decode( $details['body'] );

					if ( ! empty( $details['customer-number'] ) ) {
						// Delete registration nonce.
						delete_option( 'wc_canada_post_registration_nonce' );

						update_option( 'wc_canada_post_customer_number', sanitize_text_field( $details['customer-number'] ) );
						update_option( 'wc_canada_post_contract_number', sanitize_text_field( $details['contract-number'] ) );
						update_option( 'wc_canada_post_merchant_username', sanitize_text_field( $details['merchant-username'] ) );
						update_option( 'wc_canada_post_merchant_password', sanitize_text_field( $details['merchant-password'] ) );

					} else {
						wp_die( esc_html__( 'Unable to get merchant info - please try again later.', 'woocommerce-shipping-canada-post' ) );
					}
					break;
				default:
					wp_die( esc_html__( 'Unable to get registration token - please try again later.', 'woocommerce-shipping-canada-post' ) );
					break;
			}

			wp_safe_redirect( admin_url( 'admin.php?page=wc-settings&tab=shipping&section=canada_post' ) );
			exit;
		}

		wp_die( esc_html__( 'Invalid Response', 'woocommerce-shipping-canada-post' ) );
	}

	/**
	 * Load plugin files function.
	 *
	 * @return void
	 */
	public function includes() {
		include_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post.php';
		include_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-privacy.php';
	}

	/**
	 * Add Canada Post shipping method to WC
	 *
	 * @param mixed $methods All registered shipping methods.
	 *
	 * @return array
	 */
	public function add_method( $methods ) {
		$methods['canada_post'] = 'WC_Shipping_Canada_Post';

		return $methods;
	}

	/**
	 * Localisation.
	 */
	public function load_textdomain() {
		load_plugin_textdomain( 'woocommerce-shipping-canada-post', false, plugin_basename( WC_CANADA_POST_ABSPATH ) . '/languages/' );
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
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', 'woocommerce-shipping-canada-post/woocommerce-shipping-canada-post.php' );
		}
	}

	/**
	 * Plugin page links.
	 *
	 * @param array $links Registered plugin links.
	 *
	 * @return array
	 */
	public function plugin_links( $links ) {
		$settings_link = admin_url( 'admin.php?page=wc-settings&tab=shipping&section=canada_post' );

		$plugin_links = array(
			'<a href="' . $settings_link . '">' . __( 'Settings', 'woocommerce-shipping-canada-post' ) . '</a>',
			'<a href="https://woocommerce.com/my-account/tickets">' . __( 'Support', 'woocommerce-shipping-canada-post' ) . '</a>',
			'<a href="https://docs.woocommerce.com/document/canada-post/">' . __( 'Docs', 'woocommerce-shipping-canada-post' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * WooCommerce not installed notice
	 */
	public function wc_deactivated() {
		// translators: %s is an anchor element with WooCommerce site link.
		echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Canada Post Shipping requires %s to be installed and active.', 'woocommerce-shipping-canada-post' ), '<a href="https://woocommerce.com" target="_blank">WooCommerce</a>' ) . '</p></div>';
	}

	/**
	 * Update/migration script.
	 *
	 * @since 2.5.0
	 * @version 2.5.0
	 * @return void
	 */
	public function install() {
		// Get all saved settings and cache it.
		$canada_post_settings = get_option( 'woocommerce_canada_post_settings', false );

		// Settings exists.
		if ( $canada_post_settings ) {
			global $wpdb;

			// Unset un-needed settings.
			unset( $canada_post_settings['enabled'] );
			unset( $canada_post_settings['availability'] );
			unset( $canada_post_settings['countries'] );

			// first add it to the "rest of the world" zone when no Canada Post
			// instance.
			if ( ! $this->is_zone_has_canada_post( 0 ) ) {
				$wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->prefix}woocommerce_shipping_zone_methods ( zone_id, method_id, method_order, is_enabled ) VALUES ( %d, %s, %d, %d )", 0, 'canada_post', 1, 1 ) );
				// Add settings to the newly created instance to options table.
				$instance = $wpdb->insert_id;
				add_option( 'woocommerce_canada_post_' . $instance . '_settings', $canada_post_settings );
			}
			update_option( 'woocommerce_canada_post_show_upgrade_notice', 'yes' );
		}
		update_option( 'wc_canada_post_version', $this->version );
	}

	/**
	 * Show the user a notice for plugin updates
	 *
	 * @since 2.5.0
	 */
	public function upgrade_notice() {
		$show_notice = get_option( 'woocommerce_canada_post_show_upgrade_notice' );

		if ( 'yes' !== $show_notice ) {
			return;
		}

		$query_args      = array(
			'page' => 'wc-settings',
			'tab'  => 'shipping',
		);
		$zones_admin_url = add_query_arg( $query_args, get_admin_url() . 'admin.php' );
		?>
		<div class="notice notice-success is-dismissible wc-canada-post-notice">
			<p>
				<?php
					// translators: %1$s is anchor element opener and %2$s is anchor element closer.
					printf( esc_html__( 'Canada Post now supports shipping zones. The zone settings were added to a new Canada Post method on the "Rest of the World" Zone. See the zones %1$shere%2$s ', 'woocommerce-shipping-canada-post' ), '<a href="' . esc_url( $zones_admin_url ) . '">', '</a>' );
				?>
			</p>
		</div>

		<script type="application/javascript">
			jQuery( '.notice.wc-canada-post-notice' ).on( 'click', '.notice-dismiss', function () {
				wp.ajax.post('canada_post_dismiss_upgrade_notice');
			});
		</script>
		<?php
	}

	/**
	 * Turn of the dismisable upgrade notice.
	 *
	 * @since 2.5.0
	 */
	public function dismiss_upgrade_notice() {
		update_option( 'woocommerce_canada_post_show_upgrade_notice', 'no' );
	}

	/**
	 * Helper method to check whether given zone_id has Canada Post method instance.
	 *
	 * @since 2.5.0
	 *
	 * @param int $zone_id Zone ID.
	 *
	 * @return bool True if given zone_id has canada_post method instance
	 */
	public function is_zone_has_canada_post( $zone_id ) {
		global $wpdb;

		return (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(instance_id) FROM {$wpdb->prefix}woocommerce_shipping_zone_methods WHERE method_id = 'canada_post' AND zone_id = %d", $zone_id ) ) > 0;
	}
}
