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
 * Display the user certificate fields.
 *
 * @type array $certificateslist usage types for exemption, as `$code => $label`
 */
?>
<?php
// Get the entity parameter from URL
$entity = isset($_GET['entity']) ? sanitize_text_field($_GET['entity']) : '';
?>
<div id="FieldMapper" >
	<table class="tbl_field_mapper">
		<tbody>
			<tr>
				<td width="75%" colspan="2">
					<div class="divTblType divForm">
						<label><?php echo __("Document type", 'woocommerce-avatax') ?></label>
						<select name="entity_type" id="entity_type">
							<option value="order" <?php selected($entity, 'order'); ?>> <?php echo __("Order", 'woocommerce-avatax') ?> </option>
							<option value="refund" <?php selected($entity, 'refund'); ?>> <?php echo __("Refund", 'woocommerce-avatax') ?> </option>
						</select>
					</div>
				</td>
			</tr>
			<tr>
				<td width="75%" colspan="2">
					<div class="divTblType divForm">
						<label><?php echo __("Table format", 'woocommerce-avatax') ?></label>
						<select name="table_type" id="table_type">
						<option value="flat"> <?php echo __("Flat", 'woocommerce-avatax') ?> </option>
						<option value="eav"> <?php echo __("EAV", 'woocommerce-avatax') ?> </option>
						<option value="vertical"><?php echo __("Vertical", 'woocommerce-avatax') ?></option>
						</select>
						<a href="#"><?php echo __("Learn more about these selections and how to configure them", 'woocommerce-avatax') ?></a>
					</div>
				</td>
			</tr>
			<tr>
				<td width="75%" colspan="2">
					<div class="divForm">
						<label style="display:inline-block;"><?php echo __("Source table", 'woocommerce-avatax') ?>
								<span class="wc-avatax-help-tip" tabindex="0" data-tip="<?php echo __("One of the tables with the relevant data related to the order. <a href='#'> Learn more about the source table </a>", 'woocommerce-avatax') ?>">i</span>
						</label>
						<input type="text" name="main_table" id="main_table" maxlength="255" placeholder="Search source table" >
					</div>
				
					<div class="divForm vertical-fieldset-na">
						<label><?php echo __("Source table column", 'woocommerce-avatax') ?></label>
						<select name="main_table_ref_field" id="main_table_ref_field" placeholder="<?php echo __("Select source table column", 'woocommerce-avatax') ?>" >
							<option value=""><?php echo __("Select source table column", 'woocommerce-avatax') ?></option>
						</select>
					</div>
					<div class="eav-fieldset vertical-fieldset divForm">
						<label><?php echo __("Select column data key", 'woocommerce-avatax') ?></label>
						<select name="eav_key_field" id="eav_key_field">
							<option value=""><?php echo __("Select column data key", 'woocommerce-avatax') ?></option>
						</select>
					</div>
					<div class="eav-fieldset vertical-fieldset divForm">
						<label><?php echo __("Select column data value", 'woocommerce-avatax') ?></label>
						<select name="eav_value_field" id="eav_value_field">
							<option value=""><?php echo __("Select column data value", 'woocommerce-avatax') ?></option>
							
						</select>
					</div>
				
					<div class="divForm vertical-fieldset-na">
						<label><?php echo __("Reference table", 'woocommerce-avatax') ?></label>
						<select name="secondary_table" id="secondary_table" placeholder="<?php echo __("Select reference table", 'woocommerce-avatax') ?>">
						<option value=""><?php echo __("Select reference table", 'woocommerce-avatax') ?></option>
					</select>
					</div>
				
					<div class="divForm vertical-fieldset-na">
						<label><?php echo __("Reference table field", 'woocommerce-avatax') ?></label>
						<select name="secondary_table_ref_field" id="secondary_table_ref_field" placeholder="<?php echo __("Select reference table field", 'woocommerce-avatax') ?>">
							<option value=""><?php echo __("Select reference table field", 'woocommerce-avatax') ?></option>
							
						</select>
					</div>
					<div class="divForm cbx">
						<label><?php echo __("Has multiple records?", 'woocommerce-avatax') ?></label>
						<label style="display: inline-block;"><input type="radio" class="main_table_isarray" name="main_table_isarray" value="on" style="width:auto !important;" > <?php echo __("Yes", 'woocommerce-avatax') ?> </label>
						<label style="display: inline-block;"><input type="radio" class="main_table_isarray" name="main_table_isarray" value="off" style="width:auto !important;" > <?php echo __("No", 'woocommerce-avatax') ?>  </label>
					</div>
				</td>
			</tr>
			<tr>
			<td colspan="2">
			        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
			            <div style="flex: 1;">
			                <label id="mapper_message" style="color:red; display:none;"><?php echo __("", 'woocommerce-avatax') ?></label>
			            </div>
			            <div>
			                <button class="button-primary actionButton" id="btnSubmitMapper" type="submit"><?php echo __("Save", 'woocommerce-avatax') ?></button>
			            </div>
			        </div>
			    </td>
			</tr>
		</tbody>
	</table>
	<!-- <hr style="border-top: 1px solid gray;"> -->
	<table id="tbl_mapper" class="avatax striped">
		<thead>
			<th><?php echo __("Table format", 'woocommerce-avatax') ?></th>
			<th><?php echo __("Source table", 'woocommerce-avatax') ?></th>
			<th><?php echo __("Source table column", 'woocommerce-avatax') ?></th>
			<th><?php echo __("Reference table", 'woocommerce-avatax') ?></th>
			<th><?php echo __("Reference table field", 'woocommerce-avatax') ?></th>
			<th><?php echo __("EAV key", 'woocommerce-avatax') ?></th>
			<th><?php echo __("EAV value", 'woocommerce-avatax') ?></th>
			<th><?php echo __("Has multiple records?", 'woocommerce-avatax') ?></th>
			<th><?php echo __("Action", 'woocommerce-avatax') ?></th>
		</thead>
		<tbody>
			<?php  echo($records)  ?>
		</tbody>
	</table>
	<div class="schemaHeader">
		<input type="search" id="treeNodeSearch" placeholder="Search by data fields">
		<button class="button-primary" id="btnSchemaSelectAll" type="button">
			<span><?php echo __("Select all", 'woocommerce-avatax') ?></span>
		</button>
		<button class="button-primary" id="btnSchemaUnSelectAll" type="button">
			<span><?php echo __("Clear all", 'woocommerce-avatax') ?></span>
		</button>
		<button class="button-primary actionButton" id="btnSchemaSave" type="button">
			<span><?php echo __("Save and send to Avalara", 'woocommerce-avatax') ?></span>
		</button>
	</div>
	<div class="divSchema">
		<ul id="ulSchema"></ul>
	</div>
</div>
