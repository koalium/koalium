function cnb_warn_if_inactive_on_save() {
    const target = jQuery('#cnb-enable')
    const message = jQuery('#cnb-button-save-inactive-message')
    message.hide()
    const result = !target.is(':checked')
    if (result) {
        message.show()
    }
}

function cnb_warn_if_inactive_on_save_watcher() {
    jQuery('#cnb-enable').on('change', cnb_warn_if_inactive_on_save)
    cnb_warn_if_inactive_on_save()
}

function cnb_warn_if_mobile_only_on_save() {
    const target = jQuery('#button_options_displaymode')
    const message = jQuery('#cnb-button-save-mobile-only-message')
    message.hide()
    const result = target.val()
    if (result === 'MOBILE_ONLY') {
        message.show()
    }
}

function cnb_warn_if_mobile_only_on_save_watcher() {
    jQuery('#button_options_displaymode').on('change', cnb_warn_if_mobile_only_on_save)
    cnb_warn_if_mobile_only_on_save()
}

function cnb_switch_to_tab(tabName) {
    jQuery('[data-tab-name="' + tabName + '"').trigger('click')
}

jQuery(() => {
    cnb_warn_if_inactive_on_save_watcher()
    cnb_warn_if_mobile_only_on_save_watcher()
})
