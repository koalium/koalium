function cnb_templates_init() {
    const templates_data = {
        templates: cnb_templates_data,
        nonce: cnb_templates_ajax_data.nonce,
        ajaxUrl: cnb_templates_ajax_data.ajaxUrl,
        actionTypes: cnb_templates_ajax_data.actionTypes,
        displayModes: cnb_templates_ajax_data.displayModes,
        currentDomain: cnb_templates_ajax_data.currentDomain,
        upgradeLink: cnb_templates_ajax_data.upgradeLink
    }

    window.dispatchEvent(new CustomEvent('cnb-templates-init', {detail: templates_data}));
}

jQuery(() => {
    cnb_templates_init()
});
