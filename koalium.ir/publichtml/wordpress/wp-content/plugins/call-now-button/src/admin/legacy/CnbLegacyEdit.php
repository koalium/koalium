<?php

namespace cnb\admin\legacy;

use cnb\utils\CnbAdminFunctions;
use cnb\utils\CnbUtils;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbLegacyEdit {
    public function render() {
        do_action( 'cnb_init', __METHOD__ );

        wp_enqueue_script( CNB_SLUG . '-legacy-edit' );

        add_action( 'cnb_header_name', array( $this, 'header' ) );

        do_action( 'cnb_header' );
        $this->render_form();
        do_action( 'cnb_footer' );
        do_action( 'cnb_finish' );
    }

    private function create_tab_url( $tab ) {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page'   => 'call-now-button',
                'action' => 'edit',
                'tab'    => $tab,
            ),
            $url );
    }

    public function render_tracking() {
        $cnb_options = get_option( 'cnb' );
        $cnb_utils   = new CnbUtils();
        ?>
        <tr>
            <th scope="row">Click tracking <a
                        href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/click-tracking/', 'legacy-settings-question-mark', 'click-tracking', 'legacy' ) ) ?>"
                        target="_blank" class="cnb-nounderscore">
                    <span class="dashicons dashicons-editor-help"></span>
                </a></th>
            <td>
                <div class="cnb-radio-item">
                    <input id="tracking3" type="radio" name="cnb[tracking]"
                            value="0" <?php checked( '0', $cnb_options['tracking'] ); ?> />
                    <label for="tracking3">Disabled</label>
                </div>
                <div class="cnb-radio-item">
                    <input id="tracking4" type="radio" name="cnb[tracking]"
                            value="3" <?php checked( '3', $cnb_options['tracking'] ); ?> />
                    <label for="tracking4">Latest Google Analytics (gtag.js)</label>
                </div>
                <div class="cnb-radio-item">
                    <input id="tracking1" type="radio" name="cnb[tracking]"
                            value="2" <?php checked( '2', $cnb_options['tracking'] ); ?> />
                    <label for="tracking1">Google Universal Analytics (analytics.js)</label>
                </div>
                <div class="cnb-radio-item">
                    <input id="tracking2" type="radio" name="cnb[tracking]"
                            value="1" <?php checked( '1', $cnb_options['tracking'] ); ?> />
                    <label for="tracking2">Classic Google Analytics (ga.js)</label>
                </div>
                <p class="description">Using Google Tag Manager? Set up click tracking in GTM. <a
                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/google-tag-manager-event-tracking/', 'legacy-settings-description', 'google-tag-manager-event-tracking', 'legacy' ) ) ?>"
                            target="_blank">Learn how to do this...</a></p>
            </td>
        </tr>
        <?php
    }

    public function render_conversions() {
        $cnb_options = get_option( 'cnb' );
        $cnb_utils   = new CnbUtils();
        ?>
        <tr>
            <th scope="row">Google Ads <a
                        href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/google-ads/', 'legacy-settings-question-mark', 'google-ads', 'legacy' ) ) ?>"
                        target="_blank" class="cnb-nounderscore">
                    <span class="dashicons dashicons-editor-help"></span>
                </a></th>
            <td class="conversions">
                <div class="cnb-radio-item">
                    <input id="cnb_conversions_0" name="cnb[conversions]" type="radio"
                            value="0" <?php checked( '0', $cnb_options['conversions'] ); ?> /> <label
                            for="cnb_conversions_0">Off </label>
                </div>
                <div class="cnb-radio-item">
                    <input id="cnb_conversions_1" name="cnb[conversions]" type="radio"
                            value="1" <?php checked( '1', $cnb_options['conversions'] ); ?> /> <label
                            for="cnb_conversions_1">Conversion Tracking using Google's global site tag </label>
                </div>
                <div class="cnb-radio-item">
                    <input id="cnb_conversions_2" name="cnb[conversions]" type="radio"
                            value="2" <?php checked( '2', $cnb_options['conversions'] ); ?> /> <label
                            for="cnb_conversions_2">Conversion Tracking using JavaScript</label>
                </div>
                <p class="description">Select this option if you want to track clicks on the button as Google Ads
                    conversions. This option requires the Event snippet to be present on the page. <a
                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/google-ads/', 'legacy-settings-description', 'google-ads', 'legacy' ) ) ?>"
                            target="_blank">Learn more...</a></p>
            </td>
        </tr>
        <?php
    }

    public function render_zoom() {
        $cnb_options = get_option( 'cnb' );
        ?>
        <tr class="zoom">
            <th scope="row"><label for="cnb_slider">Button size <span id="cnb_slider_value"></span></label></th>
            <td>
                <label class="cnb_slider_value">Smaller&nbsp;&laquo;&nbsp;</label>
                <input type="range" min="0.7" max="1.3" name="cnb[zoom]"
                        value="<?php echo esc_attr( $cnb_options['zoom'] ) ?>" class="slider" id="cnb_slider" step="0.1">
                <label class="cnb_slider_value">&nbsp;&raquo;&nbsp;Bigger</label>
            </td>
        </tr>
        <?php
    }

    public function render_zindex() {
        $cnb_options = get_option( 'cnb' );
        $cnb_utils   = new CnbUtils();
        ?>
        <tr class="z-index">
            <th scope="row"><label for="cnb_order_slider">Order (<span id="cnb_order_value"></span>)</label> <a
                        href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/set-order/', 'legacy-settings-question-mark', 'Order', 'legacy' ) ) ?>"
                        target="_blank"
                        class="cnb-nounderscore">
                    <span class="dashicons dashicons-editor-help"></span>
                </a></th>
            <td>
                <label class="cnb_slider_value">Backwards&nbsp;&laquo;&nbsp;</label>
                <input type="range" min="1" max="10" name="cnb[z-index]"
                        value="<?php echo esc_attr( $cnb_options['z-index'] ) ?>" class="slider2" id="cnb_order_slider"
                        step="1">
                <label class="cnb_slider_value">&nbsp;&raquo;&nbsp;Front</label>
                <p class="description">The default (and recommended) value is all the way to the front so the button
                    sits on top of everything else. In case you have a specific usecase where you want something else to
                    sit in front of the Call Now Button (e.g. a chat window or a cookie notice) you can move this
                    backwards one step at a time to adapt it to your situation.</p>
            </td>
        </tr>

        <?php
    }

    public function header() {
        echo esc_html( CNB_NAME ) . ' <span class="cnb-version">v' . esc_html( CNB_VERSION ) . '</span>';
    }

    private function render_form() {
        $cnb_options    = get_option( 'cnb' );
        $adminFunctions = new CnbAdminFunctions();
        $cnb_utils      = new CnbUtils();

	    $display_mode = isset( $cnb_options['displaymode'] ) ? $cnb_options['displaymode'] : 'MOBILE_ONLY';

        ?>
        <div class="cnb-two-column-section">
            <div class="cnb-body-column">
                <div class="cnb-body-content">

                    <h2 class="nav-tab-wrapper">
                        <a href="<?php echo esc_url( $this->create_tab_url( 'basic_options' ) ) ?>"
                            class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>"
                            data-tab-name="basic_options">Basics</a>
                        <a href="<?php echo esc_url( $this->create_tab_url( 'extra_options' ) ) ?>"
                            class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'extra_options' ) ) ?>"
                            data-tab-name="extra_options">Presentation</a>
                        <a href="<?php echo esc_url( $this->create_tab_url( 'scheduler' ) ) ?>" class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'scheduler' ) ) ?> cnb_disabled_feature"
                            data-tab-name="scheduler"><span class="dashicons dashicons-lock"></span> Scheduler</a>
                    </h2>

                    <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ) ?>"
                            class="cnb-container">
                        <?php settings_fields( 'cnb_options' ); ?>
                        <table class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>"
                                data-tab-name="basic_options">
                            <tr>
                                <th colspan="2"></th>
                            </tr>

                            <tr>
                                <th scope="row"><label for="cnb-active">Button status</label></th>

                                <td>
                                    <input type="hidden" name="cnb[active]" value="0"/>
                                    <input id="cnb-active" class="cnb_toggle_checkbox" type="checkbox" name="cnb[active]"
                                            value="1" <?php checked( '1', $cnb_options['active'] ); ?> />
                                    <label for="cnb-active" class="cnb_toggle_label">Toggle</label>
                                    <span data-cnb_toggle_state_label="cnb-active" class="cnb_toggle_state cnb_toggle_false">(Inactive)</span>
                                    <span data-cnb_toggle_state_label="cnb-active"
                                            class="cnb_toggle_state cnb_toggle_true">Active</span>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="cnb_action_type">Button action</label></th>
                                <td>
                                    <select>
                                        <option selected="selected">Phone</option>
                                        <option disabled>* Email</option>
                                        <option disabled>* SMS/Text</option>
                                        <option disabled>* WhatsApp</option>
                                        <option disabled>* Messenger</option>
                                        <option disabled>* Signal</option>
                                        <option disabled>* Telegram</option>
                                        <option disabled>* Link</option>
                                        <option disabled>* Location</option>
                                        <option disabled>* Scroll to point</option>
                                        <option disabled>* Skype</option>
                                        <option disabled>* Line</option>
                                        <option disabled>* Viber</option>
                                        <option disabled>* WeChat</option>
                                        <option disabled>* Content Window</option>
                                        <option disabled>* Tally form window</option>
                                    </select>
                                </td>
                            </tr>

                            <tr>
                                <th scope="row"><label for="cnb-number">Phone number</label> <a
                                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/basics/phone-number/', 'legacy-basics-question-mark', 'phone-number', 'legacy' ) ) ?>"
                                            target="_blank" class="cnb-nounderscore">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </a></th>
                                <td><input type="text" id="cnb-number" name="cnb[number]"
                                            value="<?php echo esc_attr( $cnb_options['number'] ) ?>"/></td>
                            </tr>
                            <tr class="button-text">
                                <th scope="row"><label for="buttonTextField">Button text</label><a
                                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/basics/using-text-buttons/', 'legacy-basics-question-mark', 'using-text-buttons', 'legacy' ) ) ?>"
                                            target="_blank" class="cnb-nounderscore">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </a></th>
                                <td>
                                    <input id="buttonTextField" type="text" name="cnb[text]"
                                            value="<?php echo esc_attr( $cnb_options['text'] ) ?>" maxlength="30" placeholder="Optional"/>
                                    <p class="description">Leave this field empty to only show an icon.</p>
                                </td>
                            </tr>
                        </table>

                        <table class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'extra_options' ) ) ?>"
                                data-tab-name="extra_options">
                            <tr>
                                <th colspan="2"></th>
                            </tr>

                            <tr>
                                <th scope="row"><label for="cnb-color">Button color</label></th>
                                <td><input id="cnb-color" name="cnb[color]" type="text"
                                            value="<?php echo esc_attr( $cnb_options['color'] ) ?>"
                                            class="cnb-color-field" data-default-color="#009900"/></td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="cnb-icon-color">Icon color</label></th>
                                <td><input id="cnb-icon-color" name="cnb[iconcolor]" type="text"
                                            value="<?php echo esc_attr( $cnb_options['iconcolor'] ) ?>"
                                            class="cnb-color-field" data-default-color="#ffffff"/></td>
                            </tr>
                            <tr>
                                <th scope="row">Position <a
                                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/presentation/button-position/', 'legacy-presentation-question-mark', 'button-position', 'legacy' ) ) ?>"
                                            target="_blank" class="cnb-nounderscore">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </a></th>
                                <td class="appearance">
                                    <div class="appearance-options">
                                        <div class="cnb-radio-item">
                                            <input type="radio" id="appearance1" name="cnb[appearance]"
                                                    value="right" <?php checked( 'right', $cnb_options['appearance'] ); ?>>
                                            <label title="right" for="appearance1">Right corner</label>
                                        </div>
                                        <div class="cnb-radio-item">
                                            <input type="radio" id="appearance2" name="cnb[appearance]"
                                                    value="left" <?php checked( 'left', $cnb_options['appearance'] ); ?>>
                                            <label title="left" for="appearance2">Left corner</label>
                                        </div>
                                        <div class="cnb-radio-item">
                                            <input type="radio" id="appearance3" name="cnb[appearance]"
                                                    value="middle" <?php checked( 'middle', $cnb_options['appearance'] ); ?>>
                                            <label title="middle" for="appearance3">Center</label>
                                        </div>
                                        <div class="cnb-radio-item">
                                            <input type="radio" id="appearance4" name="cnb[appearance]"
                                                    value="full" <?php checked( 'full', $cnb_options['appearance'] ); ?>>
                                            <label title="full" for="appearance4">Full bottom</label>
                                        </div>

                                        <!-- Extra placement options -->
                                        <br class="cnb-extra-placement">
                                        <div class="cnb-radio-item cnb-extra-placement <?php echo $cnb_options['appearance'] == 'mright' ? 'cnb-extra-active' : ''; ?>">
                                            <input type="radio" id="appearance5" name="cnb[appearance]"
                                                    value="mright" <?php checked( 'mright', $cnb_options['appearance'] ); ?>>
                                            <label title="mright" for="appearance5">Middle right</label>
                                        </div>
                                        <div class="cnb-radio-item cnb-extra-placement <?php echo $cnb_options['appearance'] == 'mleft' ? 'cnb-extra-active' : ''; ?>">
                                            <input type="radio" id="appearance6" name="cnb[appearance]"
                                                    value="mleft" <?php checked( 'mleft', $cnb_options['appearance'] ); ?>>
                                            <label title="mleft" for="appearance6">Middle left </label>
                                        </div>
                                        <br class="cnb-extra-placement">
                                        <div class="cnb-radio-item cnb-extra-placement <?php echo $cnb_options['appearance'] == 'tright' ? 'cnb-extra-active' : ''; ?>">
                                            <input type="radio" id="appearance7" name="cnb[appearance]"
                                                    value="tright" <?php checked( 'tright', $cnb_options['appearance'] ); ?>>
                                            <label title="tright" for="appearance7">Top right corner</label>
                                        </div>
                                        <div class="cnb-radio-item cnb-extra-placement <?php echo $cnb_options['appearance'] == 'tleft' ? 'cnb-extra-active' : ''; ?>">
                                            <input type="radio" id="appearance8" name="cnb[appearance]"
                                                    value="tleft" <?php checked( 'tleft', $cnb_options['appearance'] ); ?>>
                                            <label title="tleft" for="appearance8">Top left corner</label>
                                        </div>
                                        <div class="cnb-radio-item cnb-extra-placement <?php echo $cnb_options['appearance'] == 'tmiddle' ? 'cnb-extra-active' : ''; ?>">
                                            <input type="radio" id="appearance9" name="cnb[appearance]"
                                                    value="tmiddle" <?php checked( 'tmiddle', $cnb_options['appearance'] ); ?>>
                                            <label title="tmiddle" for="appearance9">Center top</label>
                                        </div>
                                        <div class="cnb-radio-item cnb-extra-placement <?php echo $cnb_options['appearance'] == 'tfull' ? 'cnb-extra-active' : ''; ?>">
                                            <input type="radio" id="appearance10" name="cnb[appearance]"
                                                    value="tfull" <?php checked( 'tfull', $cnb_options['appearance'] ); ?>>
                                            <label title="tfull" for="appearance10">Full top</label>
                                        </div>
                                        <a href="#" id="button-more-placements">More placement options...</a>
                                        <!-- END extra placement options -->
                                    </div>

                                    <div id="hideIconTR">
                                        <br>
                                        <input type="hidden" name="cnb[hideIcon]" value="0"/>
                                        <input id="hide_icon" type="checkbox" name="cnb[hideIcon]"
                                                value="1" <?php checked( '1', $cnb_options['hideIcon'] ); ?>>
                                        <label title="right" for="hide_icon">Remove icon</label>
                                    </div>
                                </td>
                            </tr>
                            <tr class="appearance">
                                <th scope="row"><label for="button_options_displaymode">Display on </label></th>
                                <td class="appearance">
                                    <select name="cnb[displaymode]" id="button_options_displaymode">
                                        <option value="MOBILE_ONLY"<?php selected( 'MOBILE_ONLY', $display_mode ) ?>>
                                            Mobile only
                                        </option>
                                        <option value="DESKTOP_ONLY"<?php selected( 'DESKTOP_ONLY', $display_mode ) ?>>
                                            Desktop only
                                        </option>
                                        <option value="ALWAYS"<?php selected( 'ALWAYS', $display_mode ) ?>>
                                            All screens
                                        </option>
                                    </select>
                                </td>
                            </tr>
                            <tr class="appearance">
                                <th scope="row"><label for="cnb-show">Limit appearance</label> <a
                                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/presentation/limit-appearance/', 'legacy-presentation-question-mark', 'limit-appearance', 'legacy' ) ) ?>"
                                            target="_blank" class="cnb-nounderscore">
                                        <span class="dashicons dashicons-editor-help"></span>
                                    </a></th>
                                <td>
                                    <input type="text" id="cnb-show" name="cnb[show]"
                                            value="<?php echo esc_attr( $cnb_options['show'] ) ?>"
                                            placeholder="E.g. 14, 345"/>
                                    <p class="description">Enter IDs of the posts &amp; pages, separated by commas
                                        (leave blank for all). <a
                                                href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/presentation/limit-appearance/', 'legacy-presentation-description', 'limit-appearance', 'legacy' ) ) ?>"
                                                target="_blank">Learn more...</a></p>
                                    <div class="cnb-radio-item">
                                        <input id="limit1" type="radio" name="cnb[limit]"
                                                value="include" <?php checked( 'include', $cnb_options['limit'] ); ?> />
                                        <label for="limit1">Limit to these posts and pages.</label>
                                    </div>
                                    <div class="cnb-radio-item">
                                        <input id="limit2" type="radio" name="cnb[limit]"
                                                value="exclude" <?php checked( 'exclude', $cnb_options['limit'] ); ?> />
                                        <label for="limit2">Exclude these posts and pages.</label>
                                    </div>
                                    <p class="description">Display Rules give you more control. <a href="<?php echo esc_url(( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()); ?>">Sign up now</a> (it's free).</p>
                                </td>
                            </tr>
                            <tr class="appearance">
                                <th scope="row"><label for="cnb-show-fp">Show button on front page</label></th>
                                <td>
                                    <input type="hidden" name="cnb[frontpage]" value="1"/>
                                    <input id="cnb-show-fp" class="cnb_toggle_checkbox" type="checkbox" name="cnb[frontpage]"
                                            value="0" <?php checked( '0', $cnb_options['frontpage'] ); ?> />
                                    <label for="cnb-show-fp" class="cnb_toggle_label">Toggle</label>
                                    <span data-cnb_toggle_state_label="cnb-show-fp" class="cnb_toggle_state cnb_toggle_false">(No)</span>
                                    <span data-cnb_toggle_state_label="cnb-show-fp"
                                            class="cnb_toggle_state cnb_toggle_true">Yes</span>
                                </td>
                            </tr>



                        </table>
                        <table class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'scheduler' ) ) ?>" data-tab-name="scheduler">
                            <tr>
                                <th colspan="2"><a href="<?php echo esc_url(( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()); ?>">Upgrade</a> to enable the scheduler.</th>
                            </tr>
                            <tr class="cnb_disabled_feature">
                            <th scope="row">Show at all times</th>
                            <td>
                                <input class="cnb_toggle_checkbox" type="checkbox" checked="checked" disabled>
                                <label for="actions_schedule_show_always" class="cnb_toggle_label" style="background-color:#b3afaf">Toggle</label>
                                <span data-cnb_toggle_state_label="actions_schedule_show_always" class="cnb_toggle_state cnb_toggle_true">Yes</span>
                                <span data-cnb_toggle_state_label="actions_schedule_show_always" class="cnb_toggle_state cnb_toggle_false">(No)</span>
                            </td>
                            </tr>
                            <tr class="cnb_disabled_feature">
                            <th>Set days</th>
                            <td>
                                <input disabled class="cnb_day_selector" id="cnb_weekday_0" type="checkbox">
								<label title="Monday" class="cnb_day_selector" for="cnb_weekday_0">Mon</label>

                                <input disabled class="cnb_day_selector" id="cnb_weekday_1" type="checkbox">
								<label title="Tuesday" class="cnb_day_selector" for="cnb_weekday_1">Tue</label>

                                <input disabled class="cnb_day_selector" id="cnb_weekday_2" type="checkbox">
								<label title="Wednesday" class="cnb_day_selector" for="cnb_weekday_2">Wed</label>

                                <input disabled class="cnb_day_selector" id="cnb_weekday_3" type="checkbox">
								<label title="Thursday" class="cnb_day_selector" for="cnb_weekday_3">Thu</label>

                                <input disabled class="cnb_day_selector" id="cnb_weekday_4" type="checkbox">
								<label title="Friday" class="cnb_day_selector" for="cnb_weekday_4">Fri</label>

                                <input disabled class="cnb_day_selector" id="cnb_weekday_5" type="checkbox">
								<label title="Saturday" class="cnb_day_selector" for="cnb_weekday_5">Sat</label>

                                <input disabled class="cnb_day_selector" id="cnb_weekday_6" type="checkbox">
								<label title="Sunday" class="cnb_day_selector" for="cnb_weekday_6">Sun</label>
                            </td>
                            </tr>
                            <tr class="cnb_disabled_feature">
                                <th><label for="actions_schedule_outside_hours">After hours</label></th>
                                <td>
                                    <input id="actions_schedule_outside_hours" disabled class="cnb_toggle_checkbox" type="checkbox">
                                    <label for="actions_schedule_outside_hours" class="cnb_toggle_label">Toggle</label>
                                </td>
                            </tr>
                            <tr class="cnb_disabled_feature">
                                <th>Set times</th>
                                <td class="cnb-scheduler-slider">
                                    <p id="cnb-schedule-range-text">From <strong>8:00 am</strong> till <strong>5:00 pm</strong></p>
                                </td>
                            </tr>
                        </table>
                        <table class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'advanced_options' ) ) ?>">
                            <tr>
                                <th colspan="2"><h2>Advanced Settings</h2></th>
                            </tr>
                            <?php
                            $this->render_tracking();
                            $this->render_conversions();
                            $this->render_zoom();
                            $this->render_zindex();
                            ?>
                        </table>
                        <?php submit_button(); ?>
                        <div  class="description" data-tab-name="basic_options">* <a href="<?php echo esc_url(( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()); ?>">Create an account</a> to enable extra button actions.</div>


                    </form>
                </div>
            </div>
            <div class="cnb-postbox-container cnb-side-column">
                <div class="cnb-on-active-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>">
                    <?php
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        'green',
                        'Need more power?',
                        '<p><strong>Sign up to add:</strong></p>
                        <p>
                            🧰 14 button actions<br>
                            🛎️ 5 unique buttons<br>
                            🎯 Advanced display rules
                        </p>
                ',
                        'format-chat',
                        'Get all this for <b>FREE</b>!',
                        'Learn more',
                        ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()
                    );
                    ?>
                    <?php
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        '',
                        'Upgrade to PRO',
                        '<p><b>PRO includes:</b></p>
                <p>🏗️ 99 unique buttons<br>
                ⏰ Button scheduler<br>
                💬 WhatsApp Chat module<br>
                🗂️ Multi-action buttons<br>
                🖼️ Slide-in content windows<br>
                📷 Custom images<br>
                🎁 And so much more</p>
                ',
                        'awards',
                        '<b>Boost your conversions!</b>',
                        'Sign up',
                        ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()
                    );
                    ?>
                </div>
                <div class="cnb-on-active-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'extra_options' ) ) ?>">
                    <?php
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        'purple',
                        'More control with Display Rules',
                        '<p>Do you need more flexibility in selecting the pages where you want a button to appear?</p>
                  <p>Sign up to unlock 3 methods for selecting the right pages:</p>
                  <p>&check; Page URL is ...<br>
                  &check; Page URL contains ...<br>
                  &check; Page path starts with ...</p>
                  <p>PRO adds 2 more:</p>
                  <p>&check; URL Parameter contains ...<br>
                  &check; URL matches RegEx ...</p>',
                        'visibility',
                        '',
                        'Learn more',
                        ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()
                    );

                    ?>
                </div>
                <div class="cnb-on-active-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'scheduler' ) ) ?>">
                    <?php
                    ( new CnbAdminFunctions() )->cnb_promobox(
                        'purple',
                        'Phones off at 6pm?',
                        '<p>Upgrade to enable a scheduler that allows you to set the days and hours that you are available.</p>' .
                        '<p>Use the scheduler to show a mail button when you\'re off and a phone button during your business hours.</p>',
                        'clock',
                        '<strong>Try it 14 days free!</strong>',
                        'Upgrade',
                        ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()
                    );
                    ?>
                </div>
            </div>
        </div>

        <?php
    }

}
