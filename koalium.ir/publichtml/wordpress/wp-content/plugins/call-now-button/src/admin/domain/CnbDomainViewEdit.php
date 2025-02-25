<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\models\CnbUser;
use cnb\admin\settings\CnbSettingsController;
use cnb\utils\CnbUtils;

class CnbDomainViewEdit {
    /**
     * @param $domain CnbDomain
     *
     * @return void
     */
    private function header( $domain ) {
        if ( $domain->name ) {
            echo esc_html__( 'Editing domain' ) . ' <span class="cnb_button_name">' . esc_html( $domain->name ) . '</span>';
        } else {
            echo esc_html__( 'Add domain' );
        }
    }

    /**
     * Main entrypoint, used by `domain-overview.php`.
     */
    function render() {
        $cnb_remote = new CnbAppRemote();
        $domain_id = filter_input( INPUT_GET, 'id', @FILTER_SANITIZE_STRING );

        $domain = new CnbDomain();
        if ( strlen( $domain_id ) > 0 && $domain_id !== 'new' ) {
            $domain = $cnb_remote->get_domain($domain_id);
        }

        // Set default values in case they are missing
        CnbDomain::setSaneDefault( $domain, $domain_id );

        add_action( 'cnb_header_name', function () use ( $domain ) {
            $this->header( $domain );
        } );

        wp_enqueue_script( CNB_SLUG . '-timezone-picker-fix' );

        do_action( 'cnb_header' );
        ?>

        <form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ) ?>" method="post">
            <input type="hidden" name="page" value="call-now-button"/>
            <input type="hidden" name="action"
                    value="<?php echo $domain_id === 'new' ? 'cnb_create_domain' : 'cnb_update_domain' ?>"/>
            <input type="hidden" name="_wpnonce"
                    value="<?php echo esc_attr( wp_create_nonce( $domain_id === 'new' ? 'cnb_create_domain' : 'cnb_update_domain' ) ) ?>"/>

            <table class="form-table nav-tab-active" role="presentation">
                <?php $this->render_form( $domain ) ?>
            </table>

            <?php submit_button(); ?>
        </form>

