/**
 *
 * Range of minutes is 0 - 1439:
 * - 0 == midnight
 * - 24*60-1 (1439) = minute before end of day
 *
 * This is the inverse of fromFormattedToMinutes
 *
 * @param {number} time amount of minutes from midnight
 * @returns {string} hh:mm in 24h time notation, padded with 0 were needed
 */
function getFormattedTime(time) {
    let hour = Math.floor(time / 60);
    let minutes = time - (hour * 60);

    if (hour < 10) hour = "0" + hour
    if (hour === 0) hour = "00"

    if (minutes< 10) minutes = "0" + minutes
    if (minutes === 0) minutes = "00"
    return hour + ':' + minutes
}

/**
 * The inverse of getFormattedTime
 *
 * @param {string} time Formatted time string ("16:39")
 * @returns {number} number between 0 (inclusive) and 1440 (exclusive)
 */
function fromFormattedToMinutes(time) {
    const splitTime = time.split(":")
    return (splitTime[0] * 60) + (splitTime[1] * 1)
}

/**
 * This sets the actual <input> elements that are submitted as part of the scheduler
 *
 * Note that "stop" should be later than "start".
 *
 * @param {string} start Formatted 24h time ("13:00")
 * @param {string} stop Formatted 24h time ("14:50")
 */
function cnbUpdateTime(start, stop) {
    const startEle = jQuery('#actions-schedule-start')
    const stopEle = jQuery('#actions-schedule-stop')
    startEle.val(start)
    stopEle.val(stop)

    // This ensures that a rerender is triggered
    startEle.trigger('change')
}

function cnbUpdateColors() {
    const reverse = jQuery('#actions_schedule_outside_hours').prop('checked')
    const headerColor = reverse ? '#b3afaf' : '#3582c4';
    const widgetColor = headerColor === '#b3afaf' ? '#3582c4' : '#b3afaf';
    jQuery('#cnb-schedule-range .ui-widget-header').css({"background-color": headerColor});
    jQuery('#cnb-schedule-range.ui-widget-content').css({"background": widgetColor});
}

let cnb_api_counter = 0;
let cnb_api_in_progress;
function cnbAjaxTimeFormat(start, stop) {
    const local_counter = cnb_api_counter + 1
    cnb_api_counter = local_counter
    const data = {
        'action': 'cnb_time_format',
        'start': start,
        'stop': stop
    };

    if (cnb_api_in_progress) {
        cnb_api_in_progress.abort();
    }
    cnb_api_in_progress = jQuery.post(ajaxurl, data, function(response) {
        // If we have another counter request already, ignore this one
        if (local_counter === cnb_api_counter) {
            cnbUpdateTimeText(response.start, response.stop)
        }
        cnb_api_in_progress = null;
    });
}

function cnbUpdateTimeText(start, stop) {
    const outsideHoursEle = jQuery('#actions_schedule_outside_hours')
    const reverse1 = outsideHoursEle.prop('checked') ? 'Before': 'From';
    const reverse2 = outsideHoursEle.prop('checked') ? 'and after': 'till';

    jQuery( "#cnb-schedule-range-text" ).html( reverse1+" <strong>" + start + "</strong> "+reverse2+" <strong>" + stop + "</strong>")
}

function cnbRangeInverter() {
    jQuery('#actions_schedule_outside_hours').on('change', () => {
        cnbUpdateColors();

        const start = jQuery('#actions-schedule-start').val()
        const stop = jQuery('#actions-schedule-stop').val()
        cnbAjaxTimeFormat(start, stop)
    });
}

function cnbSetupScheduleStartStopSlider(start, stop) {
    jQuery( "#cnb-schedule-range" ).slider({
        range: true,
        min: 0,
        max: 24 * 60, // Do not include midnight, since "min" is inclusive
        values: [ start, stop ],
        step: 15,
        slide: function( event, ui ) {
            const start = getFormattedTime(ui.values[ 0 ])
            let stop = getFormattedTime(ui.values[ 1 ])
            stop = stop !== "24:00" ? stop : "23:59"

            cnbAjaxTimeFormat(start, stop)
            cnbUpdateTime(start, stop)
            cnbUpdateColors()
        }
    });
}

function cnbSetupActionEditScheduler(start, stop) {
    // Set time
    cnbAjaxTimeFormat(getFormattedTime(start), getFormattedTime(stop))
    cnbUpdateTime(getFormattedTime(start), getFormattedTime(stop))
    cnbSetupScheduleStartStopSlider(start, stop)

    // First time setup
    cnbRangeInverter();
    cnbUpdateColors();
}

jQuery( () => {
    // Get the current value
    const startVal = jQuery('#actions-schedule-start').val()
    let stopVal = jQuery('#actions-schedule-stop').val()
    stopVal = stopVal !== "24:00" ? stopVal : "23:59"

    // Set it or use the default
    const start = fromFormattedToMinutes(startVal ? startVal : '08:00');
    const stop = fromFormattedToMinutes(stopVal ? stopVal : '17:00');

    cnbSetupActionEditScheduler(start, stop)
});
