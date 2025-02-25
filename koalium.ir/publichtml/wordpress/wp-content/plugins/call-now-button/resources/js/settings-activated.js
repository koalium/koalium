function cnbConvertTimeZonePickerToAjax() {
    jQuery('#domain_timezone').on('change', () => {
        const data = {
            'action': 'cnb_domain_timezone_change',
            '_wpnonce': jQuery('#_wpnonce').val(),
            'timezone': jQuery('#domain_timezone').val(),
        };

        jQuery.post(ajaxurl, data)
            .done((response) => {
                const html = '<span class="dashicons dashicons-yes"></span> Set to <code>' + response.timezone + '</code>';
                jQuery('#domain_timezone-description').html(html)
            })
            .fail(() => {
                const html = '<span class="dashicons dashicons-warning"></span> Error occured during updating';
                jQuery('#domain_timezone-description').html(html)
            })
    })
}

function cnbHideSettingsUpdatedNotice() {
    const textToFind = 'Your settings have been updated!';
    jQuery('.notice-call-now-button:contains(' + textToFind + ')').hide()
}

jQuery(() => {
    cnbConvertTimeZonePickerToAjax()
    cnbHideSettingsUpdatedNotice()
})
