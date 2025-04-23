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
 * Display the user ior (importer of record) fields.
 *
 * @type string $selected_ior saved value for the current user
 */
?>

<!-- <h3><?php esc_html_e( 'Seller importer of record', 'woocommerce-avatax' ); ?></h3> -->

<table class="form-table">
	<tr>
		<th><label for="wc_avatax_user_exemption"><?php esc_html_e( 'Seller importer of record', 'woocommerce-avatax' ); ?></label></th>
		<td>
			<select name="wc_avatax_user_ior" id="wc_avatax_user_ior" style="width: 25em;">
				<!-- <option value=""><?php esc_attr_e( 'Avatax Default', 'woocommerce-avatax' ); ?></option> -->
                <option value="<?php echo esc_attr(""); ?>" <?php selected( $selected_ior, esc_attr(""), true ) ?> > <?php echo esc_attr( "Avatax Default" ); ?></option>
                <option value="<?php echo esc_attr("Yes"); ?>" <?php selected( $selected_ior, esc_attr("Yes"), true ) ?> > <?php echo esc_attr( "Yes" ); ?></option>
                <option value="<?php echo esc_attr("No"); ?>" <?php selected( $selected_ior, esc_attr("No"), true ) ?>  > <?php echo esc_attr( "No" ); ?></option>
			</select>
		</td>
	</tr>
</table>
