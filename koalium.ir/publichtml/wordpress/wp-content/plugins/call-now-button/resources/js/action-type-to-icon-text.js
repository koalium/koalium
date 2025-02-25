/**
 * Get the default glyph for a particular action type.
 *
 * This should have the same content as the PHP function cnb_actiontype_to_icontext
 *
 * @param {string} $actionType
 *
 * @returns {string} the default iconText for the given action
 */
function cnbActiontypeToIcontext($actionType) {
    switch ( $actionType ) {
        case 'ANCHOR': return 'anchor';
        case 'CHAT': return 'chat';
        case 'EMAIL': return 'email';
        case 'HOURS': return 'access_time';
        case 'LINK': return 'link';
        case 'MAP': return 'directions';
        case 'SMS': return 'chat';
        case 'WHATSAPP': return 'whatsapp';
        case 'FACEBOOK': return 'facebook_messenger';
        case 'SIGNAL': return 'signal';
        case 'TELEGRAM': return 'telegram';
        case 'IFRAME': return 'open_modal';
        case 'TALLY': return 'call3';
        case 'INTERCOM': return 'intercom';
        case 'SKYPE': return 'skype';
        case 'ZALO': return 'zalo';
        case 'VIBER': return 'viber';
        case 'LINE': return 'line';
        case 'WECHAT': return 'wechat';
        case 'CHAT': return 'conversation';

        case 'PHONE':
        default:
            return 'call';
    }
}

function cnb_trigger_rerender(jEle) {
    jEle.trigger('change')
}
/**
 * Updates the iconText property (and rerenders the preview)
 *
 * @param ele {HTMLElement} the icon clicked
 * @returns {boolean} false, to prevent the page from changing scroll position
 */
function cnb_change_icon_text(ele) {
    const jEle = jQuery(ele)
    const val = jEle.text()

    // this is to update the action-edit icons!
    const findIcontextEle = jEle.closest("[data-icon-text-target]").data('iconTextTarget')
    const cnb_action_icon_text = jQuery('#' + findIcontextEle)
    if (cnb_action_icon_text.length) {
        cnb_action_icon_text.val(val)

        // Since we support multiple icon-type's, we take the correct one from the element's metadata
        const findIcontypeEle = jEle.closest("[data-icon-type-target]").data('iconTypeTarget')
        const cnb_action_icon_type = jQuery('#' + findIcontypeEle)
        cnb_action_icon_type.val(jQuery(ele).data('icon-type'))

        // Force an update to the preview
        cnb_trigger_rerender(cnb_action_icon_text)
    } else {
        // Maybe it's the button-edit multibutton options
        // If so, get the parent and find if the 2 data attributes are set
        const parent = jQuery(ele.parentElement)
        const iconText = parent.data('icon-text')
        const iconType = parent.data('icon-type')
        if (iconText && iconType) {
            const iconTextEle = jQuery('#' + iconText)
            const iconTypeEle = jQuery('#' + iconType)
            iconTypeEle.val(jQuery(ele).data('icon-type'))
            iconTextEle.val(val)
            cnb_trigger_rerender(iconTextEle)
        }
    }
    return false
}

function updateIconText() {
    const type = jQuery('#cnb_action_type').val();
    const iconText = cnbActiontypeToIcontext(type)
    jQuery('#cnb_action_icon_text').val(iconText);

    // Also reset the iconType to default
    jQuery('#cnb_action_icon_type').val('FONT')
}

/**
 * When changing Button/Action type, the examples also need to be updated
 */
function updateIconTextExamples() {
    // Show the examples
    const type = jQuery('#cnb_action_type').val();

    if (type) {
        jQuery('.icon-text-options').hide();
        jQuery('#icon-text-' + type).show();
    }
}

/**
 * Unused for now
 *
 * Shows the advanced view for iconText. Basically - swaps the hidden iconText input to text.
 *
 * @returns {boolean} false, to prevent the page from changing scroll position
 */
function cnb_show_icon_text_advanced(ele) {
    const jEle = jQuery(ele)

    const iconText = jEle.data('icon-text')
    const iconType = jEle.data('icon-type')
    const iconDescription = jEle.data('description')
    if (iconText && iconType) {
        const iconTextEle = jQuery('#' + iconText)
        const iconTypeEle = jQuery('#' + iconType)
        const iconDescriptionEle = jQuery('#' + iconDescription)
        iconTextEle.attr('type', 'text')
        iconTypeEle.attr('type', 'text')
        iconTypeEle.val('FONT_MATERIAL')
        iconDescriptionEle.show()

        cnb_trigger_rerender(iconTextEle)
    }
    jEle.hide()
    return false;
}

function cnb_hightlight_selected_icon_all() {
    const all = jQuery("[data-icon-text-target]")
    all.each(function() {
        const root = jQuery(this)
        const findIcontextEle = root.data('iconTextTarget')
        const findIcontypeEle = root.data('iconTypeTarget')
        const iconTextEle = jQuery('#' + findIcontextEle)
        const iconTypeEle = jQuery('#' + findIcontypeEle)
        cnb_highlight_selected_icon_each(iconTextEle, iconTypeEle, root)
    })
}

function cnb_highlight_selected_icon_each(iconTextEle, iconTypeEle, root) {
    // In case of "DEFAULT", we should match that to FONT
    const iconTextVal = iconTextEle.val()
    const iconTypeVal = iconTypeEle.val() === 'DEFAULT' ? 'FONT' : iconTypeEle.val()

    if (iconTypeVal === 'CUSTOM') return

    const selector = '.cnb-font-icon[data-icon-text="' + iconTextVal + '"][data-icon-type="' + iconTypeVal + '"]'
    const current = jQuery(selector)

    const iconsInSeries = current.closest("[data-icon-text-target]").find('.cnb-font-icon')
    iconsInSeries.parent().removeClass('cnb_icon_active')
    if (current.length) {
        current.parent().addClass('cnb_icon_active')
    }

    // Clear the custom image
    cnb_remove_icon_background_image(root)
}

function cnb_remove_all_icon_highlights(jEle) {
    jEle.find('.cnb_icon_active').removeClass('cnb_icon_active')
}

function cnb_remove_icon_background_image(root) {
    // TODO Find the one that belongs to this "group", instead of wiping all!
    root.find('.cnb_action_icon_background_image').val('')
    root.find('.cnb_action_icon_background_image').attr('value', '')
    root.find('.cnb_selected_action_background_image').css({backgroundImage: '', display: "none" })
}

function initUpdateIconText() {
    // When the "Button/Action type" is changed, we need to update the icon as well as the examples
    jQuery('#cnb_action_type').on('change', function() {
        updateIconText()
        updateIconTextExamples()
        cnb_hightlight_selected_icon_all()
    });

    jQuery('#cnb_action_icon_text').on('input', () => {
        cnb_hightlight_selected_icon_all()
    })

    // Attach on click handler for the custom icons
    jQuery('.cnb-font-icon').on('click', function (event) {
        cnb_change_icon_text(event.target)
        cnb_hightlight_selected_icon_all()
    })
}

jQuery(() => {
    initUpdateIconText()
    updateIconTextExamples()
    cnb_hightlight_selected_icon_all()
})
