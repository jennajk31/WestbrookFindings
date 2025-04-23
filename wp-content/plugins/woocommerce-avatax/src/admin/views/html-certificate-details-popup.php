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

<div class="messagepop pop container" id="pop">
   <div class="exemption-zone-select-view fieldset woocommerce"  style="float:left; width:100%; display:block; padding: 0px;">
      <div class="field">
         <h3>Exemption </h3>
         <hr>
      </div>
      <form>
         <div class="field form-row form-row-first" style="margin-right: 20px;">
            <label>Select a new state/exposure zone</label>
            <select class="select2" name="exemption-zone" id="exemption-zone-state" data-bind="options: availableExemptionZones, value: exemptionZone, optionsCaption: optionsCaption">
               <option value="">Select an Exemption Zone</option>
               <?php echo $exposure_zones?>
            </select>
         </div>
         <div class="field form-row form-row-last" style="float: left;">
            <label>&nbsp;</label>
            <button type="submit" class="button button-primary wp-element-button" style="margin-top:0px;" id="btnProceed">Proceed</button>
         </div>
      </form>
   </div>
   <div class="cert-capture-sdk-view" id="divRenderSdk" style="display:none; float:left; width:100%;">
      <hr>
      <div id = "form_container" style="display:none" > </div>
   </div>
   <div class="field form-row" style="float: right;">
      <label>&nbsp;</label>
      <button class="button button-primary wp-element-button" style="margin-top:0px; display:none;" id="btnRefreshCertificates">Refresh Certificates</button>
      <button class="button button-primary wp-element-button" style="margin-top:0px;" id="btnWcClose">Close</button>
   </div>
</div>
<div id="overlay"></div>

<div class="messagepop invalidatepop container">
   <div class="invalidate-view fieldset">
      <div class="field">
         <p id="invalidateMsg">message</p>
      </div>
   </div>
</div>
<div id="overlayinvalidate"></div>

