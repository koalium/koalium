function cnb_show_condition_placeholder_action() {
    const optionSelected = jQuery('#cnb_condition_match_type').val()
    let placeholderText
    if(optionSelected === 'SIMPLE') {
        placeholderText = '/blog/'
    } else if(optionSelected === 'EXACT') {
        placeholderText = 'https://www.example.com/sample-page/'
    } else if(optionSelected === 'SUBSTRING') {
        placeholderText = 'category/'
    } else if(optionSelected === 'REGEX') {
        placeholderText = '/(index|about)(\?id=[0-9]+)?$'
    } else if(optionSelected === 'COUNTRY_CODE') {
        placeholderText = '2 letter country code (e.g. NL)'
    }
    jQuery('#cnb_condition_match_value').attr('placeholder', placeholderText)
}

/**
 * Show an example condition in the form field for each of the match types
 */
function cnb_show_condition_placeholder() {
    cnb_show_condition_placeholder_action()
    jQuery('#cnb_condition_match_type').on('change', function () {
        cnb_show_condition_placeholder_action()
    })
}

/**
 *
 * @param {HTMLElement} element
 */
function cnb_condition_type_change_action(element) {
    // Get the select item
    const selected = jQuery(element).find(":selected")
    // Get the new option
    const value = selected.val()

    // Hide all "conditionType"
    jQuery('.conditionType').attr('hidden', 'hidden')

    // Show all "conditionType_TYPE"
    jQuery('.conditionType_' + value).removeAttr('hidden')

    // Ensure the selected item is NOT a hidden item
    // If it is, select the first non-hidden version
    const matchTypeEle = jQuery('#cnb_condition_match_type')
    const selectedMatchType = matchTypeEle.find(":selected")

    if (selectedMatchType.is('[hidden="hidden"]')) {
        const firstNotHidden = matchTypeEle.find('option[hidden!="hidden"]')
        if (firstNotHidden.length) {
            firstNotHidden.first().attr('selected', 'selected')

            // Also clear the Match value, since it's no longer valid
            jQuery('#cnb_condition_match_value').val('')
            cnb_show_condition_placeholder_action()
        }
    }
}

function cnb_condition_type_change_listener() {
    const ele = jQuery('#cnb_condition_condition_type')
    if (!ele.length) {
        return
    }

    ele.on('change', function (element) {
        cnb_condition_type_change_action(element.target)
    })
    cnb_condition_type_change_action(ele[0])
}

/**
 * This calls the admin-ajax action called 'cnb_delete_condition'
 */
function cnb_delete_condition() {
    jQuery('tbody[data-wp-lists="list:cnb_list_condition"]#the-list span.delete a[data-ajax="true"]')
        .on('click', function(){
            // Prep data
            const id = jQuery(this).data('id')
            const bid = jQuery(this).data('bid')
            const data = {
                'action': 'cnb_delete_condition',
                'id': id,
                'bid': bid,
                '_ajax_nonce': jQuery(this).data('wpnonce'),
            }

            // Send remove request
            jQuery.post(ajaxurl, data)
                .done(() => {
                    // Remove container
                    const action_row = jQuery(this).closest('tr')
                    jQuery(action_row).css("background-color", "#ff726f")
                    jQuery(action_row).fadeOut(function() {
                        jQuery(action_row).css("background-color", "")
                        jQuery(action_row).remove()

                        // Special case: if this is the last item, show a "no items" row
                        const remaining_items = jQuery('table.cnb_list_conditions #the-list tr').length
                        if (!remaining_items) {
                            // Add row
                            jQuery('table.cnb_list_conditions #the-list').html('<tr class="no-items"><td class="colspanchange" colspan="5"<p class="cnb_paragraph">You have no display rules set up. This means that your button will show on all pages.</p>' +
                                '<p class="cnb_paragraph">Click the <code>Add display rule</code> button above to limit the appearance. You can freely mix and match rules to meet your requirements.</p></td></tr>')
                        }
                    })
                })

            // Remove ID from Button array
            jQuery('input[name^="conditions['+id+']"').remove()
            return false
        })
}


jQuery( function() {
    cnb_delete_condition()
    cnb_show_condition_placeholder()
    cnb_condition_type_change_listener()
})
