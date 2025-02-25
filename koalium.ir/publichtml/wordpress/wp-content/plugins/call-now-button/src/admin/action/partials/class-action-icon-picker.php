<?php
namespace cnb\admin\action;

use cnb\admin\button\CnbButton;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class ActionIcon {
	/**
	 * PHONE, EMAIL, etc
	 * @var string
	 */
	public $type;

	/**
     * Collection of icon types (name => ..., type => ...)
     * See constructor of ActionIconPicker for implementation details
	 * @var array
	 */
    public $icons;

    public function __construct($type, $icons) {
	    $this->type = $type;
	    $this->icons = $icons;
    }
}

class ActionIconPicker {

    private $icons;

    public function __construct() {
        $this->icons = array(
	        new ActionIcon('ANCHOR', array(
		        array( 'type' => 'FONT', 'text' => 'anchor' ),
		        array( 'type' => 'FONT', 'text' => 'close_down' ),
		        array( 'type' => 'FONT', 'text' => 'anchor_up' ),
	        )),
	        new ActionIcon('EMAIL', array(
		        array( 'type' => 'FONT', 'text' => 'email' ),
		        array( 'type' => 'FONT', 'text' => 'mail2' ),
		        array( 'type' => 'FONT', 'text' => 'mail3' ),
	        )),
	        new ActionIcon('HOURS', array(
		        array( 'type' => 'FONT_MATERIAL', 'text' => 'access_time' ),
		        array( 'type' => 'FONT_MATERIAL', 'text' => 'access_time_filled' ),
	        )),
	        new ActionIcon('LINK', array(
		        array( 'type' => 'FONT', 'text' => 'link' ),
		        array( 'type' => 'FONT', 'text' => 'link2' ),
		        array( 'type' => 'FONT', 'text' => 'link3' ),
		        array( 'type' => 'FONT', 'text' => 'link4' ),
		        array( 'type' => 'FONT', 'text' => 'link5' ),
		        array( 'type' => 'FONT', 'text' => 'calendar' ),
                array( 'type' => 'FONT', 'text' => 'call3' ),
                array( 'type' => 'FONT', 'text' => 'email' ),
                array( 'type' => 'FONT', 'text' => 'chat' ),
                array( 'type' => 'FONT', 'text' => 'directions3' ),
                array( 'type' => 'FONT', 'text' => 'communicate' ),
                array( 'type' => 'FONT', 'text' => 'conversation' ),
                array( 'type' => 'FONT', 'text' => 'more_info' ),
                array( 'type' => 'FONT', 'text' => 'call_back' ),
                array( 'type' => 'FONT', 'text' => 'donate' ),
                array( 'type' => 'FONT', 'text' => 'payment' ),
                array( 'type' => 'FONT', 'text' => 'fire' ),
                array( 'type' => 'FONT', 'text' => 'star' ),
                array( 'type' => 'FONT', 'text' => 'support' ),
	        )),
	        new ActionIcon('MAP', array(
		        array( 'type' => 'FONT', 'text' => 'directions' ),
		        array( 'type' => 'FONT', 'text' => 'directions2' ),
		        array( 'type' => 'FONT', 'text' => 'directions3' ),
		        array( 'type' => 'FONT', 'text' => 'directions4' ),
		        array( 'type' => 'FONT', 'text' => 'directions5' ),
		        array( 'type' => 'FONT', 'text' => 'directions6' ),
	        )),
	        new ActionIcon('PHONE', array(
		        array( 'type' => 'FONT', 'text' => 'call' ),
		        array( 'type' => 'FONT', 'text' => 'call2' ),
		        array( 'type' => 'FONT', 'text' => 'call3' ),
		        array( 'type' => 'FONT', 'text' => 'call4' ),
	        )),
	        new ActionIcon('SMS', array(
		        array( 'type' => 'FONT', 'text' => 'chat' ),
		        array( 'type' => 'FONT', 'text' => 'sms' ),
	        )),
	        new ActionIcon('WHATSAPP', array(
		        array( 'type' => 'FONT', 'text' => 'whatsapp' ),
	        )),
	        new ActionIcon('FACEBOOK', array(
		        array( 'type' => 'FONT', 'text' => 'facebook_messenger' ),
	        )),
	        new ActionIcon('TELEGRAM', array(
		        array( 'type' => 'FONT', 'text' => 'telegram' ),
	        )),
	        new ActionIcon('SIGNAL', array(
		        array( 'type' => 'FONT', 'text' => 'signal' ),
	        )),
	        new ActionIcon('IFRAME', array(
		        array( 'type' => 'FONT', 'text' => 'open_modal' ),
		        array( 'type' => 'FONT', 'text' => 'calendar' ),
		        array( 'type' => 'FONT', 'text' => 'communicate' ),
                array( 'type' => 'FONT', 'text' => 'call3' ),
                array( 'type' => 'FONT', 'text' => 'chat' ),
		        array( 'type' => 'FONT', 'text' => 'conversation' ),
                array( 'type' => 'FONT', 'text' => 'chat_2' ),
                array( 'type' => 'FONT', 'text' => 'chat_3' ),
                array( 'type' => 'FONT', 'text' => 'chat_4' ),
		        array( 'type' => 'FONT', 'text' => 'more_info' ),
		        array( 'type' => 'FONT', 'text' => 'call_back' ),
		        array( 'type' => 'FONT', 'text' => 'donate' ),
		        array( 'type' => 'FONT', 'text' => 'payment' ),
                array( 'type' => 'FONT', 'text' => 'email' ),
                array( 'type' => 'FONT', 'text' => 'mail2' ),
                array( 'type' => 'FONT', 'text' => 'directions3' ),
                array( 'type' => 'FONT', 'text' => 'support' ),
	        )),
	        new ActionIcon('TALLY', array(
		        array( 'type' => 'FONT', 'text' => 'call3' ),
		        array( 'type' => 'FONT', 'text' => 'email' ),
		        array( 'type' => 'FONT', 'text' => 'chat' ),
		        array( 'type' => 'FONT', 'text' => 'communicate' ),
		        array( 'type' => 'FONT', 'text' => 'open_modal' ),
		        array( 'type' => 'FONT', 'text' => 'donate' ),
		        array( 'type' => 'FONT', 'text' => 'payment' ),
	        )),
	        new ActionIcon('INTERCOM', array(
		        array( 'type' => 'FONT', 'text' => 'intercom' ),
                array( 'type' => 'FONT', 'text' => 'chat' ),
                array( 'type' => 'FONT', 'text' => 'conversation' ),
	        )),
	        new ActionIcon('SKYPE', array(
		        array( 'type' => 'FONT', 'text' => 'skype' ),
	        )),
	        new ActionIcon('ZALO', array(
		        array( 'type' => 'FONT', 'text' => 'zalo' ),
	        )),
	        new ActionIcon('VIBER', array(
		        array( 'type' => 'FONT', 'text' => 'viber' ),
	        )),
	        new ActionIcon('LINE', array(
		        array( 'type' => 'FONT', 'text' => 'line' ),
	        )),
	        new ActionIcon('WECHAT', array(
		        array( 'type' => 'FONT', 'text' => 'wechat' ),
	        )),
	        new ActionIcon('CHAT', array(
                array( 'type' => 'FONT', 'text' => 'conversation' ),
		        array( 'type' => 'FONT', 'text' => 'chat' ),
                array( 'type' => 'FONT', 'text' => 'chat_2' ),
                array( 'type' => 'FONT', 'text' => 'chat_3' ),
                array( 'type' => 'FONT', 'text' => 'chat_4' ),
	        )),
        );
    }

