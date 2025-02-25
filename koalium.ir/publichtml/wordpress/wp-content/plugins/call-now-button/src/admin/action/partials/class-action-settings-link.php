<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsLink {

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
        ?>
        <tr class="cnb-action-properties cnb-action-properties-LINK cnb_hide_on_modal">
            <th scope="row"><label for="actionLinkTargetSelect">Open link in</label></th>
            <td>
                <?php $action_link_target = isset( $action->properties ) && isset( $action->properties->{'link-target'} ) ? $action->properties->{'link-target'} : null; ?>
                <select id="actionLinkTargetSelect"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][link-target]">
                    <option value="_blank" <?php selected( '_blank', $action_link_target ) ?>>New window</option>
                    <option value="_self" <?php selected( '_self', $action_link_target ) ?>>Current window</option>
                </select>
            </td>
        </tr>
        <tr class="cnb-action-properties cnb-action-properties-LINK cnb_advanced_view cnb_hide_on_modal">
            <th scope="row"><label for="actionLinkDownload">Download</label></th>
            <td>
                <?php
                $action_download_enabled = isset( $action->properties ) && isset( $action->properties->{'link-download-enabled'} ) ? $action->properties->{'link-download-enabled'} : false;
                $action_download_value   = isset( $action->properties ) && isset( $action->properties->{'link-download'} ) ? $action->properties->{'link-download'} : null;
                ?>
                <p><input type="hidden"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][link-download-enabled]"
                            value="0"/>
                    <input id="cnb-action-link-download-enabled" class="cnb_toggle_checkbox" type="checkbox"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][link-download-enabled]"
                            value="true" <?php checked( true, $action_download_enabled ); ?>>
                    <label for="cnb-action-link-download-enabled" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="cnb-action-link-download-enabled"
                            class="cnb_toggle_state cnb_toggle_false">(No)</span>
                    <span data-cnb_toggle_state_label="cnb-action-link-download-enabled"
                            class="cnb_toggle_state cnb_toggle_true">Yes</span></p>
                <p><input id="actionLinkDownload" type="text"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][link-download]"
                            value="<?php echo esc_attr( $action_download_value ) ?>" placeholder="Download filename"/>
                </p>
            </td>
        </tr>
        <?php
    }
}
