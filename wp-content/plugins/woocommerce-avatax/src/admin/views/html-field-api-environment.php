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
 * Displays the address settings fields.
 *
 * @type string $value of field
 */
?>

<tr valign="top" style="display:none;">
	<th scope="row" class="titledesc">
		<label for="wc_avatax_api_environment">License Key </label>
	</th>
	<td class="forminp forminp-text">
		<select name="wc_avatax_api_environment" id="wc_avatax_api_environment" class="">
			<?php
				if($value == "development"){
			?>
					<option id="production">production</option>
					<option id="development" selected="selected">development</option>
			<?php
				}
				else{
					?>
					<option id="production" selected="selected">production</option>
					<option id="development">development</option>
					<?php
				}
			?>
		</select>
	</td>
</tr>