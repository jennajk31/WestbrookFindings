<?php
/**
 * Plugin Name: WooCommerce Australia Post Shipping
 * Plugin URI: https://woocommerce.com/products/australia-post-shipping-method/
 * Description: Obtain parcel shipping rates dynamically via the Australia Post API for your orders.
 * Version: 2.7.1
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Copyright: © 2025 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Requires Plugins: woocommerce
 * Requires PHP: 7.4
 * Requires at least: 6.6
 * Tested up to: 6.7
 * WC requires at least: 9.6
 * WC tested up to: 9.8
 *
 * Woo: 18622:1dbd4dc6bd91a9cda1bd6b9e7a5e4f43
 *
 * @package WC_Shipping_Australia_Post
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WC_SHIPPING_AUSTRALIA_POST_VERSION', '2.7.1' ); // WRCS: DEFINED_VERSION.

if ( ! defined( 'WC_SHIPPING_AUSTRALIA_POST_PLUGIN_FILE' ) ) {
	define( 'WC_SHIPPING_AUSTRALIA_POST_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'WC_SHIPPING_AUSTRALIA_POST_ABSPATH' ) ) {
	define( 'WC_SHIPPING_AUSTRALIA_POST_ABSPATH', trailingslashit( __DIR__ ) );
}

if ( ! defined( 'WC_SHIPPING_AUSTRALIA_POST_PLUGIN_URL' ) ) {
	define( 'WC_SHIPPING_AUSTRALIA_POST_PLUGIN_URL', trailingslashit( plugins_url( '', __FILE__ ) ) );
}

/**
 * Require the main class and initialize shipping method.
 */
if ( ! class_exists( 'WC_Shipping_Australia_Post_Init', false ) ) {
	require_once WC_SHIPPING_AUSTRALIA_POST_ABSPATH . '/includes/class-wc-shipping-australia-post-init.php';
}

add_action( 'plugins_loaded', 'wc_shipping_australia_post_init', 0 );

/**
 * Initialize plugin.
 */
function wc_shipping_australia_post_init() {
	require_once 'vendor/autoload_packages.php';

	WC_Shipping_Australia_Post_Init::get_instance();
}

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );
