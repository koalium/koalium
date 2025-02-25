jQuery(document).ready(function () {
    set_event_listeneres();
    disply_updates_marks();
});

function disply_updates_marks() {
    var update_count = jQuery('.extensions-list .has-update').length;
    if (update_count) {
        jQuery('li#extensions-panel-tab a').append(' <span class="update-plugins wpcf7r-update-extensions"><span class="plugin-count">' + update_count + '</span></span>');
    }
}

function set_event_listeneres() {
    activate_serial_handler();
    close_promo_box();
    serial_activation_handler();
    extension_deactivate_handler();
    extension_update_handler();
}

function extension_update_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-update', function (e) {
        e.preventDefault();
        $extension = jQuery(this).parents('.promo-box');
        show_extension_loader($extension);
        update_wpcf7r_extension($extension);
    });
}

function activate_serial_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-activate', function (e) {
        e.preventDefault();
        jQuery(this).parents('.promo-box').find('.serial').addClass('open');
    });
}

function close_promo_box() {
    jQuery('.extensions').on('click', '.promo-box .btn-close', function (e) {
        e.preventDefault();
        jQuery(this).parents('.promo-box').find('.serial').removeClass('open');
    });
}

function serial_activation_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-activate-serial', function (e) {
        e.preventDefault();
        $extension = jQuery(this).parents('.promo-box');
        var serial = $extension.find('.serial-number').val();
        if (!serial) {
            $extension.find('.serial-number').addClass('err');
            return false;
        }
        $extension.find('.serial-number').removeClass('err');
        show_extension_loader($extension);
        activate_extension($extension, serial);
    });
}

function extension_deactivate_handler() {
    jQuery('.extensions').on('click', '.promo-box .btn-deactivate', function (e) {
        e.preventDefault();
        $extension = jQuery(this).parents('.promo-box');
        show_extension_loader($extension);
        deactivate_plugin_license($extension);
    });
}

function show_extension_loader($extension) {
    $extension.append('<div class="wpcf7r_loader active"></div>');
}

function deactivate_plugin_license($extension) {
    var extension_name = $extension.data('extension');

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: "deactivate_wpcf7r_extension",
            extension_name: extension_name,
            wpcf7r_nonce: wpcf_get_nonce(),
        },
        success: function (response) {
            console.log(response);
            if (typeof response.error != 'undefined') {
                jQuery('.actions').after('<div class="err">' + response.error + '</div>');
            } else if (typeof response.extension_html != 'undefined') {
                $extension.replaceWith(response.extension_html);
            }
            remove_extension_loader();
        }
    });
}

function remove_extension_loader() {
    jQuery('.wpcf7r_loader').remove();
}

function update_wpcf7r_extension($extension) {
    var extension_name = $extension.data('extension');

    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl,
        data: {
            action: "wpcf7r_extension_update",
            extension_name: extension_name,
            wpcf7r_nonce: wpcf_get_nonce(),
        },
        success: function (response) {
            if (response.extension_html != 'undefined' && response.extension_html) {
                $extension.replaceWith(response.extension_html);
            } else if (typeof response.error != 'undefined' && response.error) {
                jQuery('.actions').after('<div class="err">' + response.error + '</div>');
            }
            remove_extension_loader();
        }
    });
}

function activate_extension($extension, serial) {
    var extension_name = $extension.data('extension');
    
    jQuery.ajax({
        type: "post",
        dataType: "json",
        url: ajaxurl, 
        data: {
            action: "activate_wpcf7r_extension",
            extension_name: extension_name,
            serial: serial,
            wpcf7r_nonce: wpcf_get_nonce(),
        },
        success: function (response) {
            if (response.extension_html != 'undefined' && response.extension_html) {
                $extension.replaceWith(response.extension_html);
                window.location.reload();
            } else if (typeof response.error != 'undefined' && response.error) {
                $extension.find('.err').remove();
                $extension.append('<div class="err">' + response.error + '</div>');
            }

            remove_extension_loader();
        }
    });
}