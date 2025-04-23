<script type="text/template" id="tmpl-wc-avatax-confirmation-modal">
    <div class="wc-backbone-modal">
        <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                    <h1>Confirm</h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text">Close modal panel</span>
                    </button>
                </header>
                <article>
                    <p>Check all the data fields before sending to Avalara, as they would impact your e-invoice reporting. Are you sure you want to send these data fields to Avalara?</p>
                </article>
                <footer>
                    <div class="inner">
                        <button class="button button-primary button-large btn-confirm">Ok</button>
                        <button class="button button-large modal-close btn-cancel">Cancel</button>
                    </div>
                </footer>
            </section>
        </div>
    </div>
</script>
<script type="text/template" id="tmpl-wc-avatax-alert-modal">
    <div class="wc-backbone-modal">
        <div class="wc-backbone-modal-content">
            <section class="wc-backbone-modal-main" role="main">
                <header class="wc-backbone-modal-header">
                    <h1>&nbsp;</h1>
                    <button class="modal-close modal-close-link dashicons dashicons-no-alt">
                        <span class="screen-reader-text">Close modal panel</span>
                    </button>
                </header>
                <article>
                    <p><%= message %></p>
                </article>
                <footer>
                    <div class="inner">
                        <button class="button button-primary button-large modal-close close">Ok</button>
                    </div>
                </footer>
            </section>
        </div>
    </div>
</script>