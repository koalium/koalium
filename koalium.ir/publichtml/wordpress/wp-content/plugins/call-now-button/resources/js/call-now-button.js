/**
 * This is used in the legacyEdit and in ButtonEdit
 */
function cnb_setup_colors() {
	// This "change" options ensures that if a color is changed and a livePreview is available,
	// we update it. Cannot not be done via an ".on('change')", since the wpColorPicker cannot
	// respond to those events
	const options = {
		change: (event) => {
			jQuery(event.target).trigger('cnb-change')
			jQuery(() => {
				if (typeof livePreview !== 'undefined') {
					livePreview()
				}
			})

		}
	}

	// Add color picker
	jQuery('.cnb-color-field').wpColorPicker(options)
}

function cnb_setup_placements() {
	// Reveal additional button placements when clicking "more"
	jQuery("#button-more-placements").on('click', function (e) {
		e.preventDefault()
		jQuery(".cnb-extra-placement").css("display", "block")
		jQuery("#button-more-placements").remove()
	})
}

function cnb_setup_sliders() {
	jQuery('#cnb_slider').on("input change", function() {
		cnb_update_sliders()
	})
	jQuery('#cnb_order_slider').on("input change", function() {
		cnb_update_sliders()
	})
	cnb_update_sliders()
}

function cnb_update_sliders() {
	// Zoom slider - show percentage
	const cnb_slider = document.getElementById("cnb_slider")
	if (cnb_slider && cnb_slider.value) {
		const cnb_slider_value = document.getElementById("cnb_slider_value")
		cnb_slider_value.innerHTML = '(' + Math.round(cnb_slider.value * 100) + '%)'
	}

	// Z-index slider - show steps
	const cnb_order_slider = document.getElementById("cnb_order_slider")
	if (cnb_order_slider && cnb_order_slider.value) {
		const cnb_order_value = document.getElementById("cnb_order_value")
		cnb_order_value.innerHTML = cnb_order_slider.value
	}
}

function cnb_hide_on_show_always() {
	let show_always_checkbox = document.getElementById('actions_schedule_show_always')
	if (show_always_checkbox) {
		if (show_always_checkbox.checked) {
			// Hide all items specific for Scheduler
			jQuery('.cnb_hide_on_show_always').addClass('cnb-settings-disabled')

			// Hide Domain Timezone notice
			jQuery('.cnb-notice-domain-timezone-unsupported').parent('.notice').hide()
		} else {
			// Show all items specific for Scheduler
			jQuery('.cnb_hide_on_show_always').removeClass('cnb-settings-disabled')

			// Show Domain Timezone notice (and move to the correct place)
			const domainTimezoneNotice = jQuery('.cnb-notice-domain-timezone-unsupported').parent('.notice')
			domainTimezoneNotice.show()
			const domainTimezoneNoticePlaceholder = jQuery('#domain-timezone-notice-placeholder')
			if (domainTimezoneNoticePlaceholder.length !== 0) {
				domainTimezoneNotice.insertAfter(domainTimezoneNoticePlaceholder)
			}
		}
	}
	cnb_clean_up_advanced_view()
	return false
}

function cnb_animate_saving() {
	jQuery('.call-now-button-plugin form.cnb-validation #submit').on('click', function (event) {
		// if value is saving, skip...
		if (jQuery(this).prop('value') === 'Saving...') {
			event.preventDefault()
			return
		}
		// Check if the form will actually validate...
		const form = jQuery(this).closest('form')
		const valid = form[0].checkValidity()
		if (valid) {
			jQuery(this).addClass('is-busy')
			jQuery(this).prop('value', 'Saving...')
			jQuery(this).prop('aria-disabled', 'true')
		} else {
			// Clear old notices
			jQuery('.cnb-form-validation-notice').remove()

			const invalidFields = form.find(':invalid')
			// Find tab with error and switch to it if found
			const tabName = invalidFields.first().closest('[data-tab-name]').data('tabName')
			if (tabName) {
				cnb_switch_tab(tabName)
			}
			// Collect all errors and create notification
			invalidFields.each( function(index,node) {
				const inner = jQuery('<p/>')
				const notification = jQuery('<div />', {class: "cnb-form-validation-notice notice notice-warning"}).append(inner)
				const label = node.labels.length > 0 ? node.labels[0].innerText + ': ' : ''
				inner.text(label + node.validationMessage)
				notification.insertBefore(form.find('#submit'))
			})
		}
	})
}
function cnb_setup_toggle_label_clicks() {
	jQuery('.cnb_toggle_state').on( "click", function() {
		const stateLabel = jQuery(this).data('cnb_toggle_state_label')
		jQuery('#' + stateLabel).trigger('click')
	})
}
function cnb_currency_toggle() {
	jQuery('.cnb-currency-toggle-cb').change(
	  function(){
		  jQuery('.cnb-currency-toggle-cb').prop('checked', jQuery(this).is(':checked'))
	      if (jQuery(this).is(':checked')) {
          jQuery('.cnb_currency_active_eur').css('font-weight','normal')
					jQuery('.cnb_currency_active_usd').css('font-weight','bold')
					jQuery('.currency-box-eur').css('display','none')
					jQuery('.currency-box-usd').css('display','flex')
	      } else {
					jQuery('.cnb_currency_active_usd').css('font-weight','normal')
					jQuery('.cnb_currency_active_eur').css('font-weight','bold')
					jQuery('.currency-box-eur').css('display','flex')
					jQuery('.currency-box-usd').css('display','none')
				}
	  });
}


