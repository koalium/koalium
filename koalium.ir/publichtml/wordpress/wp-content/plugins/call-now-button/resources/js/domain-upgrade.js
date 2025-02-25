function cnb_domain_upgrade_hide_notice() {
    const cnb_notice = jQuery('.cnb-message');
    const message = jQuery('.cnb-error-message').text();
    if (!message) {
        cnb_notice.hide();
    }
}

function cnb_stripe_hide_message() {
    const cnb_notice = jQuery('.cnb-message')
    cnb_notice.hide()
}

function cnb_stripe_show_message(type = 'success', message = '') {
    const cnb_notice = jQuery('.cnb-message')

    cnb_stripe_hide_message()
    if (message) {
        jQuery('.cnb-error-message').text(message);
        cnb_notice.removeClass('notice-error notice-warning notice-success')
        cnb_notice.addClass('notice notice-' + type)
        cnb_notice.show()
    }
}

/**
 * function for the currency selector on the Upgrade page
 */
function cnb_domain_upgrade_currency() {
    jQuery(".cnb-currency-select").on('click', function () {
        jQuery(".cnb-currency-select").removeClass('nav-tab-active');
        jQuery(".currency-box").removeClass('currency-box-active');
        jQuery(this).addClass("nav-tab-active");
        const currencyType = jQuery(this).attr("data-cnb-currency");
        const currencySelector = 'currency-box-' + currencyType;
        jQuery('.' + currencySelector).addClass('currency-box-active');
    });
}

function cnbShowStarterFeatures() {
  jQuery(".cnb-starter").show();
  jQuery(".cnbShowStarterFeatures").remove();
}

/**
 * Request a Stripe Checkout Session ID for a given domain and a selected plan
 *
 * Used on the Domain upgrade page.
 * @param planId
 */
function cnb_get_checkout(planId) {
    cnb_stripe_show_message('warning', 'Processing your request, please wait...')

    const data = {
        'action': 'cnb_get_checkout',
        'planId': planId,
        'domainId': jQuery('#cnb_domain_id').val()
    };

    jQuery.post(ajaxurl, data, function (response) {
        cnb_goto_checkout(response)
    });
}

/**
 * Request a Stripe Checkout Session ID for a given domain and a selected plan
 *
 * Used on the Domain upgrade page.
 * @param planId
 */
function cnb_get_agency_checkout(planId) {
    cnb_stripe_show_message('warning', 'Processing your agency account request, please wait...')

    const data = {
        'action': 'cnb_get_agency_checkout',
        'planId': planId,
    };

    jQuery.post(ajaxurl, data, function (response) {
        cnb_goto_checkout(response)
    });
}

/**
 * The callback function once an API response for a Stripe Checkout Session ID is received.
 * @param response
 */
function cnb_goto_checkout(response) {
    if (response.status === 'success') {
        cnb_stripe_show_message('success', 'Redirecting you...')
        location.href = response.url
    } else if (response.status === 'error') {
        cnb_stripe_show_message('warning', response.message)
    }
}

/**
 * Countdown timer to be used with coupon codes
 */
function cnb_countdown_timer() {
    const ele = jQuery("#cnb-coupon-expiration-countdown")
    const countDownDate = ele.data("coupon-expiration-time") * 1000;
    if (!countDownDate) return

    const countDownInterval = setInterval(function () {
        const now = new Date().getTime();
        const distance = countDownDate - now;

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60)).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60)).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });
        const seconds = Math.floor((distance % (1000 * 60)) / 1000).toLocaleString('en-US', {
            minimumIntegerDigits: 2,
            useGrouping: false
        });

        ele.text("Coupon expires in " + days + "d " + hours + "h " + minutes + "m " + seconds + "s ")

        if (distance < 0) {
            clearInterval(countDownInterval)
            ele.parent().remove()
        }
    }, 1000);
}

function cnb_show_tally_abandoned_checkout() {
    if (Tally) {
        Tally.loadEmbeds();
    }
}

/**
 * Requires https://github.com/loonywizard/js-confetti
 * Which is injected via PHP: <code>wp_enqueue_script( CNB_SLUG . '-confetti' );</code>
 */
function cnb_confetti() {
    const jsConfetti = new JSConfetti()
    jsConfetti.addConfetti()
}

jQuery(function () {
    cnb_domain_upgrade_hide_notice()
    cnb_domain_upgrade_currency()
    cnb_countdown_timer()
});
