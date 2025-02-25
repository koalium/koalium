function cnb_action_appearance() {
    jQuery('#cnb_action_type').on('change', function (obj) {
        cnb_action_update_appearance(obj.target.value)
    })

    // Setup WHATSAPP integration
    const input = document.querySelector("#cnb_action_value_input_whatsapp")
    if (!input || !window.intlTelInput) {
        return
    }

    const iti = window.intlTelInput(input, {
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.12/js/utils.min.js',
        nationalMode: false,
        separateDialCode: true,
        hiddenInput: 'actionValueWhatsappHidden'
    })

    // here, the index maps to the error code returned from getValidationError - see readme
    const errorMap = [
        'Invalid number',
        'Invalid country code',
        'Too short',
        'Too long',
        'Invalid number']

    const errorMsg = jQuery('#cnb-error-msg')
    const validMsg = jQuery('#cnb-valid-msg')

    const reset = function() {
        input.classList.remove('error')
        errorMsg.html('')
        errorMsg.hide()
        validMsg.hide()
    }

    const onBlur = function() {
        reset()
        if (input.value.trim()) {
            if (iti.isValidNumber()) {
                validMsg.show()
            } else {
                const errorCode = iti.getValidationError()
                if (errorCode < 0) {
                    // Unknown error, ignore for now
                    return
                }
                input.classList.add('error')
                errorMsg.text(errorMap[errorCode])
                errorMsg.show()
            }
        } else {
            // Empty
            reset()
        }
    }

    // on blur: validate
    input.addEventListener('blur', onBlur)

    // on keyup / change flag: reset
    input.addEventListener('change', onBlur)
    input.addEventListener('keyup', onBlur)

    // init
    onBlur()
}

/**
 * Update the screen with Action specific fields
 * @param {string} value PHONE, EMAIL, etc
 */
function cnb_action_update_appearance(value) {
    const valueEle = jQuery('.cnb-action-value')
    const valueTextEle = jQuery('#cnb_action_value_input')
    const valueLabelEle = jQuery('#cnb_action_value')
    const whatsappValueEle = jQuery('#cnb_action_value_input_whatsapp')
    const intlInputLabelEle = jQuery('#cnb_action_value_input_intl_input')

    // Show only properties for this particular Action
    jQuery('.cnb-action-properties').hide()
    jQuery('.cnb-action-properties-' + value).show()

    valueEle.show()
    valueTextEle.prop( 'disabled', false )
    whatsappValueEle.prop( 'disabled', true )

    valueTextEle.removeAttr("required")
    whatsappValueEle.removeAttr("required")

    valueTextEle.prop('type', 'text')

    switch (value) {
        case 'ANCHOR':
            valueLabelEle.text('Page anchor')
            valueTextEle.attr("required", "required")
            break
        case 'EMAIL':
            valueLabelEle.text('E-mail address')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'email')
            break
        case 'LINK':
            valueLabelEle.text('Full URL')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'url')
            break
        case 'MAP':
            valueLabelEle.text('Address')
            valueTextEle.attr("required", "required")
            break
        case 'PHONE':
            valueLabelEle.text('Phone number')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'tel')
            break
        case 'SMS':
            valueLabelEle.text('Phone number')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'tel')
            // SMS has a field conflict with WhatsApp, fix it
            jQuery('#action-properties-message-whatsapp').attr('disabled', true)
            jQuery('#action-properties-message-sms').attr('disabled', false)
            break
        case 'WHATSAPP':
            valueLabelEle.text('WhatsApp number')
            intlInputLabelEle.text('WhatsApp number')
            valueTextEle.prop('type', 'tel')
            cnb_switch_to_intl_input()

            // WhatsApp has a field conflict with SMS, fix it
            jQuery('#action-properties-message-whatsapp').attr('disabled', false)
            jQuery('#action-properties-message-sms').attr('disabled', true)

            // To ensure the modal properties are correct, fix them after revealing all
            cnb_set_action_modal_fields()
            break
        case 'FACEBOOK':
            valueLabelEle.text('Username')
            valueTextEle.attr("required", "required")
            break
        case 'TELEGRAM':
            valueLabelEle.text('Username')
            valueTextEle.attr("required", "required")
            break
        case 'SIGNAL':
            valueLabelEle.text('Signal number')
            intlInputLabelEle.text('Signal number')
            valueTextEle.prop('type', 'tel')
            cnb_switch_to_intl_input()
            break
        case 'IFRAME':
            valueLabelEle.text('Iframe URL')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'url')
            break
        case 'TALLY':
            valueLabelEle.text('Tally Form ID')
            valueTextEle.attr("required", "required")
            break
        case 'INTERCOM':
            valueLabelEle.text('Intercom App ID')
            valueTextEle.attr("required", "required")
            break
        case 'SKYPE':
            // Value is regular phone number by default
            valueLabelEle.text('Skype number / username')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'tel')
            break
        case 'ZALO':
            valueLabelEle.text('Zalo user/group name')
            valueTextEle.attr("required", "required")
            break
        case 'VIBER':
            valueLabelEle.text('Viber Bot URI')
            intlInputLabelEle.text('Phone number')
            valueTextEle.attr("required", "required")
            valueTextEle.prop('type', 'url')
            break
        case 'LINE':
            valueLabelEle.text('Line username')
            valueTextEle.attr("required", "required")
            break
        case 'WECHAT':
            valueLabelEle.text('WeChat username')
            valueTextEle.attr("required", "required")
            break
        case 'CHAT':
            valueLabelEle.text('Action value')
            valueTextEle.removeAttr("required", "required")
            valueEle.hide()
            break
        default:
            valueLabelEle.text('Action value')
            valueTextEle.attr("required", "required")
    }
    cnb_clean_up_advanced_view()
}

