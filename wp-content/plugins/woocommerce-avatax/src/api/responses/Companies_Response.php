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

namespace SkyVerge\WooCommerce\AvaTax\API\Responses;

use WC_AvaTax_API_Response;
use WC_AvaTax;

defined( 'ABSPATH' ) or exit;

/**
 * The AvaTax API companies response class.
 *
 * @since 1.13.0
 */
class Companies_Response extends WC_AvaTax_API_Response {


	/**
	 * Gets the company ID from the companies' response body.
	 *
	 * @since 1.13.0
	 *
	 * @param string $company_code
	 * @return string|null
	 */
	public function get_company_id( string $company_code = '' ) {
		// try to match the company ID with the one saved in settings, or fallback to the first one from the response
		if ( empty( $company_code ) ) {
			foreach ($this->value ?? [] as $company)
			{
				if($company->isDefault) {
					return (string) $company->id;
				}
			}
		}

		foreach ( $this->value ?? [] as $company ) {
			if ( isset( $company->id, $company->companyCode ) && $company_code === $company->companyCode ) {
				return (string) $company->id;
			}
		}

		return '';
	}

	/**
	 * Gets the company Name from the companies' response body.
	 *
	 * @since 2.7.0
	 *
	 * @param string $company_code
	 * @return string|null
	 */
	public function get_company_name( string $company_code = '' ) {
		// try to match the company ID with the one saved in settings, or fallback to the first one from the response
		if ( empty( $company_code ) ) 
		{
			foreach ($this->value ?? [] as $company)
			{
				if($company->isDefault) {
					return (string) $company->name;
				}
			}
		}
		else
		{
			foreach ( $this->value ?? [] as $company ) {
				if ( isset( $company->id, $company->companyCode ) && $company_code === $company->companyCode ) {
					return (string) $company->name;
				}
			}
		}
		return '';
	}

	/**
	 * Gets the company codes for an account.
	 * This would return the list of Company Codes and would also match if current account has
	 * default company code set in wc_options table under column name 'wc_avatax_company_code'. If it finds
	 * a match then the same company code would be set as default otherwise blank would set as default
	 * value in the dropdown.
	 * @since 2.3.0
	 *
	 * @return string[]
	 */
	public function get_company_code_list() {
		// initialize an empty result array and populate the first element with "Select" value
		$result = [];

		// Merge the result of first API call into result[]
		$result = $result + $this -> populate_companies_dropdown($this -> value);
		$nextLink = $this -> get_next_link($this->response_data);
		// If / while nextLink is present in response, enrich the result[]
		while(!empty($nextLink)) {
			$token_array = explode("/v2", $nextLink);
			$paginated_url = end($token_array);
			$response = WC_AvaTax::instance()->get_api()->get_companies($paginated_url);
			$result = $result + $this -> populate_companies_dropdown($response -> response_data -> value);
			$nextLink = $this -> get_next_link($response -> response_data);
		}
        return $result;
	}

	/**
	 * Populates the partial result for company code dropdown if there are multiple pages in response
	 *
	 * @param string[] $paginated_response
	 * @return string[]
	 */
	private function populate_companies_dropdown($paginated_response) {
		$partial_result = [];
		// This company code is compared(ignore case) against the list of codes we got from API
		$company_code = get_option( 'wc_avatax_company_code', '');
		foreach ($paginated_response ?? [] as $company)
		{
			//if company not selected then set it to default company
			if($company_code === '' && $company->isDefault) {
				update_option( 'wc_avatax_company_code', $company->companyCode );
				update_option( 'wc_avatax_company_id', $company->id );
				update_option( 'wc_avatax_company_name', $company->name );
			}
			if (strcasecmp($company_code, $company->companyCode) == 0) {
				$partial_result[$company_code] = $company_code;
			} else {
				$partial_result[$company->companyCode] = $company->companyCode;
			}
		}
		return $partial_result;
	}

	/**
	 * Gets the company Code from the $filter=isDefault eq true companies' response body.
	 *
	 * @since 2.10.0
	 *
	 * @return string|null
	 */
	public function get_default_company_code(): string {
		
		foreach ($this->value ?? [] as $company)
		{
			if($company->isDefault) {
				return (string) $company->companyCode;
			}
		}
		
		return '';
	}

	/**
	 * Provides nextLink if available in response, if not then ''
	 *
	 * @param mixed $response_data
	 * @return string
	 */
	private function get_next_link($response_data) {
		if (isset($response_data ->{'@nextLink'})) {
			return $response_data ->{'@nextLink'};
		}
		return '';
	}

}