    private function render_icon_picker() {
	    foreach ($this->icons as $type) {
		    echo '<div class="icon-text-options" id="icon-text-' . esc_attr($type->type) . '">';
		    foreach ($type->icons as $icons) {
			    echo '<div class="cnb-button-icon">';
			    echo '<i class="cnb-font-icon" data-icon-type="' . esc_attr($icons['type']) . '" data-icon-text="' . esc_attr($icons['text']) . '">' . esc_html($icons['text']) . '</i>';
			    echo '</div>';
		    }
		    echo '</div>';
	    }
    }
	/**
     * @param $action CnbAction
     * @param $button CnbButton
     *
     * @return void
     */
    public function render($action, $button) {
        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $button->domain->id,
            ),
                admin_url( 'admin.php' ) );

        ?>
        <tr class="cnb_hide_on_modal">
            <th scope="row"><label for="actions-<?php echo esc_attr( $action->id ) ?>-iconText">Button icon/image</label></th>
            <td data-icon-text-target="cnb_action_icon_text" data-icon-type-target="cnb_action_icon_type">
                <?php if ( $button->domain->type !== 'STARTER' ) {
                    $this->render_icon_picker();
                }
                if ( $button->domain->type === 'STARTER' ) { ?>
                    <p class="description">
                        Icon selection and custom images are <span class="cnb-pro-badge">Pro</span> features.
                        <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a>.
                    </p>
                <?php } else {
                    $this->render_image_selector($action, $button);
                } ?>

                <a
                    href="#"
                    onclick="return cnb_show_icon_text_advanced(this)"
                    data-icon-text="cnb_action_icon_text"
                    data-icon-type="cnb_action_icon_type"
                    data-description="cnb_action_icon_text_description"
                    class="cnb_advanced_view">Use a custom icon</a>
                <input
                    type="hidden"
                    name="actions[<?php echo esc_attr( $action->id ) ?>][iconText]"
                    value="<?php if ( isset( $action->iconText ) ) {
                        echo esc_attr( $action->iconText );
                    } ?>"
                    id="cnb_action_icon_text"/>
                <input
                    type="hidden"
                    readonly="readonly"
                    name="actions[<?php echo esc_attr( $action->id ) ?>][iconType]"
                    value="<?php if ( isset( $action->iconType ) ) {
                        echo esc_attr( $action->iconType );
                    } ?>"
                    id="cnb_action_icon_type"/>
                <p class="description" id="cnb_action_icon_text_description" style="display: none">
                    You can enter a custom Material Design font code here. Search the full library at <a
                        href="https://fonts.google.com/icons" target="_blank">Google Fonts</a>.<br/>
                    The Call Now Button uses the <code>filled</code> version of icons.</p>
            </td>
        </tr>
        <?php
    }

    private function render_image_selector( $action, $button ) { ?>
        <div
                class="cnb-button-icon cnb-button-image cnb_icon_active cnb_selected_action_background_image"
                style="background-image:<?php echo esc_attr( $action->iconBackgroundImage ) ?>"
        ></div>

        <input
                type="hidden"
                name="actions[<?php echo esc_attr( $action->id ) ?>][iconBackgroundImage]"
                value="<?php echo esc_attr( $action->iconBackgroundImage ) ?>"
                class="cnb_action_icon_background_image"
        />

        <input
                type='button'
                class="cnb_select_image button-secondary"
                value="<?php esc_attr_e( 'Select image' ); ?>"
                <?php if ( $button->domain->type !== 'PRO' ) { ?>disabled="disabled"
                title="Upgrade to PRO to enable custom images"<?php } ?>
        />
            <?php
    }

    /**
     * @param $action CnbAction
     * @param $button CnbButton
     *
     * @return void
     */
    public function render_icon_color_chooser($action, $button) {
        // SINGLE does not configure the color via the Action, but via the Button
        // (On the Presentation tab)
        if ( $button && $button->type === 'SINGLE' ) {
            return;
        } ?>

        <tr>
            <th scope="row">
                <label for="actions[<?php echo esc_attr( $action->id ) ?>][backgroundColor]">
                    Button color
                </label>
            </th>
            <td>
                <input name="actions[<?php echo esc_attr( $action->id ) ?>][backgroundColor]"
                        id="actions[<?php echo esc_attr( $action->id ) ?>][backgroundColor]" type="text"
                        value="<?php echo esc_attr( $action->backgroundColor ) ?>"
                        class="cnb-color-field" data-default-color="#009900"/>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for="actions[<?php echo esc_attr( $action->id ) ?>][iconColor]">
                    Icon color
                </label>
            </th>
            <td>
                <input name="actions[<?php echo esc_attr( $action->id ) ?>][iconColor]"
                        id="actions[<?php echo esc_attr( $action->id ) ?>][iconColor]" type="text"
                        value="<?php echo esc_attr( $action->iconColor ) ?>"
                        class="cnb-color-field" data-default-color="#FFFFFF"/>
            </td>
        </tr>
        <?php

        // Actions on a Single or Multi button are not allowed to hide their Icon.
        // Only the Actions on a Full (Buttonbar) are allowed to hide their Icon.
        if ( $button && ($button->type === 'MULTI' || $button->type === 'DOTS') ) { ?>
            <input name="actions[<?php echo esc_attr( $action->id ) ?>][iconEnabled]" type="hidden" value="1"/>
        <?php } else { ?>
            <tr>
                <th scope="row"></th>
                <td>
                    <input type="hidden" name="actions[<?php echo esc_attr( $action->id ) ?>][iconEnabled]"
                            id="actions[<?php echo esc_attr( $action->id ) ?>][iconEnabled]" value="0"/>
                    <input id="cnb-action-icon-enabled" class="cnb_toggle_checkbox" type="checkbox"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][iconEnabled]"
                            id="actions[<?php echo esc_attr( $action->id ) ?>][iconEnabled]"
                            value="true" <?php checked( true, $action->iconEnabled ); ?>>
                    <label for="cnb-action-icon-enabled" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="cnb-action-icon-enabled"
                            class="cnb_toggle_state cnb_toggle_false">Hide icon</span>
                    <span data-cnb_toggle_state_label="cnb-action-icon-enabled"
                            class="cnb_toggle_state cnb_toggle_true">Show icon</span>
                </td>
            </tr>
        <?php
        }
    }
}
