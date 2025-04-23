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

<?php
if($connected)
{
	?>
	<th scope="row" class="titledesc">
		<label for="wc_avatax_elr_company"><?php echo esc_attr( $label ); ?></label>
	</th>
	<td class="forminp forminp-text">
		<?php if(count($company_code_list) === 1) {
			?>
				<input name="wc_avatax_elr_company_label" id="<?php echo esc_attr( $id ); ?>_label" type="text" style="min-width:300px;" value="<?php echo current( $company_code_list )?>" readonly class="wc-avatax-connection-field" >
				<input name="wc_avatax_elr_company" id="<?php echo esc_attr( $id ); ?>" type="hidden" style="min-width:300px;" value="<?php echo key( $company_code_list )?>" readonly class="wc-avatax-connection-field" >
			<?php
		} else { 
			?>
				<select name="wc_avatax_elr_company" id="<?php echo esc_attr( $id ); ?>" style="min-width:300px;" class="wc-enhanced-select-nostd">
					<option value=""><?php esc_attr_e( '--Select a Company--', 'woocommerce-avatax' ); ?></option>
					<?php foreach ( $company_code_list as $com_id => $company ) : ?>
						<option value="<?php echo esc_attr( $com_id ); ?>" <?php selected( $company_id, $com_id, true ); ?>>
							<?php echo esc_attr( $company ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			<?php
		} ?>	
	</td>
	<?php
}
?>
