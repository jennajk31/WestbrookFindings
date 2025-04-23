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

$connected    = 'connected' === get_transient( 'wc_avatax_elr_connection_status', "not-connected" );
/**
 * Displays the address settings fields.
 *
 * @type string $id input ID
 * @type string $label input label
 * @type string $connected connection status
 * @type string $value of field
 * @type string $company_name name of selected company
 * @type string $env name of the selected environment
 * @type string $message message to be displayed 
 * @type string $connected_message message to be displayed when status connected
 */
?>

<tr valign="top">
	<th scope="row" class="titledesc">
		<label for="wc_avatax_elr_client_secret"><?php echo __("Client secret", 'woocommerce-avatax') ?> </label>
	</th>
	<td class="forminp forminp-text">
		<input name="wc_avatax_elr_client_secret" id="wc_avatax_elr_client_secret" type="password" style="min-width:300px;" value="<?php echo esc_attr( $value )?>" class="wc-avatax-connection-field" placeholder="" data-wc-avatax-elr-connection-status="<?php echo esc_attr($connected ? 'connected' : 'not-connected') ?>">
	</td>
</tr>
<?php
if(!$connected)
{
	?>
	<tr class="divSync">
		<th colspan="2">
			<button id="wc_avatax_elr_connect_production" class="button-primary avatax_elr_connect" data-environment="production"><?php echo __("Connect to Production", 'woocommerce-avatax') ?></button>
			<button id="wc_avatax_elr_connect_sandbox" class="button-secondary avatax_elr_connect" data-environment="development"><?php echo __("Connect to Sandbox", 'woocommerce-avatax') ?></button>
			<p><?php echo $message ?></p>
		</th>
	</tr>

<?php
}
?>
