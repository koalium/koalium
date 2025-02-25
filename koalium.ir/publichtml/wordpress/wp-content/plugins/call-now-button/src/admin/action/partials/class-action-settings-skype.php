<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsSkype {

	private $action_name = 'SKYPE';

	/**
	 * Options: SKYPE, INVITE and CONFERENCE
	 * @var string
	 */
	private $skype_link_type = 'SKYPE';

	/**
	 * Options: ADD, CALL, CHAT, SENDFILE, USERINFO
	 * Only valid for $skype_link_type == SKYPE
	 * @var string
	 */
	private $skype_param_type = 'CALL';

	/**
	 * @param CnbAction $action
	 *
	 * @return void
	 */
	function render( $action ) {
		$this->render_options( $action );
	}

	/**
	 * @param CnbAction $action
	 *
	 * @return void
	 */
	function render_options( $action ) {
		if ( isset( $action->properties ) && isset( $action->properties->{'skype-link-type'} ) ) {
			$this->skype_link_type = $action->properties->{'skype-link-type'};
		}
		if ( isset( $action->properties ) && isset( $action->properties->{'skype-param-type'} ) ) {
			$this->skype_param_type = $action->properties->{'skype-param-type'};
		}
        ?>
		<tr class="cnb_advanced_view cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
			<th scope="row"><label for="cnb-action-properties-skype-link-type">Button type</label></th>
			<td>
				<select id="cnb-action-properties-skype-link-type"
				        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][skype-link-type]">
					<option value="SKYPE" <?php selected( $this->skype_link_type, 'SKYPE' ); ?>>
						Skype call
					</option>
					<option value="INVITE" <?php selected( $this->skype_link_type, 'INVITE' ); ?>>
						Invite link
					</option>
					<option value="CONFERENCE" <?php selected( $this->skype_link_type, 'CONFERENCE' ); ?>>
						Conference call
					</option>
				</select>
			</td>
		</tr>
        <tr class="cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
            <th scope="row"><label for="cnb-action-properties-skype-param-type">Action</label></th>
            <td>
                <select id="cnb-action-properties-skype-link-type"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][skype-param-type]">
                    <option value="CALL" <?php selected( $this->skype_param_type, 'CALL' ); ?>>
                        Regular call
                    </option>
                    <option value="CHAT" <?php selected( $this->skype_param_type, 'CHAT' ); ?>>
                        Open chat
                    </option>
                    <option class="cnb_advanced_view" value="ADD" <?php selected( $this->skype_param_type, 'ADD' ); ?>>
                        Add contact
                    </option>
                    <option class="cnb_advanced_view" value="SENDFILE" <?php selected( $this->skype_param_type, 'SENDFILE' ); ?>>
                        Send a file
                    </option>
                    <option class="cnb_advanced_view" value="USERINFO" <?php selected( $this->skype_param_type, 'USERINFO' ); ?>>
                        Open profile
                    </option>
                </select>
            </td>
        </tr>
		<?php
	}
}
