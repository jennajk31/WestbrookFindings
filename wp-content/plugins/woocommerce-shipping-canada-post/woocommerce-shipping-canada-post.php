<?php
/**
 * Plugin Name: WooCommerce Canada Post Shipping
 * Plugin URI: https://woocommerce.com/products/canada-post-shipping-method/
 * Description: Obtain shipping rates dynamically via the Canada Post API for your orders.
 * Version: 3.1.1
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Text Domain: woocommerce-shipping-canada-post
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.7
 * WC requires at least: 9.6
 * WC tested up to: 9.8
 *
 * Copyright: © 2025 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 18623:ac029cdf3daba20b20c7b9be7dc00e0e
 *
 * @package woocommerce-shipping-canada-post
 */

/**
 * Plugin activation check
 */
function wc_canada_post_activation_check() {
	if ( ! function_exists( 'simplexml_load_string' ) ) {
		wp_die( "Sorry, but you can't run this plugin, it requires the SimpleXML library installed on your server/hosting to function." );
	}
}

if ( ! class_exists( 'WC_Shipping_Canada_Post_Init' ) ) {
	define( 'WC_CANADA_POST_VERSION', '3.1.1' ); // WRCS: DEFINED_VERSION.

	if ( ! defined( 'WC_CANADA_POST_FILE' ) ) {
		define( 'WC_CANADA_POST_FILE', __FILE__ );
	}

	if ( ! defined( 'WC_CANADA_POST_ABSPATH' ) ) {
		define( 'WC_CANADA_POST_ABSPATH', trailingslashit( __DIR__ ) );
	}

	if ( ! defined( 'WC_SHIPPING_CANADA_POST_PLUGIN_DIR' ) ) {
		define( 'WC_SHIPPING_CANADA_POST_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
	}

	if ( ! defined( 'WC_SHIPPING_CANADA_POST_PLUGIN_URL' ) ) {
		define( 'WC_SHIPPING_CANADA_POST_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	}

	if ( ! defined( 'WC_SHIPPING_CANADA_POST_DIST_DIR' ) ) {
		define( 'WC_SHIPPING_CANADA_POST_DIST_DIR', WC_SHIPPING_CANADA_POST_PLUGIN_DIR . 'dist/' );
	}

	if ( ! defined( 'WC_SHIPPING_CANADA_POST_DIST_URL' ) ) {
		define( 'WC_SHIPPING_CANADA_POST_DIST_URL', WC_SHIPPING_CANADA_POST_PLUGIN_URL . 'dist/' );
	}

	require_once WC_CANADA_POST_ABSPATH . 'includes/class-wc-shipping-canada-post-init.php';
}

register_activation_hook( WC_CANADA_POST_FILE, 'wc_canada_post_activation_check' );

add_action( 'plugins_loaded', 'wc_shipping_canada_post_init', 0 );

/**
 * Initialize plugin.
 */
function wc_shipping_canada_post_init() {
	require_once 'vendor/autoload_packages.php';

	WC_Shipping_Canada_Post_Init::get_instance();
}

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_' . basename( WC_CANADA_POST_FILE, '.php' ), '__return_true' );
