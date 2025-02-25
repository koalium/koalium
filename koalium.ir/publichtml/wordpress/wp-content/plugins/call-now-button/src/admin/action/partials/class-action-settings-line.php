<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsLine {

	private $action_name = 'LINE';

	/**
	 * Options: MESSAGE or PROFILE
	 * @var string
	 */
	private $line_link_type = 'MESSAGE';

	/**
	 * Only valid for $line_link_type == MESSAGE
	 * @var string
	 */
	private $line_message = '';

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
		if ( isset( $action->properties ) && isset( $action->properties->{'line-link-type'} ) ) {
			$this->line_link_type = $action->properties->{'line-link-type'};
		}
		if ( isset( $action->properties ) && isset( $action->properties->{'line-message'} ) ) {
			$this->line_message = $action->properties->{'line-message'};
		}
        ?>
		<tr class="cnb_advanced_view cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
			<th scope="row"><label for="cnb-action-properties-line-link-type">Button type</label></th>
			<td>
				<select id="cnb-action-properties-line-link-type"
				        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][line-link-type]">
					<option value="MESSAGE" <?php selected( $this->line_link_type, 'MESSAGE' ); ?>>
						Chat
					</option>
                    <option value="PROFILE" <?php selected( $this->line_link_type, 'PROFILE' ); ?>>
                        Profile info
                    </option>
				</select>
			</td>
		</tr>
        <tr class="cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
            <th scope="row"><label for="cnb-action-properties-line-message">Message</label></th>
            <td>
                <input placeholder="Optional" type="text" id="cnb-action-properties-line-message"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][line-message]"
                        value="<?php echo esc_attr( $this->line_message ) ?>"/>
            </td>
        </tr>
		<?php
	}
}
