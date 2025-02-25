<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsZalo {

	private $action_name = 'ZALO';

	/**
	 * Options: PERSONAL and GROUP
	 * @var string
	 */
	private $zalo_link_type = 'PERSONAL';

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
		if ( isset( $action->properties ) && isset( $action->properties->{'zalo-link-type'} ) ) {
			$this->zalo_link_type = $action->properties->{'zalo-link-type'};
		}
		?>
		<tr class="cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
			<th scope="row"><label for="cnb-action-properties-zalo-link-type">Button type</label></th>
			<td>
				<select id="cnb-action-properties-zalo-link-type"
				        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][zalo-link-type]">
					<option value="PERSONAL" <?php selected( $this->zalo_link_type, 'PERSONAL' ); ?>>
						Personal (use your phone number above)
					</option>
					<option value="INVITE" <?php selected( $this->zalo_link_type, 'GROUP' ); ?>>
						Group (use your group or username above)
					</option>
				</select>
			</td>
		</tr>
		<?php
	}
}
