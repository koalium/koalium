/**
 * Admin code for dismissing notifications.
 *
 */
function cnb_dismissables_listener() {
    jQuery( '.notice-call-now-button').on( 'click', '.notice-dismiss',
        function() {
        const $notice = jQuery(this).parent('.is-dismissible');
        const dismiss_option = $notice.attr('data-dismiss-option');
        const nonce = $notice.attr('data-dismiss-nonce');
        if ( dismiss_option ) {
            const data = {
                'action': 'cnb_hide_notice',
                '_wpnonce': nonce,
                'dismiss_option': dismiss_option
            };

            jQuery.post(ajaxurl, data)
        }
    });
}

/**
 * Since "Call Now Button" is just a small width too much to fit
 * when there is ALSO counter, we replace it the last bit with "...",
 * so that the line in the menu does not break.
 *
 * @param node object HtmlNode
 * @param hasCounter boolean true if there is content in the counter, false otherwise
 */
function cnb_set_sidenav_counter_text(node, hasCounter) {
    if (!hasCounter && node.text() === 'Call Now Bu...') {
        node.replaceWith('Call Now Button')
    }
    if (hasCounter && node.text() === 'Call Now Button') {
        node.replaceWith('Call Now Bu...')
    }
}

/**
 *
 * @param value string Value for the counter next to the plugin title
 */
function cnb_set_sidenav_counter(value) {
    const counter = jQuery('#cnb-nav-counter');
    counter.text(value)
    cnb_set_sidenav_counter_text(counter.parent().textNodes().first(), !!value)
    counter.toggle(!!value)
}

function cnb_decrease_sidenav_counter() {
    const counter = jQuery('#cnb-nav-counter');
    const counterValue = parseInt(counter.text())
    const newValue = counterValue - 1;
    cnb_set_sidenav_counter(newValue)
}

function cnb_upgrade_notice_dismiss_listener() {
    jQuery('#cnb_is_updated').closest('.notice-call-now-button').on('click', '.notice-dismiss',
        () => {
        cnb_decrease_sidenav_counter();
    })
}

function cnb_welcome_banner_dismiss_listener() {
    jQuery('#welcome-banner').on('click', '.notice-dismiss', () => {
        cnb_decrease_sidenav_counter();
        jQuery('#welcome-banner').remove();
    })
}

/**
 * Add a jQuery extension so it can be used on any jQuery object
 * Copied from https://stackoverflow.com/a/4106957
 */
function cnb_add_jquery_textnodes() {
    jQuery.fn.textNodes = function () {
        return this.contents().filter(function () {
            return (this.nodeType === Node.TEXT_NODE && this.nodeValue.trim() !== "");
        });
    }
}

jQuery(() => {
    cnb_add_jquery_textnodes();
    cnb_dismissables_listener();
    cnb_upgrade_notice_dismiss_listener();
    cnb_welcome_banner_dismiss_listener();
})
