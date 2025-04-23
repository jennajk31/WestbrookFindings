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
 */
?>
<table class="form-table" style="">
	<tr valign="top">

		<th scope="row" class="titledesc">
			<label for="wc_avatax_shipping_transport"><?php echo esc_html( "Shipping Transport" ); ?></label>
			<!-- <?php echo $tooltip_html; ?> -->
		</th>

		<td class="forminp forminp-address" data-address-id="wc_avatax_shipping_transport" style="max-height: 200px; overflow-y: scroll;">
		<div style="width: 100%; height: 100%; margin: 0; padding: 0; overflow: auto;">
			<?php 
			foreach($shipping_methods as $zone){ 
			$methods = $zone->shipping_methods;
				if(!empty($methods)){
					foreach($methods as $method){ 
						$selected_val = ( isset( $values[$method->option_short_id] ) ) ? esc_attr( $values[$method->option_short_id] ) : '';
						?>
						
						<p class="wc-avatax-address-field">
						<label for="<?php echo esc_attr($method->option_id);?>"><?php esc_html_e( $zone->name . " - " . $method->name, 'woocommerce-avatax' ); ?></label>
						<select name="<?php echo esc_attr($method->option_name); ?>" id="<?php echo esc_attr($method->option_id); ?>" style="width: 25em;">
									<option value=""><?php esc_attr_e( '', 'woocommerce-avatax' ); ?></option>
									<?php foreach ( $transport_options as $label ) : ?>
										<option value="<?php echo esc_attr( $label ); ?>" <?php selected( $method->transport, $label, true ); ?>>
											<?php echo esc_attr( $label ); ?>
										</option>
									<?php endforeach; ?>
						</select>
						</p>
					<?php 
					}
				}	
			} ?>
		</div>
		</td>
	</tr>
</table>