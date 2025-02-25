<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsChat {
    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render( $action ) {
        $this->render_header();
        $this->render_options( $action );
        $this->render_close_header();
        $this->render_chat_header();
        $this->render_chat_options( $action );
        $this->render_close_header();
    }

    function render_chat_header() {
        ?>
        <tr class="cnb-action-properties cnb-action-properties-CHAT cnb-settings-section cnb-settings-section-chat-2">
        <td colspan="2">
        <h3 class="cnb-settings-section-title" data-cnb-settings-block="chat-2">
            <span class="dashicons dashicons-arrow-right"></span>
            Chat settings
        </h3>
        <?php
    }
    /**
     * NOTE: This function does NOT close its opened tags - that is done via "render_close_header"
     * @return void
     */
    function render_header() {
        ?>
        <tr class="cnb-action-properties cnb-action-properties-CHAT cnb-settings-section cnb-settings-section-chat">
        <td colspan="2">
        <h3 class="cnb-settings-section-title" data-cnb-settings-block="chat">
            <span class="dashicons dashicons-arrow-right"></span>
            Extra Window settings
        </h3>
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
     *
     * @return void
     */
    function render_options( $action ) {
        ?>
        <table class="cnb-settings-section-table cnb-settings-section-chat">

            <tr class="cnb-action-properties cnb-action-properties-CHAT cnb-action-properties-chatmodal">
                <th scope="row"><label for="cnb-action-chat-show-notification-count">Show unread messages badge</label></th>
                <td class="appearance">
                    <input type="hidden"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][show-notification-count]"
                            value=""/>
                    <input id="cnb-action-chat-show-notification-count" class="cnb_toggle_checkbox" type="checkbox"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][show-notification-count]"
                            value="true"
				        <?php checked( true, isset( $action->properties ) && isset( $action->properties->{'show-notification-count'} ) && $action->properties->{'show-notification-count'} ); ?> />
                    <label for="cnb-action-chat-show-notification-count" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="cnb-action-show-notification-count"
                            class="cnb_toggle_state cnb_toggle_false">(Off)</span>
                    <span data-cnb_toggle_state_label="cnb-action-show-notification-count"
                            class="cnb_toggle_state cnb_toggle_true">Yes</span>
                </td>
            </tr>

            <tr class="cnb-action-properties cnb-action-properties-CHAT cnb-action-properties-chatmodal">
                <th scope="row"><label for="actionChatmodalWelcomeMessage">Welcome message</label></th>
                <td>
                    <textarea id="actionChatmodalWelcomeMessage" rows="3"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chatmodal-welcome-message]"
                                placeholder="How can we help?"><?php if ( isset( $action->properties ) && isset( $action->properties->{'chatmodal-welcome-message'} ) ) {
                            echo esc_textarea( $action->properties->{'chatmodal-welcome-message'} );
                        } ?></textarea>
                    <p class="description">Start a new line by pressing the <code>Enter</code> key. Every line will
                        become its own speech bubble. Speech bubbles appear in sequence with a short pause between them.
                    </p>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-CHAT cnb-action-properties-chatmodal-modal">
                <th scope="row"><label for="actionChatmodalPlaceholderMessage">Placeholder visitor input</label></th>
                <td>
                    <input id="actionChatmodalPlaceholderMessage" type="text"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chatmodal-placeholder-message]"
                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chatmodal-placeholder-message'} ) ) {
                                echo esc_attr( $action->properties->{'chatmodal-placeholder-message'} );
                            } ?>" placeholder="Type your message"/>
                </td>
            </tr>
        </table>
        <?php
    }

        /**
         * @param CnbAction $action
         *
         * @return void
         */
        function render_chat_options( $action ) {
        ?>
        <table class="cnb-settings-section-table cnb-settings-section-chat-2">
            <tr class="cnb-action-properties cnb-action-properties-CHAT">
                <th scope="row"><label for="cnb-action-properties-chat-agent-message">Average response time</label></th>
                <td><input placeholder="Optional" id="cnb-action-properties-chat-agent-message"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-agent-message]" type="text"
                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-agent-message'} ) ) {
                                echo esc_attr( $action->properties->{'chat-agent-message'} );
                            } ?>"/>
                    <p class="description">Inform your users about your average response times.</p>
                </td>
            </tr>
            <tr class="cnb_hide_on_modal">
                <th scope="row"><label for="cnb-enable-legal">Require legal consent</label></th>

                <td class="activated">
                    <?php
                    $chat_legal_enabled = isset( $action->properties->{'chat-legal-enabled'} )
                        ? $action->properties->{'chat-legal-enabled'} === 'true'
                        : false;
                    ?>
                    <input type="hidden"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-enabled]"
                            value=""/>
                    <input id="cnb-action-chat-legal-enabled" class="cnb_toggle_checkbox" type="checkbox"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-enabled]"
                            value="true" <?php checked( true, $chat_legal_enabled ); ?>>
                    <label for="cnb-action-chat-legal-enabled" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="cnb-action-chat-legal-enabled"
                            class="cnb_toggle_state cnb_toggle_false">(No)</span>
                    <span data-cnb_toggle_state_label="cnb-action-chat-legal-enabled"
                            class="cnb_toggle_state cnb_toggle_true">Yes</span>
                    
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cnb_legal_notice">Legal consent message</label>
                </th>
                <td>
                    <?php
                    $legal_notice = 'I agree to the {link1}, {link2} and {link3} of COMPANY.';
                    if ( isset( $action->properties->{'chat-legal-notice'} ) ) {
                        $legal_notice = $action->properties->{'chat-legal-notice'};
                    }
                    ?>
                    <textarea
                            rows="3"
                            class="large-text code"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-notice]"
                                placeholder="How can we help?"><?php  echo esc_textarea( $legal_notice ); ?></textarea>

                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <h4>Your legal links</h4>
                    <table class="cnb-chat-legal-values">
                        <thead>
                            <tr>
                                <th class="cnb-legal-tokens">Token</th>
                                <th>Document name</th>
                                <th>Link to document</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{link1}</td>
                                <td>
                                    <input placeholder="E.g. Privacy policy"
                                            id="cnb-action-properties-chat-legal-link1-text"
                                            type="text"
                                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-link1-text]"
                                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-legal-link1-text'} ) ) {
												echo esc_attr( $action->properties->{'chat-legal-link1-text'} );
											} ?>"/>
                                </td>
                                <td>
                                    <input placeholder="https://website.com/privacy.html"
                                            id="cnb-action-properties-chat-legal-link1-link"
                                            type="url"
                                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-link1-link]"
                                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-legal-link1-link'} ) ) {
												echo esc_attr( $action->properties->{'chat-legal-link1-link'} );
											} ?>"/>
                                </td>
                            </tr>
                            <tr>
                                <td>{link2}</td>
                                <td>
                                    <input placeholder="E.g. Terms"
                                            id="cnb-action-properties-chat-legal-link2-text"
                                            type="text"
                                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-link2-text]"
                                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-legal-link2-text'} ) ) {
												echo esc_attr( $action->properties->{'chat-legal-link2-text'} );
											} ?>"/>
                                </td>
                                <td>
                                    <input placeholder="https://website.com/terms.html"
                                            id="cnb-action-properties-chat-legal-link2-link"
                                            type="url"
                                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-link2-link]"
                                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-legal-link2-link'} ) ) {
												echo esc_attr( $action->properties->{'chat-legal-link2-link'} );
											} ?>"/>
                                </td>
                            </tr>

                            <tr>
                                <td>{link3}</td>
                                <td>
                                    <input placeholder="E.g. GDPR statement"
                                            id="cnb-action-properties-chat-legal-link3-text"
                                            type="text"
                                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-link3-text]"
                                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-legal-link3-text'} ) ) {
												echo esc_attr( $action->properties->{'chat-legal-link3-text'} );
											} ?>"/>
                                </td>
                                <td>
                                    <input placeholder="https://website.com/gdpr.html"
                                            id="cnb-action-properties-chat-legal-link3-link"
                                            type="url"
                                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][chat-legal-link3-link]"
                                            value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'chat-legal-link3-link'} ) ) {
												echo esc_attr( $action->properties->{'chat-legal-link3-link'} );
											} ?>"/>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>
    <?php
    }
}