function cnb_hide_edit_action_if_advanced() {
	const element = jQuery('#toplevel_page_call-now-button li.current a')
	if (element.text() === 'Edit action') {
		element.removeAttr('href')
		element.css('cursor', 'default')
	}
}

function cnb_hide_edit_domain_upgrade_if_advanced() {
	const element = jQuery('#toplevel_page_call-now-button li.current a')
	if (element.text() === 'Upgrade domain') {
		element.removeAttr('href')
		element.css('cursor', 'default')
	}
}

function cnb_hide_on_modal() {
	jQuery('.cnb_hide_on_modal').hide()
	jQuery('.cnb_hide_on_modal input').removeAttr('required')
}

/**
 * Used in admin-header.php
 *
 * @param ele HTMLElement
 * @returns {boolean}
 */
function cnb_enable_advanced_view(ele) {
	window.cnb_show_advanced_view_only_set=1
	cnb_clean_up_advanced_view()
	jQuery(ele.parentElement.parentElement).remove()
	return false
}

function cnb_is_advanced_view() {
	return typeof window.cnb_show_advanced_view_only_set !== 'undefined' &&
		window.cnb_show_advanced_view_only_set &&
		window.cnb_show_advanced_view_only_set === 1
}

function show_advanced_view_only() {
	jQuery('.cnb_advanced_view').show()
}

function cnb_clean_up_advanced_view() {
	const advanced_views = jQuery('.cnb_advanced_view')
	advanced_views.hide()
	if(cnb_is_advanced_view()) {
		show_advanced_view_only()
	}
}

function cnb_strip_beta_from_referrer() {
	const referer = jQuery('input[name="_wp_http_referer"]')
	if (referer && referer.val()) {
		referer.val(referer.val().replace(/[?&]beta/, ''))
		referer.val(referer.val().replace(/[?&]api_key=[0-9a-z-]+/, ''))
		referer.val(referer.val().replace(/[?&]api_key_ott=[0-9a-z-]+/, ''))
		referer.val(referer.val().replace(/[?&]cloud_enabled=[0-9]/, ''))
	}
}

/**
 * This calls the admin-ajax action called 'cnb_delete_action'
 */
function cnb_delete_action() {
	jQuery('.cnb-button-edit-action-table tbody[data-wp-lists="list:cnb_list_action"]#the-list span.delete a[data-ajax="true"]')
		.on('click', function(){
		// Prep data
		const id = jQuery(this).data('id')
		const bid = jQuery(this).data('bid')
		const data = {
			'action': 'cnb_delete_action',
			'id': id,
			'bid': bid,
			'_ajax_nonce': jQuery(this).data('wpnonce'),
		}

		// Send remove request
		jQuery.post(ajaxurl, data)
			.done((result) => {
				// Update the global "cnb_actions" variable
				if (result && result.button && result.button.actions) {
					cnb_actions = result.button.actions
					// livePreview is also called again below in case the Ajax call comes back before the fadeOut is done.
					if (typeof livePreview !== 'undefined') {
						livePreview()
					}
				}
			})

		// Remove container
		const action_row = jQuery(this).closest('tr')
		jQuery(action_row).css("background-color", "#ff726f")
		jQuery(action_row).fadeOut(function() {
			jQuery(action_row).css("background-color", "")
			jQuery(action_row).remove()

			// Special case: if this is the last item, show a "no items" row
			const remaining_items = jQuery('.cnb-button-edit-action-table tbody[data-wp-lists="list:cnb_list_action"]#the-list tr').length
			if (!remaining_items) {
				// Add row
				jQuery('.cnb-button-edit-action-table tbody[data-wp-lists="list:cnb_list_action"]#the-list').html('<tr class="no-items"><td class="colspanchange" colspan="4">This button has no actions yet. Let\'s add one!</td></tr>')
			}

			// We call livePreview /again/ (in case the Ajax call comes back before the fadeOut is done).
			if (typeof livePreview !== 'undefined') {
				livePreview()
			}
		})

		// Remove ID from Button array
		jQuery('input[name^="actions['+id+']"').remove()
		return false
	})
}

