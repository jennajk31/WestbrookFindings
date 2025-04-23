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
 * The AvaTax API post schema to CCS request class.
 *
 * @since 2.9.0
 */
class WC_AvaTax_API_Post_Payload_Schema_Request extends WC_AvaTax_Elr_API_Request {

	/**
	 * Post Einvoice schema to CCS in Avatax
	 *
	 * @since 2.9.0
	 *
	 */
	public function __construct($args) {
		$application_id = wc_avatax()::ELR_CONNECTOR_ID;
		$website_id = get_option("wc_avatax_website_id");
		
		$payload_id = "ELRAR" . $args["doctype"];

		$this->path = "/active-applications/".$application_id."/configs/" . $website_id."_elr"."/sections/elr/payloads/".$payload_id;
		$this->method = $args["type"];
	}

	/**
	 * Prepares the schema data for CCS in Avatax
	 *
	 * @since 2.9.0
	 *
	 */
	public function set_payload_data($payload, $doctype)
	{
		$data = new stdClass();
		$data->documentType = $doctype;
		$data->flowType = 'outbound';
		$data->schema = $payload;
		$this->data = $data;
	}
}
