<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsViber {

	private $action_name = 'VIBER';
	/**
	 * Options: PA_CHAT, PA_INFO, FORWARD, ADD_NUMBER, GROUP_INVITE and GROUP2_INVITE
	 * @var string
	 */
	private $viber_link_type = 'PA_CHAT';

	/**
	 * Only valid for $viber_link_type == PA_CHAT
	 * @var string
	 */
	private $viber_text = '';

	/**
	 * Only valid for $viber_link_type == PA_CHAT
	 * @var string
	 */
	private $viber_content = '';

	/**
	 * Only valid for $viber_link_type == GROUP_INVITE || $viber_link_type == GROUP2_INVITE
     * defaults to "en"
	 * @var string
	 */
	private $viber_lang = '';

	/**
	 * @param CnbAction $action
	 *
	 * @return void
	 */
	function render( $action ) {
		wp_enqueue_script(CNB_SLUG . '-action-edit-viber');
		$this->render_options( $action );
	}

	/**
	 * @param CnbAction $action
	 *
	 * @return void
	 */
	function render_options( $action ) {
		if ( isset( $action->properties ) && isset( $action->properties->{'viber-link-type'} ) ) {
			$this->viber_link_type = $action->properties->{'viber-link-type'};
		}
		if ( isset( $action->properties ) && isset( $action->properties->{'viber-text'} ) ) {
			$this->viber_text = $action->properties->{'viber-text'};
		}
		if ( isset( $action->properties ) && isset( $action->properties->{'viber-content'} ) ) {
			$this->viber_content = $action->properties->{'viber-content'};
		}
		if ( isset( $action->properties ) && isset( $action->properties->{'viber-lang'} ) ) {
			$this->viber_lang = $action->properties->{'viber-lang'};
		}
        ?>
		<tr class="cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
			<th scope="row"><label for="cnb-action-properties-viber-link-type">Chat type</label></th>
			<td>
				<select id="cnb-action-properties-viber-link-type"
				        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][viber-link-type]">
					<option value="PA_CHAT" <?php selected( $this->viber_link_type, 'PA_CHAT' ); ?>>
						Viber Bot
					</option>
                    <option value="CHAT" <?php selected( $this->viber_link_type, 'CHAT' ); ?>>
                        Personal Chat
                    </option>
                    <option class="cnb_advanced_view" value="PA_INFO" <?php selected( $this->viber_link_type, 'PA_INFO' ); ?>>
                        Profile info
                    </option>
                    <option class="cnb_advanced_view" value="FORWARD" <?php selected( $this->viber_link_type, 'FORWARD' ); ?>>
                        Forward
                    </option>
                    <option class="cnb_advanced_view" value="LANDING_PAGE" <?php selected( $this->viber_link_type, 'LANDING_PAGE' ); ?>>
                        Open bot landing page
                    </option>
                    <option class="cnb_advanced_view" value="ADD_NUMBER" <?php selected( $this->viber_link_type, 'ADD_NUMBER' ); ?>>
                        Add contact
                    </option>
                    <option class="cnb_advanced_view" value="GROUP_INVITE" <?php selected( $this->viber_link_type, 'GROUP_INVITE' ); ?>>
                        Group invite
                    </option>
                    <option class="cnb_advanced_view" value="GROUP2_INVITE" <?php selected( $this->viber_link_type, 'GROUP2_INVITE' ); ?>>
                        Private group invite
                    </option>
				</select>
			</td>
		</tr>

        <tbody class="cnb-action-properties-viber-pa-chat">
            <tr class="cnb-settings-section cnb-settings-section-viber">
                <td colspan="2">
                    <h3 class="cnb-settings-section-title" data-cnb-settings-block="viber"><span
                                class="dashicons dashicons-arrow-right"></span> Additional Viber Bot parameters</h3>

                    <table class="cnb-settings-section-table">
                        <tr>
                            <th scope="row"><label for="cnb-action-properties-viber-text">Text
                                <a
                                        href="https://developers.viber.com/docs/tools/deep-links/#text"
                                        target="_blank" class="cnb-nounderscore">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </a>
                                </label></th>
                            <td>
                                <input placeholder="Optional" type="text" id="cnb-action-properties-viber-text"
                                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][viber-text]"
                                        value="<?php echo esc_attr( $this->viber_text ) ?>"/>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="cnb-action-properties-viber-content">Context
                                <a
                                        href="https://developers.viber.com/docs/tools/deep-links/#context"
                                        target="_blank" class="cnb-nounderscore">
                                    <span class="dashicons dashicons-editor-help"></span>
                                </a>
                                </label></th>
                            <td>
                                <input placeholder="Optional" type="text" id="cnb-action-properties-viber-content"
                                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][viber-content]"
                                        value="<?php echo esc_attr( $this->viber_content ) ?>"/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>

        <tbody class="cnb-action-properties-viber-group-invite">
            <tr>
                <th scope="row"><label for="cnb-action-properties-viber-lang">Lang
                    <a
                            href="https://developers.viber.com/docs/tools/deep-links/#text"
                            target="_blank" class="cnb-nounderscore">
                        <span class="dashicons dashicons-editor-help"></span>
                    </a>
                    (defaults to "en")</label></th>
                <td>
                    <input placeholder="Optional" type="text" id="cnb-action-properties-viber-lang"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][viber-lang]"
                            value="<?php echo esc_attr( $this->viber_lang ) ?>"/>
                </td>
            </tr>
        </tbody>

		<?php
	}
}
