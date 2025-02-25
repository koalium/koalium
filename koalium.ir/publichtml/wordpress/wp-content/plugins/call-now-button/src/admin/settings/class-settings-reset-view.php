<?php

namespace cnb\admin\settings;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\notices\CnbAdminNotices;

class Settings_Reset_view {
    private function render_success() {
        $success = filter_input( INPUT_GET, 'success', @FILTER_SANITIZE_STRING );
        $noticeHandler = CnbAdminNotices::get_instance();
        if ($success === '1') {
            $noticeHandler->renderSuccess('<p>Settings deleted.</p>');
        }
        if ($success === '2') {
            $noticeHandler->renderSuccess('<p>Settings set to their defaults.</p>');
        }
    }
    function render() {
        $options = get_option('cnb');
        $this->render_success();
        ?>
        <h1>Reset options</h1>
        <form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('cnb_delete_all_settings') ?>
            <input type="hidden" name="action" value="cnb_delete_all_settings">
            <?php submit_button('Reset', 'primary', 'submit', true, array( 'data-cy-reset' => true )) ?>
        </form>

        <h1>Set default options</h1>
        <form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('cnb_set_default_settings') ?>
            <input type="hidden" name="action" value="cnb_set_default_settings">
            <?php submit_button('Reset', 'primary', 'submit', true, array( 'data-cy-set-default-values' => true )) ?>
        </form>

        <h1>Set changelog version</h1>
        <form action="<?php echo esc_attr(admin_url('admin-post.php')); ?>" method="post">
            <?php wp_nonce_field('cnb_set_default_settings') ?>
            <input type="hidden" name="action" value="cnb_set_changelog_version">
            <label>New version:
                <input type="text" data-cy-changelog-version="1" name="changelog_version" value="<?php echo esc_attr($options['changelog_version']) ?>">
            </label>
            <?php submit_button('Reset', 'primary', 'submit', true, array( 'data-cy-set-changelog-version' => true )) ?>
        </form>

        <?php
    }
}
