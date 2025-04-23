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
 * The AvaTax API utility response class.
 *
 * @since 3.0.0
 */
class WC_AvaTax_Elr_API_Response extends \WC_AvaTax_API_Response {


	/**
	 * Checks if the response contains an error code.
	 * This method verifies if the ErrorCode property is set in the response object.
	 *
	 * @since 3.0.0
	 *
	 * @return bool Returns true if an error code exists, false otherwise.
	 */
	public function has_error_code() : bool{
		return isset($this->response_data->ErrorCode);
	}

	/**
	 * Gets the invoice error message from the response.
	 *
	 * @since 3.0.0
	 *
	 * @return string The error message if it exists, empty string otherwise.
	 */
	public function get_invoice_error_message() : string {
		return isset($this->response_data->Message) ? $this->response_data->Message : "";
	}

}
