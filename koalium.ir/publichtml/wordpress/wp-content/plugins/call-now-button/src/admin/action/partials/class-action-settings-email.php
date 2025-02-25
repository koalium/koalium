<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class ActionSettingsEmail {

    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render( $action ) {
        $cnb_utils = new CnbUtils();

        ?>
        <tr class="cnb-action-properties cnb-action-properties-EMAIL cnb-settings-section cnb-settings-section-email">
            <td colspan="2">
                <h3 class="cnb-settings-section-title" data-cnb-settings-block="email"><span
                            class="dashicons dashicons-arrow-right"></span> Extra email settings</h3>

                <table class="cnb-settings-section-table">
                    <tr>
                        <th scope="row"><label for="action-properties-subject">Subject</label></th>
                        <td><input placeholder="Optional" id="action-properties-subject"
                                    name="actions[<?php echo esc_attr( $action->id ) ?>][properties][subject]"
                                    type="text"
                                    value="<?php if ( isset( $action->properties ) && isset( $action->properties->subject ) ) {
                                        echo esc_attr( $action->properties->subject );
                                    } ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="action-properties-body">Message template <a
                                        href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress/buttons/message-template/', 'question-mark', 'message-template' ) ) ?>"
                                        target="_blank" class="cnb-nounderscore">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </a></label></th>
                        <td><textarea placeholder="Optional" id="action-properties-body"
                                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][body]"
                                        class="large-text code"
                                        rows="3"><?php if ( isset( $action->properties ) && isset( $action->properties->body ) ) {
                                    echo esc_textarea( $action->properties->body );
                                } ?></textarea></td>

                    </tr>
                    <tr>
                        <th scope="row"><label for="action-properties-cc">CC</label></th>
                        <td><input placeholder="Optional" id="action-properties-cc"
                                    name="actions[<?php echo esc_attr( $action->id ) ?>][properties][cc]" type="text"
                                    value="<?php if ( isset( $action->properties ) && isset( $action->properties->cc ) ) {
                                        echo esc_attr( $action->properties->cc );
                                    } ?>"/></td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="action-properties-bcc">BCC</label></th>
                        <td><input placeholder="Optional" id="action-properties-bcc"
                                    name="actions[<?php echo esc_attr( $action->id ) ?>][properties][bcc]" type="text"
                                    value="<?php if ( isset( $action->properties ) && isset( $action->properties->bcc ) ) {
                                        echo esc_attr( $action->properties->bcc );
                                    } ?>"/></td>
                    </tr>
                </table>
            </td>
        </tr>
        <?php
    }
}
