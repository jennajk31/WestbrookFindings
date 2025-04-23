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
 * @author    Avalara
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

define( 'AVA_BLOCK_VERSION', '1.0.0' );


/**
 * Class for integrating with WooCommerce Blocks
 */
class Avatax_Blocks_Integration implements IntegrationInterface {

	/**
	 * The name of the integration.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'ecm-links';
	}

	/**
	 * When called invokes any initialization/setup for the integration.
	 */
	public function initialize() {
		$this->register_block_frontend_scripts();
		$this->register_block_editor_scripts();
		$this->register_block_style();
	}

	function register_block_editor_scripts() {
		$script_path       = '/build/index.js';
		$script_url        = plugins_url( 'woocommerce-avatax' . $script_path );
		$script_asset_path = plugins_url( 'woocommerce-avatax/build/index.asset.php' );
		$script_asset      = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);
	
		wp_register_script(
			'ecm-links-block-editor',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}
	
	function register_block_frontend_scripts() {
		$script_path       = '/build/ecm-links.js';
		$script_url        = plugins_url( '/woocommerce-avatax' . $script_path );
		$script_asset_path = WP_PLUGIN_DIR . '/woocommerce-avatax/build/ecm-links.asset.php';
	
	
		$script_asset = file_exists( $script_asset_path )
			? require $script_asset_path
			: array(
				'dependencies' => array(),
				'version'      => $this->get_file_version( $script_asset_path ),
			);
	
	
		wp_register_script(
			'ecm-links',
			$script_url,
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);
	}

	function register_block_style(){
		/* register style for ecm links inner block */
		wp_enqueue_style( 'avatax-blocks-style' );
	}

	/**
	 * Returns an array of script handles to enqueue in the frontend context.
	 *
	 * @return string[]
	 */
	public function get_script_handles() {
		return [ 'ecm-links' ];
	}

	/**
	 * Returns an array of script handles to enqueue in the editor context.
	 *
	 * @return string[]
	 */
	public function get_editor_script_handles() {
		return [ 'ecm-links-block-editor' ];
	}

	/**
	 * An array of key, value pairs of data made available to the block on the client side.
	 *
	 * @return array
	 */
	public function get_script_data() {
		return array();
	}

	/**
	 * Get the file modified time as a cache buster if we're in dev mode.
	 *
	 * @param string $file Local path to the file.
	 * @return string The cache buster value to use for the given file.
	 */
	protected function get_file_version( $file ) {
		if ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG && file_exists( $file ) ) {
			return filemtime( $file );
		}
		return AVA_BLOCK_VERSION;
	}
}
