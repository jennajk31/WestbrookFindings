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

<table class="tbl_custom_fields">
    <tr>
        <td width="20%">
        <lable>
            <?php echo __( 'Custom field type', 'woocommerce-avatax' ); ?>
            <span class="wc-avatax-help-tip" tabindex="0" data-tip="<?php echo __("A customizable field added for ELR data collection. If you select ‘Company-level’ option, the field you add will appear on the configuration page, and for the ‘Customer-level', it will be added to your <a href='/my-account/edit-account/' target='blank'>customer’s account details page</a>", 'woocommerce-avatax') ?>">i</span>
        </lable>
            <select id="field_type">
                <option value="company"><?php echo __( 'Company-level', 'woocommerce-avatax' );?></option>
                <option value="customer"><?php echo __( 'Customer-level', 'woocommerce-avatax' );?></option>
            </select>
        </td>
        <td width="20%">
            <lable><?php echo  __( 'Custom field name', 'woocommerce-avatax' );?></lable>
            <input type="text" id="field_name" maxlength="50" class="alphanumeric" value="" placeholder="<?php echo __("Enter custom field name", 'woocommerce-avatax') ?>"/>
        </td>
        <td width="20%">
            <lable><?php echo __( 'Data type', 'woocommerce-avatax' );?></lable>
            <select id="data_type">
                <option value="string"><?php echo __( 'String', 'woocommerce-avatax' );?></option>
                <option value="number"><?php echo __( 'Number', 'woocommerce-avatax' );?></option>
                <option value="date"><?php echo __( 'Date', 'woocommerce-avatax' );?></option>
            </select>
        </td width="20%">
        <td width="20%">
            <lable>&nbsp;</lable>
            <a href="javascript:void(0)" id="add_custom_field" class="button-primary"> <?php echo __( 'Save', 'woocommerce-avatax' );?></a>
        </td>
    </tr>
</table>

<table class="avatax striped ">
    <thead>
    <tr>
        <td>
            <?php echo __( 'Custom field type', 'woocommerce-avatax' );?>
        </td>
        <td>
            <?php echo __( 'Custom field name', 'woocommerce-avatax' );?>
        </td>
        <td>
            <?php echo __( 'Data type', 'woocommerce-avatax' );?>
        </td>
        <td>
            <?php echo __( 'Action', 'woocommerce-avatax' );?>
        </td>
    </tr>
    </thead>
    <tbody class="custom_field_list">
    <?php 
        if(!empty($field_list)){
            foreach((array)$field_list->company as $field){
                echo '<tr data-field_type="'.$field->field_type.'" data-field_name="'.$field->field_name.'" data-data_type="'.$field->data_type.'" data-field_id="'. $field->field_id. '" data-is_default="'. $field->is_default. '" data-selected="'. $field->selected. '" ">
                 <td>'.($field->field_type == 'company' ? 'Company-level' : 'Customer-level').'</td>
                <td>'.$field->field_name.'</td>
               <td>'.($field->data_type == 'string' ? 'String' : ($field->data_type == 'number' ? "Number" : 'Date')).'</td>
               <td>'. (!$field->is_default ?  ('<a href="javascript:void(0)" class="delete_custom_field button-secondary" data-field-name="'.$field->field_name.'">'.
                __('Delete', 'woocommerce-avatax'). '</a>') : '') .'</td></tr>';
            }
            foreach((array)$field_list->customer as $field){
                echo '<tr data-field_type="'.$field->field_type.'" data-field_name="'.$field->field_name.'" data-data_type="'.$field->data_type.'" data-field_id="'. $field->field_id. '" data-is_default="'. $field->is_default. '" data-selected="'. $field->selected. '" ">
                <td>'.($field->field_type == 'company' ? 'Company-level' : 'Customer-level').'</td>
                <td>'.$field->field_name.'</td>
                <td>'.($field->data_type == 'string' ? 'String' : ($field->data_type == 'number' ? "Number" : 'Date')).'</td>
               <td>'. (!$field->is_default ?  ('<a href="javascript:void(0)" class="delete_custom_field button-secondary" data-field-name="'.$field->field_name.'">'.
                __('Delete', 'woocommerce-avatax'). '</a>') : '') .'</td></tr>';
            }
        }

        ?>
    </tbody>
</table>