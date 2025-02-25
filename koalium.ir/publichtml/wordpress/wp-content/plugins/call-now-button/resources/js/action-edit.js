// If the modal is enabled, we need to clear and disable the "Default message"

/**
 * This sets the various WhatsApp Modal fields to appear only when needed.
 *
 * The reason this exists, is because we have fields that are not only part
 * of a certain action_type, but are also dependent on properties within that
 * action_type.
 *
 * It's not pretty or nicely built, but here we are :-).
 *
 * It helps to call this function anytime you update the state of the properties fields,
 * to ensure that nested/dependent (modal) properties are properly shown/hidden.
 */
function cnb_set_action_modal_fields() {
    const ele = jQuery('#cnb-action-modal')

    // messageRow contains just the message (the non-modal message)
    const messageRow = jQuery("#action-properties-message-row")
    const messageEle = jQuery('#action-properties-message-whatsapp')
    const modalElements = jQuery('.cnb-action-properties-whatsapp-modal')

    const isVisible = ele.is(":visible")

    let isModal = false
    if(ele.val() === "popout") {
      isModal = true
    }

    if (!isVisible) {
        messageRow.hide()
        modalElements.hide()
    } else if (isModal) {
        messageEle.attr('disabled', 'disabled')
        messageRow.hide()
        modalElements.show()
    } else {
        messageEle.removeAttr('disabled')
        messageRow.show()
        modalElements.hide()
    }
}

function cnb_clear_default_on_modal() {
    const ele = jQuery('#cnb-action-modal')
    ele.on('click change', () => {
        cnb_set_action_modal_fields()
    })
}

function cnb_refresh_on_action_change() {
    jQuery('#cnb_action_type').on('change', () => {
        cnb_set_action_modal_fields()
    })
}

function cnb_add_sortable_to_action_table() {
    // Only add sortable if the table exists (otherwise ".sortable()" might not even exist
    jQuery('table.cnb_list_actions #the-list').each(function(){
        const ele = jQuery(this)

        // Only set sortable if >1 item in the table
        // And if there are 0 or 1 item, hide the draggable item, to avoid confusion
        const childCount = jQuery('tr', ele).length

        if (childCount > 1) {
            ele.sortable({
                stop: function () {
                    livePreview()
                },
                placeholder: 'ui-state-highlight'
            })
        } else {
            jQuery('.column-draggable', ele.parentElement).hide()
        }
    })
}

function cnb_action_icon_background_image() {
    jQuery('.cnb_action_icon_background_image').on('change input', (event) => {
        const jEle = jQuery(event.target).parent()
        const findIcontypeEle = jEle.data('iconTypeTarget')
        const cnb_action_icon_type = jQuery('#' + findIcontypeEle)
        cnb_action_icon_type.val('CUSTOM')
        // Clear the custom icons
        cnb_remove_all_icon_highlights(jEle)
    })
}

function cnb_init_image_select() {
    jQuery('.cnb_select_image').on('click', (event) => {
        const image_frame = wp.media({
            title: 'Select Image',
            multiple : false,
            library : {
                type : 'image',
            }
        });

        const target = jQuery(event.target).parent()

        image_frame.on('close',function() {
            const selection = image_frame.state().get('selection')
            if (!selection || selection.length === 0) return

            const first = selection.first()
            if (!first) return

            const selected = first.toJSON();
            if (!selected) return

            const url = cnb_get_url_from_selection(selected)

            // Update the real property
            target.find('.cnb_action_icon_background_image').val('url(' + url + ')').trigger('change')
            // Preview window
            target.find('.cnb_selected_action_background_image').css({backgroundImage: 'url(' + url + ')', display: "flex" })
        });

        image_frame.open();
    })
}

/**
 * If a smaller version is available, pick that one to ensure we do not use huge bandwidth-guzzling
 * files, just for a relatively small image.
 *
 * @param selected
 * @returns {string} url of the
 */
function cnb_get_url_from_selection(selected) {
    if (selected && selected.sizes) {
        // Pick thumbnail first, probably the safest one
        if (selected.sizes.thumbnail && selected.sizes.thumbnail.url) {
            return selected.sizes.thumbnail.url
        }
    }
    // Use the original version, just in case we cannot find a smaller size
    return selected.url
}


function cnb_set_whatsapp_title_placeholder() {
  const labelValue = jQuery('#buttonTextField').val()
  jQuery("#actionWhatsappTitle").attr('placeholder',labelValue)
}

function cnb_update_whatsapp_title_placeholder() {
  const ele = jQuery('#buttonTextField')
  ele.on('input change click', function() {
      cnb_set_whatsapp_title_placeholder()
  })
  cnb_set_whatsapp_title_placeholder()
}

jQuery(() => {
    // These 2 set up action handler to process changes on the form
    cnb_clear_default_on_modal()
    cnb_refresh_on_action_change()
    // This ensures that the default state matches the state of the page when it is loaded
    cnb_set_action_modal_fields()
    cnb_add_sortable_to_action_table()
    // Set up the custom image property
    cnb_action_icon_background_image()
    cnb_init_image_select()
    cnb_update_whatsapp_title_placeholder()
})
