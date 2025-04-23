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

use SkyVerge\WooCommerce\PluginFramework\v5_10_14 as Framework;

defined( 'ABSPATH' ) or exit;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API Get Settings response class.
 *
 * @since 2.8.0
 */
class WC_AvaTax_API_Get_Settings_Response extends WC_AvaTax_API_Response {


	/**
	 * Get Woocommerce Settings from response body.
	 *
	 * @since 2.8.0
	 *
	 * @return object
	 */
	public function get_configuration_settings( ):object {
		$settings =  new stdClass();
		
		if($this->has_errors() || empty($this->response_data) || property_exists($this->response_data, 'Message') ){
			return $settings;
		}
		else{
			$settings = $this->response_data->config;

			if($this->response_data == null || $this->response_data->config == null ){
				return array(
					'code'  => 500,
					'error' => 'Could not connect to AvaTax. Pleae Try again.'
				);
			}
			return $settings;
		}
	}
}
