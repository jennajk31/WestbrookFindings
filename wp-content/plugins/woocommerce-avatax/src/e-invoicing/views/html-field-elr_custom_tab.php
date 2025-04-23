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
<tr class="divSync border_bottom">
		<th colspan="2">
			<button type="button" id="wc_avatax_elr_disconnect" class="button-primary actionButton" ><?php echo __(("Disconnect from " . ($env == 'production' ? 'Production' : 'Sandbox') ), 'woocommerce-avatax') ?></button>
			<button type="button" id="wc_avatax_save" class="button-primary actionButton" ><?php echo __( 'Save', 'woocommerce-avatax' ) ?></button>
		</th>
	</tr>
<tr>
	<th colspan="2">
		<div class="elr_container tabs">
			<nav>
				<a><?php echo __("Custom fields", 'woocommerce-avatax') ?></a>
				<a><?php echo __("Data selector", 'woocommerce-avatax') ?></a>
				<a class="tablinks ConditionalFieldMapper"><?php echo __("Conditional fields", 'woocommerce-avatax') ?></a>
				<a><?php echo __("Preview data", 'woocommerce-avatax') ?></a>
			</nav>
			<div class="content">
				<?php include( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-custom-fields.php' ); ?>
			</div>
			<div class="content">
				<?php 
					$records = wc_avatax()->wc_avatax_elr_utilities()->getMapperTableRows();
					include( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-field-mapper.php' );
				?>
			</div>
			<div class="content">
				<?php 
				$main_table_list = wc_avatax()->wc_avatax_elr_utilities()->getMainTableList();
				$conditionalRecords = wc_avatax()->wc_avatax_elr_utilities()->getConditionalMapperTableRows();

				include( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-conditional-field-mapper.php' ); ?>
			</div>
			<div class="content">
				<?php include( wc_avatax()->get_plugin_path() . '/src/e-invoicing/views/html-elr-preview-data.php' ); ?>
			</div>
		</div>
	</th>
</tr>

