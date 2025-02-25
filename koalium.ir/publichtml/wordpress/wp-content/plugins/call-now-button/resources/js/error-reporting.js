/**
 * Error reporting is optional and disabled by default.
 *
 * It needs to be enabled via Settings in order to take effect.
 */

function cnb_capture_js_errors() {
    cnb_sentry_add_to_head()
    return cnb_sentry_wait()
}

function cnb_sentry_add_to_head() {
    // <script src='https://js.sentry-cdn.com/c88ed2804458402cad2a13537dac603f.min.js' crossorigin="anonymous"></script>
    const s = document.createElement("script")
    s.type = "text/javascript"
    s.async = true
    s.defer = true
    s.crossOrigin = "anonymous"
    s.src = "https://js.sentry-cdn.com/c88ed2804458402cad2a13537dac603f.min.js"
    jQuery("head").append(s)
}

function cnb_sentry_wait() {
    const timeout = 10000 //10 seconds
    const start = Date.now()
    return new Promise(cnb_wait_for_sentry)

    function cnb_wait_for_sentry(resolve, reject) {
        if (window.Sentry && window.Sentry.init)
            resolve(cnb_sentry_onload())
        else if (timeout && (Date.now() - start) >= timeout)
            reject(new Error("window.Sentry not found (after waiting for " + timeout + "ms)"))
        else
            setTimeout(cnb_wait_for_sentry.bind(this, resolve, reject), 30)
    }
}

function cnb_sentry_onload() {
    Sentry.onLoad(function () {
        const data = jQuery('#cnb-data')
        if (data.length) {

            Sentry.init({
                release: data.data('pluginVersion'),
                environment: data.data('wordpressEnvironment'),
            })

            Sentry.setContext("WordPress", {
                version: data.data('wordpressVersion'),
            })
        }
    })
    return true
}

jQuery( function() {
    cnb_capture_js_errors()
        .catch((e) => {
            // Ignore
            console.debug('Could not load Sentry, client side JS errors will not be sent', e)
        })
})
