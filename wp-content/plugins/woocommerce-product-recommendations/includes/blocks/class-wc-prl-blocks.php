<?php
/**
 * Class for handling blocks.
 *
 * @package  WooCommerce Product Recommendations
 * @since    4.1.0
 * @version  4.1.0
 */
class WC_PRL_Blocks {

	/**
	 * Min required WC version.
	 *
	 * @var string
	 */
	private $wc_min_version = '9.2.0';

	/**
	 * The single instance of the class.
	 *
	 * @var WC_PRL_Blocks
	 */
	protected static $_instance = null;

	/**
	 * Main WC_PRL_Blocks instance. Ensures only one instance of WC_PRL_Blocks is loaded or can be loaded.
	 *
	 * @static
	 * @return WC_PRL_Blocks
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-recommendations' ), '4.1.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Foul!', 'woocommerce-product-recommendations' ), '4.1.0' );
	}

	/**
	 * Construct.
	 */
	private function __construct() {

		if ( ! WC_PRL_Core_Compatibility::wc_current_theme_is_fse_theme() ) {
			return;
		}

		// Compatibility check.
		if ( version_compare( WC()->version, $this->wc_min_version ) < 0 ) {
			return;
		}

		$this->includes();

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Include files.
	 */
	private function includes() {

		// Load block types.
		require_once WC_PRL_ABSPATH . 'includes/blocks/abstracts/class-wc-prl-abstract-product-collection.php';
		require_once WC_PRL_ABSPATH . 'includes/blocks/block-types/class-wc-prl-blocks-collection-recently-viewed.php';
		require_once WC_PRL_ABSPATH . 'includes/blocks/block-types/class-wc-prl-blocks-collection-viewed-not-purchased.php';
		require_once WC_PRL_ABSPATH . 'includes/blocks/block-types/class-wc-prl-blocks-collection-others-also-bought.php';
		require_once WC_PRL_ABSPATH . 'includes/blocks/block-types/class-wc-prl-blocks-collection-frequently-bought-together.php';
	}

	/**
	 * Init.
	 */
	public function init() {

		$this->register_product_collection_types();

		if ( is_admin() ) {
			add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ), 100 );
		}
	}

	/**
	 * Enqueue block scripts.
	 */
	public function enqueue_block_editor_assets() {

		$script_path = 'assets/dist/admin/blocks/index.js';
		$script_url  = WC_PRL()->get_plugin_url() . '/' . $script_path;

		$script_asset_path = WC_PRL_ABSPATH . 'assets/dist/admin/blocks/index.asset.php';
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => WC_PRL()->get_file_version( WC_PRL_ABSPATH . $script_path ),
			);

		wp_enqueue_script(
			'wc-prl-blocks-collections',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_script_add_data( 'wc-prl-blocks-collections', 'strategy', 'defer' );

		// Load JS translations.
		if ( function_exists( 'wp_set_script_translations' ) ) {
			wp_set_script_translations(
				'wc-prl-blocks-collections',
				'woocommerce-product-recommendations',
				WC_PRL_ABSPATH . '/languages'
			);
		}

		$style_path = 'assets/css/admin/blocks.css';
		$style_url  = WC_PRL()->get_plugin_url() . '/' . $style_path;

		wp_enqueue_style(
			'wc-prl-blocks-collections',
			$style_url,
			'',
			WC_PRL()->get_file_version( WC_PRL_ABSPATH . $style_path ),
			'all'
		);

		wp_style_add_data( 'wc-prl-blocks-collections', 'rtl', 'replace' );
	}

	/**
	 * Register product collection types.
	 */
	public function register_product_collection_types() {

		$collections = array(
			'WC_PRL_Blocks_Collection_Recently_Viewed',
			'WC_PRL_Blocks_Collection_Viewed_Not_Purchased',
			'WC_PRL_Blocks_Collection_Others_Also_Bought',
			'WC_PRL_Blocks_Collection_Fequently_Bought_Together',
		);

		foreach ( $collections as $collection ) {
			if ( class_exists( $collection ) ) {
				$collection = new $collection();
			}
		}
	}
}

WC_PRL_Blocks::instance();
