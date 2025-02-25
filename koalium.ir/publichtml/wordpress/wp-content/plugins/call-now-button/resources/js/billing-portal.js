function cnb_goto_billing_portal() {
    const data = {
        'action': 'cnb_get_billing_portal'
    };

    jQuery.get(ajaxurl, data, function (response) {
        window.open(response.url, '_blank');
    });
    return false
}

function cnb_request_billing_portal() {
    const data = {
        'action': 'cnb_request_billing_portal'
    };

    // Response is a JSON object of type StripeRequestBillingPortalResponse (contains success boolean)
    jQuery.get(ajaxurl, data, function () {
        const result = jQuery('.cnb-request-billing-portal-result');
        result.removeClass('hidden')
    });
    return false
}