function cnb_switch_to_intl_input() {
    // Switch to intl phone number input
    const valueEle = jQuery('.cnb-action-value')
    const valueTextEle = jQuery('#cnb_action_value_input')
    const whatsappValueEle = jQuery('#cnb_action_value_input_whatsapp')

    valueEle.hide()
    valueTextEle.prop( 'disabled', true )
    whatsappValueEle.prop( 'disabled', false )
    whatsappValueEle.attr("required", "required")

    jQuery('.cnb-action-properties-intl-input').show()
}
function cnb_action_update_map_link(element) {
    jQuery(element).prop("href", "https://maps.google.com?q=" + jQuery('#cnb_action_value_input').val())
}

function cnb_action_iframe_modal_height() {
    const value = jQuery('#cnb-action-properties-modal-height-value')
    const unit = jQuery('#cnb-action-properties-modal-height-unit')

    const updateVal = () => {
        let v = parseInt(value.val())
        const u = unit.val() // "px" or "vh"

        // px defaults
        let min = 250
        let max = 1500

        // Update slider val
        if (u === 'vh') {
            min = 20
            max = 100
        }
        value.attr('min', min)
        value.attr('max', max)

        if (v < min) {
            v = min
        }
        if (v > max) {
            v = max
        }

        // Update the slider left/right "helper" value
        jQuery('#cnb-action-properties-modal-height-value-min').text(min)
        jQuery('#cnb-action-properties-modal-height-value-max').text(max)

        // Update the fields that hold the result
        const result = v + u
        jQuery('#cnb-action-properties-modal-height').val(result)
        jQuery('.cnb-action-properties-modal-height-result').text(result)
    }

    value.on('change input', updateVal)
    unit.on('change input', updateVal)

    // Also run on render
    updateVal()
}

function cnb_action_settings_section() {
    jQuery(".cnb-settings-section-table").addClass("cnb-settings-section-collapsed")
    jQuery(".cnb-settings-section-title").on('click', function () {
        const section = jQuery(this).data("cnb-settings-block")
        jQuery(this).find(".dashicons-arrow-right").toggleClass("cnb-rotate-90")
        jQuery(".cnb-settings-section-" + section + " .cnb-settings-section-table").toggleClass("cnb-settings-section-collapsed")
        // To ensure the modal properties are correct, fix them after revealing all
        cnb_set_action_modal_fields()
    });
}

function cnb_get_action_type() {
    return jQuery('#cnb_action_type').val()
}

jQuery( function() {
    cnb_action_appearance()
    cnb_action_update_appearance(cnb_get_action_type())
    cnb_action_iframe_modal_height()
    cnb_action_settings_section()
})
