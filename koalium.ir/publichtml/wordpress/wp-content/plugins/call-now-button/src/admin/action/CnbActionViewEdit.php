<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\button\CnbButton;
use cnb\admin\domain\CnbDomain;
use cnb\admin\models\ValidationMessage;
use cnb\admin\partials\Preview;
use cnb\utils\CnbAdminFunctions;
use cnb\CnbHeaderNotices;
use cnb\notices\CnbAdminNotices;
use cnb\utils\CnbUtils;
use stdClass;
use WP_Locale;

class CnbActionViewEdit {
    /**
     * @param $action CnbAction
     *
     * @return void
     */
    function add_header( $action ) {
        if ( is_wp_error( $action ) ) {
            esc_html_e( 'An error occurred' );

            return;
        }
        if ( $action->id !== 'new' ) {
            $actionTypes = ( new CnbAdminFunctions() )->cnb_get_action_types();
            $name        = $actionTypes[ $action->actionType ]->name;
            if ( $action->actionValue ) {
                $name = $action->actionValue;
            }
            echo esc_html__( 'Editing action' ) . ' <span class="cnb_button_name">' . esc_html( $name ) . '</span>';
        } else {
            echo esc_html__( 'Add action' );
        }
    }

    /**
     * @param $button CnbButton
     * @param $tab string
     *
     * @return string
     */
    private function create_tab_url( $button, $tab ) {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page'   => CNB_SLUG,
                'action' => 'edit',
                'type'   => strtolower( $button->type ),
                'id'     => $button->id,
                'tab'    => $tab,
            ),
            $url );
    }

    /**
     *
     * WP_Locale considers "0" to be Sunday, whereas the CallNowButton APi considers "0" to be Monday. See the below table:
     *
     * ```
     * +-----------+-----------+------------+
     * | Day       | WP_Locale | API Server |
     * +-----------+-----------+------------+
     * | Monday    | 1         | 0          |
     * +-----------+-----------+------------+
     * | Tuesday   | 2         | 1          |
     * +-----------+-----------+------------+
     * | Wednesday | 3         | 2          |
     * +-----------+-----------+------------+
     * | Thursday  | 4         | 3          |
     * +-----------+-----------+------------+
     * | Friday    | 5         | 4          |
     * +-----------+-----------+------------+
     * | Saturday  | 6         | 5          |
     * +-----------+-----------+------------+
     * | Sunday    | 0         | 6          |
     * +-----------+-----------+------------+
     *```
     * So, we need to translate.
     *
     * @param int $wp_locale_day
     *
     * @return int The index for the CNB API Server
     */
    function wp_locale_day_to_daysofweek_array_index( $wp_locale_day ) {
        if ( $wp_locale_day == 0 ) {
            return 6;
        }

        return $wp_locale_day - 1;
    }

    /**
     * CNB week starts on Monday (0), WP_Local starts on Sunday (0)
     * See `wp_locale_day_to_daysofweek_array_index()`.
     *
     * This array only signifies the order to DISPLAY the days in the UI according to WP_Locale.
     * So, in this case, we make the UI render the week starting on Monday (1) and end on Sunday (0).
     */
    function get_order_of_days() {
        return array( 1, 2, 3, 4, 5, 6, 0 );
    }

    /**
     * previously cnb_render_form_action
     *
     * @param $action CnbAction
     * @param $button CnbButton
     * @param $domain CnbDomain
     */
    private function render_table( $action, $button = null, $domain = null ) {
        /**
         * @global WP_Locale $wp_locale WordPress date and time locale object.
         */
        global $wp_locale;

        // In case a domain is not passed, we take it from the button
        $domain = isset( $domain ) ? $domain : ( isset( $button ) ? $button->domain : null );

        $cnb_days_of_week_order = $this->get_order_of_days();

        if ( empty( $action->actionType ) ) {
            $action->actionType = 'PHONE';
        }
        if ( empty( $action->iconText ) ) {
            $action->iconText = ( new CnbUtils() )->cnb_actiontype_to_icontext( $action->actionType );
        }
        if ( empty( $action->iconType ) ) {
            $action->iconType = 'DEFAULT';
        }

        $adminFunctions = new CnbAdminFunctions();

        wp_enqueue_style( CNB_SLUG . '-jquery-ui' );
        wp_enqueue_style( CNB_SLUG . '-client' );
        wp_enqueue_script( CNB_SLUG . '-timezone-picker-fix' );

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( CNB_SLUG . '-action-edit-scheduler' );
        wp_enqueue_script( CNB_SLUG . '-action-edit-fields' );

        // For the image selector
        wp_enqueue_media();

	    (new Preview())->register_preview_data();
        // Uses domain timezone if no timezone can be found
        $timezone                        = ( isset( $action->schedule ) && ! empty( $action->schedule->timezone ) ) ? $action->schedule->timezone : ( isset( $domain ) ? $domain->timezone : null );
        $action_tz_different_from_domain = isset( $domain ) && ! empty( $domain->timezone ) && $domain->timezone !== $timezone;

        $timezone_set_correctly = ( new CnbHeaderNotices() )->is_timezone_valid( $domain );

        $domain_type = $domain != null && !is_wp_error($domain) ? $domain->type : null;
        $isPro = $domain_type === 'PRO';

        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $button->domain->id,
            ),
                admin_url( 'admin.php' ) );

        ?>
        <input type="hidden" name="actions[<?php echo esc_attr( $action->id ) ?>][id]"
                value="<?php if ( $action->id !== null && $action->id !== 'new' ) {
                    echo esc_attr( $action->id );
                } ?>"/>
        <input type="hidden" name="actions[<?php echo esc_attr( $action->id ) ?>][delete]"
                id="cnb_action_<?php echo esc_attr( $action->id ) ?>_delete" value=""/>
        <table data-tab-name="basic_options"
                class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>">
            <?php if ( ! $button ) { ?>
                <tr>
                    <th colspan="2"><h2>Action Settings</h2>
                    </th>
                </tr>
            <?php } ?>

            <tr class="cnb_hide_on_modal">
                <th scope="row"><label for="cnb_action_type">Button action</label></th>
                <td>
                    <select id="cnb_action_type" name="actions[<?php echo esc_attr( $action->id ) ?>][actionType]">
                        <?php foreach ( ( new CnbAdminFunctions() )->cnb_get_action_types() as $action_type_key => $action_type_value ) { ?>
                            <option
                                    value="<?php echo esc_attr( $action_type_key ) ?>"
                                <?php selected( $action_type_value->type, $action->actionType ) ?>
	                            <?php if (!$isPro && !in_array($domain_type, $action_type_value->plans)) { echo ' disabled="disabled"'; } ?>>
	                            <?php echo esc_html( $action_type_value->name ) ?>
                                <?php if (!$isPro && !in_array($domain_type, $action_type_value->plans)) { echo ' (PRO)'; } ?>
                            </option>
                        <?php } ?>
                    </select>

                    <p class="description cnb-action-properties cnb-action-properties-TALLY"><a target="_blank" href="https://tally.so?ref=callnowbutton&utm_source=callnowbutton&utm_medium=wordpress">Tally</a> is our favorite form tool.</p>
                </td>
            </tr>
            <tr class="cnb-action-value cnb_hide_on_modal">
                <th scope="row">
                    <label for="cnb_action_value_input">
                        <span id="cnb_action_value">Action value</span>
                    </label>
                </th>
                <td>
                    <input type="text" id="cnb_action_value_input"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][actionValue]"
                            value="<?php echo esc_attr( $action->actionValue ) ?>"/>
                    <p class="description cnb-action-properties cnb-action-properties-MAP">Preview on <a href="#"
                                                                                    onclick="cnb_action_update_map_link(this)"
                                                                                    target="_blank">Google Maps</a></p>
                    <p class="description cnb-action-properties cnb-action-properties-TALLY">ID is last part of the share link, e.g. <code>wA74do</code> for <code>https://tally.so/r/wA74do</code>.</p>
                    <p class="description cnb-action-properties cnb-action-properties-ANCHOR">The anchor can be either a HTML tag name (e.g. <code>body</code>), a name attribute (e.g. a form field) or an ID attribute.</p>
                    <p class="description cnb-action-properties cnb-action-properties-INTERCOM">E.g. <code>gkeb4bs</code>. See <a
                                href="https://www.intercom.com/help/en/articles/3539-where-can-i-find-my-workspace-id-app-id?utm_source=callnowbutton&utm_medium=callnowbutton-plugin"
                                target="_blank">this Intercom article</a> on how to find your app ID.</p>
                    <p class="description cnb-action-properties cnb-action-properties-VIBER cnb-action-properties-viber-pa-chat">For personal chat change <i>Chat type</i> below. </p>
                </td>
            </tr>
            <tr class="cnb-action-properties cnb-action-properties-intl-input">
                <th scope="row"><label id="cnb_action_value_input_intl_input" for="cnb_action_value_input_whatsapp"></label></th>
                <td>
                    <input type="tel" id="cnb_action_value_input_whatsapp"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][actionValueWhatsapp]"
                            value="<?php echo esc_attr( $action->actionValue ) ?>"/>
                    <p class="description" id="cnb-valid-msg">✓ Valid</p>
                    <p class="description" id="cnb-error-msg"></p>
                    <p class="description cnb-action-properties cnb-action-properties-VIBER cnb-action-properties-viber-chat">For Viber Bot change <i>Chat type</i> below.</p>
                </td>
            </tr>
	        <?php if ($button->type === 'DOTS') { ?>
        <input id="buttonTextField" type="hidden"
                name="actions[<?php echo esc_attr( $action->id ) ?>][labelText]"
                value="<?php echo esc_attr( $action->labelText ) ?>"/>
	        <?php } else { ?>
            <tr class="button-text cnb_hide_on_modal">
                <th scope="row"><label for="buttonTextField">Button label</label></th>
                <td>
                    <input id="buttonTextField" type="text"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][labelText]"
                            value="<?php echo esc_attr( $action->labelText ) ?>" maxlength="30" placeholder="Optional"/>
            <?php } ?>
                </td>
            </tr>

            <?php $this->render_action_settings($action, $button); ?>

        </table>
        <table data-tab-name="scheduler"
                class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'scheduler' ) ) ?>">
            <tr class="cnb_hide_on_modal">
                <th></th>
                <td></td>
            </tr>
            <tr class="cnb_hide_on_modal">
                <th scope="row">Show at all times</th>
                <td>
                    <?php
                    $showAlwaysValue = $action->id === 'new' || (( isset( $action->schedule ) && ($action->schedule->showAlways === null || $action->schedule->showAlways) ));

                    if ( $timezone_set_correctly ) { ?>
                        <input name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][showAlways]" type="hidden"
                                value="<?php if ( $button->domain->type === 'STARTER' ) { echo 'true'; } else { echo 'false'; } ?>"/>
                        <input id="actions_schedule_show_always" class="cnb_toggle_checkbox"
                                onchange="return cnb_hide_on_show_always();"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][showAlways]"
                                type="checkbox"
                                value="true"
                                <?php if ( $button->domain->type === 'STARTER' ) {
                                    $showAlwaysValue = true;
                                    ?>disabled="disabled"<?php } ?>
                                <?php checked( true, $showAlwaysValue ); ?>
                        />
                        <label for="actions_schedule_show_always" class="cnb_toggle_label">Toggle</label>
                        <span data-cnb_toggle_state_label="actions_schedule_show_always"
                                class="cnb_toggle_state cnb_toggle_true">Yes</span>
                        <span data-cnb_toggle_state_label="actions_schedule_show_always"
                                class="cnb_toggle_state cnb_toggle_false">(No)</span>
                    <?php } else if ( $showAlwaysValue ) { ?>
                        <p class="description"><span class="dashicons dashicons-warning"></span>The scheduler is
                            disabled because your timezone is not set correctly yet.</p>
                        <input id="actions_schedule_show_always" class="cnb_toggle_checkbox"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][showAlways]"
                                type="checkbox" value="true" checked="checked"/>
                    <?php } else { ?>
                        <input name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][showAlways]" type="hidden"
                                value="false"/>
                        <input id="actions_schedule_show_always" class="cnb_toggle_checkbox"
                                onchange="return cnb_hide_on_show_always();"
                                name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][showAlways]"
                                type="checkbox"
                                value="true"/>
                        <label for="actions_schedule_show_always" class="cnb_toggle_label">Toggle</label>
                        <span data-cnb_toggle_state_label="actions_schedule_show_always"
                                class="cnb_toggle_state cnb_toggle_true">Yes</span>
                        <span data-cnb_toggle_state_label="actions_schedule_show_always"
                                class="cnb_toggle_state cnb_toggle_false">(No)</span>
                        <p class="description"><span class="dashicons dashicons-warning"></span>Please set your timezone
                            before making any more changes. See the notice at the top of the page for more information.
                        </p>
                    <?php } ?>
                    <?php if ( $button->domain->type === 'STARTER' ) { ?>
                        <p class="description">
                            Scheduling is a <span class="cnb-pro-badge">Pro</span> feature.
                            <a href="<?php echo esc_url( $upgrade_link ) ?>">Upgrade</a>.
                        </p>
                    <?php } ?>

                </td>
            </tr>
            <tr>
                <td colspan="2" class="cnb_padding_0">
                    <span id="domain-timezone-notice-placeholder"></span>
                </td>
            </tr>
            <tr class="cnb_hide_on_show_always">
                <th>Set days</th>
                <td>
                    <?php
                    foreach ( $cnb_days_of_week_order as $cnb_day_of_week ) {
                        $api_server_index = $this->wp_locale_day_to_daysofweek_array_index( $cnb_day_of_week );
                        echo '
                <input class="cnb_day_selector" id="cnb_weekday_' . esc_attr( $api_server_index ) . '" type="checkbox" name="actions[' . esc_attr( $action->id ) . '][schedule][daysOfWeek][' . esc_attr( $api_server_index ) . ']" value="true" ' . checked( isset( $action->schedule ) && isset( $action->schedule->daysOfWeek ) && isset( $action->schedule->daysOfWeek[ $api_server_index ] ) && $action->schedule->daysOfWeek[ $api_server_index ], true, false ) . '>
            	  <label title="' . esc_attr( $wp_locale->get_weekday( $cnb_day_of_week ) ) . '" class="cnb_day_selector" for="cnb_weekday_' . esc_attr( $api_server_index ) . '">' . esc_attr( $wp_locale->get_weekday_abbrev( $wp_locale->get_weekday( $cnb_day_of_week ) ) ) . '</label>
                ';
                    }

                    ?>
                </td>
            </tr>
            <tr class="cnb_hide_on_show_always">
                <th><label for="actions_schedule_outside_hours">After hours</label></th>
                <td>
                    <input id="actions_schedule_outside_hours" class="cnb_toggle_checkbox"
                            name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][outsideHours]" type="checkbox"
                            value="true" <?php checked( true, isset( $action->schedule ) && $action->schedule->outsideHours ); ?> />
                    <label for="actions_schedule_outside_hours" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="actions_schedule_outside_hours"
                            class="cnb_toggle_state cnb_toggle_true">Active</span>
                    <span data-cnb_toggle_state_label="actions_schedule_outside_hours"
                            class="cnb_toggle_state cnb_toggle_false">(Off)</span>
                </td>
            </tr>
            <tr class="cnb_hide_on_show_always">
                <th>Set times</th>
                <td class="cnb-scheduler-slider">
                    <p id="cnb-schedule-range-text"></p>
                    <div id="cnb-schedule-range" style="max-width: 300px"></div>
                </td>
            </tr>
            <tr class="cnb_hide_on_show_always cnb_advanced_view">
                <th><label for="actions-schedule-start">Start time</label></th>
                <td><input type="time" name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][start]"
                            id="actions-schedule-start" value="<?php if ( isset( $action->schedule ) ) {
                        echo esc_attr( $action->schedule->start );
                    } ?>"></td>
            </tr>
            <tr class="cnb_hide_on_show_always cnb_advanced_view">
                <th><label for="actions-schedule-stop">End time</label></th>
                <td><input type="time" name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][stop]"
                            id="actions-schedule-stop" value="<?php if ( isset( $action->schedule ) ) {
                        echo esc_attr( $action->schedule->stop );
                    } ?>"></td>
            </tr>
            <tr class="cnb_hide_on_show_always<?php if ( ! $action_tz_different_from_domain ) { ?> cnb_advanced_view<?php } ?>">
                <th><label for="actions[<?php echo esc_attr( $action->id ) ?>][schedule][timezone]">Timezone</label>
                </th>
                <td>
                    <select name="actions[<?php echo esc_attr( $action->id ) ?>][schedule][timezone]"
                            id="actions[<?php echo esc_attr( $action->id ) ?>][schedule][timezone]"
                            class="cnb_timezone_picker">
                        <?php
                        // phpcs:ignore WordPress.Security
                        echo wp_timezone_choice( $timezone );
                        ?>
                    </select>
                    <p class="description" id="domain_timezone-description">
                        <?php if ( empty( $timezone ) ) { ?>
                            Please select your timezone.
                        <?php } else { ?>
                            Set to <code><?php echo esc_html( $timezone ) ?></code>.
                        <?php } ?>
                    </p>
                    <?php if ( $action_tz_different_from_domain ) { ?>
                        <div class="notice notice-warning inline">
                            <p>Be aware that the timezone for this action
                                (<code><?php echo esc_html( $timezone ) ?></code>) is different from the timezone for
                                your domain (<code><?php echo esc_html( $domain->timezone ) ?></code>).</p>
                        </div>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php
    }

	/**
     * @param $action CnbAction
	 * @param $button CnbButton
     *
	 * @return void
	 */
    private function render_action_settings($action, $button) {
	    $icon_picker = new ActionIconPicker();
	    $icon_picker->render($action, $button);
	    $icon_picker->render_icon_color_chooser($action, $button);

	    (new ActionSettingsSms())->render($action);
	    (new ActionSettingsWhatsapp())->render($action, $button);
	    (new ActionSettingsFacebook())->render($action, $button);
	    (new ActionSettingsEmail())->render($action);
	    (new ActionSettingsLink())->render($action);
	    (new ActionSettingsMap())->render($action);
	    (new ActionSettingsIframe())->render($action);
	    (new ActionSettingsTally())->render($action);
	    (new ActionSettingsIntercom())->render($action);
	    (new ActionSettingsSkype())->render($action);
	    (new ActionSettingsZalo())->render($action);
	    (new ActionSettingsViber())->render($action);
	    (new ActionSettingsLine())->render($action);
	    (new ActionSettingsWeChat())->render($action);
	    (new ActionSettingsChat())->render( $action );
    }
    /**
     * previously cnb_admin_page_action_edit_render_main
     * used by button-edit
     *
     * @param $action CnbAction
     * @param $button CnbButton
     * @param $domain CnbDomain
     */
    public function render_main( $action, $button, $domain = null ) {
        wp_enqueue_style( CNB_SLUG . '-intl-tel-input' );
        wp_enqueue_script( CNB_SLUG . '-intl-tel-input' );
        $bid = ( new CnbUtils() )->get_query_val( 'bid', null );
        // Set some sane defaults
        $action->backgroundColor = ! empty( $action->backgroundColor )
            ? $action->backgroundColor
            : '#009900';
        $action->iconColor       = ! empty( $action->iconColor )
            ? $action->iconColor
            : '#FFFFFF';
        /** @noinspection PhpTernaryExpressionCanBeReplacedWithConditionInspection */
        $action->iconEnabled = isset( $action->iconEnabled )
            // phpcs:ignore PHPCompatibility.FunctionUse
            ? boolval( $action->iconEnabled )
            : true;
        ?>
        <input type="hidden" name="bid" value="<?php echo esc_attr( $bid ) ?>"/>
        <input type="hidden" name="action_id" value="<?php echo esc_attr( $action->id ) ?>"/>
        <input type="hidden" name="_wpnonce" value="<?php echo esc_attr( wp_create_nonce( 'cnb-action-edit' ) ) ?>"/>
        <?php
        $this->render_table( $action, $button, $domain );
    }

    public function render() {
        $cnb_remote          = new CnbAppRemote();
        $action_id           = filter_input( INPUT_GET, 'id', @FILTER_SANITIZE_STRING );
        $action              = new CnbAction();
        $action->id          = 'new';
        $action->actionType  = 'PHONE';
        $action->actionValue = null;
        $action->labelText   = null;

        if ( strlen( $action_id ) > 0 && $action_id !== 'new' ) {
            $action = $cnb_remote->get_action( $action_id );
        }

        add_action( 'cnb_header_name', function () use ( $action ) {
            $this->add_header( $action );
        } );

        $back_to_button_link = '#';
        $button = null;
        $bid    = ( new CnbUtils() )->get_query_val( 'bid', null );
        if ( $bid !== null && ! is_wp_error( $action ) ) {
            $button = $cnb_remote->get_button( $bid );

            // Create back link
            $url                 = admin_url( 'admin.php' );
            $back_to_button_link = add_query_arg(
                array(
                    'page'   => 'call-now-button',
                    'action' => 'edit',
                    'id'     => $bid,
                ),
                $url );

            $action_verb = $action->id === 'new' ? 'adding' : 'editing';
            $message     = '<p><strong>You are ' . $action_verb . ' an Action</strong>.
                        Click <a href="' . esc_url( $back_to_button_link ) . '">here</a> to go back to continue configuring the Button.</p>';
            CnbAdminNotices::get_instance()->renderInfo( $message );
        }

        $url           = admin_url( 'admin-post.php' );
        $form_action   = esc_url( $url );
        $redirect_link = add_query_arg(
            array(
                'bid' => $bid,
            ),
            $form_action
        );

        $adminFunctions = new CnbAdminFunctions();

        wp_enqueue_script( CNB_SLUG . '-action-type-to-icon-text' );
        wp_enqueue_script( CNB_SLUG . '-form-to-json' );
        wp_enqueue_script( CNB_SLUG . '-preview' );
        wp_enqueue_script( CNB_SLUG . '-client' );
        wp_enqueue_script( CNB_SLUG . '-action-edit' );


	    do_action( 'cnb_header' );

        if ( is_wp_error( $action ) ) {
            return;
        }

        $notices = ValidationMessage::get_validation_notices_for_action($action);
        do_action('cnb_validation_notices', $notices, true);
        ?>
        <div class="cnb-two-column-section-preview">
            <div class="cnb-body-column">
                <div class="cnb-body-content">

                    <?php if ( $bid !== null ) { ?>
                        <h2 class="nav-tab-wrapper">
                            <a href="<?php echo esc_url( $back_to_button_link ); ?>" class="cnb-nav-tab"><span
                                        class="dashicons dashicons-arrow-left-alt"></span></a>
                            <a data-tab-name="basic_options"
                                href="<?php echo esc_url( $this->create_tab_url( $button, 'basic_options' ) ) ?>"
                                class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>">Basics</a>
                            <a data-tab-name="scheduler"
                                href="<?php echo esc_url( $this->create_tab_url( $button, 'scheduler' ) ) ?>"
                                class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'scheduler' ) ) ?>">Scheduling</a>
                        </h2>
                    <?php } ?>
                    <?php if ( $button ) { ?>
                        <script>
                            let cnb_button = <?php echo wp_json_encode( $button ); ?>;
                            let cnb_actions = <?php echo wp_json_encode( $button->actions ); ?>;
                            let cnb_domain = <?php echo wp_json_encode( $button->domain ) ?>;
                            // disable scheduler for the action-edit screen
                            let cnb_ignore_schedule = true
                        </script>
                    <?php } ?>

                    <form class="cnb-container cnb-validation" action="<?php echo esc_url( $redirect_link ); ?>"
                            method="post">
                        <input type="hidden" name="page" value="call-now-button-actions"/>
                        <input type="hidden" name="action"
                                value="<?php echo $action->id === 'new' ? 'cnb_create_action' : 'cnb_update_action' ?>"/>
                        <?php
                        $this->render_main( $action, $button );
                        submit_button();
                        ?>
                    </form>
                </div>
            </div>
            <div class="cnb-side-column">
                <div id="phone-preview">
                    <div class="phone-outside double">
                        <div class="speaker single"></div>
                        <div class="phone-inside single">
                            <div id="cnb-button-preview"></div>
                        </div>
                        <div class="mic double"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php do_action( 'cnb_footer' );
    }
}
