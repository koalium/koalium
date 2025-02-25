
function cnb_viber_update_action_value(linkType) {
    const chat = jQuery('.cnb-action-properties-viber-chat')
    const pa_chat = jQuery('.cnb-action-properties-viber-pa-chat')
    const group_invite = jQuery('.cnb-action-properties-viber-group-invite')
    chat.hide()
    pa_chat.hide()
    group_invite.hide()

    // if is visible and changed
    if (linkType.is(':visible')) {
        // Reset appearance
        cnb_action_update_appearance('VIBER')
        if (linkType.val() === 'CHAT' || linkType.val() === 'ADD_NUMBER') {
            cnb_switch_to_intl_input()
            chat.show()
        }

        if (linkType.val() === 'PA_CHAT') {
            pa_chat.show()
        }

        if (linkType.val() === 'GROUP_INVITE' || linkType.val() === 'GROUP2_INVITE') {
            group_invite.show()
        }
    }
}

function cnb_viber_update_action_value_handler() {
    const linkType = jQuery('#cnb-action-properties-viber-link-type')
    linkType.on('change', () => {
        cnb_viber_update_action_value(linkType)
    })

    const select = jQuery('#cnb_action_type')
    select.on('change', () => {
        cnb_viber_update_action_value(linkType)
    })

    // Also trigger onLoad
    cnb_viber_update_action_value(linkType)
}

jQuery(() => {
    cnb_viber_update_action_value_handler()
})
