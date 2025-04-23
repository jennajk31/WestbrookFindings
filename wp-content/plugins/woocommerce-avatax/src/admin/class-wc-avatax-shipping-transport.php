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
 * @author    SkyVerge
 * @copyright Copyright (c) 2016-2022, SkyVerge, Inc.
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

use SkyVerge\WooCommerce\AvaTax\Landed_Cost_Sync_Handler;
use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

/**
 * Set up the admin settings.
 *
 * @since 2.4.0
 */
class WC_AvaTax_Shipping_Transport {

	protected $transport_options = array();

	/**
	 * Constructs the class.
	 *
	 * @since 2.4.0
	 */
	public function __construct() {

		$this->add_hooks();
		$this->get_transport_options();
	}

	/**
	 * Gets an instance of the plugin main class.
	 *
	 * @since 2.4.0
	 *
	 * @return WC_AvaTax
	 */
	protected function get_plugin() : WC_AvaTax {

		return wc_avatax();
	}

	/**
	 * Adds action and filter hooks.
	 *
	 * @since 2.4.0
	 */
	private function add_hooks() {

		add_action( 'woocommerce_init', array($this, 'add_custom_field_to_shipping_methods'), 10  );
		add_action( 'woocommerce_init', array($this, 'save_custom_field_for_shipping_methods'), 10  );
		
	}

	public function get_transport_options()
	{
		if(!get_transient( 'wc_avatax_transport_options' ))
		{
			if($this->get_plugin()->check_api())
			{
				$options = $this->get_plugin()->get_api()->get_transport_parmeter_list();
			
				foreach($options as $opt)
				{
					$this->transport_options[$opt] = $opt;
				}

				set_transient( 'wc_avatax_transport_options', $this->transport_options, 1 * DAY_IN_SECONDS );
			}
		}
		else
		{
			$this->transport_options = get_transient( 'wc_avatax_transport_options' );
		}
	}
 
	/**
	 * Add custom field to shipping method settings.
	 *
	 * @since 2.4.0
	 *
	 */
	function add_custom_shipping_field( $settings ) {
		$settings['transport'] = array(
			'title'       => __( 'Avalara Transport', 'woocommerce-avatax' ),
			'desc_tip' => __( 'Specify who is responsible for handling the transportation of goods in a VAT transaction. If you don\'t specify a value, Seller is used by default.', 'woocommerce-avatax' ),
			'default'     => 'Seller',
			'type'     => 'select',
			'class'    => 'wc-enhanced-select',
			'options'  => $this->transport_options
		);

		return $settings;
	}

	/**
	 * Add the custom field to all available shipping methods.
	 *
	 * @since 2.4.0
	 *
	 */
	function add_custom_field_to_shipping_methods() {
		if ( ! function_exists( 'WC' ) || ! WC()->shipping instanceof WC_Shipping ) {
			return;
		}

		$shipping_methods = WC()->shipping->get_shipping_methods();

		foreach ( $shipping_methods as $method_id => $shipping_method ) {
			add_filter( 'woocommerce_shipping_instance_form_fields_' . $method_id, array($this,'add_custom_shipping_field') );
		}
	}


	/**
	 * Save custom field value.
	 *
	 * @since 2.4.0
	 *
	 */
	function save_custom_shipping_field( $instance_id, $data ) {
		$transport = isset( $data['transport'] ) ? wc_clean( $data['transport'] ) : '';
		update_option( 'woocommerce_transport_' . $instance_id, $transport );
	}

	/**
	 * Save the custom field value for all available shipping methods.
	 *
	 * @since 2.4.0
	 *
	 */
	function save_custom_field_for_shipping_methods() {
		if ( ! function_exists( 'WC' ) || ! WC()->shipping instanceof WC_Shipping ) {
			return;
		}

		$shipping_methods = WC()->shipping->get_shipping_methods();

		foreach ( $shipping_methods as $method_id => $shipping_method ) {
			if($method_id === "flat_rate" || $method_id === "free_shipping" || $method_id === "local_pickup")
			 {
				add_action( 'woocommerce_update_options_shipping_' . $method_id, array($this, 'save_custom_shipping_field'), 10, 2 );
			 }
		}
	}

}
