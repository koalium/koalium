<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class ActionSettingsSms {
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
        $cnb_utils = new CnbUtils();
        ?>
        <tr class="cnb-action-properties cnb-action-properties-SMS">
            <th scope="row"><label for="action-properties-message-sms">Message template <a
                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress/buttons/message-template/', 'question-mark', 'message-template' ) ) ?>"
                            target="_blank" class="cnb-nounderscore">
                        <span class="dashicons dashicons-editor-help"></span>
                    </a></label></th>
            <td>
                    <textarea id="action-properties-message-sms"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][properties][message]" class="code"
                                rows="3"
                                placeholder="Optional"><?php if ( isset( $action->properties ) && isset( $action->properties->message ) ) {
                            echo esc_textarea( $action->properties->message );
                        } ?></textarea>
            </td>
        </tr>
		<?php
	}
}