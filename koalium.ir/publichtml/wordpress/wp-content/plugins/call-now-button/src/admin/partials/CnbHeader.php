<?php

namespace cnb;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\notices\CnbAdminNotices;
use cnb\utils\CnbUtils;

class CnbHeader {
    /**
     * @var CnbUtils
     */
    private $utils;

    public function __construct() {
        $this->utils = new CnbUtils();
    }

    public function render() {
        $this->preHeader();
        $this->renderHeader();

        do_action( 'cnb_admin_notices' );

	    if (has_action('cnb_header_name') || has_action('cnb_after_header')) {
		    echo '<h1>';
		    do_action( 'cnb_header_name' );
		    do_action( 'cnb_after_header' );
		    echo '</h1>';
	    }
    }

    private function preHeader() {
        // CSS
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_style( 'wp-components' );
        wp_enqueue_style( CNB_SLUG . '-styling' );

        // JS
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( CNB_SLUG . '-call-now-button' );
        wp_enqueue_script( CNB_SLUG . '-dismiss' );
        if ($this->utils->is_reporting_enabled()) {
            wp_enqueue_script( CNB_SLUG . '-error-reporting' );
        }
    }

    private function renderHeader() {
	    $default_classes = array( 'wrap', 'call-now-button-plugin' );
	    $classes = apply_filters('cnb_header_wrapper_classes', $default_classes);
	    // Convert array to space-separated string and escape
	    $class_string = esc_attr(implode(' ', array_filter($classes)));

	    echo '<div class="' . esc_attr($class_string) . '">'; // This is closed in CnbFooter::render

        $noticeHandler = new CnbHeaderNotices();

        $noticeHandler->get_cloud_notices();
        $cnb_cloud_notifications = $noticeHandler->get_notices();
        $noticeHandler->add_button_is_disabled_notice( $cnb_cloud_notifications );
        $noticeHandler->cnb_button_legacy_enabled_but_no_number_notice( $cnb_cloud_notifications );
        $noticeHandler->warn_about_caching_plugins( $cnb_cloud_notifications );
        $noticeHandler->upgrade_notice( $cnb_cloud_notifications );
        $noticeHandler->cnb_show_advanced( $cnb_cloud_notifications );

        // Add the notifications after updating the cloud
        CnbAdminNotices::get_instance()->notices( $cnb_cloud_notifications );
    }
}
