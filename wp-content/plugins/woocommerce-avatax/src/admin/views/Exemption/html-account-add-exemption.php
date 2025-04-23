<script type="text/template" id="tmpl-wc-avatax-sync-modal">
   <div class="wc-backbone-modal" >
   		<div class="wc-backbone-modal-content" style="width:50%">
   			<section class="wc-backbone-modal-main" role="main">
   				<header class="wc-backbone-modal-header">
   					<h1>{{{data.title}}}</h1>
   				</header>
   				<article>
   					<div>
   					<table class="form-table">
   						<tbody>
   							<tr>
   								<th style="width:225px;">
   								<label>Select a new state/exposure zone</label>
   								</th>
   								<td>
   									<div style="float:left; margin-right:10px;">
   										<select name="exemption-zone" id="exemption-zone-state" data-bind="options: availableExemptionZones, value: exemptionZone, optionsCaption: optionsCaption">
   											<option value="">Select an Exemption Zone</option>
   											{{{data.exposure_zones}}}
   										</select>
   									</div>
   									<div style="float:left; margin-right:10px;">
   										<input data-userId=<?php echo $userId; ?> data-db-billing-email="<?php echo $db_billing_email; ?>" type="button" class="button button-primary" id="btnProceed" value="Proceed"/>
   									</div>
   								</td>
   							</tr>
   						</tbody>
   					</table>
   
   					<div class="cert-capture-sdk-view" id="divRenderSdk" style="display:none"><div id = "form_container" style="display:none" > </div></div>
   					<button class="button button-primary action primary" id="btnRefreshCertificates"  style="display:none;" data-bind="click: closeModal, i18n: 'Refresh Certificates'">Refresh Certificates</button>
   					</div>
   				</article>
   				<footer>
   					<div class="inner">
   						<button id="btn-close" class="button button-large button-secondary modal-close"><?php esc_html_e( 'Close', 'woocommerce-avatax' ); ?></button>
   					</div>
   				</footer>
   			</section>
   		</div>
   	</div>
   	<div class="wc-backbone-modal-backdrop modal-close"></div>
</script>
