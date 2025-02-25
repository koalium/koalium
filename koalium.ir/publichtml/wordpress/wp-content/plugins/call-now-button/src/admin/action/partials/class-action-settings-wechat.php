<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsWeChat {

	private $action_name = 'WECHAT';

	/**
	 * Options: CHAT (web link) and WEIXIN_CHAT (mobile native)
	 * @var string
	 */
	private $wechat_link_type = 'CHAT';

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
		if ( isset( $action->properties ) && isset( $action->properties->{'wechat-link-type'} ) ) {
			$this->wechat_link_type = $action->properties->{'wechat-link-type'};
		}
		?>
		<tr class="cnb-action-properties cnb-action-properties-<?php echo esc_attr($this->action_name) ?>">
			<th scope="row"><label for="cnb-action-properties-wechat-link-type">Button type</label></th>
			<td>
				<select id="cnb-action-properties-wechat-link-type"
				        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][wechat-link-type]">
					<option value="CHAT" <?php selected( $this->wechat_link_type, 'CHAT' ); ?>>
						Chat
					</option>
					<option value="WEIXIN_CHAT" <?php selected( $this->wechat_link_type, 'WEIXIN_CHAT' ); ?>>
						Weixin Chat (mobile only)
					</option>
				</select>
			</td>
		</tr>
		<?php
	}
}
