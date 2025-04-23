<?php
/**
 * Plugin Name: WooCommerce Royal Mail
 * Plugin URI: https://woocommerce.com/products/royal-mail/
 * Description: Offer Royal Mail shipping rates automatically to your customers. Prices according to the 2025 <a target="_blank" href="https://www.royalmail.com/sites/royalmail.com/files/2025-03/our-prices-april-2025--v1-ta.pdf">regular price guide</a> and <a target="_blank" href="https://www.royalmail.com/sites/royalmail.com/files/2025-03/online-price-guide-april-2025-v1-ta.pdf">online price guide</a>.
 * Version: 3.5.3
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Text Domain: woocommerce-shipping-royalmail
 * Domain Path: /languages
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
 * Woo: 182719:03839cca1a16c4488fcb669aeb91a056
 *
 * @package WC_RoyalMail
 */

if ( ! defined( 'WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE' ) ) {
	define( 'WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE', __FILE__ );
}

if ( ! defined( 'WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH' ) ) {
	define( 'WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH', trailingslashit( __DIR__ ) );
}

/**
 * Only load the plugin if WooCommerce is activated
 */
if ( ! class_exists( 'WC_RoyalMail' ) ) :
	define( 'WOOCOMMERCE_SHIPPING_ROYALMAIL_VERSION', '3.5.3' ); // WRCS: DEFINED_VERSION.

	require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-services.php';
	require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-packaging.php';
	require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-shipping-zones.php';
	require_once WOOCOMMERCE_SHIPPING_ROYALMAIL_ABSPATH . 'includes/class-wc-royalmail.php';
endif;

add_action( 'plugins_loaded', 'woocommerce_shipping_royalmail_init' );

/**
 * Initializes extension.
 *
 * @since 2.5.24
 * @return void
 */
function woocommerce_shipping_royalmail_init() {
	require_once 'vendor/autoload_packages.php';

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_shipping_royalmail_missing_wc_notice' );
		return;
	}

	new WC_RoyalMail();
}

/**
 * WooCommerce fallback notice.
 *
 * @since 2.5.24
 * @return void
 */
function woocommerce_shipping_royalmail_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Royalmail requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-shipping-royalmail' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

/**
 * Localisation.
 */
// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_' . basename( WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE, '.php' ), '__return_true' );
add_action( 'after_setup_theme', 'woocommerce_shipping_royalmail_load_textdomain' );

/**
 * Load plugin textdomain.
 *
 * @return void
 */
function woocommerce_shipping_royalmail_load_textdomain() {
	load_plugin_textdomain( 'woocommerce-shipping-royalmail', false, plugin_basename( dirname( WOOCOMMERCE_SHIPPING_ROYALMAIL_FILE ) ) . '/languages' );
}
