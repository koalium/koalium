<?php

namespace cnb\admin\settings;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\domain\CnbDomain;
use cnb\admin\domain\CnbDomainViewEdit;
use cnb\admin\legacy\CnbLegacyEdit;
use cnb\admin\models\CnbUser;
use cnb\utils\CnbAdminFunctions;
use cnb\notices\CnbAdminNotices;
use cnb\utils\CnbUtils;
use WP_Error;

class CnbSettingsViewEdit {
    function header() {
        echo 'Settings';
    }

    private function create_tab_url( $tab ) {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page' => 'call-now-button-settings',
                'tab'  => $tab,
            ),
            $url );
    }

    /**
     * This is only rendered on the /legacy/ version of the Plugin
     *
     * @return void
     */
    private function render_legacy_options() {
        $view = new CnbLegacyEdit();
        ?>
        <tr>
            <th colspan="2"><h2>Tracking</h2></th>
        </tr>
        <?php
        $view->render_tracking();
        $view->render_conversions();
        ?>
        <tr>
            <th colspan="2"><h2>Button display</h2></th>
        </tr>
        <?php
        $view->render_zoom();
        $view->render_zindex();
    }

    private function render_error_reporting_options() {
        $cnb_utils = new CnbUtils();
        ?>
        <tr>
            <th colspan="2"><h2>Miscellaneous</h2></th>
        </tr>
        <tr>
            <th>Errors and usage</th>
            <td>
                <input type="hidden" name="cnb[error_reporting]" value="0"/>
                <input id="cnb-error-reporting" class="cnb_toggle_checkbox" type="checkbox"
                        name="cnb[error_reporting]"
                        value="1" <?php checked( $cnb_utils->is_reporting_enabled() ); ?> />
                <label for="cnb-error-reporting" class="cnb_toggle_label">Toggle</label>
                <span data-cnb_toggle_state_label="cnb-error-reporting"
                        class="cnb_toggle_state cnb_toggle_false">(Not sharing)</span>
                <span data-cnb_toggle_state_label="cnb-error-reporting"
                        class="cnb_toggle_state cnb_toggle_true">Share</span>
                <p class="description">Allows us to capture anonymous error reports and usage statistics to help us
                    improve the product.</p>
            </td>
        </tr>
        <?php
    }

    /**
     * @param $cnb_user CnbUser|WP_Error
     *
     * @return void
     */
    private function render_account_options( $cnb_user ) {
        global $wp_version;
        $cnb_options             = get_option( 'cnb' );
        $show_advanced_view_only = CnbSettingsController::is_advanced_view();
        $adminFunctions          = new CnbAdminFunctions();
        $cnb_utils               = new CnbUtils();

        ?>
        <table data-tab-name="account_options"
                class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'account_options' ) ) ?>">
            <tr>
                <th colspan="2"></th>
            </tr>

            <?php if ( $cnb_user !== null && ! is_wp_error( $cnb_user ) ) { ?>
                <tr>
                    <th scope="row">Account owner</th>
                    <td>
                        <?php echo esc_html( $cnb_user->name ) ?>
                        <?php
                        if ( $cnb_user->email !== $cnb_user->name ) {
                            echo esc_html( ' (' . $cnb_user->email . ')' );
                        } ?>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Account ID</th>
                    <td>
                        <code id="cnb_user_id"><?php echo esc_html( $cnb_user->id ) ?></code>
                    </td>
                </tr>
                <?php if ($cnb_user->is_pro_user()) { ?>
                    <tr>
                        <th scope="row">Account status</th>
                        <td>
                            This domain is managed via a PRO account. Contact the account owner for changes.
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Invoices</th>
                        <td class="cnb-request-billing-portal">
                            <a href="#" onclick="return cnb_request_billing_portal()">Billing portal</a>
                            <div class="notice notice-success inline hidden cnb-request-billing-portal-result">
                                <p>A personal Billing Portal Link has been sent to the account owner.</p>
                            </div>

                        </td>
                    </tr>
                <?php  }else { ?>
                <tr>
                    <th scope="row">Invoices</th>
                    <td><a href="#" onclick="return cnb_goto_billing_portal()">Billing portal</a>
                    </td>
                </tr>
                <tr>
                    <th>Product updates</th>
                    <td>
                        <input type="hidden" name="cnb[user_marketing_email_opt_in]" value="0"/>
                        <input id="cnb_user_marketing_email_opt_in" class="cnb_toggle_checkbox"
                                name="cnb[user_marketing_email_opt_in]"
                                type="checkbox"
                                value="1" <?php checked( $cnb_user->marketingData->emailOptIn ); ?> />
                        <label for="cnb_user_marketing_email_opt_in" class="cnb_toggle_label">Receive e-mail</label>
                        <span data-cnb_toggle_state_label="cnb_user_marketing_email_opt_in"
                                class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                        <span data-cnb_toggle_state_label="user_marketing_email_opt_in"
                                class="cnb_toggle_state cnb_toggle_true">Enabled</span>
                        <p class="description">Receive email updates on new features we're adding and how to use
                            them.</p>
                    </td>
                </tr>
                <?php } ?>
            <?php } ?>
            <tr>
                <th scope="row">API key</th>
                <td>
                    <?php if ( is_wp_error( $cnb_user ) || $show_advanced_view_only ) { ?>
                        <label>
                            <input type="text" class="regular-text" name="cnb[api_key]"
                                    id="cnb_api_key"
                                    placeholder="e.g. b52c3f83-38dc-4493-bc90-642da5be7e39"/>
                        </label>
                        <p class="description">Get your API key at <a
                                    href="<?php echo esc_url( $cnb_utils->get_website_url( '', 'settings-account', 'get-api-key' ) ) ?>"><?php echo esc_html( CNB_WEBSITE ) ?></a>
                        </p>
                    <?php } ?>
                    <?php if ( is_wp_error( $cnb_user ) && ! empty( $cnb_options['api_key'] ) ) { ?>
                        <p><span class="dashicons dashicons-warning"></span> There is an API key,
                            but it seems to be invalid or outdated.</p>
                        <p class="description">Clicking "Disconnect account" will drop the API key and disconnect the
                            plugin from your NowButtons.com account. You will lose access to your buttons and all cloud functionality
                            until you reconnect with a NowButtons.com account.
                            <br>
                            <input type="button" name="cnb_api_key_delete" id="cnb_api_key_delete"
                                    class="button button-link"
                                    value="<?php esc_attr_e( 'Disconnect account' ) ?>"
                                    onclick="return cnb_delete_apikey();">
                        </p>
                    <?php } ?>
                    <?php if ( ! is_wp_error( $cnb_user ) && isset( $cnb_options['api_key'] ) ) {
                        $icon = version_compare( $wp_version, '5.5.0', '<' ) ? 'dashicons-yes' : 'dashicons-saved';
                        ?>
                        <p><strong><span class="dashicons <?php echo esc_attr( $icon ) ?>"></span></strong>
                            The plugin is connected to your NowButtons.com account.</p>
                        <p>
                            <input type="button" name="cnb_api_key_delete" id="cnb_api_key_delete"
                                    class="button button-secondary"
                                    value="<?php esc_attr_e( 'Disconnect account' ) ?>"
                                    onclick="return cnb_delete_apikey();"></p>
                        <p class="description">Clicking "Disconnect account" will drop the API key and disconnect the
                            plugin from your NowButtons.com account. You will lose access to your buttons and all cloud functionality
                            until you reconnect with a NowButtons.com account.</p>


                        <input type="hidden" name="cnb[api_key]" id="cnb_api_key" value="delete_me"
                                disabled="disabled"/>
                    <?php } ?>
                </td>
            </tr>
        </table>
        <?php
    }

	/**
	 * @param $cnb_domain CnbDomain|WP_Error
	 * @param $cnb_user CnbUser|WP_Error
	 *
	 * @return void
	 */
    private function render_advanced_options( $cnb_domain, $cnb_user ) {
        $cnb_options = get_option( 'cnb' );
        global $cnb_domains;
        /** @var $cnb_settings UrlSettings */
	    global $cnb_settings;

        $adminFunctions     = new CnbAdminFunctions();
        $cnbAppRemote       = new CnbAppRemote();
        $cnb_clean_site_url = $cnbAppRemote->cnb_clean_site_url();
        $status             = CnbSettingsController::getStatus( $cnb_options );

        $user_nonce = wp_create_nonce( 'cnb-user' );
        $switch = $cnb_settings->get_storage_type() === 'R2' ? 'GCS' : 'R2';
        ?>
        <table data-tab-name="advanced_options"
                class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'advanced_options' ) ) ?>">
            <?php if ( isset( $cnb_domain ) && ! is_wp_error( $cnb_domain ) && $status === 'cloud' ) { ?>
                <tr>
                    <th colspan="2">
                        <h2>Domain settings</h2>
                        <input type="hidden" id="cnb_domain_id" value="<?php echo esc_attr( $cnb_domain->id ) ?>">
                    </th>
                </tr>
                <?php
                ( new CnbDomainViewEdit() )->render_form_advanced( $cnb_domain, false );
            } ?>
            <tr class="when-cloud-enabled cnb_advanced_view">
                <th colspan="2"><h2>For power users</h2></th>
            </tr>
            <tr class="when-cloud-enabled cnb_advanced_view">
                <th><label for="cnb-advanced-view">Advanced view</label></th>
                <td>
                    <input type="hidden" name="cnb[advanced_view]" value="0"/>
                    <input id="cnb-advanced-view" class="cnb_toggle_checkbox" type="checkbox"
                            name="cnb[advanced_view]"
                            value="1" <?php checked( '1', $cnb_options['advanced_view'] ); ?> />
                    <label for="cnb-advanced-view" class="cnb_toggle_label">Toggle</label>
                    <span data-cnb_toggle_state_label="cnb-advanced-view"
                            class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                    <span data-cnb_toggle_state_label="cnb-advanced-view"
                            class="cnb_toggle_state cnb_toggle_true">Enabled</span>
                    <p class="description">For power users only.</p>
                </td>
            </tr>
            <?php if ( $status === 'cloud' ) { ?>
                <tr class="cnb_advanced_view">
                    <th><label for="cnb-show-traces">Show traces</label></th>
                    <td>
                        <input type="hidden" name="cnb[footer_show_traces]" value="0"/>
                        <input id="cnb-show-traces" class="cnb_toggle_checkbox" type="checkbox"
                                name="cnb[footer_show_traces]"
                                value="1" <?php checked( '1', $cnb_options['footer_show_traces'] ); ?> />
                        <label for="cnb-show-traces" class="cnb_toggle_label">Toggle</label>
                        <span data-cnb_toggle_state_label="cnb-show-traces"
                                class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                        <span data-cnb_toggle_state_label="cnb-show-traces"
                                class="cnb_toggle_state cnb_toggle_true">Enabled</span>
                        <p class="description">Display API calls and timings in the footer.</p>
                    </td>
                </tr>
                <?php if ( ! is_wp_error( $cnb_user ) && isset( $cnb_domain ) ) { ?>
                    <tr class="when-cloud-enabled">
                        <th scope="row"><label for="cnb[cloud_use_id]">JavaScript snippet</label></th>
                        <td>
                            <div>
                                <?php if ( $cnb_domain instanceof WP_Error ) {
                                    CnbAdminNotices::get_instance()->warning( 'Almost there! Create your domain using the button at the top of this page.' )
                                    ?>
                                <?php } ?>
                                <?php if ( isset( $cnb_options['cloud_use_id'] ) ) { ?>
                                    <label><select name="cnb[cloud_use_id]" id="cnb[cloud_use_id]">


                                            <option
                                                    value="<?php echo esc_attr( $cnb_user->id ) ?>"
                                                <?php selected( $cnb_user->id, $cnb_options['cloud_use_id'] ) ?>
                                            >
                                                Full account (all domains)
                                            </option>

                                            <?php
                                            $loop_domains = array_filter( $cnb_domains, function ( $domain ) use ( $cnb_options, $cnb_clean_site_url ) {
                                                if ( CnbSettingsController::is_advanced_view() ) {
                                                    return true;
                                                } // In case of advanced mode, show all
                                                if ( $domain->name === $cnb_clean_site_url ) {
                                                    return true;
                                                } // Always show the current domain
                                                if ( $domain->id === $cnb_options['cloud_use_id'] ) {
                                                    return true;
                                                } // If a previous weird option was selected, allow it

                                                return false;
                                            } );
                                            foreach ( $loop_domains as $domain ) { ?>
                                                <option
                                                        value="<?php echo esc_attr( $domain->id ) ?>"
                                                    <?php selected( $domain->id, $cnb_options['cloud_use_id'] ) ?>
                                                >
                                                    <?php echo esc_html( $domain->name ) ?>
                                                    (single domain)
                                                </option>
                                            <?php } ?>

                                        </select></label>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
    		    <?php } ?>
	            <?php if ( ! is_wp_error( $cnb_user ) && isset( $cnb_domain ) ) { // Aliases ?>
                    <tr class="when-cloud-enabled cnb_advanced_view">
                        <th scope="row"><label for="add-alias-input">Aliases</label></th>
                        <td>
            			    <?php if (!empty($cnb_domain->aliases)) { ?>
                            Current aliases:
                            <ul>
                                <?php foreach ($cnb_domain->aliases as $alias) { ?>
                                    <li>
                                        <input type="hidden" name="domain[aliases][]" value="<?php echo esc_attr($alias) ?>" />
                                        <?php echo esc_html($alias) ?>
                                        <input type="button" name="delete-alias-button" value="Delete" />
                                    </li>
                                <?php } ?>
                            </ul>
			                <?php } ?>
                            <?php if (empty($cnb_domain->aliases)) { ?>
                                <div>You have no aliases (yet?).</div>
                            <?php } ?>
                            <div id="add-alias-div">
                                Add a new domain alias:
                                <input type="text" id="add-alias-input" placeholder="Domain name">
                                <a class="button" id="add-alias-button">Add domain</a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                <tr class="when-cloud-enabled cnb_advanced_view">
                    <th><label for="cnb-all-domains">Show all buttons</label></th>
                    <td>
                        <input type="hidden" name="cnb[show_all_buttons_for_domain]" value="0"/>
                        <input id="cnb-all-domains" class="cnb_toggle_checkbox" type="checkbox"
                                name="cnb[show_all_buttons_for_domain]"
                                value="1" <?php checked( '1', $cnb_options['show_all_buttons_for_domain'] ); ?> />
                        <label for="cnb-all-domains" class="cnb_toggle_label">Toggle</label>
                        <span data-cnb_toggle_state_label="cnb-all-domains"
                                class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                        <span data-cnb_toggle_state_label="cnb-all-domains"
                                class="cnb_toggle_state cnb_toggle_true">Enabled</span>
                        <p class="description">When checked, the "All Buttons" overview shows all
                            buttons for this account, not just for the current domain.</p>
                    </td>
                </tr>
                <tr class="when-cloud-enabled cnb_advanced_view">
                    <th><label for="cnb[api_base]">API endpoint</label></th>
                    <td><label>
                            <input type="text" id="cnb[api_base]" name="cnb[api_base]"
                                    class="regular-text"
                                    value="<?php echo esc_attr( CnbAppRemote::cnb_get_api_base() ) ?>"/>
                        </label>
                        <p class="description">The API endpoint to use to communicate with the
                            CallNowButton Cloud service.<br/>
                            <strong>Do not change this unless you know what you're doing!</strong>
                        </p>
                    </td>
                </tr>
                <tr class="cnb_advanced_view">
                    <th><label for="cnb-api-caching">API caching</label></th>
                    <td>
                        <input type="hidden" name="cnb[api_caching]" value="0"/>
                        <input id="cnb-api-caching" class="cnb_toggle_checkbox" type="checkbox"
                                name="cnb[api_caching]"
                                value="1" <?php checked( '1', $cnb_options['api_caching'] ); ?> />
                        <label for="cnb-api-caching" class="cnb_toggle_label">Toggle</label>
                        <span data-cnb_toggle_state_label="cnb-api-caching"
                                class="cnb_toggle_state cnb_toggle_false">(Disabled)</span>
                        <span data-cnb_toggle_state_label="cnb-api-caching"
                                class="cnb_toggle_state cnb_toggle_true">Enabled</span>
                        <p class="description">Cache API requests (using WordPress transients)</p>
                    </td>
                </tr>
                <tr class="cnb_advanced_view">
                    <th><label for="cnb-storage_type">Storage type</label></th>
                    <td>
                        <p>Storage type: <code><?php echo esc_html($cnb_settings->get_storage_type())?></code></p>
                        <p class="description">What storage backend is NowButtons using?</p>

                        <p>
                            JS Location: <code><?php echo esc_html($cnb_settings->get_js_location())?></code><br />
                            CSS Location: <code><?php echo esc_html($cnb_settings->get_css_location())?></code><br />
                            Static Root: <code><?php echo esc_html($cnb_settings->get_static_root())?></code><br />
                            Chat Root: <code><?php echo esc_html($cnb_settings->get_chat_root())?></code>
                        </p>
                        <p class="description">Snippet locations</p>

                        <p>
                            User Root: <code><?php echo esc_html($cnb_settings->get_user_root())?></code>
                        </p>
                        <p class="description">Root for the User files</p>
                        <div>
                            <input
                                    class="cnb-switch-storage-type button button-secondary"
                                    type="button"
                                    data-storage-type="<?php echo esc_attr($switch) ?>"
                                    data-wpnonce="<? echo esc_attr($user_nonce) ?>"
                                    value="Switch to <?php echo esc_attr($switch) ?>"
                            />
                            <div class="notice inline hidden cnb-switch-storage-type-result"></div>
                        </div>
                    </td>
                </tr>
            <?php } // end of cloud check ?>
        </table>
        <?php
    }

    /**
     * @param $use_cloud boolean
     * @param $cnb_domain CnbDomain
     *
     * @return void
     */
    private function render_promos( $use_cloud, $cnb_domain, $status ) {
        echo '<div class="cnb-postbox-container cnb-side-column">';
        if ( ! $use_cloud && $status != 'cloud' ) {
            ( new CnbAdminFunctions() )->cnb_promobox(
                'green',
                'Enable NowButtons features!',
                '<p>Sign up to enable NowButtons features. A set of additional actions and features to power your website.</p>',
                'unlock',
                '<strong>Get more for FREE!</strong>',
                'Learn more',
                ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page()
            );
        }
        if ( $use_cloud && isset( $cnb_domain ) && ! is_wp_error( $cnb_domain ) && $cnb_domain->type !== 'PRO' ) {
            $custom_image = plugins_url('resources/images/custom-image.jpg', CNB_PLUGINS_URL_BASE );
            $schedule_illustration = plugins_url('resources/images/scheduler.png', CNB_PLUGINS_URL_BASE );
            $promoboxes = range(1,3);
            shuffle($promoboxes);
            $promoItem = array_rand($promoboxes);
            $upgrade_url = ( new CnbUtils() )->get_cnb_domain_upgrade();
            if ( $promoItem == 1) {
                ( new CnbAdminFunctions() )->cnb_promobox(
                    'green',
                    'Schedule your buttons',
                    '<h4>Show a call button during office hours</h4>' .
                    '<div class="cnb-center" style="padding: 10px 30px"><img src="' . esc_url( $schedule_illustration ) . '" alt="Upgrade your domain to PRO with an extra discount" style="max-width:300px; width:100%; height:auto;" /></div>' .
                    '<p>A mail button when you\'re off.</p>' ,
                    'clock',
                    'Try PRO 14 days free',
                    'Upgrade',
                    $upgrade_url
                );
            } elseif ( $promoItem == 2) {
                ( new CnbAdminFunctions() )->cnb_promobox(
                    'green',
                    'Professional features',
                    '<p>
                        <span class="dashicons dashicons-yes cnb-green"></span> Button scheduler<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Multi-action buttons<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Icon picker & custom images<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Advanced display rules<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Geo targeting<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Set scroll height for buttons to appear<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Slide-in content windows<br>
                        <span class="dashicons dashicons-yes cnb-green"></span> Integrate your Intercom chat</p><h3>And much more!</h3>',
                    'performance',
                    'Try PRO 14 days free!',
                    'Upgrade',
                    $upgrade_url
                );
            } else {
                ( new CnbAdminFunctions() )->cnb_promobox(
                    'green',
                    'Customize your buttons',
                    '<h4>Unlock more icons...</h4>' .
                    '<p>Upgrade to Pro to enable an icon picker for your actions.</p>' .
                    '<h4>...or personalize with Custom Images</h4>' .
                    '<div class="cnb-center" style="padding: 0 34px"><img src="' . esc_url( $custom_image ) . '" alt="Custom button images" style="max-width:246px; width:100%; height:auto;" /></div>' .
                    '<p>With custom images you can add your own image to your buttons.</p>',
                    'art',
                    'Try PRO 14 days free!',
                    'Upgrade',
                    $upgrade_url
                );
            }
        }

        $support_illustration = plugins_url('resources/images/support.png', CNB_PLUGINS_URL_BASE );
        ( new CnbAdminFunctions() )->cnb_promobox(
            'blue',
            'Need help?',
            '<p>Please head over to our <strong>Help Center</strong> for all your questions and support needs.</p>

                  <div class="cnb-right" style="padding: 10px 10px 10px 70px"><img src="' . esc_url( $support_illustration ) . '" alt="Our Help Center and support options" style="max-width:300px; width:100%; height:auto;" /></div>',
            'welcome-learn-more',
            '',
            'Open Help Center',
            ( new CnbUtils() )->get_support_url( '', 'promobox-need-help', 'Help Center' )
        );
        echo '</div>';
    }

    /**
     * @param $cloud_successful boolean
     * @param $cnb_domain CnbDomain
     *
     * @return void
     */
    private function render_premium_option( $cloud_successful, $cnb_domain ) {
        $cnb_options = get_option( 'cnb' );
        $cnb_utils = new CnbUtils();
        ?>
        <tr>
            <th colspan="2"><h2>NowButtons.com</h2></th>
        </tr>
        <tr>
            <th scope="row">
                <label for="cnb_cloud_enabled">Connection
                    <?php if ( $cnb_options['cloud_enabled'] == 0 ) { ?>
                        <a href="<?php echo esc_url( ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page() ) ?>"
                            class="cnb-nounderscore">
                            <span class="dashicons dashicons-editor-help"></span>
                        </a>
                    <?php } ?>
                    <label>
            </th>
            <td>
                <input type="hidden" name="cnb[cloud_enabled]" value="0"/>
                <input id="cnb_cloud_enabled" class="cnb_toggle_checkbox" name="cnb[cloud_enabled]"
                        type="checkbox"
                        value="1" <?php checked( '1', $cnb_options['cloud_enabled'] ); ?> />
                <label for="cnb_cloud_enabled" class="cnb_toggle_label">Enable Cloud</label>
                <span data-cnb_toggle_state_label="cnb_cloud_enabled"
                        class="cnb_toggle_state cnb_toggle_false">(Inactive)</span>
                <span data-cnb_toggle_state_label="cnb_cloud_enabled"
                        class="cnb_toggle_state cnb_toggle_true">Active</span>
                <?php if ( $cnb_options['cloud_enabled'] == 0 ) { ?>
                    <p class="description"><a
                                href="<?php echo esc_url( ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page() ) ?>">Sign
                            up</a> (free) to add extra functionality.
                        <a href="<?php echo esc_url( ( new CnbAdminFunctions() )->cnb_legacy_upgrade_page() ) ?>">Learn
                            more</a>
                    </p>
                <?php } ?>

                <?php if ( $cnb_options['cloud_enabled'] == 1 && $cloud_successful && $cnb_domain->type !== 'PRO' ) { ?>
                    <p class="description">Free and paid options available.
                        <a href="<?php echo esc_url( ( new CnbUtils() )->get_cnb_domain_upgrade() ) ?>">Learn
                            more</a>
                    </p>
                <?php } ?>

                <?php if ( $cnb_options['cloud_enabled'] == 1 && $cloud_successful ) {
                    ?>
                    
                    <div id="cnb_not_working_tips" class="cnb_inpage_notice">
                        <h3>👋 Plugin not playing nice?</h3>
                        <p>No worries—we've got your back. Before you hit that off switch, let's try a couple of quick fixes:</p>
                        <ol style="list-style:none">
                            <li>
                                <p><strong>🧹 Sweep out that cache!</strong><br>
                                <i>Caching plugins can be sneaky and hide our button. Give your cache a good flush, then check if our button pops back up.</i></p>
                            </li>
                            <li>
                                <p><strong>👀 Double-check those visibility settings</strong><br>
                                <i>Make sure our button's set to shine on both desktop and mobile. Hop into the "Visibility" tab when you're editing your button to check.</i></p>
                            </li>
                        </ol>
                        <p>Still no luck? Don't sweat it! <a
                        href="<?php echo esc_url( $cnb_utils->get_support_url( 'contact/contact-support/', 'Settings_page', 'Cloud_disable_toggle' ) ) ?>" target="_blank">Drop us a line</a>, and we'll get you sorted in no time.</p>
                        <p>We're here to help you succeed! 🚀</p>
                    </div>
                <?php } ?>
            </td>
        </tr>
        <?php
    }

    function render() {
        global $cnb_user, $cnb_domain;
        $cnb_options = get_option( 'cnb' );

        $adminFunctions = new CnbAdminFunctions();

        wp_enqueue_script( CNB_SLUG . '-settings' );
        wp_enqueue_script( CNB_SLUG . '-premium-activation' );
        wp_enqueue_script( CNB_SLUG . '-timezone-picker-fix' );
	    wp_enqueue_script( CNB_SLUG . '-tally' );
	    wp_enqueue_script( CNB_SLUG . '-domain-upgrade' );
	    wp_enqueue_script( CNB_SLUG . '-billing-portal' );

        add_action( 'cnb_header_name', array( $this, 'header' ) );

        $use_cloud        = ( new CnbUtils() )->is_use_cloud( $cnb_options );
        $status           = CnbSettingsController::getStatus( $cnb_options );

        if ( $use_cloud ) {
            CnbDomain::setSaneDefault( $cnb_domain );
        }

        do_action( 'cnb_header' );

        $cloud_successful = $status === 'cloud' && isset( $cnb_domain ) && ! ( $cnb_domain instanceof WP_Error );
        ?>

        <div class="cnb-two-column-section">
            <div class="cnb-body-column">
                <div class="cnb-body-content">
                    <h2 class="nav-tab-wrapper">
                        <a data-tab-name="basic_options"
                            href="<?php echo esc_url( $this->create_tab_url( 'basic_options' ) ) ?>"
                            class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>">General</a>
                        <?php if ( $use_cloud ) { ?>
                            <a data-tab-name="account_options"
                                href="<?php echo esc_url( $this->create_tab_url( 'account_options' ) ) ?>"
                                class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'account_options' ) ) ?>">Account</a>
                            <a data-tab-name="advanced_options"
                                href="<?php echo esc_url( $this->create_tab_url( 'advanced_options' ) ) ?>"
                                class="nav-tab <?php echo esc_attr( $adminFunctions->is_active_tab( 'advanced_options' ) ) ?>">Advanced</a>
                        <?php } ?>
                    </h2>
                    <form method="post" action="<?php echo esc_url( admin_url( 'options.php' ) ) ?>"
                            class="cnb-container">
                        <?php settings_fields( 'cnb_options' ); ?>
                        <table data-tab-name="basic_options"
                                class="form-table <?php echo esc_attr( $adminFunctions->is_active_tab( 'basic_options' ) ) ?>">
                            <?php
                            $this->render_premium_option( $cloud_successful, $cnb_domain );
                            if ( $status !== 'cloud' ) {
                                $this->render_legacy_options();
                            }

                            if ( $cloud_successful ) {
                                $domain_edit = new CnbDomainViewEdit();
                                $domain_edit->render_form_plan_details( $cnb_domain );
                                $domain_edit->render_form_tracking( $cnb_domain );
                                $domain_edit->render_form_button_display( $cnb_domain );
                            }

                            $this->render_error_reporting_options();

                            ?>
                        </table>
                        <?php if ( $status === 'cloud' ) {
                            $this->render_account_options( $cnb_user );
                            $this->render_advanced_options( $cnb_domain, $cnb_user );
                        }
                        ?>
                        <?php submit_button(); ?>
                    </form>
                </div>
            </div>
            <?php $this->render_promos( $use_cloud, $cnb_domain, $status ); ?>
        </div>

        <?php
        do_action( 'cnb_footer' );
    }
}
