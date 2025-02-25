/**
 * When the "open" colors for the multi button change, we want to reset the "close" colors.
 *
 * The close can be changed in "advanced mode", but under normal circumstances, when a user changes
 * the primary (open) colors, the expanded (close) colors should match that
 */
function cnb_change_icon_color() {
    jQuery('#button-multiButtonOptions-iconBackgroundColorOpen').on('cnb-change', () => {
        jQuery('#button-multiButtonOptions-iconBackgroundColorClose').val('').change()
    })
    jQuery('#button-multiButtonOptions-iconColorOpen').on('cnb-change', () => {
        jQuery('#button-multiButtonOptions-iconColorClose').val('').change()
    })
}

jQuery(() => {
    cnb_change_icon_color()
})
