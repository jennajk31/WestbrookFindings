<?php
/**
 * Plugin Name: WooCommerce Min/Max Quantities
 * Plugin URI: https://woocommerce.com/products/minmax-quantities/
 * Description: Define minimum/maximum allowed quantities for products, variations and orders.
 * Version: 5.2.4
 * Author: Woo
 * Author URI: https://woocommerce.com
 *
 * Requires PHP: 7.4
 *
 * Requires at least: 6.2
 * Tested up to: 6.8
 *
 * WC tested up to: 9.8
 * WC requires at least: 8.2
 *
 * Requires Plugins: woocommerce
 *
 * Text Domain: woocommerce-min-max-quantities
 * Domain Path: /languages
 *
 * Copyright: © 2022 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Woo: 18616:2b5188d90baecfb781a5aa2d6abb900a
 *
 * @package woocommerce-min-max-quantities
 */

if ( ! class_exists( 'WC_Min_Max_Quantities' ) ) :

	define( 'WC_MIN_MAX_QUANTITIES', '5.2.4' ); // WRCS: DEFINED_VERSION.

	/**
	 * Min Max Quantities class.
	 *
	 * @version  5.2.4
	 */
	class WC_Min_Max_Quantities {

		/**
		 * Minimum WooCommerce version.
		 *
		 * @var string
		 */
		public $min_wc_version = '8.2.0';

		/**
		 * Minimum order quantity.
		 *
		 * @var int
		 */
		public $minimum_order_quantity;

		/**
		 * Maximum order quantity.
		 *
		 * @var int
		 */
		public $maximum_order_quantity;

		/**
		 * Minimum order value.
		 *
		 * @var float
		 */
		public $minimum_order_value;

		/**
		 * Maximum order value.
		 *
		 * @var float
		 */
		public $maximum_order_value;

		/**
		 * List of excluded product titles.
		 *
		 * @var array
		 */
		public $excludes = array();

		/**
		 * Instance of compatibility class.
		 *
		 * @var WC_MMQ_Compatibility
		 */
		public $compatibility;

		/**
		 * Instance of addons class.
		 *
		 * @var WC_Min_Max_Quantities_Addons
		 */
		public $addons;

		/**
		 * Class instance.
		 *
		 * @var WC_Min_Max_Quantities
		 */
		private static $instance;

		/**
		 * Get the class instance.
		 *
		 * @return WC_Min_Max_Quantities Instance of this class.
		 */
		public static function get_instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor.
		 */
		public function __construct() {

			if ( ! function_exists( 'WC' ) || version_compare( WC()->version, $this->min_wc_version ) < 0 ) {
				add_action( 'admin_notices', array( $this, 'woocommerce_required_notice' ) );

				return;
			}

			if ( ! function_exists( 'phpversion' ) || version_compare( phpversion(), '7.4.0', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'php_version_notice' ) );
			}

			$this->maybe_define_constant( 'WC_MMQ_ABSPATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
			$this->maybe_define_constant( 'WC_MMQ_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

			/**
			 * Localisation.
			 */
			$this->load_plugin_textdomain();

			if ( is_admin() ) {
				include_once WC_MMQ_ABSPATH . 'includes/admin/class-wc-min-max-quantities-admin.php';
				include_once WC_MMQ_ABSPATH . 'includes/admin/class-wc-min-max-quantities-admin-notices.php';
			}

			add_action( 'init', array( $this, 'maybe_install' ) );

			// REST API hooks.
			include_once WC_MMQ_ABSPATH . 'includes/api/class-wc-mmq-rest-api.php';

			// Quantity rules class.
			require_once WC_MMQ_ABSPATH . 'includes/class-wc-min-max-quantities-quantity-rules.php';

			// Extensions compatibility functions and hooks.
			include_once WC_MMQ_ABSPATH . 'includes/compatibility/class-wc-min-max-quantities-compatibility.php';
			$this->compatibility = WC_MMQ_Compatibility::instance();

			// Tracker.
			include_once WC_MMQ_ABSPATH . 'includes/class-wc-min-max-quantities-tracker.php';

			if ( $this->compatibility->is_module_loaded( 'product_addons' ) ) {
				$this->addons = new WC_Min_Max_Quantities_Addons();
			}

			$this->minimum_order_quantity = absint( get_option( 'woocommerce_minimum_order_quantity' ) );
			$this->maximum_order_quantity = absint( get_option( 'woocommerce_maximum_order_quantity' ) );
			$this->minimum_order_value    = abs( (float) ( get_option( 'woocommerce_minimum_order_value' ) ) );
			$this->maximum_order_value    = abs( (float) ( get_option( 'woocommerce_maximum_order_value' ) ) );

			// Check items.
			add_action( 'woocommerce_check_cart_items', array( $this, 'check_cart_items' ) );

			// If we have errors, make sure those are shown on the checkout page.
			add_action( 'woocommerce_cart_has_errors', array( $this, 'output_errors' ) );

			// Quantity selelectors (2.0+).
			add_filter( 'woocommerce_quantity_input_args', array( $this, 'update_quantity_args' ), 10, 2 );
			add_filter( 'woocommerce_available_variation', array( $this, 'available_variation' ), 10, 3 );
			add_filter( 'wc_min_max_use_group_as_min_quantity', array( $this, 'use_group_as_min_quantity' ), 10, 3 );

			// Prevent add to cart.
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'add_to_cart' ), 10, 4 );

			// Min add to cart ajax.
			add_filter( 'woocommerce_loop_add_to_cart_link', array( $this, 'add_to_cart_link' ), 10, 2 );

			// Show a notice when items would have to be on back order because of min/max.
			add_filter( 'woocommerce_get_availability', array( $this, 'maybe_show_backorder_message' ), 10, 2 );

			add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );

			add_filter( 'woocommerce_add_to_cart_product_id', array( $this, 'modify_add_to_cart_quantity' ) );

			add_action( 'before_woocommerce_init', array( $this, 'declare_feature_compatibilities' ) );

			add_action( 'init', array( $this, 'register_custom_taxonomies' ) );
		}

		/**
		 * Sets DB version and conditionally displays welcome notice.
		 *
		 * @since 4.2.3
		 */
		public function maybe_install() {
			$installed_version = get_option( 'woocommerce_mmq_version', null );

			if ( is_null( $installed_version ) ) {
				include_once WC_MMQ_ABSPATH . 'includes/admin/class-wc-min-max-quantities-admin-notices.php';
				WC_Min_Max_Quantities_Admin_Notices::add_maintenance_notice( 'welcome' );
				add_option( 'woocommerce_mmq_version', WC_MIN_MAX_QUANTITIES );
			}

			// Setup cron jobs.
			if ( WC_MIN_MAX_QUANTITIES !== $installed_version ) {
				if ( ! wp_next_scheduled( 'wc_mmq_daily' ) ) {
					wp_schedule_event( time() + 10, 'daily', 'wc_mmq_daily' );
				}

				if ( ! wp_next_scheduled( 'wc_mmq_hourly' ) ) {
					wp_schedule_event( time() + 10, 'hourly', 'wc_mmq_hourly' );
				}

				update_option( 'woocommerce_mmq_version', WC_MIN_MAX_QUANTITIES );
			}
		}

		/**
		 * Registers custom taxonomies.
		 *
		 * @since 4.1.4
		 */
		public function register_custom_taxonomies() {
			register_meta(
				'term',
				'group_of_quantity',
				array(
					'show_in_rest' => true,
					'type'         => 'string',
					'single'       => true,
				)
			);
		}


		/**
		 * Plugin version getter.
		 *
		 * @since  4.3.0
		 *
		 * @param  boolean $base Whether to return base version.
		 * @param  string  $version Version string.
		 * @return string
		 */
		public function plugin_version( $base = false, $version = '' ) {

			$version = $version ? $version : WC_MIN_MAX_QUANTITIES;

			if ( $base ) {
				$version_parts = explode( '-', $version );
				$version       = count( $version_parts ) > 1 ? $version_parts[0] : $version;
			}

			return $version;
		}

		/**
		 * Define constants if not present.
		 *
		 * @since 4.0.4
		 *
		 * @param string $name  Constant name.
		 * @param mixed  $value Constant value.
		 * @return void
		 */
		protected function maybe_define_constant( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Plugin URL getter.
		 *
		 * @return string
		 */
		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		/**
		 * Output a notice if Woocommerce isn't active.
		 */
		public function woocommerce_required_notice() {

			?><div class="notice notice-error is-dismissible">
				<p>
					<?php
					/* translators: %s: Minimum required WooCommerce version */
					echo wp_kses_post(
						sprintf(
							__(
								'<strong>Min/Max Quantities</strong> requires at least WooCommerce <strong>%s</strong>.',
								'woocommerce-min-max-quantities'
							),
							esc_html( $this->min_wc_version )
						)
					);
					?>
				</p>
			</div>
			<?php
		}

		/**
		 * Output a notice if PHP is older than 7.0
		 */
		public function php_version_notice() {

			?>
			<div class="notice notice-error is-dismissible">
			<p>
				<?php
				/* translators: %s: Minimum required PHP version */
				echo wp_kses_post( sprintf( __( 'WooCommerce Min/Max Quantities requires at least PHP <strong>%1$s</strong>. Learn <a href="%2$s">how to update PHP</a>.', 'woocommerce-min-max-quantities' ), '7.4.0', 'https://woocommerce.com/document/how-to-update-your-php-version/' ) );
				?>
			</p>
			</div>
			<?php
		}

		/**
		 * Load scripts.
		 */
		public function load_scripts() {
			// Only load on single product page and cart page.
			if ( is_product() || is_cart() ) {
				$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
				wp_register_script( 'wc-mmq-frontend', $this->plugin_url() . '/assets/js/frontend/validate' . $suffix . '.js', array( 'jquery' ), WC_MIN_MAX_QUANTITIES );
				wp_script_add_data( 'wc-mmq-frontend', 'strategy', 'defer' );
				wp_enqueue_script( 'wc-mmq-frontend' );
			}
		}

		/**
		 * Declare HPOS (Custom Order tables) and Blocks compatibility.
		 *
		 * @since 4.0.2
		 */
		public function declare_feature_compatibilities() {
			if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				return;
			}

			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__ );
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', __FILE__ );
		}

		/**
		 * Load Localisation files.
		 *
		 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
		 *
		 * Frontend/global Locales found in:
		 * - WP_LANG_DIR/woocommerce-min-max-quantities/woocommerce-min-max-quantities-LOCALE.mo
		 * - woocommerce-min-max-quantities/woocommerce-min-max-quantities-LOCALE.mo (which if not found falls back to:)
		 * - WP_LANG_DIR/plugins/woocommerce-min-max-quantities-LOCALE.mo
		 */
		public function load_plugin_textdomain() {
			// phpcs:ignore
			$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-min-max-quantities' );

			load_textdomain( 'woocommerce-min-max-quantities', WP_LANG_DIR . '/woocommerce-min-max-quantities/woocommerce-min-max-quantities-' . $locale . '.mo' );
			load_plugin_textdomain( 'woocommerce-min-max-quantities', false, plugin_basename( __DIR__ ) . '/' );
		}

		/**
		 * Handle plugin activation process.
		 *
		 * @return void
		 */
		public static function on_activation() {
			// Add daily maintenance process.
			if ( ! wp_next_scheduled( 'wc_mmq_daily' ) ) {
				wp_schedule_event( time() + 10, 'daily', 'wc_mmq_daily' );
			}

			// Add hourly maintenance process.
			if ( ! wp_next_scheduled( 'wc_mmq_hourly' ) ) {
				wp_schedule_event( time() + 10, 'hourly', 'wc_mmq_hourly' );
			}
		}

		/**
		 * Handle plugin deactivation process.
		 *
		 * @return void
		 */
		public static function on_deactivation() {
			// Clear daily maintenance process.
			wp_clear_scheduled_hook( 'wc_mmq_daily' );

			// Clear hourly maintenance process.
			wp_clear_scheduled_hook( 'wc_mmq_hourly' );
		}

		/**
		 * Add an error.
		 *
		 * @since 1.0.0
		 * @version 2.3.18
		 * @param string $error Error text.
		 */
		public function add_error( $error = '' ) {
			if ( $error && ! wc_has_notice( $error, 'error' ) ) {
				wc_add_notice( $error, 'error', array( 'source' => 'woocommerce-min-max-quantities' ) );
			}
		}

		/**
		 * Output any plugin specific error messages
		 *
		 * We use this instead of wc_print_notices so we
		 * can remove any error notices that aren't from us.
		 */
		public function output_errors() {
			$notices  = wc_get_notices( 'error' );
			$messages = array();

			foreach ( $notices as $i => $notice ) {
				if ( isset( $notice['notice'] ) && isset( $notice['data']['source'] ) && 'woocommerce-min-max-quantities' === $notice['data']['source'] ) {
					$messages[] = $notice['notice'];
				} else {
					unset( $notice[ $i ] );
				}
			}

			if ( ! empty( $messages ) ) {
				ob_start();

				wc_get_template(
					'notices/error.php',
					array(
						'messages' => array_filter( $messages ), // @deprecated 3.9.0
						'notices'  => array_filter( $notices ),
					)
				);

				echo wc_kses_notice( ob_get_clean() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
		}

		/**
		 * Add quantity property to add to cart button on shop loop for simple products.
		 *
		 * @param  string     $html    Add to cart link.
		 * @param  WC_Product $product Product object.
		 * @return string
		 */
		public function add_to_cart_link( $html, $product ) {

			if ( 'variable' !== $product->get_type() ) {
				$quantity_attribute = 1;
				[
					WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
					WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
				]                   = $this->get_quantity_rules_for_product( $product );

				if ( $minimum_quantity || $group_of_quantity ) {

					$quantity_attribute = $minimum_quantity;

					if ( $group_of_quantity > 0 && $minimum_quantity < $group_of_quantity ) {
						$quantity_attribute = $group_of_quantity;
					}

					$html = str_replace( '<a ', '<a data-quantity="' . $quantity_attribute . '" ', $html );
				}
			}

			return $html;
		}

		/**
		 * Get product or variation ID to check
		 *
		 * @param array $values List of values.
		 * @return int
		 */
		public function get_id_to_check( $values ) {
			if ( $values['variation_id'] ) {
				$variation         = wc_get_product( $values['variation_id'] );
				$min_max_rules     = $variation ? $variation->get_meta( 'min_max_rules', true ) : false;
				$allow_combination = $variation ? 'yes' === $variation->get_meta( 'allow_combination', true ) : false;

				if ( 'yes' === $min_max_rules && ! $allow_combination ) {
					$checking_id = $values['variation_id'];
				} else {
					$checking_id = $values['product_id'];
				}
			} else {
				$checking_id = $values['product_id'];
			}

			return $checking_id;
		}

		/**
		 * Validate cart items against set rules
		 *
		 * @throws Exception When cart validation fails.
		 */
		public function check_cart_items() {

			try {
				$checked_ids         = array();
				$product_quantities  = array();
				$category_quantities = array();
				$total_quantity      = 0;
				$total_cost          = 0;
				$apply_cart_rules    = false;
				/**
				 * Use this filter to include/exclude tax when calculating total cost of the cart.
				 *
				 * @since 5.1.0
				 *
				 * @param  boolean $include_tax Include tax.
				 */
				$include_tax = apply_filters( 'wc_min_max_quantity_include_tax_order_value_restrictions', true );

				$price_fn = $include_tax ? 'wc_get_price_including_tax' : 'wc_get_price_excluding_tax';

				// Count items + variations first.
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$product     = $values['data'];
					$checking_id = $this->get_id_to_check( $values );

					/**
					 * Use this filter to prevent the quantity of a cart item from being counted in cart-level rules.
					 *
					 * @since 2.4.11
					 *
					 * @param  boolean $do_not_count
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					if ( apply_filters( 'wc_min_max_cart_quantity_do_not_count', false, $checking_id, $cart_item_key, $values ) ) {
						$values['quantity'] = 0;
					}

					if ( ! isset( $product_quantities[ $checking_id ] ) ) {
						$product_quantities[ $checking_id ] = $values['quantity'];
					} else {
						$product_quantities[ $checking_id ] += $values['quantity'];
					}

					$checking_product = wc_get_product( $checking_id );
					$parent_product   = $values['product_id'] === $checking_id ? $checking_product : wc_get_product( $values['product_id'] );

					$minmax_do_not_count = 'no';
					if ( $checking_product && 'yes' === $checking_product->get_meta( 'variation_minmax_do_not_count', true ) ) {
						$minmax_do_not_count = 'yes';
					} elseif ( $parent_product && 'yes' === $parent_product->get_meta( 'minmax_do_not_count', true ) ) {
						$minmax_do_not_count = 'yes';
					}

					/**
					 * Use this filter to prevent the quantity or cost of a cart item from being counted in cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $do_not_count
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', $minmax_do_not_count, $checking_id, $cart_item_key, $values );

					$minmax_cart_exclude = 'no';
					if ( $checking_product && 'yes' === $checking_product->get_meta( 'variation_minmax_cart_exclude', true ) ) {
						$minmax_cart_exclude = 'yes';
					} elseif ( $parent_product && 'yes' === $parent_product->get_meta( 'minmax_cart_exclude', true ) ) {
						$minmax_cart_exclude = 'yes';
					}

					/**
					 * Use this filter to exclude a product from cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $exclude
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', $minmax_cart_exclude, $checking_id, $cart_item_key, $values );

					if ( 'yes' !== $minmax_do_not_count && 'yes' !== $minmax_cart_exclude ) {
						$total_cost += $price_fn(
							$product,
							array(
								'price' => (float) $product->get_price(),
								'qty'   => (float) $values['quantity'],
							)
						);
					}
				}

				// Check cart items.
				foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
					$checking_id    = $this->get_id_to_check( $values );
					$terms          = get_the_terms( $values['product_id'], 'product_cat' );
					$found_term_ids = array();

					// If a product belongs to multiple categories with different 'Group of' values, find the category with the smallest 'Group of' value.
					$min_group_of_category_id    = 0;
					$min_group_of_category_value = 0;

					$checking_product = wc_get_product( $checking_id );
					$parent_product   = $values['product_id'] === $checking_id ? $checking_product : wc_get_product( $values['product_id'] );

					if ( $terms ) {

						foreach ( $terms as $term ) {

							if ( in_array( $term->term_id, $found_term_ids, true ) ) {
								continue;
							}

							if ( ( $checking_product && 'yes' === $checking_product->get_meta( 'variation_minmax_category_group_of_exclude', true ) ) || ( $parent_product && 'yes' === $parent_product->get_meta( 'minmax_category_group_of_exclude', true ) ) ) {
								continue;
							}

							if ( ( ! $checking_product || ! empty( $checking_product->get_meta( 'variation_group_of_quantity', true ) ) ) || ( ! $parent_product || ! empty( $parent_product->get_meta( 'group_of_quantity', true ) ) ) ) {
								continue;
							}

							$found_term_ids[]        = $term->term_id;
							$category_group_of_value = absint( get_term_meta( $term->term_id, 'group_of_quantity', true ) );

							if ( 0 !== $category_group_of_value && ( 0 === $min_group_of_category_value || $category_group_of_value < $min_group_of_category_value ) ) {
								$min_group_of_category_value = $category_group_of_value;
								$min_group_of_category_id    = $term->term_id;
							}

							// Record count in parents of this category too.
							$parents = get_ancestors( $term->term_id, 'product_cat' );

							foreach ( $parents as $parent ) {
								if ( in_array( $parent, $found_term_ids, true ) ) {
									continue;
								}

								$found_term_ids[]        = $parent;
								$category_group_of_value = absint( get_term_meta( $parent, 'group_of_quantity', true ) );

								if ( 0 !== $category_group_of_value && ( 0 === $min_group_of_category_value || $category_group_of_value < $min_group_of_category_value ) ) {
									$min_group_of_category_value = $category_group_of_value;
									$min_group_of_category_id    = $parent;
								}
							}
						}
					}

					if ( 0 !== $min_group_of_category_id && 0 !== $min_group_of_category_value ) {
						$category_quantities[ $min_group_of_category_id ] = isset( $category_quantities[ $min_group_of_category_id ] )
							? $category_quantities[ $min_group_of_category_id ] + $values['quantity']
							: $values['quantity'];
					}

					// Check item rules once per product ID.
					if ( in_array( $checking_id, $checked_ids, true ) ) {
						continue;
					}

					$product = $values['data'];

					$minmax_do_not_count = 'no';
					if ( $checking_product && 'yes' === $checking_product->get_meta( 'variation_minmax_do_not_count', true ) ) {
						$minmax_do_not_count = 'yes';
					} elseif ( $parent_product && 'yes' === $parent_product->get_meta( 'minmax_do_not_count', true ) ) {
						$minmax_do_not_count = 'yes';
					}

					/**
					 * Use this filter to prevent the quantity or cost of a cart item from being counted in cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $do_not_count
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_do_not_count = apply_filters( 'wc_min_max_quantity_minmax_do_not_count', $minmax_do_not_count, $checking_id, $cart_item_key, $values );

					$minmax_cart_exclude = 'no';
					if ( $checking_product && 'yes' === $checking_product->get_meta( 'variation_minmax_cart_exclude', true ) ) {
						$minmax_cart_exclude = 'yes';
					} elseif ( $parent_product && 'yes' === $parent_product->get_meta( 'minmax_cart_exclude', true ) ) {
						$minmax_cart_exclude = 'yes';
					}

					/**
					 * Use this filter to exclude a product from cart-level rules.
					 *
					 * @since 2.3.6
					 *
					 * @param  boolean $exclude
					 * @param  int     $checking_id
					 * @param  string  $cart_item_key
					 * @param  array   $values
					 */
					$minmax_cart_exclude = apply_filters( 'wc_min_max_quantity_minmax_cart_exclude', $minmax_cart_exclude, $checking_id, $cart_item_key, $values );

					if ( 'yes' === $minmax_do_not_count || 'yes' === $minmax_cart_exclude ) {
						// Do not count.
						$this->excludes[] = $product->get_name();

					} else {
						$total_quantity += $product_quantities[ $checking_id ];
					}

					if ( 'yes' !== $minmax_cart_exclude ) {
						$apply_cart_rules = true;
					}

					$checked_ids[] = $checking_id;

					if ( $values['variation_id'] ) {
						$variation         = wc_get_product( $values['variation_id'] );
						$min_max_rules     = $variation ? 'yes' === $variation->get_meta( 'min_max_rules', true ) : false;
						$allow_combination = $parent_product ? 'yes' === $parent_product->get_meta( 'allow_combination', true ) : false;

						[
							WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
							WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $maximum_quantity,
							WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
						] = $this->get_quantity_rules_for_product( $values['variation_id'] );

						$filter_id = $min_max_rules && ! $allow_combination ? $values['variation_id'] : $values['product_id'];
					} else { // Not a variable product.
						[
							WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
							WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $maximum_quantity,
							WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
						] = $this->get_quantity_rules_for_product( $checking_id );

						$filter_id = $checking_id;
					}

					/**
					 * Use this filter to filter the Minimum Quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $product_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$minimum_quantity = absint( apply_filters( 'wc_min_max_quantity_minimum_allowed_quantity', $minimum_quantity, $filter_id, $cart_item_key, $values ) );

					/**
					 * Use this filter to filter the Maximum Quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $product_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$maximum_quantity = absint( apply_filters( 'wc_min_max_quantity_maximum_allowed_quantity', $maximum_quantity, $filter_id, $cart_item_key, $values ) );

					/**
					 * Use this filter to filter the Group of quantity of a product/variation.
					 *
					 * @since 2.2.7
					 *
					 * @param  string  $quantity
					 * @param  int     $product_id
					 * @param  string  $cart_item_key
					 * @param  array   $cart_item
					 */
					$group_of_quantity = absint( apply_filters( 'wc_min_max_quantity_group_of_quantity', $group_of_quantity, $filter_id, $cart_item_key, $values ) );

					$this->check_rules( $product, $product_quantities[ $checking_id ], $minimum_quantity, $maximum_quantity, $group_of_quantity, $checking_id );
				}

				// Cart rules.
				if ( $apply_cart_rules ) {

					$excludes = '';

					if ( count( $this->excludes ) > 0 ) {
						$excludes = ' (' . __( 'excludes ', 'woocommerce-min-max-quantities' ) . implode( ', ', $this->excludes ) . ')';
					}

					if ( $this->minimum_order_quantity > 0 && $total_quantity < $this->minimum_order_quantity ) {
						/* translators: %d: Minimum amount of items in the cart */
						$notice = sprintf( __( 'To place an order, your cart must contain at least %d items.', 'woocommerce-min-max-quantities' ), $this->minimum_order_quantity ) . $excludes;
						throw new Exception( $notice );
					}

					if ( $this->maximum_order_quantity > 0 && $total_quantity > $this->maximum_order_quantity ) {
						/* translators: %d: Maximum amount of items in the cart */
						$notice = sprintf( __( 'Your cart must not contain more than %d items to place an order.', 'woocommerce-min-max-quantities' ), $this->maximum_order_quantity );

						throw new Exception( $notice );

					}

					// Check cart value.
					if ( $this->minimum_order_value && $total_cost < $this->minimum_order_value ) {
						/* translators: %s: Minimum order value */
						$notice = sprintf( __( 'To place an order, your cart total must be at least %s.', 'woocommerce-min-max-quantities' ), wc_price( $this->minimum_order_value ) ) . $excludes;

						throw new Exception( $notice );
					}

					if ( $this->maximum_order_value && $total_cost > $this->maximum_order_value ) {
						/* translators: %s: Maximum order value */
						$notice = sprintf( __( 'Your cart total must not be higher than %s to place an order.', 'woocommerce-min-max-quantities' ), wc_price( $this->maximum_order_value ) );

						throw new Exception( $notice );
					}
				}

				// Check category rules.
				foreach ( $category_quantities as $category => $quantity ) {

					$group_of_quantity = get_term_meta( $category, 'group_of_quantity', true );

					if ( $group_of_quantity > 0 && ( intval( $quantity ) % intval( $group_of_quantity ) > 0 ) ) {

						$term          = get_term_by( 'id', $category, 'product_cat' );
						$product_names = array();

						foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {

							// If exclude is enable, skip.
							if ( ( $parent_product && 'yes' === $parent_product->get_meta( 'minmax_category_group_of_exclude', true ) ) || ( $variation && 'yes' === $variation->get_meta( 'variation_minmax_category_group_of_exclude', true ) ) ) {
								continue;
							}

							if ( has_term( $category, 'product_cat', $values['product_id'] ) ) {
								$product_names[] = $values['data']->get_title();
							}
						}

						if ( $product_names ) {
							/* translators: %1$s: Category name, %2$s: Comma separated list of product names, %3$d: Group amount */
							$notice = sprintf( __( 'Products in the <strong>%1$s</strong> category (<em>%2$s</em>) must be bought in multiples of %3$d.', 'woocommerce-min-max-quantities' ), $term->name, implode( ', ', $product_names ), $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );

							throw new Exception( $notice );
						}
					}
				}
			} catch ( Exception $e ) {

				if ( WC_MMQ_Core_Compatibility::is_store_api_request() ) {
					throw $e;

				} else {

					$notice = $e->getMessage();

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}
				}
			}
		}

		/**
		 * If the minimum allowed quantity for purchase is lower then the current stock, we need to
		 * let the user know that they are on backorder, or out of stock.
		 *
		 * @param array      $args    List of arguments.
		 * @param WC_Product $product Product object.
		 */
		public function maybe_show_backorder_message( $args, $product ) {
			if ( ! $product->managing_stock() ) {
				return $args;
			}

			// Figure out what our minimum_quantity is.
			[
				WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
			] = $this->get_quantity_rules_for_product( $product );

			$parent_product = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : null;
			// If the product is a variation and the parent allows combination, we need to get the sum of stock for all variations.
			if ( $parent_product && 'yes' === $parent_product->get_meta( 'allow_combination', true ) ) {
				$stock_quantity = 0;
				$parent         = wc_get_product( $product->get_parent_id() );
				$variations     = $parent->get_available_variations( 'objects' );
				foreach ( $variations as $variation ) {
					// if there is a variation in stock without stock being managed, we can't know the real stock quantity.
					if ( ! $variation->get_manage_stock() && 'instock' === $variation->get_stock_status() ) {
						return $args;
					}
					$stock_quantity += $variation->get_stock_quantity();
				}
			} else {
				$stock_quantity = $product->get_stock_quantity();
			}

			// If the minimum quantity allowed for purchase is smaller then the amount in stock, we need clearer messaging.
			if ( $minimum_quantity > 0 && $stock_quantity < $minimum_quantity ) {
				if ( $product->backorders_allowed() ) {
					return array(
						'availability' => __( 'Available on backorder', 'woocommerce-min-max-quantities' ),
						'class'        => 'available-on-backorder',
					);
				} else {
					return array(
						'availability' => __( 'Out of stock', 'woocommerce-min-max-quantities' ),
						'class'        => 'out-of-stock',
					);
				}
			}

			return $args;
		}

		/**
		 * Add respective error message depending on rules checked.
		 *
		 * @throws Exception When quantity rules are violated.
		 *
		 * @param WC_Product $product           Product object.
		 * @param int        $quantity          Quantity to check.
		 * @param int        $minimum_quantity  Minimum quantity.
		 * @param int        $maximum_quantity  Maximum quanitty.
		 * @param int        $group_of_quantity Group quantity.
		 * @param int|null   $checking_id       Variation ID.
		 * @return void
		 */
		public function check_rules( $product, $quantity, $minimum_quantity, $maximum_quantity, $group_of_quantity, $checking_id = null ) {

			if ( $product->is_sold_individually() ) {
				return;
			}

			try {

				$parent_id         = $product->is_type( 'variation' ) ? $product->get_parent_id() : $product->get_id();
				$variation_title   = $checking_id ? get_the_title( $checking_id ) : $product->get_title();
				$parent            = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : $product;
				$allow_combination = $parent && 'yes' === $parent->get_meta( 'allow_combination', true );

				if ( $allow_combination ) {
					$parent_product = wc_get_product( $parent_id );

					if ( ! is_a( $parent_product, 'WC_Product' ) ) {
						return;
					}

					$variation_title = $parent_product->get_title();
				}

				if ( $minimum_quantity > 0 && $quantity < $minimum_quantity ) {

					if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) || $product->is_type( 'variable_subscription' ) ) ) {

						/* translators: %1$s: Product name, %2$s: Minimum order quantity, %3$s: Total cart quantity */
						$notice = sprintf( __( 'To place an order, the quantity of "%1$s" must be at least %2$s. You currently have %3$s in your cart.', 'woocommerce-min-max-quantities' ), $variation_title, $minimum_quantity, $quantity );

						throw new Exception( $notice );

					} else {

						if ( WC_MMQ_Core_Compatibility::is_store_api_request( 'cart' ) ) {
							/* translators: %1$s: Product name, %2$s: Minimum order quantity */
							$notice = sprintf( __( 'The quantity of "%1$s" has been increased to %2$s. This is the minimum required quantity.', 'woocommerce-min-max-quantities' ), $variation_title, $minimum_quantity );
						} else {
							/* translators: %1$s: Product name, %2$s: Minimum order quantity */
							$notice = sprintf( __( 'To place an order, the quantity of "%1$s" must be at least %2$s.', 'woocommerce-min-max-quantities' ), $variation_title, $minimum_quantity );
						}

						throw new Exception( $notice );
					}
				} elseif ( $maximum_quantity > 0 && $quantity > $maximum_quantity ) {
					if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) || $product->is_type( 'variable_subscription' ) ) ) {

						/* translators: %1$s: Product name, %2$s: Maximum order quantity, %3$s: Total cart quantity */
						$notice = sprintf( __( 'The quantity of "%1$s" cannot be higher than %2$s to place an order. You currently have %3$s in your cart.', 'woocommerce-min-max-quantities' ), $variation_title, $maximum_quantity, $quantity );

						throw new Exception( $notice );

					} else {

						if ( WC_MMQ_Core_Compatibility::is_store_api_request( 'cart' ) ) {
							/* translators: %1$s: Product name, %2$s: Maximum order quantity */
							$notice = sprintf( __( 'The quantity of "%1$s" has been decreased to %2$s. This is the maximum allowed quantity.', 'woocommerce-min-max-quantities' ), $variation_title, $maximum_quantity );
						} else {
							/* translators: %1$s: Product name, %2$s: Maximum order quantity */
							$notice = sprintf( __( 'The quantity of "%1$s" cannot be higher than %2$s to place an order.', 'woocommerce-min-max-quantities' ), $variation_title, $maximum_quantity );
						}

						throw new Exception( $notice );
					}
				}

				if ( $group_of_quantity > 0 && ( intval( $quantity ) % intval( $group_of_quantity ) > 0 ) ) {

					if ( $allow_combination && ( $product->is_type( 'variation' ) || $product->is_type( 'variable' ) || $product->is_type( 'variable_subscription' ) ) ) {

						/* translators: %1$s: Product name, %2$d: Group amount */
						$notice = sprintf( __( '"%1$s" must be bought in multiples of %2$d. Please adjust its quantity to continue.', 'woocommerce-min-max-quantities' ), $variation_title, $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );

						throw new Exception( $notice );

					} elseif ( WC_MMQ_Core_Compatibility::is_store_api_request( 'cart' ) ) {

							/* translators: %1$s: Product name, %2$d: Group amount */
							$notice = sprintf( __( 'The quantity of "%1$s" has been adjusted. "%1$s" must be bought in multiples of %2$d.', 'woocommerce-min-max-quantities' ), $variation_title, $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );
					} else {
						/* translators: %1$s: Product name, %2$d: Group amount */
						$notice = sprintf( __( '"%1$s" must be bought in multiples of %2$d. Please adjust its quantity to continue.', 'woocommerce-min-max-quantities' ), $variation_title, $group_of_quantity, $group_of_quantity - ( $quantity % $group_of_quantity ) );
					}

					throw new Exception( $notice );
				}
			} catch ( Exception $e ) {

				if ( WC_MMQ_Core_Compatibility::is_store_api_request() ) {

					throw $e;

				} else {

					$notice = $e->getMessage();

					if ( $notice ) {
						wc_add_notice( $notice, 'error' );
					}
				}
			}
		}

		/**
		 * Add to cart validation
		 *
		 * @param  mixed $pass         Filter value.
		 * @param  mixed $product_id   Product ID.
		 * @param  mixed $quantity     Quantity.
		 * @param  int   $variation_id Variation ID (default none).
		 * @return mixed
		 */
		public function add_to_cart( $pass, $product_id, $quantity, $variation_id = 0 ) {

			$_product = wc_get_product( $variation_id ? $variation_id : $product_id );

			if ( ! is_a( $_product, 'WC_Product' ) ) {
				return $pass;
			}

			if ( $_product->is_sold_individually() ) {
				return $pass;
			}

			// Handles an edge case where the product is a variation, but the $variation_id is not set.
			if ( empty( $variation_id ) && $_product->is_type( 'variation' ) ) {
				$variation_id = $product_id;
				$product_id   = $_product->get_parent_id();
			}

			$parent_product = wc_get_product( $product_id );

			[
				WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $maximum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
			] = $this->get_quantity_rules_for_product( $_product );

			$allow_combination = $parent_product && 'yes' === $parent_product->get_meta( 'allow_combination', true );

			// Validate if the selected product/variation quantity satisfies the Minimum/Maximum/Group of quantity restrictions.
			if ( 0 !== $group_of_quantity && ! $allow_combination ) {

				if ( $quantity % $group_of_quantity ) {

					/* translators: %1$s: Product name, %2$d: Group of quantity */
					$message = sprintf( __( '"%1$s" can only be bought in multiples of %2$d.', 'woocommerce-min-max-quantities' ), $_product->get_name(), $group_of_quantity );
					$this->add_error( $message );
					return false;
				}

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If an invalid Minimum/Maximum Quantity has been saved in the database, adjust it and validate add-to-cart quantity based on the adjusted, valid value.
				 */
				$minimum_quantity = self::adjust_min_quantity( $minimum_quantity, $group_of_quantity );
				$maximum_quantity = self::adjust_max_quantity( $maximum_quantity, $group_of_quantity, $minimum_quantity );

			}

			// Check if the add-to-cart quantity is greater than the Minimum Quantity.
			if ( 0 !== $minimum_quantity && ! $allow_combination ) {

				if ( $quantity < $minimum_quantity ) {

					/* translators: %1$s: Product name, %2$d: Minimum Quantity */
					$message = sprintf( __( 'The minimum required quantity for "%1$s" is %2$d.', 'woocommerce-min-max-quantities' ), $_product->get_name(), $minimum_quantity );
					$this->add_error( $message );
					return false;
				}
			}

			// Check if the add-to-cart quantity is less than the Maximum Quantity.
			if ( 0 !== $maximum_quantity && ! $allow_combination ) {

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If Maximum Quantity is less than Minimum, set Maximum Quantity equal to Minimum value.
				 */
				if ( 0 !== $minimum_quantity ) {
					if ( $minimum_quantity > $maximum_quantity ) {
						$maximum_quantity = $minimum_quantity;
					}
				}

				if ( $quantity > $maximum_quantity ) {

					/* translators: %1$s: Product name, %2$d: Maximum quantity */
					$message = sprintf( __( 'The maximum allowed quantity for "%1$s" is %2$d.', 'woocommerce-min-max-quantities' ), $_product->get_name(), $maximum_quantity );
					$this->add_error( $message );
					return false;
				}
			}

			return $pass;
		}

		/**
		 * Updates the quantity arguments.
		 *
		 * @param array      $data    List of data to update.
		 * @param WC_Product $product Product object.
		 * @return array
		 */
		public function update_quantity_args( $data, $product ) {
			// Multiple shipping address product plugin compat
			// don't update the quantity args when on set multiple address page.
			if ( is_a( $this->addons, 'WC_Min_Max_Quantities_Addons' ) && $this->addons->is_multiple_shipping_address_page() ) {
				return $data;
			}

			if ( $product->is_sold_individually() ) {
				return $data;
			}

			[
				WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $maximum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
			] = $this->get_quantity_rules_for_product( $product );

			$parent_product    = $product->is_type( 'variation' ) ? wc_get_product( $product->get_parent_id() ) : $product;
			$allow_combination = $parent_product && 'yes' === $parent_product->get_meta( 'allow_combination', true );

			/*
			* If it's a variable product and allow combination is enabled,
			* we don't need to set the quantity to default minimum.
			*/
			if ( $allow_combination && $product->is_type( 'variation' ) ) {
				$data['max_value'] = $maximum_quantity;
				return $data;
			}

			// If variable product, only apply in cart.
			$variation_id = $product->get_id();

			if ( 0 !== $group_of_quantity ) {
				$data['step'] = $group_of_quantity;

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If an invalid Minimum/Maximum Quantity has been saved in the database, adjust it and validate add-to-cart quantity based on the adjusted, valid value.
				 */
				if ( 0 !== $minimum_quantity ) {
					$adjusted_min_quantity = self::adjust_min_quantity( $minimum_quantity, $data['step'] );

					if ( $adjusted_min_quantity !== $minimum_quantity ) {
						$minimum_quantity = $adjusted_min_quantity;
					}
				}

				if ( 0 !== $maximum_quantity ) {
					$adjusted_max_quantity = self::adjust_max_quantity( $maximum_quantity, $data['step'], $minimum_quantity );

					if ( $adjusted_max_quantity !== $maximum_quantity ) {
						$maximum_quantity = $adjusted_max_quantity;
					}
				}

				/**
				 * Check if we should use the group of setting as our minimum.
				 *
				 * @since 2.4.22
				 * @param boolean    $use_group Whether we should use the group of setting.
				 * @param WC_Product $product   Product object.
				 * @param array      $data      Available product data.
				 */
				if ( ( ! isset( $minimum_quantity ) || 0 === $minimum_quantity ) && apply_filters( 'wc_min_max_use_group_as_min_quantity', true, $product, $data ) ) {
					$data['min_value'] = $group_of_quantity;
				}
			}

			if ( isset( $minimum_quantity ) && 0 !== $minimum_quantity ) {

				if ( $product->managing_stock() && ! $product->backorders_allowed() && absint( $minimum_quantity ) > $product->get_stock_quantity() ) {
					$data['min_value'] = $product->get_stock_quantity();

				} else {
					$data['min_value'] = $minimum_quantity;
				}
			}

			if ( $maximum_quantity ) {

				if ( $product->managing_stock() && $product->backorders_allowed() ) {
					$data['max_value'] = $maximum_quantity;

				} elseif ( $product->managing_stock() && absint( $maximum_quantity ) > $product->get_stock_quantity() ) {
					$data['max_value'] = $product->get_stock_quantity();

				} else {
					$data['max_value'] = $maximum_quantity;
				}
			}

			// Don't apply for cart or checkout as cart/checkout form has qty already pre-filled.
			if ( ! is_cart() && ! is_checkout() ) {
				// If we have a group of quantity and no minimum then set the quantity to the group of quantity.
				if ( ! empty( $minimum_quantity ) ) {
					$data['input_value'] = $minimum_quantity;
					/**
					 * Check if the Group of setting should be used as the input value.
					 *
					 * @since 5.2.2
					 *
					 * @param boolean $use_group Whether the Group of setting should be used as the input value
					 * @param WC_Product $product   Product object.
					 * @param array      $data      Available product data.
					 */
				} elseif ( ! empty( $group_of_quantity ) && apply_filters( 'wc_min_max_use_group_as_min_quantity', true, $product, $data ) ) {
					$data['input_value'] = $group_of_quantity;
				}
			}

			return $data;
		}

		/**
		 * If on a grouped product page, don't use Group as for our minimum.
		 *
		 * @param boolean $use_group Whether to use group quantity as minimum. Default true.
		 * @param object  $product   Product object.
		 * @param array   $data      Available product data.
		 * @return boolean
		 */
		public function use_group_as_min_quantity( $use_group, $product, $data ) {
			$parent_product = wc_get_product( get_queried_object_id() );

			if (
				$parent_product
				&& $parent_product->get_id() !== $product->get_id()
				&& 'grouped' === $parent_product->get_type()
			) {
				return false;
			}

			return $use_group;
		}

		/**
		 * Adds variation min max settings to the localized variation parameters to be used by JS.
		 *
		 * @param array  $data      Available variation data.
		 * @param object $product   Product object.
		 * @param object $variation Variation object.
		 * @return array $data
		 */
		public function available_variation( $data, $product, $variation ) {

			if ( $product->is_sold_individually() ) {
				return $data;
			}

			$allow_combination = 'yes' === $product->get_meta( 'allow_combination', true );

			// Override product level.
			if ( $variation->managing_stock() ) {
				$product = $variation;
			}

			[
				WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $maximum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
			] = $this->get_quantity_rules_for_product( $variation );

			if ( 0 !== $group_of_quantity ) {
				$data['step'] = $group_of_quantity;

				/*
				 * Backwards compatibility for versions earlier than v3.
				 *
				 * If an invalid Minimum/Maximum Quantity has been saved in the database, adjust it and validate add-to-cart quantity based on the adjusted, valid value.
				 */
				if ( 0 !== $minimum_quantity ) {
					$adjusted_min_quantity = self::adjust_min_quantity( $minimum_quantity, $data['step'] );

					if ( $adjusted_min_quantity !== $minimum_quantity ) {
						$minimum_quantity = $adjusted_min_quantity;
					}
				}

				if ( 0 !== $maximum_quantity ) {
					$adjusted_max_quantity = self::adjust_max_quantity( $maximum_quantity, $data['step'], $minimum_quantity );

					if ( $adjusted_max_quantity !== $maximum_quantity ) {
						$maximum_quantity = $adjusted_max_quantity;
					}
				}
			}

			if ( $minimum_quantity ) {

				if ( $product->managing_stock() && ! $product->backorders_allowed() && absint( $minimum_quantity ) > $product->get_stock_quantity() ) {
					$data['min_qty'] = $product->get_stock_quantity();

				} else {
					$data['min_qty'] = $minimum_quantity;
				}
			}

			if ( $maximum_quantity ) {

				if ( $product->managing_stock() && $product->backorders_allowed() ) {
					$data['max_qty'] = $maximum_quantity;

				} elseif ( $product->managing_stock() && absint( $maximum_quantity ) > $product->get_stock_quantity() ) {
					$data['max_qty'] = $product->get_stock_quantity();

				} else {
					$data['max_qty'] = $maximum_quantity;
				}
			}

			// Don't apply for cart as cart has qty already pre-filled.
			if ( ! is_cart() ) {
				if ( ! $minimum_quantity && $group_of_quantity ) {
					$data['input_value'] = $group_of_quantity;
				} else {
					$data['input_value'] = ! empty( $minimum_quantity ) ? $minimum_quantity : 1;
				}

				if ( $allow_combination ) {
					$data['input_value'] = 1;
					$data['min_qty']     = 1;
					$data['step']        = 1;
				}
			}

			return $data;
		}

		/**
		 * Get group_of_quantity setting for a product.
		 *
		 * @param WC_Product $product Product object.
		 *
		 * Doesn't handle variations on variable products.
		 *
		 * @return int
		 */
		public function get_group_of_quantity_for_product( $product ) {
			return ( new WC_Min_Max_Quantities_Quantity_Rules( $product ) )->get()[ WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF ];
		}

		/**
		 * Get group_of_quantity setting for a product.
		 *
		 * @param WC_Product|int $product Product object.
		 *
		 * Doesn't handle variations on variable products.
		 *
		 * @return array<string, int>
		 */
		public function get_quantity_rules_for_product( $product ): array {
			return ( new WC_Min_Max_Quantities_Quantity_Rules( $product ) )->get();
		}

		/**
		 * Modify quantity for add to cart action inside loop to respect minimum rules.
		 *
		 * @param int $product_id Product ID.
		 *
		 * @return int
		 */
		public function modify_add_to_cart_quantity( $product_id ) {
			if ( ! isset( $_GET['add-to-cart'] ) || ! is_numeric( wp_unslash( $_GET['add-to-cart'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return $product_id;
			}

			if ( ! empty( $_REQUEST['quantity'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
				return $product_id;
			}

			$product = wc_get_product( $product_id );

			if ( ! is_a( $product, 'WC_Product' ) || 'variable' === $product->get_type() ) {
				return $product_id;
			}

			$quantity = 0;

			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( intval( $product->get_id() ) === intval( $cart_item['product_id'] ) ) {
					$quantity = $cart_item['quantity'];
					break; // stop the loop if product is found.
				}
			}

			[
				WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $minimum_quantity,
				WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_quantity
			] = $this->get_quantity_rules_for_product( $product );

			if ( $quantity < $minimum_quantity ) {
				$_REQUEST['quantity'] = $minimum_quantity - $quantity;
				return $product_id;
			}

			if ( $group_of_quantity ) {
				if ( $group_of_quantity > $quantity ) {
					$_REQUEST['quantity'] = $group_of_quantity - $quantity;
					return $product_id;
				}

				$remainder = $quantity % $group_of_quantity;

				if ( 0 === $remainder ) {
					$_REQUEST['quantity'] = $group_of_quantity;
				} else {
					$_REQUEST['quantity'] = $group_of_quantity - $remainder;
				}
				return $product_id;
			}

			return $product_id;
		}

		/**
		 * Filter Minimum Quantity based on "Group of" option on runtime.
		 *
		 * @param  int $min_quantity      Minimum quantity value.
		 * @param  int $group_of_quantity Group of quantity value.
		 *
		 * @return int
		 */
		public static function adjust_min_quantity( $min_quantity, $group_of_quantity ) {

			// Zero min quantity is always allowed.
			if ( ! $min_quantity || ! $group_of_quantity ) {
				return $min_quantity;
			}

			if ( $min_quantity < $group_of_quantity ) {

				// If Group of = 2 and Minimum Quantity = 1, set Minimum Quantity to 2.
				$min_quantity = $group_of_quantity;

			} elseif ( $min_quantity > $group_of_quantity ) {
				$remainder = $min_quantity / $group_of_quantity;

				// If Group of = 2 and Minimum Quantity = 5, set Minimum Quantity to 2 * ceil( 5/2 ) = 6.
				// If Group of = 4 and Minimum Quantity = 5, set Minimum Quantity to 4 * ceil( 5/4 ) = 8.
				if ( $remainder ) {
					$min_quantity = $group_of_quantity * ceil( $remainder );
				}
			}

			return absint( $min_quantity );
		}

		/**
		 * Filter Maximum Quantity based on "Group of" option on runtime.
		 *
		 * @param  int $max_quantity      Maximum quantity value.
		 * @param  int $group_of_quantity Group of quantity value.
		 * @param  int $min_quantity      Minimum quantity value.
		 *
		 * @return int
		 */
		public static function adjust_max_quantity( $max_quantity, $group_of_quantity, $min_quantity = 0 ) {

			// Return early for infinite max quantities.
			if ( empty( $max_quantity ) ) {
				return $max_quantity;
			}

			// If the Minimum Quantity is greater than the Maximum, then set the Maximum equal to the Minimum.
			if ( ! empty( $min_quantity ) && $min_quantity > $max_quantity ) {
				$max_quantity = $min_quantity;
			}

			// If the Group of Quantity is 0, skip quantity adjustments based on the step.
			if ( empty( $group_of_quantity ) ) {
				return $max_quantity;
			}

			if ( $max_quantity > $group_of_quantity ) {
				$remainder = $max_quantity / $group_of_quantity;

				// If Group of = 4 and Maximum Quantity = 5, set Maximum Quantity to 4 * floor( 5/4 ) = 4.
				// If Group of = 4 and Maximum Quantity = 9, set Maximum Quantity to 4 * floor( 9/4 ) = 8.
				if ( $remainder ) {
					$max_quantity = $group_of_quantity * floor( $remainder );
				}
			} elseif ( $max_quantity < $group_of_quantity ) {
				// If Group of = 2 and Maximum Quantity = 1, set Maximum Quantity to 2.
				$max_quantity = $group_of_quantity;
			}

			return absint( $max_quantity );
		}

		/**
		 * Check if the product always conforms to MMQ rules.
		 *
		 * That is, there are either no rules set, or the min/max rules are set and equal.
		 *
		 * @param WC_Product $product Product object.
		 *
		 * @return bool
		 */
		private function can_skip_product_rules_validation( $product ) {

			if ( ! $product ) {
				return false;
			}

			[
				WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $product_min_qty,
				WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $product_max_qty,
				WC_Min_Max_Quantities_Quantity_Rules::GROUP_OF => $group_of_qty
			] = $this->get_quantity_rules_for_product( $product );

			// If no min/max rules are set, there's nothing to check -> can skip validation.
			if ( 1 >= $product_min_qty && 0 === $product_max_qty && 1 >= $group_of_qty ) {
				return true;
			}

			// If min and max are set and equal, the product has qty locked -> can skip validation.
			if ( 0 < $product_min_qty && 0 < $product_max_qty && $product_min_qty === $product_max_qty ) {
				return true;
			}

			return false;
		}

		/**
		 * Check if the variable product supports express checkout.
		 *
		 * Simplified version that is a bit lighter on resources: If there are any variations with min/max rules enabled, return false.
		 * Variations can have different prices, too. But that's handled outside of this plugin.
		 *
		 * @param WC_Product $product Product object.
		 *
		 * @return bool
		 */
		private function variable_product_supports_express_checkout( $product ) {

			if ( ! $product ) {
				return false;
			}

			if ( ! in_array( $product->get_type(), array( 'variable', 'variable-subscription' ), true ) ) {
				// This is counterintuitive, but if a non-variable product is passed,
				// we shouldn't forbid the express checkout button here.
				return true;
			}

			$combine_variations = $product->get_meta( 'allow_combination', true );

			if ( 'yes' === $combine_variations ) {
				/*
				If Combine variations is set to true, there are no rules on the variations.
					But if min == max on the variable product with combined variations, qty is not set automatically.
					=> Can't show express checkout.
				*/
				[
					WC_Min_Max_Quantities_Quantity_Rules::MINIMUM => $product_min_qty,
					WC_Min_Max_Quantities_Quantity_Rules::MAXIMUM => $product_max_qty,
				] = $this->get_quantity_rules_for_product( $product );

				if ( 1 < $product_min_qty && 0 < $product_max_qty && $product_min_qty === $product_max_qty ) {

					return false;
				}
			} else {
				/*
				If Combine variations is set to false, there might be min/max rules on the variations.
					=> Check if any variation has them enabled.
				*/
				$variations = $product->get_children();
				foreach ( $variations as $variation_id ) {
					$variation = wc_get_product( $variation_id );

					if ( ! is_a( $variation, 'WC_Product' ) ) {
						continue;
					}

					if ( 'yes' === $variation->get_meta( 'min_max_rules', true ) ) {
						return false;
					}
				}
			}

			return true;
		}

		/**
		 * Check if global rules apply to a product.
		 *
		 * That is, if there are any global rules set, and the product is not excluded from them.
		 *
		 * Exclusion from group of rules applied through categories is done in can_skip_product_rules_validation(),
		 * as get_group_of_quantity_for_product returns 0 if the rule is set, but the product is excluded.
		 *
		 * @since 4.3.2
		 * @version 4.3.2
		 *
		 * @param WC_Product $product Product object.
		 *
		 * @return bool
		 */
		private function global_rules_apply_to_product( $product ) {

			if ( ! $product ) {
				return false;
			}

			// If no global rules are set, they don't apply to the product.
			$min_quantity = $this->minimum_order_quantity;
			$max_quantity = $this->maximum_order_quantity;
			$min_value    = $this->minimum_order_value;
			$max_value    = $this->maximum_order_value;

			$epsilon = 0.0001;

			if ( 1 >= $min_quantity && 0 === $max_quantity && $epsilon > abs( $min_value ) && $epsilon > abs( $max_value ) ) {
				return false;
			}

			// If product is excluded from order min/max quantity/value and category rules, they don't apply.
			$mmq_exclude = $product->get_meta( 'minmax_cart_exclude' );

			// If there are any global qty/value rules set and this product is excluded from them, they don't apply.
			if ( 'yes' === $mmq_exclude && ( 1 < $min_quantity || 0 < $max_quantity || 0.0 < $min_value || 0.0 < $max_value ) ) {
				return false;
			}

			// Otherwise, rules apply.
			return true;
		}

		/**
		 * Check if the express checkout button(s) can be displayed on single product page.
		 *
		 * The only thing we can do from PHP is to hide the smart buttons if the min/max quantity or value is set.
		 * The end customer can increase the quantity and click the express checkout button and only at that point
		 * we can check if the quantity/value is within the allowed range.
		 * Since we can't validate this in PHP, we hide the express checkout button.
		 *
		 * @since 4.3.2
		 * @version 4.3.2
		 *
		 * @param WC_Product $product Product object.
		 *
		 * @return bool
		 */
		public function can_display_express_checkout( $product ) {
			/*
			If global MMQ rules apply to the product,
				we can't verify the quantity/value in PHP, so we can't display the express checkout button.
			*/
			if ( $this->global_rules_apply_to_product( $product ) ) {
				return false;
			}

			/*
			If the product has rules that need to be validated,
				(and we can't verify the quantity in PHP), we can't display the express checkout button.
			*/
			if ( ! $this->can_skip_product_rules_validation( $product ) ) {
				return false;
			}

			/*
			If the product is a variable product and there are variations with min/max rules,
				we can't verify the quantity in PHP, so we can't display the express checkout button.

				We can test for variable products after the test for simple products, because all the cases
				that are handled incorrectly by product_always_conforms_to_rules() allow this check to run:
				- if there are no rules on variations, product_always_conforms_to_rules is almost correct for variable products, too.
				- if there are no rules on variable product, but there are rules on variation
					-> product_always_conforms_to_rules will return true, but this will be corrected to false here.
				- if min == max on the variable product, but there are rules on the variation
					-> product_always_conforms_to_rules will return true, but this will be corrected to false here.
				- if min == max on the variable product and combine variations is true, qty is not forced,
					-> product_always_conforms_to_rules will return true, but this will be corrected to false here.
			*/
			if ( ! $this->variable_product_supports_express_checkout( $product ) ) {
				return false;
			}

			// Otherwise, we allow the express checkout button to be displayed.
			return true;
		}
	}

	add_action( 'plugins_loaded', array( 'WC_Min_Max_Quantities', 'get_instance' ) );
	// Subscribe to automated translations.
	add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );

	register_activation_hook( __FILE__, array( 'WC_Min_Max_Quantities', 'on_activation' ) );
	register_deactivation_hook( __FILE__, array( 'WC_Min_Max_Quantities', 'on_deactivation' ) );

endif;
