jQuery(document).ready(function($) {
    // Add event listener to AvaTax Deactivate buttons on the plugins page
    var DeactivateAlertView = Backbone.View.extend({
        // Define the template using underscore.js template syntax
        template: _.template(jQuery('#tmpl-wc-avatax-disconnect-confirmation').html()),
        events: {
            'click #deactivate-woocommerce-com-woocommerce-avatax': 'showModal'
        },
    
        initialize: function(options) {
            // Delegate events for dynamically added modal
            $('body').on('click', '.disconnect-confirm', _.bind(this.handleConfirm, this));
            $('body').on('click', '.modal-close, .modal-close-link', _.bind(this.closeModal, this));
            $('body').on('click', '.wc-backbone-modal', _.bind(this.handleOutsideClick, this));
        },
    
        showModal: function(e) {
            e.preventDefault();
            this.deactivateUrl = $(e.currentTarget).attr('href');
            
            $('body')
                .append(this.template())
                .addClass('modal-open');
        },
    
        handleConfirm: function(e) {
            e.preventDefault();
            if (this.deactivateUrl) {
                window.location.href = this.deactivateUrl;
            }
        },
    
        closeModal: function(e) {
            if (e) {
                e.preventDefault();
            }
            $('.wc-backbone-modal').remove();
            $('body').removeClass('modal-open');
        },
    
        handleOutsideClick: function(e) {
            if ($(e.target).hasClass('wc-backbone-modal')) {
                this.closeModal();
            }
        },
    
        // Clean up event handlers when view is removed
        remove: function() {
            $('body').off('click', '.disconnect-confirm');
            $('body').off('click', '.modal-close, .modal-close-link');
            $('body').off('click', '.wc-backbone-modal');
            Backbone.View.prototype.remove.call(this);
        }
    });
    
    var deactivateAlert = new DeactivateAlertView({
        el: '.deactivate'
    });
});