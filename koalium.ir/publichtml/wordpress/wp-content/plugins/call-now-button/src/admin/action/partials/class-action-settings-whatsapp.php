<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\button\CnbButton;
use cnb\utils\CnbUtils;

class ActionSettingsWhatsapp {
    /**
     * @param CnbAction $action
     * @param CnbButton $button
     *
     * @return void
     */
    function render( $action, $button ) {
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
        <tr class="cnb-action-properties cnb-action-properties-WHATSAPP cnb-settings-section cnb-settings-section-whatsapp">
        <td colspan="2">
        <h3 class="cnb-settings-section-title">Extra WhatsApp settings</h3>
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
        $cnb_utils = new CnbUtils();

        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $button->domain->id,
            ),
                admin_url( 'admin.php' ) );

        ?>
        <table class="cnb-settings-section-tables">
            <tr class="cnb-action-properties cnb-action-properties-WHATSAPP">
                <th scope="row">
                    <label for="cnb-action-modal">
                    When clicked...
                    </label>
                </th>
                <td class="appearance">
                    <?php $value = isset( $action->properties ) && isset( $action->properties->{'whatsapp-dialog-type'} ) && $action->properties->{'whatsapp-dialog-type'} ? $action->properties->{'whatsapp-dialog-type'} : ''; ?>
                    <select id="cnb-action-modal"                           
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][whatsapp-dialog-type]">
                        <option value="" <?php selected( $value, '' ); ?>>...open external WhatsApp app</option>
                        <option <?php if ( $button->domain->type === 'STARTER' ) { ?>disabled="disabled"<?php } ?> value="popout" <?php selected( $value, 'popout' ); ?>>...open WhatsApp chat widget
                        </option>
                    </select>
                    <?php if ( $button->domain->type === 'STARTER' ) { ?>
                        <p class="description">
                            WhatsApp chat widget is a <span class="cnb-pro-badge">Pro</span> feature.
                            <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a>.
                        </p>

                    <?php } ?>
                </td>
            </tr>
            <!--  TODO: Message template kijkt naar isVisible maar "Extra WhatsApp Settings" zijn nu hidden by default -->
            <tr id="action-properties-message-row" class="cnb-action-properties cnb-action-properties-WHATSAPP">
                <th scope="row"><label for="action-properties-message-whatsapp">Message template <a
                                href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress/buttons/actions/message-template/', 'question-mark', 'message-template' ) ) ?>"
                                target="_blank" class="cnb-nounderscore">
                            <span class="dashicons dashicons-editor-help"></span>
                        </a></label></th>
                <td>
                    <textarea id="action-properties-message-whatsapp"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][properties][message]" class="code"
                                rows="3"
                                placeholder="Optional"><?php if ( isset( $action->properties ) && isset( $action->properties->message ) ) {
                            echo esc_textarea( $action->properties->message );
                        } ?></textarea>
                </td>
            </tr>

            <tr class="cnb-action-properties cnb-action-properties-WHATSAPP cnb-action-properties-whatsapp-modal">
                <th scope="row"><label for="actionWhatsappTitle">Window title</label></th>
                <td>
                    <input id="actionWhatsappTitle" type="text"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][whatsapp-title]"
                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'whatsapp-title'} ) ) {
                                echo esc_attr( $action->properties->{'whatsapp-title'} );
                            } ?>" maxlength="30" placeholder="Optional"/>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-WHATSAPP cnb-action-properties-whatsapp-modal">
                <th scope="row"><label for="actionWhatsappWelcomeMessage">Welcome message</label></th>
                <td>
                    <textarea id="actionWhatsappWelcomeMessage" rows="3"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][properties][whatsapp-welcomeMessage]"
                                placeholder="How can we help?"><?php if ( isset( $action->properties ) && isset( $action->properties->{'whatsapp-welcomeMessage'} ) ) {
                            echo esc_textarea( $action->properties->{'whatsapp-welcomeMessage'} );
                        } ?></textarea>
                    <p class="description">Start a new line by pressing the <code>Enter</code> key. Every line will
                        become its own speech bubble. Speech bubbles appear in sequence with a short pause between them.
                    </p>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-WHATSAPP cnb-action-properties-whatsapp-modal">
                <th scope="row"><label for="cnb-action-show-notification-count">Show notification badge</label></th>
                <td class="appearance">
                    <input type="hidden"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][show-notification-count]"
                            value=""/>
                    <input id="cnb-action-show-notification-count" class="cnb_toggle_checkbox" type="checkbox"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][show-notification-count]"
                            value="true"
                        <?php checked( true, isset( $action->properties ) && isset( $action->properties->{'show-notification-count'} ) && $action->properties->{'show-notification-count'} ); ?> />
                    <label for="cnb-action-show-notification-count" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="cnb-action-show-notification-count"
                            class="cnb_toggle_state cnb_toggle_false">(Off)</span>
                    <span data-cnb_toggle_state_label="cnb-action-show-notification-count"
                            class="cnb_toggle_state cnb_toggle_true">Yes</span>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-WHATSAPP cnb-action-properties-whatsapp-modal">
                <th scope="row"><label for="actionWhatsappPlaceholderMessage">Placeholder visitor input</label></th>
                <td>
                    <input id="actionWhatsappPlaceholderMessage" type="text"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][whatsapp-placeholderMessage]"
                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'whatsapp-placeholderMessage'} ) ) {
                                echo esc_attr( $action->properties->{'whatsapp-placeholderMessage'} );
                            } ?>" placeholder="Type your message"/>
                </td>
            </tr>
        </table>
        <?php
    }
}