/**
 * function for the button type selection in the New button modal
 */
function cnb_button_overview_modal() {
	jQuery(".cnb_type_selector_item").on('click', function(){
		jQuery(".cnb_type_selector").removeClass('cnb_type_selector_active')
		jQuery(this).addClass("cnb_type_selector_active")
		const cnbType = jQuery(this).attr("data-cnb-selection")
		jQuery('#button_type').val(cnbType)

		// Special case for the css class for Multi Flower
		jQuery('#button_options_css_classes').val("") // reset css to empty
		if (jQuery(this).hasClass("cnb_type_selector_multi_flower")) {
			jQuery('#button_options_css_classes').val("cnb-multi-flower")
		}
	})

	jQuery(".cnb-button-overview-modal-add-new").on("click", function() {
		setTimeout(function () {
			// Ensure that the hidden value input is not required
			jQuery('#cnb_action_value_input').removeAttr('required')
			jQuery("input[name='button[name]']").trigger("focus")
		})
	})
}

function cnb_button_overview_add_new_click() {
	jQuery(".cnb-button-overview-modal-add-new").trigger("click")
	return false
}

function cnb_init_tabs() {
	jQuery('a.nav-tab').on('click', (e) => {
		e.preventDefault()
		return cnb_switch_tab(jQuery( e.target ).data('tabName'))
	})
}

function cnb_switch_tab(tabName, addToHistory = true) {
	const tab = jQuery('a.nav-tab[data-tab-name][data-tab-name="' + tabName + '"]')
	const tabContent = jQuery('table[data-tab-name][data-tab-name="' + tabName + '"], div[data-tab-name][data-tab-name="' + tabName + '"]')

	// Does tab name exist (if not, don't do anything)
	if (tab.length === 0) return false

	// Hide all tabs
	const otherTabs = jQuery('a.nav-tab[data-tab-name][data-tab-name!="' + tabName + '"]')
	const otherTabsContent = jQuery('table[data-tab-name][data-tab-name!="' + tabName + '"], div[data-tab-name][data-tab-name!="' + tabName + '"]')
	otherTabs.removeClass('nav-tab-active')
	otherTabsContent.hide()

	// Display passed in tab
	tab.addClass('nav-tab-active')
	tabContent.show()

	// If there is an element keeping track of the tab, update it
	jQuery('input[name="tab"]').val(tabName)

	// Push this to URL
	if (addToHistory) {
		const url = new URL(window.location)
		const data = {
			cnb_switch_tab_event: true,
			tab_name: tabName
		}

		url.searchParams.set('tab', tabName)
		window.history.pushState(data, '', url)
	}

	return false
}

function cnb_switch_tab_from_history_listener() {
	window.addEventListener('popstate', (event) => {
		if (event && event.state && event.state.cnb_switch_tab_event && event.state.tab_name) {
			// Switch back but do NOT add this action to the history again to prevent loops
			cnb_switch_tab(event.state.tab_name, false)
		}
	})
}

function cnb_hide_add_new_on_error() {
	// Find an error box - if that exists, remove the "Add new" macro
	if (jQuery('.cnb-remove-add-new').length) {
		jQuery("li.toplevel_page_call-now-button li:contains('Add New') a").hide()
	}
}

