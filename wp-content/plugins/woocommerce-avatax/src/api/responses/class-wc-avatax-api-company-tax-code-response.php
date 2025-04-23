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
 * The AvaTax API companies tax code response class.
 *
 * @since 2.6.1
 */
class WC_Avatax_API_Company_Tax_Code_Response extends WC_AvaTax_API_Response {


	public $isSuccess = false;
	protected $table_name = "wp_wc_avatax_tax_codes";
	protected $create_ddl = "CREATE TABLE wp_wc_avatax_tax_codes (taxCode VARCHAR(100) NOT NULL, taxCodeTypeId VARCHAR(10) NOT NULL, description VARCHAR(500) DEFAULT NULL, entityUseCode VARCHAR(100) DEFAULT NULL, isActive TEXT NOT NULL, INDEX(taxCode, description) )";

	/**
	 * Gets the tax codes from AvaTax.
	 * 
	 * @since 2.6.1
	 *
	 */
	public function get_company_tax_code_list() {
		
		$this->save_tax_codes($this -> value);

		$nextLink = $this -> get_next_link($this->response_data);

		while(!empty($nextLink)) {
			$token_array = explode("/v2", $nextLink);
			$paginated_url = end($token_array);

			$response = WC_AvaTax::instance()->get_api()->get_company_tax_codes($paginated_url);
			
			$this->save_tax_codes($response->response_data->value);

			$nextLink = $this->get_next_link($response->response_data);
		}

        return true;
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

	/**
	 * Saves the tax codes.
	 * 
	 * @since 2.6.1
	 *
	 */
	private function save_tax_codes($response)
	{
		if($this->maybe_create_table($this->table_name, $this->create_ddl))
		{
			foreach ($response ?? [] as $tax_code)
			{
				$this->create_update_tax_code($tax_code);
			}
		}
		else
		{
			wc_avatax()->log("TaxCode table creation failed.");
		}
	}

	/**
	 * Inserts taxcode data into table.
	 * 
	 * @since 2.6.1
	 *
	 */
	private function create_update_tax_code($tax_code)
	{
		global $wpdb;

		$checkIfExists = $wpdb->get_var("SELECT taxCode FROM $this->table_name WHERE taxCode = '$tax_code->taxCode'");

    	if ($checkIfExists == NULL) {
			$query = "INSERT INTO $this->table_name (taxCode, taxCodeTypeId, description, entityUseCode, isActive) VALUES ('$tax_code->taxCode','$tax_code->taxCodeTypeId','$tax_code->description','$tax_code->entityUseCode','$tax_code->isActive')";
			$res = $wpdb->query($query);
		}
		else
		{
			$query = "UPDATE $this->table_name SET taxCodeTypeId='$tax_code->taxCodeTypeId', description='$tax_code->description',entityUseCode='$tax_code->entityUseCode',isActive='$tax_code->isActive' WHERE taxCode = '$tax_code->taxCode'";
			$res = $wpdb->query($query);
		}
	}

	/**
	 * Checks if the table is present or not
	 * 
	 * @since 2.6.1
	 *
	 */
	private function maybe_create_table( $table_name, $create_ddl ) {
		global $wpdb;
	
		$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
		
		if ( $wpdb->get_var( $query ) === $table_name ) {
			return true;
		}
	
		// Didn't find it, so try to create it.
		$wpdb->query( $create_ddl );
	
		// We cannot directly tell that whether this succeeded!
		if ( $wpdb->get_var( $query ) === $table_name ) {
			return true;
		}
	
		return false;
	}

}
