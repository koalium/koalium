/**
 * This is used to rewrite the "action" and "action2" of the bulk actions
 * into names that do not conflict with admin{-post}.php
 */
function cnb_rewrite_form_bulk_actions() {
    jQuery('form.cnb_list_event')
        .find('#bulk-action-selector-top, #bulk-action-selector-bottom')
        .each(function() {
            const ele = jQuery(this)
            ele.attr('name', 'bulk-' + ele.attr('name'))
        })
}

jQuery(() => {
    cnb_rewrite_form_bulk_actions()
})
