<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\button\CnbButton;

class ActionSettingsFacebook {
    /**
     * @param CnbAction $action
     * @param CnbButton $button
     *
     * @return void
     */
    function render( $action, $button ) {
        wp_enqueue_script(CNB_SLUG . '-action-edit-facebook');
        $this->render_header();
        $this->render_options( $action, $button );
        $this->render_close_header();
    }

    /**
     * NOTE: This function does NOT close its opened tags - that is done via "render_close_header"
     * @return void
     */
    function render_header() {
        ?>
        <tr class="cnb-action-properties cnb-action-properties-FACEBOOK cnb-settings-section cnb-settings-section-facebook">
        <td colspan="2">
        <h3 class="cnb-settings-section-title">Extra Messenger settings</h3>
        <?php
    }

    /**
     * This function closes the tags opened in render_header
     * @return void
     */
    function render_close_header() {
        ?>
        </td>
        </tr>
        <?php
    }

    /**
     * @param CnbAction $action
     * @param CnbButton $button
     *
     * @return void
     */
    function render_options( $action, $button ) {
        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $button->domain->id,
            ),
                admin_url( 'admin.php' ) );

        ?>
        <table class="cnb-settings-section-tables">
            <tr>
                <th scope="row">
                    <label for="cnb-action-facebook-dialog-type">
                    When clicked...
                    </label>
                </th>
                <td>
                    <?php
                    $value = isset( $action->properties ) && isset( $action->properties->{'facebook-dialog-type'} ) && $action->properties->{'facebook-dialog-type'}
                        ? $action->properties->{'facebook-dialog-type'}
                        : '';
                    if ($button->domain->type !== 'PRO') {
                        $value = '';
                    }
                    ?>

                    <select id="cnb-action-facebook-dialog-type"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][facebook-dialog-type]">
                        <option value="" <?php selected( $value, '' ); ?>>...open external Messenger app</option>
                        <option <?php if ($button->domain->type !== 'PRO') { ?>disabled="disabled"<?php } ?> value="widget" <?php selected( $value, 'widget' ); ?>>...open Messenger Chat widget</option>
                    </select>
                    <?php if ( $button->domain->type !== 'PRO' ) { ?>
                        <p class="description">
                            Messenger widget is a <span class="cnb-pro-badge">Pro</span> feature.
                            <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a>.
                        </p>
                    <?php } ?>
                    <?php if ( $button->domain->type === 'PRO' ) { ?>
                        <p class="description cnb-action-facebook-widget">
                            For the Messenger widget (Chat Plugin) to work on your website, there are a few things you need to do:
                        </p>
                        <ol class="ol-decimal description cnb-action-facebook-widget">
                            <li>
                                Whitelist your domain at <a href="https://business.facebook.com/latest/inbox/settings/chat_plugin" target="_blank">https://business.facebook.com/latest/inbox/settings/chat_plugin</a>.
                            </li>
                            <li>Copy the <code>asset_id</code> from the URL (e.g. <code>161246154026360</code>) and enter it in the Page ID field above.</li>
                        </ol>
                    <?php } ?>
                </td>
            </tr>
            <tr class="cnb-action-facebook-widget">
                <th scope="row">
                    <label for="cnb-action-facebook-widget-default-state">Widget starts</label>
                </th>
                <td>
                    <?php $value = isset( $action->properties ) && isset( $action->properties->{'facebook-widget-default-state'} ) && $action->properties->{'facebook-widget-default-state'}
                        ? $action->properties->{'facebook-widget-default-state'}
                        : 'closed';
                    ?>
                    <select id="cnb-action-facebook-widget-default-state"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][facebook-widget-default-state]">
                        <option value="closed" <?php selected( $value, 'closed' ); ?>>Closed</option>
                        <option value="open" <?php selected( $value, 'open' ); ?>>Open</option>
                    </select>
                </td>
            </tr>
            <tr class="cnb-action-facebook-widget">
                <th scope="row">
                    <label for="cnb-action-facebook-widget-app-id">App ID</label>
                </th>
                <td>
                    <?php $value = isset( $action->properties ) && isset( $action->properties->{'facebook-widget-app-id'} ) && $action->properties->{'facebook-widget-app-id'}
                        ? $action->properties->{'facebook-widget-app-id'} : '';
                    ?>
                    <input id="cnb-action-facebook-widget-app-id" type="text"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][facebook-widget-app-id]"
                            value="<?php echo esc_attr( $value ) ?>" placeholder="Optional"/>
                    <p class="description">If you use an App (instead of the Chat Plugin), enter your App ID below. </p>
                    <ol class="description ol-decimal">
                        <li>Go to your app via <a href="https://developers.facebook.com/apps" target="_blank">https://developers.facebook.com/apps</a></li>
                        <li>Select Messenger -> "Settings" (or "Set up") listed under your Products</li>
                        <li>Ensure your Page is listed here (Click "Add or remove Pages" if it is not)</li>
                        <li>Copy your Page ID to the Page ID field above</li>
                        <li>Copy your App ID to the App ID field below</li>
                        <li>Under Settings -> Basic: Whitelist your domain using the "App domains" field</li>

                    </ol>
                </td>
            </tr>
        </table>
        <?php
    }
}
