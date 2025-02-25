/**
 * Remove <optgroup label="Manual Offsets"> (and Pacific/Kanton, since that is not supported by date-fns)
 *
 * We DO allow <optgroup label="UTC">, since that is the only UTC variant supported by date-fns.
 */
function cnb_remove_utc_and_utc_offsets() {
    const timezonePicker = jQuery(".cnb_timezone_picker");

    // Remove the "Manual Offsets" optgroup
    // We do it this way instead of by name or "last optgroup", since:
    // - the value "Manual Offset" is translated across WordPress
    // - the order of optgroups might change (so it might not ALWAYS be the last optgroup)
    const manualOffsetOptGroup = timezonePicker
        .find("[value='UTC+0']")
        .parent('optgroup');
    manualOffsetOptGroup.remove();

    // We also specifically do not support "Pacific/Kanton"
    timezonePicker.find("option[value='Pacific/Kanton']").remove();
}

jQuery(() => {
    cnb_remove_utc_and_utc_offsets();
})
