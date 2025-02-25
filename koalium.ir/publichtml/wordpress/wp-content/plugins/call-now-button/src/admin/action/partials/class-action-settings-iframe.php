<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionSettingsIframe {
    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render( $action ) {
        $this->render_header();
        $this->render_iframe_options( $action );
        $this->render_modal_options( $action );
        $this->render_close_header();
    }

    /**
     * NOTE: This function does NOT close its opened tags - that is done via "render_close_header"
     * @return void
     */
    function render_header() { ?>
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY cnb-action-properties-CHAT cnb-settings-section cnb-settings-section-iframe">
        <td colspan="2">
        <h3 class="cnb-settings-section-title" data-cnb-settings-block="iframe"><span
                    class="dashicons dashicons-arrow-right"></span> Window settings</h3>
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

    // region iframe
    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render_iframe_options( $action ) {
        ?>

        <table class="cnb-settings-section-table">
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY cnb-action-properties-CHAT">
            <th scope="row"><label for="cnb-action-properties-iframe-title">Title</label></th>
            <td><input placeholder="Optional" id="cnb-action-properties-iframe-title"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][iframe-title]" type="text"
                        value="<?php if ( isset( $action->properties ) && isset( $action->properties->{'iframe-title'} ) ) {
                            echo esc_attr( $action->properties->{'iframe-title'} );
                        } ?>"/>

            </td>
        </tr>
    <?php }
    // endregion

    // region modal
    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render_modal_options( $action ) {
        $this->render_modal_height( $action );
        $this->render_modal_width( $action );
        $this->render_modal_colors( $action );
    }

    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render_modal_height( $action ) {
        // Default
        $action_properties_modal_height = '';
        if ( isset( $action->properties ) && isset( $action->properties->{'modal-height'} ) ) {
            $action_properties_modal_height = $action->properties->{'modal-height'};
        }

        // Set defaults for slider
        $action_properties_modal_height_value = 400;
        $action_properties_modal_height_unit  = 'px';
        // If there is a value, split it up
        if ( $action_properties_modal_height ) {
            $action_properties_modal_height_split = preg_split( '#(?<=\d)(?=[a-z])#i', $action_properties_modal_height );
            if ( isset( $action_properties_modal_height_split[0] ) ) {
                $action_properties_modal_height_value = $action_properties_modal_height_split[0];
            }
            if ( isset( $action_properties_modal_height_split[1] ) ) {
                $action_properties_modal_height_unit = $action_properties_modal_height_split[1];
            }
        }
        ?>
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY">
            <th scope="row"><label for="cnb-action-properties-modal-height-value">Window height <span
                            class="cnb_font_normal">(<span
                                class="cnb-action-properties-modal-height-result"><?php echo esc_attr( $action_properties_modal_height ) ?></span>)</span></label>
            </th>
            <td>
                <input
                        type="hidden"
                        placeholder="Optional" id="cnb-action-properties-modal-height"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][modal-height]"
                        value="<?php echo esc_attr( $action_properties_modal_height ) ?>"
                />

                <div id="cnb-action-properties-modal-height-slider">
                    <label class="cnb_slider_value" id="cnb-action-properties-modal-height-value-min"></label>
                    <input id="cnb-action-properties-modal-height-value"
                            type="range"
                            min="10"
                            max="2000"
                            step="5"
                            value="<?php echo esc_attr( $action_properties_modal_height_value ) ?>"/>
                    <label class="cnb_slider_value" id="cnb-action-properties-modal-height-value-max"></label>
                    <select id="cnb-action-properties-modal-height-unit">
                        <option value="px" <?php selected( 'px', $action_properties_modal_height_unit ) ?>>px</option>
                        <option value="vh" <?php selected( 'vh', $action_properties_modal_height_unit ) ?>>vh</option>
                    </select>
                </div>
            </td>
        </tr>
    <?php }

    /**
     * @param CnbAction $action
     *
     * @return void
     */
    function render_modal_width( $action ) {
        // Default
        $action_properties_modal_width = '';
        if ( isset( $action->properties ) && isset( $action->properties->{'modal-width'} ) ) {
            $action_properties_modal_width = $action->properties->{'modal-width'};
        }

        ?>
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY">
            <th scope="row"><label for="cnb-action-properties-modal-width">Modal Width</label></th>
            <td>
                <select id="cnb-action-properties-modal-width"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][modal-width]">
                    <option value="250px" <?php selected( '250px', $action_properties_modal_width ) ?>>Slim</option>
                    <option value="400px" <?php selected( '400px', $action_properties_modal_width ); selected( '', $action_properties_modal_width ) ?>>Normal</option>
                    <option value="500px" <?php selected( '500px', $action_properties_modal_width ) ?>>Wide</option>
                    <option value="600px" <?php selected( '600px', $action_properties_modal_width ) ?>>Extra Wide</option>
                </select>
            </td>
        </tr>
    <?php }

    function render_modal_colors( $action ) {
        $modal_background_color        = '';
        $modal_header_background_color = '#009900';
        $modal_header_text_color       = '#ffffff';
        if ( isset( $action->properties ) && isset( $action->properties->{'modal-background-color'} ) ) {
            $modal_background_color = $action->properties->{'modal-background-color'};
        }
        if ( isset( $action->properties ) && isset( $action->properties->{'modal-header-background-color'} ) ) {
            $modal_header_background_color = $action->properties->{'modal-header-background-color'};
        }
        if ( isset( $action->properties ) && isset( $action->properties->{'modal-header-text-color'} ) ) {
            $modal_header_text_color = $action->properties->{'modal-header-text-color'};
        }
        ?>
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY cnb-action-properties-CHAT cnb_advanced_view">
            <th scope="row"><label for="cnb-action-properties-modal-background-color">Modal Background Color</label>
            </th>
            <td>
                <input
                        id="cnb-action-properties-modal-background-color"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][modal-background-color]"
                        type="text"
                        value="<?php echo esc_attr( $modal_background_color ) ?>"
                        class="cnb-color-field" data-default-color="#ffffff"/>
            </td>
        </tr>
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY cnb-action-properties-CHAT">
            <th scope="row"><label for="cnb-action-properties-modal-header-background-color">Header color</label></th>
            <td>
                <input
                        id="cnb-action-properties-modal-header-background-color"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][modal-header-background-color]"
                        type="text"
                        value="<?php echo esc_attr( $modal_header_background_color ) ?>"
                        class="cnb-color-field" data-default-color="#009900"/>
            </td>
        </tr>
        <tr class="cnb-action-properties cnb-action-properties-IFRAME cnb-action-properties-TALLY cnb-action-properties-CHAT">
            <th scope="row"><label for="cnb-action-properties-modal-header-text-color">Header text color</label>
            </th>
            <td>
                <input
                        id="cnb-action-properties-modal-header-text-color"
                        name="actions[<?php echo esc_attr( $action->id ) ?>][properties][modal-header-text-color]"
                        type="text"
                        value="<?php echo esc_attr( $modal_header_text_color ) ?>"
                        class="cnb-color-field" data-default-color="#ffffff"/>

            </td>
        </tr>
        </table>
        <?php
    }
    // endregion
}
