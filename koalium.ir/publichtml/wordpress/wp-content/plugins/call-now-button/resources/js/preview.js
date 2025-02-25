function getCleanDomain() {
    return document.location.hostname
}

function createButtonFromData(formData) {
    let domainType = 'STARTER'
    let domainId = "domain"
    if (formData && formData.domain) {
        domainType = formData.domain.type
        domainId = formData.domain.id
    }

    return {
        "userId": cnb_preview_data.user.id,
        "domains": [
            {
                "id": domainId,
                "user": cnb_preview_data.user.id,
                "type": domainType,
                "name": getCleanDomain()
            }
        ],
        "buttons": [
            {
                "id": "button",
                "domain": getCleanDomain(),
                "domainId": domainId,
                "active": true,
                "name": "Live preview",
                "type": formData.button.type,
                "options": formData.button.options,
                "multiButtonOptions": formData.button.multiButtonOptions,
                "actions": Object.values(formData.actions_ordered),
                "conditions": []
            }
        ],
        "actions": Object.values(formData.actions),
        "conditions": [],
        "options": {
            "debugMode": false,
            "cssLocation": cnb_preview_data.cssLocation + "/css/main.css",
            "apiRoot": cnb_preview_data.apiRoot,
            ...formData.options
        }
    }
}

/**
 * Via https://dev.to/afewminutesofcode/how-to-convert-an-array-into-an-object-in-javascript-25a4
 *
 * NOTE that this does not work for "daysOfWeek", since the proper order of the array is lost.
 */
function convertArrayToObject (array, key) {
    const initialValue = {}
    return array.reduce((obj, item) => {
        return {
            ...obj,
            [item[key]]: item,
        }
    }, initialValue)
}

