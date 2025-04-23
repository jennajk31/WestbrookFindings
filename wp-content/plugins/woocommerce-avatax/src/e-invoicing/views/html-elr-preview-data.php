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
$field_list = get_option('wc_avatax_elr_custom_fields', array());
?>

<table id="tblPreviewData" class="tbl_preview_data" width="100%">
    <tr>
        <td>
            <div class="divForm" style="display: inline-block; margin-right: 20px;">
                <label>Document type</label>
                <select name="entity_type" id="prew_entity_type" style="width: 200px; height: 35px !important;">
                    <option value="order" <?php selected($entity, 'order'); ?>> <?php echo __("Order", 'woocommerce-avatax') ?> </option>
                    <option value="refund" <?php selected($entity, 'refund'); ?>> <?php echo __("Refund", 'woocommerce-avatax') ?> </option>
                </select>
            </div>
            <div class="divForm" style="display: inline-block; margin-right: 20px;">
                <label>&nbsp;</label>
                <input type="number" id="order_number" placeholder="Enter ID" style="width: 200px; height: 35px !important;"/>
            </div>
            <div class="divForm" style="display: inline-block;">
                <label>&nbsp;</label>
                <a href="javascript:void(0)" id="btn_preview_data" class="button-primary"> <?php echo __( 'Preview', 'woocommerce-avatax' );?></a>
            </div>
        </td>
    </tr>
    <tr>
        <td style="padding: 15px 0;">
            <ul id="preview_data" class="preview_data"></ul>
        </td>
    </tr>
</table>
