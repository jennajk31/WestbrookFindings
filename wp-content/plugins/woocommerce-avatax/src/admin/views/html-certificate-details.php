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

<style>
.mycss{
	color: green;
    border:1px solid #000;
    background: #ccc;
    padding: 10px;
}
table{
    border-collapse: collapse;
}
.firstLine td{
    border-bottom: 2px solid black;
    padding:0 15px 0 15px;
}
.allBorder td{
    border-bottom: 2px solid black;
    border-top: 2px solid black;
    font-weight: bold;
    padding:0 15px 0 15px;
}

table.avatax {
    border: 1px solid #c3c4c7;
    box-shadow: 0 1px 1px rgba(0,0,0,.04);
	background: #fff;
}

table.avatax {
    border-spacing: 0;
    width: 100%;
    clear: both;
    margin: 0;
}

table.avatax td, .widefat th {
    color: #50575e;
}
table.avatax td, table.avatax td ol, table.avatax td p, table.avatax td ul {
    font-size: 13px;
    line-height: 1.5em;
}
table.avatax td {
    vertical-align: top;
}
table.avatax td, .widefat th {
    padding: 8px 10px;
}

table.avatax * {
    word-wrap: break-word;
}

table.avatax thead td, table.avatax thead th {
    border-bottom: 1px solid #c3c4c7;
}

.messagepop {
  background-color:#FFFFFF;
  border:1px solid #999999;
  cursor:default;
  display:none;
  margin-top: 15px;
  position:absolute;
  text-align:left;
  width:394px;
  z-index:50;
  padding: 25px 25px 20px;
}

label {
  display: block;
  margin-bottom: 3px;
  padding-left: 15px;
  text-indent: -15px;
}

.messagepop p, .messagepop.div {
  border-bottom: 1px solid #EFEFEF;
  margin: 8px 0;
  padding-bottom: 8px;
}
.ontop {
				z-index: 999;
				width: 100%;
				height: 100%;
				top: 0;
				left: 0;
				display: none;
				position: absolute;				
				background-color: #cccccc;
				color: #aaaaaa;
				opacity: .4;
				filter: alpha(opacity = 50);
			}
      #overlay {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  z-index: 2;
  cursor: pointer;
}
#overlayinvalidate, #overlayinvite {
  position: fixed;
  display: none;
  width: 100%;
  height: 100%;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0,0,0,0.5);
  z-index: 2;
  cursor: pointer;
}
.container {
  left:0;
  right:0;
  top:25%;
  margin-left: auto;
  margin-right: auto;
  position: fixed;
  width: 30%;
  outline: 1px solid black;
  background: white;
  /* overflow-y: scroll;
  height: 400px; */
}
.scrolldiv {
	overflow-y: auto;
	max-height: 300px;
}
/* .wc-avatax-blockUI, .wc-avatax-blockOverlay{
background-image: URL("../../wordpress/wp-content/plugins/woocommerce-avatax/assets/images/Loading_200x200.gif.875006d0f72cbba5fd409866ef94a2ba.gif");
z-index: 1000;
border: medium;
margin: 0px;
padding: 0px;
width: 100%;
height: 100%;
top: 45%;
background: rgb(255, 255, 255);
opacity: 0.6;
cursor: default;
position: absolute;
background-position: center;
background-repeat: no-repeat;
background-size: 100px;
background-color: lightgray;
} */

</style>

    <table class="form-table" width="100%">
        <tbody>
            <tr>
                <th width="20%">
                  <label style="display:inline-block;"><?php esc_html_e( 'Tax Certificates', 'woocommerce-avatax' ); ?></label>
                </th>
                <td width="80%" style="text-align:right; padding-right:0;">
                  <?php if($isAdmin == 'true') : ?>
                    <button class="wc-avatax-invite-customer-to-add-certificate button button-primary wp-element-button" data-userId="<?php echo $userId; ?>" data-db-billing-email="<?php echo $db_billing_email; ?>" type="button" >Invite Customer To Add Certificate</button>
                  <?php endif; ?>
                <button type="submit" id="btnAddExemption" class="button button-primary wp-element-button" >Add Exemption</button>
                </td>
            </tr>
        </tbody>
    </table>

<div class="scrolldiv"> 
<table class="avatax striped">
    <thead>
<tr class=''>  
    <td>State</td>  
    <td>Signed Date</td>  
    <td>Expiration Date</td>  
    <td>Status</td>  
    <td>View</td>  
    <td>Invalidate</td>  
</tr>
</thead>
<?php 
if(empty($certificateslist))
{
    echo "<tr>";
    echo '<td colspan="6" style="text-align: center;">' . "No Certificates found for this customer." . '</td>';
}
else{
    foreach($certificateslist as $certificates){
        $user_id = $certificates['customerCode'];
        $certid=$certificates['id'];
        echo "<tr>";
        echo '<td>' . $certificates['state'] . '</td>';
        echo '<td>' . $certificates['signedDate'] . '</td>';
        echo '<td>' . $certificates['expirationDate'] . '</td>';
        echo '<td>' . $certificates['status'] . '</td>';
        echo "<td><a class=\"wc-avatax-download-certificate\" cert-id=\"$certid\" href=\"\">View Certificate </a></td>";
        echo "<td><a class=\"wc-avatax-unlink-certificate\" cert-id=\"$certid\" user-id=\"$user_id\" href=\"\">Invalidate Certificate </a></td>";
}
}
?>   
</table>
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
<div id="wc-avatax-block-UI"></div>