async function livePreview() {
    const parsedData = jQuery('.cnb-container').serializeAssoc()

    // Find a button via JS (instead of via a form)
    if (typeof cnb_button !== 'undefined') {
        parsedData.button = cnb_button
    }

    // If there is no button at all, fabricate one
    // This might happen when no Button is associated with an action
    if (!parsedData.button) {
        parsedData.button = {
            type: 'SINGLE',
            options: {},
            multiButtonOptions: {},
        }
    }
    if (typeof cnb_options !== 'undefined') {
        parsedData.options = cnb_preview_data.options
    }

    // Ensure it is always visible
    parsedData.button.options.displayMode = 'ALWAYS'

    // This ensures that the scroll option(s) do not affect the preview
    delete parsedData.button.options.scroll

    // Ensure all Actions are visible
    if (typeof cnb_actions !== 'undefined' && cnb_ignore_schedule) {
        cnb_actions = cnb_actions.map((item) => {
            item.schedule.showAlways = true
            return item
        })
    }

    if (!cnb_ignore_schedule) {
        jQuery('#phone-preview').addClass('using-scheduler')
    }

    // This ensures we keep the order in the table
    parsedData.actions_ordered = []
    if (parsedData.actions) {
        parsedData.actions_ordered = Object.values(parsedData.actions).map((item) => item.id)
    }

    // Ensure a Multi button / Buttonbar gets its actions
    if (typeof cnb_actions !== 'undefined') {
        if (parsedData &&
            parsedData.actions
            && ((parsedData.actions[Object.keys(parsedData.actions)[0]]
                    && parsedData.actions[Object.keys(parsedData.actions)[0]].actionType)
                || parsedData.actions.new)) {
            // Editing Multi & Full Button
            parsedData.actions = Object.assign(convertArrayToObject(cnb_actions, 'id'), parsedData.actions)

            // Since we're editing, we need to do this a bit different. Also see 'new' logic below
            parsedData.actions_ordered = Object.values(parsedData.actions).map((item) => item.id)

        } else {
            // Overview Multi & Full Button
            parsedData.actions = convertArrayToObject(cnb_actions, 'id')
        }
    }

    // Ensure a "new" Action (in case of a new SINGLE) gets an ID to work with
    if (parsedData && parsedData.actions && parsedData.actions.new) {
        parsedData.actions.new.id = 'new'
        parsedData.actions_ordered.pop()
        parsedData.actions_ordered.push('new')
    }

    if (typeof cnb_actions !== 'undefined') {
        cnb_actions = cnb_actions.map((item) => {
            item.schedule.showAlways = item.schedule.showAlways || item.schedule.showAlways === 'true'
            item.schedule.daysOfWeek = item.schedule.daysOfWeek.map((daysOfWeek) =>
                daysOfWeek
            )
            return item
        })
    }

    // Fix: Force all booleans for schedule (and force daysOfWeek into array)
    if (!cnb_ignore_schedule && parsedData.action_id && parsedData.actions &&
        parsedData.actions[parsedData.action_id]) {

        const showAlways = parsedData.actions[parsedData.action_id].schedule.showAlways
        parsedData.actions[parsedData.action_id].schedule.showAlways = showAlways !== "false"

        const daysOfWeek = [0, 1, 2, 3, 4, 5, 6]
        let newDaysOfWeek = []
        for (const day in daysOfWeek) {
            const ele = jQuery('#cnb_weekday_' + day)
            newDaysOfWeek[day] = ele.prop('checked')
        }
        parsedData.actions[parsedData.action_id].schedule.daysOfWeek = newDaysOfWeek
    }

    // Fix iconenabled (should be true/false instead of 0/1)
    if (parsedData.action_id && parsedData.actions &&
        parsedData.actions[parsedData.action_id]) {
        const iconEnabled = parsedData.actions[parsedData.action_id].iconEnabled
        parsedData.actions[parsedData.action_id].iconEnabled = iconEnabled !== "0"
    }

    if (typeof cnb_domain !== 'undefined') {
        parsedData.domain = cnb_domain
    }

    // Ensure WhatsApp/Signal works
    if (parsedData.action_id && parsedData.actions &&
        parsedData.actions[parsedData.action_id]) {
        const viberIntlInput = parsedData.actions[parsedData.action_id].actionType === 'VIBER' && (parsedData.actions[parsedData.action_id].properties['viber-link-type'] === 'CHAT' || parsedData.actions[parsedData.action_id].properties['viber-link-type'] === 'ADD_NUMBER')
        if
        (
            parsedData.actions[parsedData.action_id].actionType === 'WHATSAPP' ||
            parsedData.actions[parsedData.action_id].actionType === 'SIGNAL' ||
            viberIntlInput
        ) {
            const input = document.querySelector('#cnb_action_value_input_whatsapp')
            const iti = window.intlTelInputGlobals.getInstance(input)
            const number = iti.getNumber()
            if (number) {
                parsedData.actions[parsedData.action_id].actionValue = number
            }
        }
    }

    // Fix: Filter out LINK and IFRAME actions, then test if the actionValue is a proper URL, otherwise show an error
    for (const actionId in parsedData.actions) {
        const action = parsedData.actions[actionId]
        const result = verifyAction(action)
        if (!result) {
            delete parsedData.actions[actionId]
            parsedData.actions_ordered = parsedData.actions_ordered.filter(item => item !== actionId);
        }
    }

    // Delete old items
    jQuery('.cnb-single.call-now-button').remove()
    jQuery('.cnb-full.call-now-button').remove()
    jQuery('.cnb-multi.call-now-button').remove()
    jQuery('.cnb-dots.call-now-button').remove()
    jQuery('.cnb-message-modal').remove()

    const cnbData = createButtonFromData(parsedData)
    const previewContainer = jQuery('#cnb-button-preview')
    previewContainer.text('')
    if (typeof CNB !== 'undefined') {
        // pass "false" to ensure we do NOT add the client's native observers
        const result = await CNB.render(cnbData, false)

        const isWhatsappEditScreen = parsedData.action_id && parsedData.actions &&
            parsedData.actions[parsedData.action_id] &&
            parsedData.actions[parsedData.action_id].actionType === 'WHATSAPP'

        // If there is a Whatsapp modal, trigger it
        // The "parsedData.action_id" check is to ensure it does not expand on a FULL or MULTI overview page
        // We should also NOT expand if the edit screen for the current type is NOT a WhatsApp edit screen
        // Basically, only expand on a WhatsApp edit screen
        if (isWhatsappEditScreen) {
            const whatsappButton = jQuery('.call-now-button a[data-action-type="WHATSAPP"]')
            if (whatsappButton.length > 0) {
                whatsappButton[0].dispatchEvent(new window.CustomEvent('toggle'))
            }
        }

        // If there is a Multibutton, expand it (test this AFTER the modal, so we only toggle if it isn't ALREADY expanded)
        if (!window.cnbMultiDoNotExpand) {
            const multiButton = jQuery('.cnb-multi.call-now-button:not(.cnb-expand) .cnb-floating-main')
            if (multiButton.length > 0) {
                multiButton[0].dispatchEvent(new window.CustomEvent('toggle'))
            }
        }
        window.cnbMultiDoNotExpand = undefined

        // Move the result into a new special div (if found)
        const button = jQuery('.cnb-single.call-now-button, .cnb-full.call-now-button, .cnb-multi.call-now-button, .cnb-dots.call-now-button').detach()
        if (previewContainer.length > 0) {
            previewContainer.append(button)
        }

        // There are no actions to work with...
        const previewMoment = jQuery('.cnb-preview-moment')
        previewMoment.show()
        if (parsedData.actions && Object.keys(parsedData.actions).length === 0) {
            let message = '<h3 class="cnb_inscreen_notification">Nothing to show yet...</h3><p class="cnb_inscreen_notification">Once you add an Action, a preview of your Button will be shown here..</p>'
            previewContainer.html(message)
            previewMoment.hide()
        } else if (result.length === 0 && !cnb_ignore_schedule) {
            let message = '<h3 class="cnb_inscreen_notification">Nothing to show...</h3><p class="cnb_inscreen_notification">Following your schedule there\'s <strong>nothing to display at the current time</strong>.</p>'
            message += '<p class="cnb_inscreen_notification">You can adjust the day and time at the top of this screen to preview what your visitors will see at the selected time.</p>'
            previewContainer.html(message)
        }

        return result
    }
}

