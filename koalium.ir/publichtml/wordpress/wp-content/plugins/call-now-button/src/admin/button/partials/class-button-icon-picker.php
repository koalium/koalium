<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class Button_Icon_Picker {

    /**
     * @param CnbButton $button
     *
     * @return string
     */
    private function getIconBackGroundOpen( $button ) {
        if ( $button->multiButtonOptions ) {
            if ( $button->multiButtonOptions->iconBackgroundColorOpen ) {
                return $button->multiButtonOptions->iconBackgroundColorOpen;
            }
            if ( $button->multiButtonOptions->iconBackgroundColor ) {
                return $button->multiButtonOptions->iconBackgroundColor;
            }
        }
        if ( $button->options && $button->options->iconBackgroundColor ) {
            return $button->options->iconBackgroundColor;
        }

        return '#009900';
    }

    /**
     * @param CnbButton $button
     *
     * @return string
     */
    private function getIconColorOpen( $button ) {
        if ( $button->multiButtonOptions ) {
            if ( $button->multiButtonOptions->iconColorOpen ) {
                return $button->multiButtonOptions->iconColorOpen;
            }
            if ( $button->multiButtonOptions->iconColor ) {
                return $button->multiButtonOptions->iconColor;
            }
        }
        if ( $button->options && $button->options->iconColor ) {
            return $button->options->iconColor;
        }

        return '#FFFFFF';
    }

    /**
     * @param CnbButton $button
     *
     * @return void
     */
    public function render( $button ) {
        $multi_button_id = ( $button->multiButtonOptions && $button->multiButtonOptions->id ) ? $button->multiButtonOptions->id : '';

        $iconTypeOpen            = ( $button->multiButtonOptions && $button->multiButtonOptions->iconTypeOpen ) ? $button->multiButtonOptions->iconTypeOpen : 'FONT';
        $iconTextOpen            = ( $button->multiButtonOptions && $button->multiButtonOptions->iconTextOpen ) ? $button->multiButtonOptions->iconTextOpen : 'more_vert';
        $iconBackgroundImageOpen = ( $button->multiButtonOptions && $button->multiButtonOptions->iconBackgroundImageOpen ) ? $button->multiButtonOptions->iconBackgroundImageOpen : '';
        $iconColorOpen           = $this->getIconColorOpen( $button );
        $iconBackgroundColorOpen = $this->getIconBackGroundOpen( $button );

        $iconTypeClose            = ( $button->multiButtonOptions && $button->multiButtonOptions->iconTypeClose ) ? $button->multiButtonOptions->iconTypeClose : 'FONT';
        $iconTextClose            = ( $button->multiButtonOptions && $button->multiButtonOptions->iconTextClose ) ? $button->multiButtonOptions->iconTextClose : 'close';
        $iconBackgroundImageClose = ( $button->multiButtonOptions && $button->multiButtonOptions->iconBackgroundImageClose ) ? $button->multiButtonOptions->iconBackgroundImageClose : '';
        $iconColorClose           = ( $button->multiButtonOptions && $button->multiButtonOptions->iconColorClose ) ? $button->multiButtonOptions->iconColorClose : $iconColorOpen;
        $iconBackgroundColorClose = ( $button->multiButtonOptions && $button->multiButtonOptions->iconBackgroundColorClose ) ? $button->multiButtonOptions->iconBackgroundColorClose : $iconBackgroundColorOpen;

        ?>
        <tr>
            <th scope="row">
                <label for="button-multiButtonOptions-iconBackgroundColorOpen">
                    Button color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][id]" type="hidden"
                        value="<?php echo esc_attr( $multi_button_id ); ?>"/>
                <input name="button[multiButtonOptions][iconBackgroundColorOpen]"
                        id="button-multiButtonOptions-iconBackgroundColorOpen" type="text"
                        value="<?php echo esc_attr( $iconBackgroundColorOpen ); ?>"
                        class="cnb-color-field" data-default-color="#009900"/>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="button-multiButtonOptions-iconColorOpen">
                    Icon color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][iconColorOpen]" id="button-multiButtonOptions-iconColorOpen"
                        type="text" value="<?php echo esc_attr( $iconColorOpen ); ?>"
                        class="cnb-color-field" data-default-color="#FFFFFF"/>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="button-multiButtonOptions-iconTextOpen">
                    Button icon/image
                </label>
            </th>
            <td>
                <div class="icon-text-options" id="icon-text-open"
                    data-icon-text-target="button-multiButtonOptions-iconTextOpen"
                    data-icon-type-target="button-multiButtonOptions-iconTypeOpen">
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT"
                            data-icon-text="more_vert">more_vert</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="menu">menu</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT"
                            data-icon-text="communicate">communicate</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="more_info">more_info</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="conversation">conversation</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="conversation">chat</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="conversation">chat_2</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="conversation">chat_3</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="conversation">chat_4</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="call3">call3</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="whatsapp">whatsapp</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="signal">signal</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="telegram">telegram</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="facebook_messenger">facebook_messenger</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="viber">viber</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="line">line</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="skype">skype</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="zalo">zalo</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="email">email</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="call">call</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="directions3">directions3</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="directions5">directions5</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="calendar">calendar</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="star">star</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="fire">fire</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="support">support</i>
                    </div>
                    <?php
                    $this->render_image_selector( 'iconBackgroundImageOpen', $iconBackgroundImageOpen );
                    ?>
                </div>
                <div class="cnb_advanced_view">
                    <a
                            href="#"
                            onclick="return cnb_show_icon_text_advanced(this)"
                            data-icon-text="button-multiButtonOptions-iconTextOpen"
                            data-icon-type="button-multiButtonOptions-iconTypeOpen"
                            data-description="button-multiButtonOptions-iconTextOpen-description"
                            class="cnb_advanced_view">Use a custom icon</a>
                    <input name="button[multiButtonOptions][iconTextOpen]"
                            id="button-multiButtonOptions-iconTextOpen" type="hidden"
                            data-cnb-multi-do-not-expand="true"
                            value="<?php echo esc_attr( $iconTextOpen ); ?>"/>
                    <input name="button[multiButtonOptions][iconTypeOpen]"
                            id="button-multiButtonOptions-iconTypeOpen" type="hidden"
                            value="<?php echo esc_attr( $iconTypeOpen ); ?>"/>
                    <p class="description" id="button-multiButtonOptions-iconTextOpen-description"
                        style="display: none">
                        You can enter a custom Material Design font code here. Search the full library at <a
                                href="https://fonts.google.com/icons" target="_blank">Google Fonts</a>.<br/>
                        The Call Now Button uses the <code>filled</code> version of icons.</p>
                </div>
            </td>
        </tr>

        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-iconBackgroundColorClose">
                    Close button color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][id]" type="hidden"
                        value="<?php echo esc_attr( $multi_button_id ); ?>"/>
                <input name="button[multiButtonOptions][iconBackgroundColorClose]"
                        id="button-multiButtonOptions-iconBackgroundColorClose" type="text"
                        value="<?php echo esc_attr( $iconBackgroundColorClose ); ?>"
                        class="cnb-color-field" data-default-color="#009900"/>
            </td>
        </tr>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-iconColorClose">
                    Close icon color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][iconColorClose]" id="button-multiButtonOptions-iconColorClose"
                        type="text" value="<?php echo esc_attr( $iconColorClose ); ?>"
                        class="cnb-color-field" data-default-color="#FFFFFF"/>
            </td>
        </tr>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-iconTextClose">
                    Close Icon
                </label>
            </th>
            <td>
                <div class="icon-text-options" id="icon-text-close"
                    data-icon-text-target="button-multiButtonOptions-iconTextClose"
                    data-icon-type-target="button-multiButtonOptions-iconTypeClose">
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon" data-icon-type="FONT" data-icon-text="close">close</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon family-material" data-icon-type="FONT_MATERIAL"
                            data-icon-text="cancel">cancel</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon family-material" data-icon-type="FONT_MATERIAL"
                            data-icon-text="close">close</i>
                    </div>
                    <div class="cnb-button-icon">
                        <i class="cnb-font-icon family-material" data-icon-type="FONT_MATERIAL"
                            data-icon-text="zoom_in_map">zoom_in_map</i>
                    </div>
                    <?php
                    $this->render_image_selector( 'iconBackgroundImageClose', $iconBackgroundImageClose );

                    ?>
                </div>
                <a
                        href="#"
                        onclick="return cnb_show_icon_text_advanced(this)"
                        data-icon-text="button-multiButtonOptions-iconTextClose"
                        data-icon-type="button-multiButtonOptions-iconTypeClose"
                        data-description="button-multiButtonOptions-iconTextClose-description"
                        class="cnb_advanced_view">Use a custom icon</a>
                <input name="button[multiButtonOptions][iconTextClose]"
                        id="button-multiButtonOptions-iconTextClose" type="hidden"
                        value="<?php echo esc_attr( $iconTextClose ); ?>"/>
                <input name="button[multiButtonOptions][iconTypeClose]"
                        id="button-multiButtonOptions-iconTypeClose" type="hidden"
                        value="<?php echo esc_attr( $iconTypeClose ); ?>"/>
                <p class="description" id="button-multiButtonOptions-iconTextClose-description"
                    style="display: none">
                    You can enter a custom Material Design font code here. Search the full library at <a
                            href="https://fonts.google.com/icons" target="_blank">Google Fonts</a>.<br/>
                    The Call Now Button uses the <code>filled</code> version of icons.</p>
            </td>
        </tr>

        <?php
    }

    /**
     * @param string $attribute_name
     *
     * @return void
     */
    private function render_image_selector( $attribute_name, $attribute_value ) { ?>
        <div
                class="cnb-button-icon cnb-button-image cnb_icon_active cnb_selected_action_background_image"
                style="background-image:<?php echo esc_attr( $attribute_value ) ?>"
        ></div>

        <input
                type="hidden"
                name="button[multiButtonOptions][<?php echo esc_attr( $attribute_name ) ?>]"
                value="<?php echo esc_attr( $attribute_value ) ?>"
                class="cnb_action_icon_background_image"
        />

        <input
                type='button'
                class="cnb_select_image button-secondary"
                value="<?php esc_attr_e( 'Select image' ); ?>"
        />
        <?php
    }
}
