jQuery(function($) {

    'use-strict';
    /**
     * WooCommerce AvaTax FrontEnd scripts
     *
     * @since 2.6.0
     */

    var wc_avatax_frontend = window.wc_avatax_frontend;
    var wc_avatax_frontend_misc = window.wc_avatax_frontend_misc;
    var accounting, ref, ref1, ref2, ref3, wc_avatax_admin, woocommerce_admin, woocommerce_admin_meta_boxes, show_gencert_popup, open_gencert, onCertificateComplete, update_alternate_id, update_caching_transient_for_customer;
    wc_avatax_admin = (ref = window.wc_avatax_admin) != null ? ref : {};
    woocommerce_admin = (ref1 = window.woocommerce_admin) != null ? ref1 : {};
    woocommerce_admin_meta_boxes = (ref2 = window.woocommerce_admin_meta_boxes) != null ? ref2 : {};
    accounting = (ref3 = window.accounting) != null ? ref3 : {};
    var avaTaxTokenStorageKey = 'admin-avatax-token';
    var isCertificateUploadSuccess = false;


	$('.wc-avatax-download-certificate').on('click', function (e) {
		e.preventDefault();
		var data = {
			action: 'wc_avatax_download_certificate',
			certid: $(this).attr('cert-id')
		};
		return jQuery.post(wc_avatax_frontend.ajax_url, data, function (response) {
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

    $('#btnAddExemption').on('click', function() {
        $(".container").css({
            "overflow-y": "",
            "height": ""
        });
        $('.pop').show();
        $('#overlay').show();
        $("#exemption-zone-state").select2();
        return false;
    });

    $('.close').on('click', function() {
        deselect($('#contact'));
        return false;
    });

    $('#cert_link').on('click', function() {
        $(".container").css({
            "overflow-y": "",
            "height": ""
        });
        $('.pop').show();
        $('#overlay').show();
        $("#exemption-zone-state").select2();
        return false;
    });

    $('#btnProceed').on('click', function(e) {
        e.preventDefault();
        var tokenInfo = window.localStorage.getItem(avaTaxTokenStorageKey);
        if (tokenInfo === null) {
            tokenInfo = '{}';
        }
        tokenInfo = JSON.parse(tokenInfo);
        var exposure_zone = $('#exemption-zone-state').find(":selected").val();
        if (exposure_zone == undefined || exposure_zone == null || exposure_zone == "") {
            alert(wc_avatax_frontend_misc.select_zone);
            return;
        }
        var customerId = "";
    
        if (wc_avatax_frontend_misc.is_checkout_block) {
            customerId = (document.getElementById('email') != "" && document.getElementById('email') != null) ? document.getElementById('email').value : "";
        }
        else {
            customerId = (document.getElementById('billing_email') != "" && document.getElementById('billing_email') != null) ? document.getElementById('billing_email').value : "";
        }
        if (customerId == "") {
            if (wc_avatax_frontend.is_checkout) {
                let text = wc_avatax_frontend_misc.enter_billing_address;
                alert(text);
                return;
            } else {
                customerId = wc_avatax_frontend_misc.user_email;
            }
        }
        if (tokenInfo[customerId] !== void(0) && tokenInfo[customerId].data.expirationDate > new Date().toISOString()) {
            open_gencert(tokenInfo, tokenInfo[customerId], customerId);
            return;
        }
        // Token has expired (or never existed), so remove it from storage
        delete tokenInfo[customerId];
        show_gencert_popup(tokenInfo, customerId);
        return false;
    });

    $('#btnWcClose').on('click', function(e) {
        e.preventDefault();
        GenCert.__reset();
        clearForm(".pop");
        $('.pop').hide();
        $('#overlay').hide();
        if (isCertificateUploadSuccess) {
            if (wc_avatax_frontend.is_checkout) {
                $(document.body).trigger('update_checkout');
            } else {
                location.reload();
            }
        }
    });


    $('#overlay').on('click', function() {
        GenCert.__reset();
        clearForm(".pop");
        $('.pop').hide();
        $('#overlay').hide();
        if (isCertificateUploadSuccess) {
            if (wc_avatax_frontend.is_checkout) {
                $(document.body).trigger('update_checkout');
            } else {
                location.reload();
            }
        }
    });

    $('#overlayinvalidate').on('click', function() {
        $('.invalidatepop').hide();
        $('#overlayinvalidate').hide();
        location.reload();
    });

    function clearForm(form) {
        $(":input", form).each(function() {
            var type = this.type;
            var tag = this.tagName.toLowerCase();
            if (type == 'select-one') {
                this.value = "";
            }
        });
    };


    show_gencert_popup = function(tokenInfo, customerId) {
        var data = {
            action: 'wc_avatax_get_ecommerce_token',
            custid: customerId
        };
        jQuery.post(wc_avatax_frontend.ajax_url, data, function(response) {
            if (response === 0) {
                return;
            }
            if (response.code === 200) {
                open_gencert(tokenInfo, response, customerId);

            } else {
                alert(wc_avatax_frontend_misc.gencert_generic_error)
            }

        });
    }
    open_gencert = function(tokenInfo, response, customerId) {
        GenCert.init(document.getElementById("form_container"), {
            ship_zone: $('#exemption-zone-state').find(":selected").text(),
            token: response.data.token,
            onCertSuccess: onCertificateComplete,
            onUpload: onCertificateComplete,
            //Customize colors of buttons
            primary_color: '#ff6600',
            secondary_color: '#ff6600'
        });
        tokenInfo[customerId] = response;
        // Cache the token in local storage
        window.localStorage.setItem(avaTaxTokenStorageKey, JSON.stringify(tokenInfo));
        GenCert.show();
        $('#divRenderSdk').show();
    };

    onCertificateComplete = function onCertificateComplete() {
        isCertificateUploadSuccess = true;
        update_caching_transient_for_customer(GenCert.customerNumber);
        update_alternate_id(GenCert.customerNumber);
        $("#btnRefreshCertificates").show();
        $('#btnRefreshCertificates').on('click', function(e) {
            e.preventDefault();
            location.reload();
        });
    }
    update_caching_transient_for_customer = function(customerNumber) {
        var data = {
            action: 'wc_avatax_update_caching_transient_for_customer',
            customerCode: customerNumber
        };
        jQuery.post(wc_avatax_frontend.ajax_url, data, function(response) {
            return;
        });
    }
    update_alternate_id = function(customerNumber) {
        var userId = wc_avatax_frontend_misc.user_id;
        var data = {
            action: 'wc_avatax_update_alternate_id',
            customerCode: customerNumber,
            userId: userId
        };
        jQuery.post(wc_avatax_frontend.ajax_url, data, function(response) {
            return;
        });
    }

    $('.wc-avatax-unlink-certificate').on('click', function(e) {
        e.preventDefault();
        var data = {
            certificateId: $(this).attr('cert-id'),
            userId: $(this).attr('customer-code'),
            action: 'wc_avatax_unlink_customer_certificate'
        };
        let text = wc_avatax_frontend_misc.confirm_invalidate_certificate;
        if (confirm(text) == true) {
            return jQuery.post(wc_avatax_frontend.ajax_url, data, function(response) {
                if (response.code === 200) {
                    $(".container").css({
                        "overflow-y": "",
                        "height": ""
                    });
                    $('.invalidatepop').show();
                    $('#invalidateMsg').text(response.message)
                    $('#overlayinvalidate').show();
                    return false;
                } else {
                    $(".container").css({
                        "overflow-y": "",
                        "height": ""
                    });
                    $('.invalidatepop').show();
                    $('#invalidateMsg').text(response.message)
                    $('#overlayinvalidate').show();
                    return false;
                }
            });
        } else {
            return;
        }

    });

    $(document).on('change', 'form.woocommerce-checkout #billing_email', function() {
        if (wc_avatax_frontend.is_checkout) {
            $(document.body).trigger('update_checkout');
        }
    });
});