function verifyAction(action) {
    if (action.actionType === 'LINK' || action.actionType === 'IFRAME') {
        try {
            new URL(action.actionValue)
            jQuery('#cnb_action_value_input')[0]?.setCustomValidity('');
            return true;
        } catch (e) {
            jQuery('#cnb_action_value_input')[0]?.setCustomValidity('This action requires a valid URL, including the protocol (http:// or https://).');
            return false
        }
    }
    return true
}

/*** Scheduler: Day and Time selector **/

function updateScheduler(day, hour, minute) {
    const date = new Date()
    date.setHours(hour)
    date.setMinutes(minute)
    date.setSeconds(0)

    // Settings day is weird...
    const currentDay = date.getDay()
    const distance = day - currentDay
    date.setDate(date.getDate() + distance)

    cnb_options.date = date.getTime()

    // Trigger a rerender
    livePreview()
}

function updateSchedulerCall() {
    const day = jQuery('#call-now-button-preview-selector-day').val()
    const hour = jQuery('#call-now-button-preview-selector-hour').val()
    const minute = jQuery('#call-now-button-preview-selector-minute').val()
    updateScheduler(day, hour, minute)

}

function initPreviewDayAndTimeSelector() {
    jQuery('.call-now-button-preview-selector').on('change', () => {
        updateSchedulerCall()
    })
}
/*** END: Scheduler: Day and Time selector **/

function initButtonEdit() {
    jQuery(() => {
        const idElement = jQuery('form.cnb-container :input[name="button[id]"]')
        if (idElement.length > 0 && !idElement.val().trim()) {
            return false
        }

        // Load the required dependencies and render the preview once
        // All refreshes happen inside
        formToJson()
        livePreview()
        jQuery("form.cnb-container :input").on('change input', function() {
            // An input can signal that the MultiButton should stay closed
            window.cnbMultiDoNotExpand = !!jQuery(this).closest("[data-cnb-multi-do-not-expand]").length
            livePreview()
        })
        // No need to call "livePreview", this is done via the ".done()" handler on cnb_delete_action()
        // jQuery('form.cnb-container a[data-ajax="true"]').on('change input', function() {})
    })
}

jQuery(() => {
    // This enables the scheduler (which can be disabled on a per-screen basis)
    window.cnb_ignore_schedule = false

    initButtonEdit()
    initPreviewDayAndTimeSelector()
})
