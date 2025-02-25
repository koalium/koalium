/**
 * This handles the ajax call to create (and email) a chat token.
 * @returns {boolean}
 */
function cnb_create_chat() {
    const data = {
        'action': 'cnb_create_chat_token'
    }

    jQuery('.cnb-create-chat-token')
        .attr('disabled', 'disabled')
        .text('Creating your token...')

    jQuery.get(ajaxurl, data, function (response) {
        jQuery('.cnb-chat-token-created').removeClass('hidden')
        jQuery('.cnb-create-chat-token').text('Token created')
        jQuery('.cnb-chat-token-created-token').text(JSON.stringify(response.token))
    })
    return false
}

/**
 * This attaches the AJAX call to request a magic token to the button
 */
function cnb_button_create_chat_action() {
    jQuery('.cnb-create-chat-token').on('click', function () {
        return cnb_create_chat()
    })
}

jQuery(() => {
    cnb_button_create_chat_action()
})
