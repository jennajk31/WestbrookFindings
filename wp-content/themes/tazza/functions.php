<?php
/**
 * Tazza functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Two
 * @since Tazza 1.0
 */


if ( ! function_exists( 'tazza_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * @since Tazza 1.0
	 *
	 * @return void
	 */
	function tazza_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}

endif;

add_action( 'after_setup_theme', 'tazza_support' );

if ( ! function_exists( 'tazza_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @since Tazza 1.0
	 *
	 * @return void
	 */
	function tazza_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'tazza-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'tazza-style' );

		// Enqueue the additional stylesheet for Twenty Twenty Two.
		if ( class_exists( 'WooCommerce' ) && function_exists( 'WC' ) && defined( 'WC_ABSPATH' ) && file_exists( WC_ABSPATH . 'assets/css/twenty-twenty-two.css' ) ) {

			wp_enqueue_style( 'woocommerce-twenty-twenty-two', WC()->plugin_url() . '/assets/css/twenty-twenty-two.css' );

			wp_dequeue_style( 'woocommerce-general' );
		}
	}

endif;

add_action( 'wp_enqueue_scripts', 'tazza_styles' );

if ( ! function_exists( 'tazza_woocommerce_init' ) ) :
	/**
	 * Initialize WooCommerce compatibility.
	 *
	 * @since Tazza 1.0.1
	 * @return void
	 */
	function tazza_woocommerce_init() {
		if ( ! class_exists( 'WooCommerce' ) || ! defined( 'WC_ABSPATH' ) || ! file_exists( WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twenty-two.php' ) ) {
			return;
		}

		// Load WooCommerce compatibility file if WooCommerce is loaded and the compatibility file exists.
		include_once WC_ABSPATH . 'includes/theme-support/class-wc-twenty-twenty-two.php';
	}
endif;

add_action( 'after_setup_theme', 'tazza_woocommerce_init' );

if ( ! function_exists( 'tazza_preload_webfonts' ) ) :

	/**
	 * Preloads the main web font to improve performance.
	 *
	 * Only the main web font (font-style: normal) is preloaded here since that font is always relevant (it is used
	 * on every heading, for example). The other font is only needed if there is any applicable content in italic style,
	 * and therefore preloading it would in most cases regress performance when that font would otherwise not be loaded
	 * at all.
	 *
	 * @since Tazza 1.0
	 *
	 * @return void
	 */
	function tazza_preload_webfonts() {
		?>
		<link rel="preload" href="<?php echo esc_url( get_theme_file_uri( 'assets/fonts/SourceSerif4Variable-Roman.ttf.woff2' ) ); ?>" as="font" type="font/woff2" crossorigin>
		<?php
	}

endif;

add_action( 'wp_head', 'tazza_preload_webfonts' );

