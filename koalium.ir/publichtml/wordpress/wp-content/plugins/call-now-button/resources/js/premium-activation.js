function cnb_email_activation_reenable_fields(form, showSomethingWentWrong = true) {
    let errorMessage = ''
    if (showSomethingWentWrong) {
        errorMessage = '<h3 class="title">Something went wrong!</h3>' +
            '<p>Something has gone wrong and we do not know why...</p>' +
            '<p>As unlikely as it is, our service might be experiencing issues (check <a href="https://status.callnowbutton.com" target="_blank">our status page</a>).</p>' +
            '<p>If you think you\'ve found a bug, please report it <a href="https://nowbuttons.com/support/contact/contact-support/" target="_blank">here</a>.' +
            '<p>Technical details:</p>'
    }
    const errorDetails = '<p style="color:red"><span class="cnb_email_activation_errors"></span></p>'

    const submitButton = jQuery(form).find('[name="cnb_email_activation_submit"]')
    jQuery(form).find('[name="cnb_email_activation_address"]').removeAttr("disabled")
    submitButton.removeAttr("disabled")
    submitButton.val("Activate")
    jQuery(form).find('.cnb_email_activation_message').html(errorMessage + errorDetails)
}

function cnb_email_activation_taking_too_long(form) {
    const errorMessage = '<h3 class="title">Hmm, that\'s taking a while...</h3>' +
        '<p>This call should not take this long. Please try again in a minute or so.</p>' +
        '<p>As unlikely as it is, our service might be experiencing issues (check <a href="https://status.callnowbutton.com" target="_blank">our status page</a>).</p>' +
        '<p>If you think you\'ve found a bug, please report it <a href="https://nowbuttons.com/support/contact/contact-support/" target="_blank">here</a>.'
    const errorDetails = '<p>Technical details:</p><p style="color:red"><span class="cnb_email_activation_errors"></span></p>'

    const submitButton = jQuery(form).find('[name="cnb_email_activation_submit"]')
    jQuery(form).find('[name="cnb_email_activation_address"]').removeAttr("disabled")
    submitButton.removeAttr("disabled")
    submitButton.val("Activate")
    jQuery(form).find('.cnb_email_activation_message').html(errorMessage + errorDetails)
}

/**
 * This calls the admin-ajax action called 'cnb_email_activation' (function cnb_admin_cnb_email_activation)
 */
function cnb_email_activation(form) {
    const email_address = jQuery(form).find('[name="cnb_email_activation_address"]').val()

    // Prep data
    const data = {
        'action': 'cnb_email_activation',
        'admin_email': email_address
    }

    // Disable the Email and Button fields (reactivate in case of errors)
    const submitButton = jQuery(form).find('[name="cnb_email_activation_submit"]')
    jQuery(form).find('[name="cnb_email_activation_address"]').attr("disabled", "disabled")
    submitButton.attr("disabled", "disabled")
    submitButton.val("Check your e-mail")

    // Clear the error fields
    jQuery(form).find('.cnb_email_activation_message').empty()
    jQuery(form).find('[name="cnb_email_activation_address"]').empty()

    const statusTimeout = 5000
    const fn_is_taking_too_long = () => cnb_email_activation_taking_too_long(form)
    const takingTooLongTimer = setTimeout(fn_is_taking_too_long, statusTimeout)

    // Send remove request
    jQuery.post(ajaxurl, data)
        .done((result) => {
            if (result && result.email) {
                clearTimeout(takingTooLongTimer)
                jQuery(form).find('.cnb_email_activation_message').html('<span class="cnb_check_email_message">Check your inbox for an activation email sent to <strong><span class="cnb_email_activation_address"></span></strong>.</span>')
                jQuery(form).find('span.cnb_email_activation_address').text(result.email)
            }

            if (result && result.errors) {
                clearTimeout(takingTooLongTimer)
                const keys = Object.keys(result.errors)

                let showSomethingWentWrong = true
                if (keys.length === 1 && (keys[0] === 'CNB_EMAIL_INVALID'|| keys[0] === 'CNB_EMAIL_EMPTY')) {
                    // Skip showing the big block with links, since we know exactly what's going on
                    showSomethingWentWrong = false
                }
                cnb_email_activation_reenable_fields(form, showSomethingWentWrong)

                keys.forEach((key) => {
                    // Create Text Nodes to ensure escaping of the content
                    const codeMsg = document.createTextNode(key)
                    const errorMsg = document.createTextNode(result.errors[key])
                    const code = jQuery('<code>').append(codeMsg)
                    jQuery(form).find('.cnb_email_activation_errors').append('<br />', code, ': ', errorMsg)
                })
            }
        })
        .fail((result) => {
            clearTimeout(takingTooLongTimer)
            cnb_email_activation_reenable_fields(form)

            // Create Text Nodes to ensure escaping of the content
            const codeMsg = document.createTextNode(result.status + ' ' + result.statusText)
            const errorMsg = document.createTextNode(result.responseText)
            const code = jQuery('<code>').append(codeMsg)
            jQuery(form).find('.cnb_email_activation_errors').append('<br />', code, ': ', errorMsg)
        })
    return false
}

function cnb_email_activation_init() {
    jQuery('form.cnb_email_activation').on('submit', (event) => {
        const form = jQuery(event.target)
        return cnb_email_activation(form)
    })
}
function cnb_activation_notice_ui() {
    jQuery(".option2-apikey, #option1-email").hide();
    jQuery("#option1-email, #option2-apikey").on("click", () => {
        jQuery(".option2-apikey, #option2-apikey, #option1-email, .option1-email").toggle();
    });
}

jQuery(() => {
    cnb_email_activation_init()
    cnb_activation_notice_ui()
})
