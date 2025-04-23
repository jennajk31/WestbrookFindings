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

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax Einvoice API companies response class.
 *
 * @since 2.9.0
 */
class WC_AvaTax_Elr_API_Get_Companies_Response extends \WC_AvaTax_Elr_API_Response {
	/**
	 * Gets einvoice company codes for an account
	 * @since 2.9.0
	 *
	 * @return array
	 */
	public function get_elr_company_code_list() {
		$compValues = $this->value;
		//check for resposne of error
		if(!$compValues || !isset($compValues)) return [];
	
		$result = $this->populate_companies_dropdown($compValues);
        return $result;
	}

	/**
	 * Populates the result for ELR company code dropdown if there are multiple pages in response
	 *
	 * @param array $response
	 * @return array
	 */
	private function populate_companies_dropdown($response) {
		$result = [];
		foreach ($response ?? [] as $company)
		{
			$result[$company->id] = $company->companyName;
		}
		return $result;
	}

	/**
	 * Retrieves the tenant identifier from the company values.
	 * 
	 * This function extracts the tenant identifier from the first company in the company values.
	 * If the company values are empty or not set, returns an empty string.
	 * 
	 * @since 3.0.0
	 * @access public
	 * 
	 * @return string The tenant identifier if found, empty string otherwise.
	 */
	public function get_tenant_id() {
		$compValues = $this->value;
		//check for resposne of error
		if(!$compValues || !isset($compValues)) return '';

		foreach ($compValues ?? [] as $company)
		{
			return $company->tenant->identifier;
		}
	}
}
