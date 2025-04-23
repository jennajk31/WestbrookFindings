/*global wc_avatax_admin_misc*/
(function() {
    "use strict";

    /**
     * WooCommerce AvaTax Admin scripts
     *
     * @since 2.6.0
     */
    jQuery(function($) {
        var accounting, ref, ref1, ref2, ref3, wc_avatax_admin, woocommerce_admin, woocommerce_admin_meta_boxes, show_gencert_popup, open_gencert;
        wc_avatax_admin = (ref = window.wc_avatax_admin) != null ? ref : {};
        woocommerce_admin = (ref1 = window.woocommerce_admin) != null ? ref1 : {};
        woocommerce_admin_meta_boxes = (ref2 = window.woocommerce_admin_meta_boxes) != null ? ref2 : {};
        accounting = (ref3 = window.accounting) != null ? ref3 : {};
        var avaTaxTokenStorageKey = 'admin-avatax-token';
        var isCertificateUploadSuccess = false;

        $(document).on('wc_backbone_modal_loaded', function() {
            $('#btnProceed').on('click', function(e) {
                e.preventDefault();
                var tokenInfo = window.localStorage.getItem(avaTaxTokenStorageKey);
                if (tokenInfo === null) {
                    tokenInfo = '{}';
                }
                tokenInfo = JSON.parse(tokenInfo);
                var customerId = $(this).attr('data-db-billing-email');
                var enteredcustomerId = document.getElementById('billing_email').value;
                var exposure_zone = $('#exemption-zone-state').find(":selected").val();
                if (exposure_zone == undefined || exposure_zone == null || exposure_zone == "") {
                    alert(wc_avatax_admin_misc.select_zone);
                    return;
                }
                if (enteredcustomerId == undefined || enteredcustomerId == null || enteredcustomerId == "" || customerId == "") {
                    $('#wc-backbone-modal-dialog .modal-close').trigger('click');
                    alert(wc_avatax_admin_misc.enter_billing_address);
                    return;
                } else if (customerId != document.getElementById('billing_email').value && customerId != "") {
                    let text = wc_avatax_admin_misc.enter_billing_address_different;
                    if (confirm(text) == true) {
                        show_popup(tokenInfo, document.getElementById('billing_email').value);
                    } else {
                        $('#wc-backbone-modal-dialog .modal-close').trigger('click')
                        return;
                    }
                } else {
                    show_popup(tokenInfo, customerId);
                }
            })
        });

        var show_popup = function(tokenInfo, customerId) {
            if (customerId != "" && tokenInfo[customerId] !== void(0) && Date.parse(tokenInfo[customerId].data.expirationDate + "z") > Date.parse(new Date().toISOString())) {
                open_gencert(tokenInfo, tokenInfo[customerId], customerId);
                return;
            }
            // Token has expired (or never existed), so remove it from storage
            delete tokenInfo[customerId];
            show_gencert_popup(tokenInfo, customerId);
        }
        var show_gencert_popup = function(tokenInfo, customerId) {
            var data = {
                action: 'wc_avatax_get_ecommerce_token',
                custid: customerId
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                if (response === 0) {
                    return;
                }
                if (response.code === 200) {
                    open_gencert(tokenInfo, response, customerId);

                } else {
                    alert(wc_avatax_admin_misc.gencert_generic_error)
                }

            });
        }

        open_gencert = function(tokenInfo, response, customerId) {
            GenCert.init(document.getElementById("form_container"), {
                ship_zone: $('#exemption-zone-state').find(":selected").text(),
                token: response.data.token,
                onCertSuccess: onCertificateComplete,
                onUpload: onCertificateComplete,
                primary_color: '#ff6600',
                secondary_color: '#ff6600'
            });
            tokenInfo[customerId] = response;
            // Cache the token in local storage
            window.localStorage.setItem(avaTaxTokenStorageKey, JSON.stringify(tokenInfo));
            GenCert.show();
            $('#divRenderSdk').show();
        }

        function avatax_Block_UI() {
            $("#wc-avatax-block-UI").addClass("wc-avatax-blockUI");
            $("#wc-avatax-block-UI").addClass("blockOverlay");
            $("body").attr("style", "overflow: hidden;");
        }

        function avatax_UnBlock_UI() {
            $("#wc-avatax-block-UI").removeClass("wc-avatax-blockUI");
            $("#wc-avatax-block-UI").removeClass("wc-avatax-blockOverlay");
            $("body").attr("style", "overflow: auto;");
        }

        var onCertificateComplete = function() {
            isCertificateUploadSuccess = true;
            update_caching_transient_for_customer(GenCert.customerNumber);
            update_alternate_id(GenCert.customerNumber);
            $("#btnRefreshCertificates").show();
            $('#btnRefreshCertificates').on('click', function(e) {
                e.preventDefault();
                $('#wc-backbone-modal-dialog .modal-close').trigger('click');
                location.reload();
            });
        }
        var update_caching_transient_for_customer = function(customerNumber) {
            var data = {
                action: 'wc_avatax_update_caching_transient_for_customer',
                customerCode: customerNumber
            };
            jQuery.post(wc_avatax_admin.ajax_url, data, function(response) {
                return;
            });
        }
        var update_alternate_id = function(customerNumber) {
            var data = {
                action: 'wc_avatax_update_alternate_id',
                userId: user_id.value,
                customerCode: customerNumber
            };
            jQuery.post(window.ajaxurl, data, function(response) {
                return;
            });
        }

        $('#overlayinvalidate').on('click', function() {
            $('.invalidatepop').hide();
            $('#overlayinvalidate').hide();
            location.reload();
        });

        $('button.wc-avatax-invite-customer-to-add-certificate').on('click', function(e) {
            e.preventDefault();
            avatax_Block_UI();
            var customerId = $(this).attr('data-db-billing-email');
            if (customerId == undefined || customerId == null || customerId == "") {
                avatax_UnBlock_UI();
                alert(wc_avatax_admin_misc.enter_billing_address_confirmation);
                return;
            }
            var data = {
                userId: $(this).attr('data-userid'),
                action: 'wc_avatax_invite_customer_certificate'
            };
            return jQuery.post(window.ajaxurl, data, function(response) {
                if (response === 0) {
                    avatax_UnBlock_UI();
                    alert(response.message);
                    return;
                }
                if (response.code === 200) {
                    avatax_UnBlock_UI();
                    alert(response.message);
                } else {
                    avatax_UnBlock_UI();
                    alert(response.message);
                }
            });
        });

        $('.wc-avatax-download-certificate').on('click', function(e) {
            e.preventDefault();
            var data = {
                action: 'wc_avatax_download_certificate',
                certid: $(this).attr('cert-id')
            };
            return jQuery.post(window.ajaxurl, data, function(response) {
                if (response.code === 200) {
                    e.preventDefault();
                    downloadBase64File("application/pdf", response.data, "Certificate");
                } else {
                    return;
                }
            })
        });

        function downloadBase64File(contentType, base64Data, fileName) {
            const linkSource = `data:${contentType};base64,${base64Data}`;
            const downloadLink = document.createElement("a");
            downloadLink.href = linkSource;
            downloadLink.download = fileName;
            downloadLink.click();
        }

        $('.wc-avatax-unlink-certificate').on('click', function(e) {
            e.preventDefault();
            var data = {
                certificateId: $(this).attr('cert-id'),
                userId: $(this).attr('user-id'),
                action: 'wc_avatax_unlink_customer_certificate'
            };
            let text = wc_avatax_admin_misc.confirm_invalidate_certificate;
            if (confirm(text) == true) {
                avatax_Block_UI();
                return jQuery.post(window.ajaxurl, data, function(response) {
                    if (response === 0) {
                        avatax_UnBlock_UI();
                        $(".container").css({
                            "overflow-y": "",
                            "height": ""
                        });
                        $('.invalidatepop').show();
                        $('#invalidateMsg').text(response.message)
                        $('#overlayinvalidate').show();
                        return;
                    }
                    if (response.code === 200) {
                        avatax_UnBlock_UI();
                        $(".container").css({
                            "overflow-y": "",
                            "height": ""
                        });
                        $('.invalidatepop').show();
                        $('#invalidateMsg').text(response.message)
                        $('#overlayinvalidate').show();
                    } else {
                        avatax_UnBlock_UI();
                        $(".container").css({
                            "overflow-y": "",
                            "height": ""
                        });
                        $('.invalidatepop').show();
                        $('#invalidateMsg').text(response.message)
                        $('#overlayinvalidate').show();
                    }
                });
            } else {
                return;
            }

        });
    });

}).call(this);
