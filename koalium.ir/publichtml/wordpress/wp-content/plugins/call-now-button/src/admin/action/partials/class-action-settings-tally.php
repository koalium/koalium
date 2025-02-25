<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsTally {
    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render( $action ) {
        $this->render_header();
        $this->render_options( $action );
        $this->render_close_header();
    }

    /**
     * NOTE: This function does NOT close its opened tags - that is done via "render_close_header"
     * @return void
     */
    function render_header() { ?>
        <tr class="cnb-action-properties cnb-action-properties-TALLY cnb-settings-section cnb-settings-section-tally">
        <td colspan="2">
        <h3 class="cnb-settings-section-title" data-cnb-settings-block="tally"><span
                    class="dashicons dashicons-arrow-right"></span> Tally form settings</h3>
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
        <table class="cnb-settings-section-table">
            <tr class="cnb-action-properties cnb-action-properties-TALLY">
                <th scope="row"><label for="cnb-action-properties-tally-hide-title">Form title</label></th>
                <td>
                    <?php
                    $value = '1';
                    if ( isset( $action->properties ) && isset( $action->properties->{'tally-hide-title'} ) ) {
                        $value = $action->properties->{'tally-hide-title'};
                    }
                    ?>
                    <select id="cnb-action-properties-tally-hide-title"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][tally-hide-title]">
                        <option value="" <?php selected( $value, '' ); ?>>
                            Show
                        </option>
                        <option value="1" <?php selected( $value, '1' ); ?>>
                            Hide
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-TALLY cnb_advanced_view">
                <th scope="row"><label for="cnb-action-properties-tally-transparent-background">Form background</label>
                </th>
                <td>
                    <?php
                    $value = '';
                    if ( isset( $action->properties ) && isset( $action->properties->{'tally-transparent-background'} ) ) {
                        $value = $action->properties->{'tally-transparent-background'};
                    }
                    ?>
                    <select id="cnb-action-properties-tally-transparent-background"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][tally-transparent-background]">
                        <option value="" <?php selected( $value, '' ); ?>>
                            Default background
                        </option>
                        <option value="1" <?php selected( $value, '1' ); ?>>
                            Transparent background (recommended)
                        </option>
                    </select>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-TALLY">
                <th scope="row"><label for="cnb-action-properties-tally-align-left">Content alignment</label></th>
                <td>
                    <?php
                    $value = '1';
                    if ( isset( $action->properties ) && isset( $action->properties->{'tally-align-left'} ) ) {
                        $value = $action->properties->{'tally-align-left'};
                    }
                    ?>
                    <select id="cnb-action-properties-tally-align-left"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][properties][tally-align-left]">
                        <option value="" <?php selected( $value, '' ); ?>>
                            Tally default
                        </option>
                        <option value="1" <?php selected( $value, '1' ); ?>>
                            Left
                        </option>
                    </select>
                </td>
            </tr>
        </table>
        <?php
    }
}
