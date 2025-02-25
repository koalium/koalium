function cnb_action_facebook_is_widget() {
    const ele = jQuery('#cnb-action-facebook-dialog-type')
    return ele.val() === 'widget'
}

/**
 * Whenever the "When clicked..." dialog is updated,
 * we have to update the above "Username" vs "Page ID"
 */
function cnb_action_facebook_update_action_value() {
    if (cnb_get_action_type() !== 'FACEBOOK') return
    const actionValue = jQuery('#cnb_action_value')
    actionValue.text('Username')
    if (cnb_action_facebook_is_widget()) {
        actionValue.text('Page ID')
    }
}

function cnb_action_facebook_update_options() {
    if (cnb_get_action_type() !== 'FACEBOOK') return
    jQuery('.cnb-action-facebook-widget').toggle(cnb_action_facebook_is_widget())
}

function cnb_action_facebook_update_action_value_trigger() {
    jQuery('#cnb-action-facebook-dialog-type').on('change', () => {
        cnb_action_facebook_update_action_value()
        cnb_action_facebook_update_options()
    })
    const select = jQuery('#cnb_action_type')
    select.on('change', () => {
        cnb_action_facebook_update_action_value()
        cnb_action_facebook_update_options()
    })

    cnb_action_facebook_update_action_value()
    cnb_action_facebook_update_options()
}

jQuery(() => {
    cnb_action_facebook_update_action_value_trigger()
})