        <?php
        do_action( 'cnb_footer' );
    }

    /**
     * @param CnbDomain $domain
     *
     * @return void
     */
    function render_form_plan_details( $domain ) {
	    global $cnb_subscription_data;
        /** @var CnbUser $cnb_user */
        global $cnb_user;
        $url          = admin_url( 'admin.php' );
        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $domain->id,
            ),
                $url );
	    $payment_link =
		    add_query_arg( array(
			    'page'   => 'call-now-button-domains',
			    'action' => 'payment',
		    ),
			    $url );
        ?>
        <tr>
            <th>Plan</th>
            <td>
                <code><?php echo esc_html( $domain->type ) ?></code>
                <input name="domain[renew]" type="hidden" value="<?php echo $domain->renew ? 'true' : 'false' ?>"/>
                <?php
                if ( $domain->type === 'PRO' && $domain->status === 'TRIALING' ) {
                    echo ' (Free trial)';
                }
                if ( $cnb_user->is_pro_user() ) {
                    echo '(via PRO account)';
                }
                if ( $cnb_subscription_data && $cnb_subscription_data->invoiceUrl ) {
	                echo '⚠️<p class="description"><span class="cnb-alert-text"><a class="button button-primary" href="' . esc_url( $payment_link ) . '">Pay now</a> to reactivate Pro features.</span><br>(You have an outstanding invoice. Pro features are disabled until payment is received.)</p> ';
                } else if ( $domain->type !== 'PRO' && ! empty( $domain->id ) ) {
                    echo '<p class="description"><a href="' . esc_url( $upgrade_link ) . '">Upgrade</a> to <span class="cnb-pro-badge">Pro</span> to get tons of buttons, more features and extra button types.</p>';
                }
                if ( $domain->status === 'TRIALING' && ! empty( $domain->expires ) ) { ?>
                    <p class="description" id="domain_expires-description">
                        <?php
                        switch ($domain->renew) {
                            case 1:
                                echo ' Paid subscription is starting on ' . esc_html( $this->get_formatted_date( $domain->expires ) ) . '. To cancel your trial please visit the ';
                                break;
                            default:
                                echo ' Your free trial has been cancelled and you are reverted to the free plan on ' . esc_html( $this->get_formatted_date( $domain->expires ) ) . '. To stay on the PRO plan go to the ';
                        }
                        ?>
                        <a href="#" onclick="return cnb_goto_billing_portal()">billing portal</a>.
                    </p>
                <?php
                    } ?>
            </td>
        </tr>
        <?php if ( $cnb_user->is_pro_user() ) { ?>
            <tr>
            <th scope="row">
                Status
            </th>
                <td>This domain is managed via a PRO account. Contact the account owner for changes.</td>
            </tr>
        <?php } ?>
        <?php if ( $domain->type === 'PRO' && $domain->status === 'ACTIVE' && !$cnb_user->is_pro_user() ) { ?>
            <tr>
                <th scope="row">
                    Auto renew
                </th>
                <td>
            <?php
            if ( ! empty( $domain->expires ) ) { ?>
                <input id="cnb-renew" class="cnb_toggle_checkbox" name="domain[renew]" type="checkbox"
                            value="true" <?php checked( true, $domain->renew ); ?> />
                <label for="cnb-renew" class="cnb_toggle_label">Toggle</label>
                <span data-cnb_toggle_state_label="cnb-renew" class="cnb_toggle_state cnb_toggle_false">(Off)</span>
                <span data-cnb_toggle_state_label="cnb-renew" class="cnb_toggle_state cnb_toggle_true">On</span>

                <p class="description" id="domain_expires-description">
                    Your subscription will
                    <?php echo $domain->renew ? ' renew automatically ' : ' expire '; ?>
                    on <?php echo esc_html( $this->get_formatted_date( $domain->expires ) ) ?>.
                </p>
                <?php } ?>
                </td>
            </tr>
        <?php }
        }

    function render_form_tracking( $domain ) {
        $cnb_utils = new CnbUtils();
        ?>
        <tr>
            <th colspan="2"><h2>Tracking</h2></th>
        </tr>
        <tr>
            <th scope="row"><label for="google_analytics">Google Analytics<label></th>
            <td>
                <input type="hidden" name="domain[trackGA]" value="0"/>
                <input id="google_analytics" class="cnb_toggle_checkbox" name="domain[trackGA]" type="checkbox"
                        value="true" <?php checked( true, $domain->trackGA ); ?> />
                <label for="google_analytics" class="cnb_toggle_label">Enable GA tracking</label>
                <span data-cnb_toggle_state_label="google_analytics" class="cnb_toggle_state cnb_toggle_false">(Click tracking inactive)</span>
                <span data-cnb_toggle_state_label="google_analytics" class="cnb_toggle_state cnb_toggle_true">Click tracking active</span>

                <p class="description">
                    Supports Classic, Universal Analytics and Global site tag (v3 and v4).<br>
                    Using Google Tag Manager? Set up click tracking in GTM. <a
                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'web-app/domains/tracking-button-clicks-gtm/', 'domain-settings-description', 'GA-tracking-with-GTM' ) ) ?>"
                            target="_blank">Here's how...</a>
                </p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="conversion_tracking">Google Ads conversions</label></th>
            <td>
                <input type="hidden" name="domain[trackConversion]" value="0"/>
                <input id="conversion_tracking" class="cnb_toggle_checkbox" name="domain[trackConversion]"
                        type="checkbox" value="true" <?php checked( true, $domain->trackConversion ); ?> />
                <label for="conversion_tracking" class="cnb_toggle_label">Enable Google Ads conversion tracking</label>
                <span data-cnb_toggle_state_label="conversion_tracking" class="cnb_toggle_state cnb_toggle_false">(Conversion tracking inactive)</span>
                <span data-cnb_toggle_state_label="conversion_tracking" class="cnb_toggle_state cnb_toggle_true">Conversion tracking active</span>

                <p class="description">Select this option if you want to count clicks on the button as Google Ads
                    conversions. This option requires the Event snippet to be present on the page. <a
                            href="https://support.google.com/google-ads/answer/6331304" target="_blank">Learn
                        more...</a></p>
            </td>
        </tr>
        <?php
    }

    function render_form_button_display( $domain ) {
        $domain_controller              = new CnbDomainController();
        $cnb_utils                      = new CnbUtils();
        $domain_properties_zindex_order = $domain_controller->zindex_to_order( $domain->properties->zindex );
        $upgrade_link =
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'upgrade',
                'id'     => $domain->id,
            ),
                admin_url( 'admin.php' ) );

        ?>
        <tr>
            <th colspan="2"><h2>Button display</h2></th>
        </tr>
        <tr class="zoom">
            <th scope="row"><label for="cnb_slider">Button size <span id="cnb_slider_value"></span></label></th>
            <td>
                <fieldset>
                    <label class="cnb_slider_value" for="cnb_slider"
                            onclick="jQuery('#cnb_slider:enabled')[0].stepDown();cnb_update_sliders()">Smaller&nbsp;&laquo;&nbsp;</label>
                    <input type="range" min="0.7" max="1.3" step="0.1" name="domain[properties][scale]"
                            value="<?php echo esc_attr( $domain->properties->scale ) ?>" class="slider" id="cnb_slider">
                    <label class="cnb_slider_value" for="cnb_slider"
                            onclick="jQuery('#cnb_slider:enabled')[0].stepUp();cnb_update_sliders()">&nbsp;&raquo;&nbsp;Bigger</label>
                </fieldset>
            </td>
        </tr>
        <tr class="z-index">
            <th scope="row"><label for="cnb_order_slider">Order (<span id="cnb_order_value"></span>)</label> <a
                        href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/set-order/', 'domain-settings-question-mark', 'Order' ) ) ?>"
                        target="_blank"
                        class="cnb-nounderscore">
                    <span class="dashicons dashicons-editor-help"></span>
                </a></th>
            <td>
                <label class="cnb_slider_value" for="cnb_order_slider"
                        onclick="jQuery('#cnb_order_slider:enabled')[0].stepDown();cnb_update_sliders()">Backwards&nbsp;&laquo;&nbsp;</label>
                <input type="range" min="1" max="10" name="domain[properties][zindex]"
                        value="<?php echo esc_attr( $domain_properties_zindex_order ) ?>" class="slider2"
                        id="cnb_order_slider"
                        step="1">
                <label class="cnb_slider_value" for="cnb_order_slider"
                        onclick="jQuery('#cnb_order_slider:enabled')[0].stepUp();cnb_update_sliders()">&nbsp;&raquo;&nbsp;Front</label>
                <p class="description">The default (and recommended) value is all the way to the front so the
                    button sits on top of everything. <a
                            href="<?php echo esc_url( $cnb_utils->get_support_url( 'wordpress-free/settings/set-order/', 'domain-settings-question-mark', 'Order' ) ) ?>" target="_blank">Learn
                        more...</a></p>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="domain_properties_allow_multiple_buttons">Multiple buttons per page</label>
                <?php if ( $domain->type === 'STARTER' ) { ?>
                <a href="<?php echo esc_url( $upgrade_link ) ?>"><span class="cnb-pro-badge">Pro</span></a>
                <?php } ?>
            </th>
            <td>
                <input type="hidden" name="domain[properties][allowMultipleButtons]" value="false"/>
                <input
                    id="domain_properties_allow_multiple_buttons"
                    class="cnb_toggle_checkbox"
                    name="domain[properties][allowMultipleButtons]"
                    type="checkbox"
                    value="true"
                    <?php if ( $domain->type === 'STARTER' ) {
                    $domain->properties->allowMultipleButtons = false;
                        ?>disabled="disabled"<?php } ?>
                    <?php checked( true, $domain->properties->allowMultipleButtons ); ?>
                />
                <label for="domain_properties_allow_multiple_buttons" class="cnb_toggle_label">Allow multiple Buttons on
                    a single page</label>
                <span data-cnb_toggle_state_label="domain_properties_allow_multiple_buttons"
                        class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                <span data-cnb_toggle_state_label="domain_properties_allow_multiple_buttons"
                        class="cnb_toggle_state cnb_toggle_true">Enabled</span>
                <?php if ( $domain->type !== 'STARTER' ) { ?>
                    <p class="description">
                        When enabled, more than one button can be displayed on a single page.
                    </p>
                <?php } ?>
            </td>
        </tr>
        <?php
    }

    /**
     * @param $domain CnbDomain
     * @param $header boolean
     *
     * @return void
     */
    function render_form_advanced( $domain, $header = true ) {
        $show_advanced_view_only = CnbSettingsController::is_advanced_view();

        if ( $header ) { ?>
            <tr>
                <th colspan="2"><h2>Advanced</h2></th>
            </tr>
        <?php } ?>
        <tr>
            <th scope="row"><label for="domain_name">Domain name</label></th>
            <td>
                <input type="hidden" name="domain[id]" value="<?php echo esc_attr( $domain->id ) ?>"/>
                <?php if ( $show_advanced_view_only ) { ?>
                    <input type="text" id="domain_name" name="domain[name]"
                            value="<?php echo esc_attr( $domain->name ) ?>"
                            class="regular-text" <?php if ( ! empty( $domain->id ) ) {
                        echo 'disabled="disabled"';
                    } ?> />
                    <?php if ( ! empty( $domain->id ) ) { ?>
                        <p class="description">
                            <strong>Warning</strong>: Changing your domain name means remapping all existing Buttons for
                            that domain. Please use with caution. <a class="cnb_cursor_pointer"
                                                                    onclick="return jQuery('#domain_name').prop('disabled', false);">Click
                                here to change your domain.</a>
                        </p>
                    <?php } ?>
                <?php } else {
                    echo esc_html( $domain->name );
                } ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="domain_timezone">Timezone</label></th>
            <td>
                <?php
                // phpcs:ignore WordPress.Security
                echo $this->getTimezoneSelect( $domain );
                ?>
            </td>
        </tr>
        <tr>
            <th scope="row"><label for="domain_properties_debug">Debug mode</label></th>
            <td>
                <input type="hidden" name="domain[properties][debug]" value="false"/>
                <input id="domain_properties_debug" class="cnb_toggle_checkbox" name="domain[properties][debug]"
                        type="checkbox" value="true" <?php checked( true, $domain->properties->debug ); ?> />
                <label for="domain_properties_debug" class="cnb_toggle_label">Enable debugging mode</label>
                <span data-cnb_toggle_state_label="domain_properties_debug" class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                <span data-cnb_toggle_state_label="domain_properties_debug" class="cnb_toggle_state cnb_toggle_true">Enabled</span>

                <p class="description">
                    This setting enables debug information in your browser's console, which can help during
                    troubleshooting.
                </p>
            </td>
        </tr>
        <?php
    }

    public function getTimezoneSelect( $domain ) {
        if ( $domain && ! is_wp_error($domain) ) {
            $result = '<select name="domain[timezone]" id="domain_timezone" class="cnb_timezone_picker">';
            $result .= wp_timezone_choice( $domain->timezone );
            $result .= '</select>';
            $result .= '<p class="description" id="domain_timezone-description">';
            if ( empty( $domain->timezone ) ) {
                $wordpress_timezone_string = wp_timezone_string();
                if ( ( new CnbUtils() )->is_valid_timezone_string( $wordpress_timezone_string ) ) {
                    $result .= '<br/>WordPress is set to: <code>' . $wordpress_timezone_string . '</code>';
                }
            } else {
                $result .= 'Set to <code>' . esc_html( $domain->timezone ) . '</code>';
            }
            $result .= '</p>';

            return $result;
        }
        return '<p class="description" id="domain_timezone-description">Something went wrong</p>';
    }

    private function get_formatted_date( $datetime ) {
        return date( 'F j Y', strtotime( $datetime ) );
    }

    private function render_form( $domain ) {
        $this->render_form_plan_details( $domain );
        $this->render_form_tracking( $domain );
        $this->render_form_button_display( $domain );
        $this->render_form_advanced( $domain );
    }
}
