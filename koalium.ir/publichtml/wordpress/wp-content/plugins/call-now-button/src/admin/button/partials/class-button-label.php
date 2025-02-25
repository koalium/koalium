<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class Button_Label {
    /**
     * @param CnbButton $button
     *
     * @return void
     */
    public function render( $button ) {
        $this->renderOpen( $button );
        $this->renderClose( $button );
    }

    /**
     * @param CnbButton $button
     *
     * @return void
     */
    private function renderOpen( $button ) {
        $labelTextOpen            = ( $button->multiButtonOptions && $button->multiButtonOptions->labelTextOpen ) ? $button->multiButtonOptions->labelTextOpen : null;
        $labelBackgroundColorOpen = ( $button->multiButtonOptions && $button->multiButtonOptions->labelBackgroundColorOpen ) ? $button->multiButtonOptions->labelBackgroundColorOpen : null;
        $labelTextColorOpen       = ( $button->multiButtonOptions && $button->multiButtonOptions->labelTextColorOpen ) ? $button->multiButtonOptions->labelTextColorOpen : null;

        global $cnb_domain;
        $isPro = $cnb_domain != null && ! is_wp_error( $cnb_domain ) && $cnb_domain->type === 'PRO';
        ?>
        <tr>
            <th scope="row">
                <label for="button-multiButtonOptions-labelTextOpen">
                    Button label
                </label>
                <?php if ( ! $isPro ) {
                $upgrade_link =
                    add_query_arg( array(
                        'page'   => 'call-now-button-domains',
                        'action' => 'upgrade',
                        'id'     => $cnb_domain->id,
                    ),
                        admin_url( 'admin.php' ) );
                ?>
                <a href="<?php echo esc_url( $upgrade_link ) ?>"><span class="cnb-pro-badge">Pro</span></a>
            <?php } ?>
            </th>
            <td>
                <input
                        data-cnb-multi-do-not-expand="true"
                        name="button[multiButtonOptions][labelTextOpen]"
                        id="button-multiButtonOptions-labelTextOpen"
                        type="text"
                        <?php if ( ! $isPro ) { ?>disabled="disabled"<?php } ?>
                        value="<?php echo esc_attr( $labelTextOpen ); ?>"
                />
            </td>
        </tr>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-labelBackgroundColorOpen">
                    Main Label background color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][labelBackgroundColorOpen]"
                        id="button-multiButtonOptions-labelBackgroundColorOpen" type="text"
                        value="<?php echo esc_attr( $labelBackgroundColorOpen ); ?>"
                        class="cnb-color-field" data-default-color="#3c434a"/>
            </td>
        </tr>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-labelTextColorOpen">
                    Main Label text color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][labelTextColorOpen]"
                        id="button-multiButtonOptions-labelTextColorOpen" type="text"
                        value="<?php echo esc_attr( $labelTextColorOpen ); ?>"
                        class="cnb-color-field" data-default-color="#000000"/>
            </td>
        </tr>
        <?php
    }

    /**
     * @param CnbButton $button
     *
     * @return void
     */
    private function renderClose( $button ) {
        $labelTextClose            = ( $button->multiButtonOptions && $button->multiButtonOptions->labelTextClose ) ? $button->multiButtonOptions->labelTextClose : null;
        $labelBackgroundColorClose = ( $button->multiButtonOptions && $button->multiButtonOptions->labelBackgroundColorClose ) ? $button->multiButtonOptions->labelBackgroundColorClose : null;
        $labelTextColorClose       = ( $button->multiButtonOptions && $button->multiButtonOptions->labelTextColorClose ) ? $button->multiButtonOptions->labelTextColorClose : null;
        ?>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-labelTextClose">
                    Open Label text
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][labelTextClose]" id="button-multiButtonOptions-labelTextClose"
                        type="text" value="<?php echo esc_attr( $labelTextClose ); ?>"/>
            </td>
        </tr>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-labelBackgroundColorClose">
                    Open Label background color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][labelBackgroundColorClose]"
                        id="button-multiButtonOptions-labelBackgroundColorClose" type="text"
                        value="<?php echo esc_attr( $labelBackgroundColorClose ); ?>"
                        class="cnb-color-field" data-default-color="#3c434a"/>
            </td>
        </tr>
        <tr class="cnb_advanced_view">
            <th scope="row">
                <label for="button-multiButtonOptions-labelTextColorClose">
                    Open Label text color
                </label>
            </th>
            <td>
                <input name="button[multiButtonOptions][labelTextColorClose]"
                        id="button-multiButtonOptions-labelTextColorClose" type="text"
                        value="<?php echo esc_attr( $labelTextColorClose ); ?>"
                        class="cnb-color-field" data-default-color="#000000"/>
            </td>
        </tr>
        <?php
    }
}
