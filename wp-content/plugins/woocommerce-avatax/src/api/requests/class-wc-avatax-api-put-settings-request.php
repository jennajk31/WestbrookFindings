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

/**
 * The AvaTax API Get Settings request class.
 *
 * @since 2.8.0
 */
class WC_AvaTax_API_Put_Settings_Request extends WC_AvaTax_API_Request {

	/**
	 * Get Woocommerce Settings from customer account in Avatax
	 *
	 * @since 2.8.0
	 *
	 */
	public function __construct($type) {
		$application_id = wc_avatax()::CONNECTOR_ID;
		$version_id = wc_avatax()::CLIENT_STRING;
		$schema_id = wc_avatax()::SCHEMA_ID;
		$website_id = get_option("wc_avatax_website_id");

		$this->path = "/active-applications/".$application_id."/configs/" . $website_id."/sections/".$schema_id."?version=".$version_id;
		$this->method = $type;
	}

	/**
	 * Prepares the Settings data from customer account in Avatax
	 *
	 * @since 2.8.0
	 *
	 */
	public function prepare_settings_data()
	{
		if(wc_avatax()->has_api_credentials_set() && wc_avatax()->check_api())
		{
			$data = new stdClass();
			$tax_calculation = new stdClass();
			$logs = new stdClass();

			$tax_calculation->wc_avatax_enable_tax_calculation = (get_option('wc_avatax_enable_tax_calculation', '') === 'yes');
			$tax_calculation->wc_avatax_record_calculations = (get_option('wc_avatax_record_calculations', '') === 'yes');
			$tax_calculation->wc_avatax_calculate_on_cart = (get_option('wc_avatax_calculate_on_cart', '') === 'yes');
			$tax_calculation->wc_avatax_sku_as_item_code = (get_option('wc_avatax_sku_as_item_code', '') === 'yes');
			$tax_calculation->wc_avatax_company_code = get_option('wc_avatax_company_code', '');
			$tax_calculation->wc_avatax_company_id = (int) get_option('wc_avatax_company_id', '');
			$tax_calculation->wc_avatax_shipping_code = get_option('wc_avatax_shipping_code', '');
			
			$data->tax_calculation = $tax_calculation;

			$logs->wc_avatax_debug = (get_option('wc_avatax_debug') === 'yes');
			$data->logs = $logs;

			if(!wc_avatax()->wc_avatax_utilities()->has_nexus_outside_countries()){
				$address_validation = new stdClass();
				$address_validation->wc_avatax_enable_address_validation = (get_option('wc_avatax_enable_address_validation', '') === 'yes');
				$data->address_validation = $address_validation;
			}
			else {
				$transactions_outside_the_us = new stdClass();
				$transactions_outside_the_us->wc_avatax_enable_vat = (get_option('wc_avatax_enable_vat') === 'yes');
				$data->transactions_outside_the_us = $transactions_outside_the_us;
			}

			if(wc_avatax()->wc_avatax_utilities()->has_ecm_subscription()) {
				$exemption_certificate_management = new stdClass();
				$exemption_certificate_management->wc_avatax_enable_ecm = (get_option('wc_avatax_enable_ecm') === 'yes');
				$data->exemption_certificate_management = $exemption_certificate_management;
			}

			$this->data = $data;
		}
	}
}
