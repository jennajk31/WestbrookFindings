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
class WC_AvaTax_API_Delete_Setting_Request extends WC_AvaTax_API_Request {

	/**
	 * Get Woocommerce Settings from customer account in Avatax
	 *
	 * @since 2.8.0
	 *
	 */
	public function __construct($args) {
		$application_id = wc_avatax()::CONNECTOR_ID;
		$schema_id = wc_avatax()::SCHEMA_ID;
		$website_id = get_option("wc_avatax_website_id");

		$this->path = "/active-applications/".$application_id."/configs/" . $website_id."/sections/".$schema_id;
		$this->method = 'DELETE';
	}
}