function cnb_setup_pricing() {
	// Find the elements
	const elements = jQuery('.eur-per-year, .usd-per-year, .eur-per-month, .usd-per-month, .eur-discount, .usd-discount, .eur-trial-period-days, .usd-trial-period-days')

	// If there are elements, find the pricing (ajax call)
	if (elements.length) {
		const data = {
			'action': 'cnb_get_plans',
		}
		jQuery.post(ajaxurl, data)
			.done((result) => {
				// Fix the elements
				result['eur_per_year'] = (Math.round(result['eur_per_year'] * 100) / 100).toFixed(2)
				result['usd_per_year'] = (Math.round(result['usd_per_year'] * 100) / 100).toFixed(2)
				result['eur_per_month'] = parseFloat(result['eur_per_month']).toFixed(2)
				result['usd_per_month'] = parseFloat(result['usd_per_month']).toFixed(2)
				result['eur_discount'] = Math.ceil(parseFloat(result['eur_discount']))
				result['usd_discount'] = Math.ceil(parseFloat(result['usd_discount']))
				result['eur_trial_period_days'] = Math.ceil(parseFloat(result['eur_trial_period_days']))
				result['usd_trial_period_days'] = Math.ceil(parseFloat(result['usd_trial_period_days']))

				jQuery('.eur-per-year').text(result['eur_per_year'])
				jQuery('.usd-per-year').text(result['usd_per_year'])
				jQuery('.eur-per-month').text(result['eur_per_month'])
				jQuery('.usd-per-month').text(result['usd_per_month'])
				jQuery('.eur-discount').text(result['eur_discount'])
				jQuery('.usd-discount').text(result['usd_discount'])
				if (result['eur_trial_period_days'] > 0) {
					jQuery('.eur-trial-period-days').text(result['eur_trial_period_days'])
					jQuery('.eur-trial-period-days-wrapper').removeClass('eur-trial-period-days-wrapper')
				}
				if (result['usd_trial_period_days'] > 0) {
					jQuery('.usd-trial-period-days').text(result['usd_trial_period_days'])
					jQuery('.usd-trial-period-days-wrapper').removeClass('usd-trial-period-days-wrapper')
				}
			})
	}
}

/**
 * Slide switcher to show content in slides
 *
 */
function cnb_slide_switcher() {
	jQuery('.cnb-slide-next').on('click',function() {
	  var currentSlide = jQuery('.cnb-slide-active')
	  var nextSlide = currentSlide.next()

	  if(nextSlide.length) {
	    currentSlide.removeClass('cnb-slide-active').css('z-index',-10)
	    nextSlide.addClass('cnb-slide-active').css('z-index',10)
	  } else {
			currentSlide.removeClass('cnb-slide-active').css('z-index',-10)
	    jQuery('.cnb-slide1').addClass('cnb-slide-active').css('z-index',10)
		}
	});

	jQuery('.cnb-slide-prev').on('click', function() {
	  var currentSlide = jQuery('.cnb-slide-active')
	  var prevSlide = currentSlide.prev()

	  if(prevSlide.length) {
	    currentSlide.removeClass('cnb-slide-active').css('z-index',-10)
	    prevSlide.addClass('cnb-slide-active').css('z-index',10)
	  } else {
			currentSlide.removeClass('cnb-slide-active').css('z-index',-10)
	    jQuery('.cnb-slide8').addClass('cnb-slide-active').css('z-index',10)
		}
	})
}

jQuery( function() {
	// Generic
	cnb_setup_colors()
	cnb_setup_placements()
	cnb_setup_sliders()
	cnb_hide_on_show_always()
	cnb_hide_edit_action_if_advanced()
	cnb_hide_edit_domain_upgrade_if_advanced()
	cnb_strip_beta_from_referrer()
	cnb_animate_saving()
	cnb_setup_toggle_label_clicks()
	cnb_currency_toggle()
	cnb_switch_tab_from_history_listener()

	// Allow for tab switching to be dynamic
	cnb_init_tabs()

	cnb_clean_up_advanced_view()

	// This needs to go AFTER the "advanced_view" check so a modal does not get additional (unneeded) "advanced" items
	if (typeof cnb_hide_on_modal_set !== 'undefined' && cnb_hide_on_modal_set === 1) {
		cnb_hide_on_modal()
	}

	// page: button-edit (conditions tabs)

	cnb_delete_action()
	cnb_button_overview_modal()

	cnb_hide_add_new_on_error()

	cnb_slide_switcher()
	cnb_setup_pricing()
})
