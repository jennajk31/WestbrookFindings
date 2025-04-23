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
 * The AvaTax REST API utility request class.
 *
 * This is used when testing connectivity, etc...
 *
 * @since 3.0.0
 */
class WC_Avatax_Elr_API_Download_Invoice_Request extends \WC_AvaTax_Elr_API_Request {


	/**
	 * Constructs the class.
	 *
	 * @since 3.0.0
	 */
	public function __construct($invoice_id) {
		$invoice_id = $invoice_id;
		$this->path   = '/documents/'. $invoice_id .'/$download?v='. idate('U');
		$this->method = 'POST';
	}

	public function prepare_request($doctype) {

		$config_id = get_option("wc_avatax_website_id");
		$app_id = wc_avatax()::ELR_CONNECTOR_ID;
		$company_id = get_option("wc_avatax_elr_company");
		$document_type = $doctype; //e.g 'ubl-invoice'
		$country_mandate = 'AD-B2B-PEPPOL';

		$payload = array(
			'metadata' => json_encode(array( "configId" => $config_id, "appId" => $app_id, "companyId" => $company_id, "documentType" => $document_type, "countryMandate"=> $country_mandate)),
		);
		$this->data = $payload;
	}
}
