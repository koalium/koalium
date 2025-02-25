<?php

namespace cnb;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\domain\CnbDomain;
use cnb\admin\domain\SubscriptionStatus;
use cnb\admin\models\CnbPlan;
use cnb\admin\models\CnbUser;
use cnb\admin\settings\CnbSettingsController;
use cnb\cache\CacheHandler;
use cnb\notices\CnbAdminNotices;
use cnb\notices\CnbNotice;
use cnb\utils\CnbUtils;
use WP_Error;

class CnbHeaderNotices {
	/**
	 * All notices that are prefixed with this string are Cloud notices specific to this plugin.
	 *
	 * @var string
	 */
	private $cnb_notice_prefix = 'call-now-button-notice-';

	/**
	 * Create a unique ID for a notice, with a prefix specific to this plugin.
	 *
	 * @return string
	 */
	public function generate_notice_id() {
		return $this->cnb_notice_prefix . wp_generate_uuid4();
	}

    /**
     * @return CnbNotice[]|string[]
     */
    public function get_notices() {
        $transient_id = sanitize_key( filter_input( INPUT_GET, 'tid' ) );

        $notices = array();

        if ( $transient_id && check_admin_referer( $transient_id ) && strpos( $transient_id, $this->cnb_notice_prefix ) === 0 ) {
            $notices_cloud = get_transient( $transient_id );
            if ( is_array( $notices_cloud ) ) {
                $notices = array_merge( $notices, $notices_cloud );
            }
            delete_transient( $transient_id );
        }

        $options_notice = get_transient( 'cnb-options' );
        if ( $options_notice ) {
            $notices = array_merge( $notices, $options_notice );
            delete_transient( 'cnb-options' );
        }

        return $notices;
    }

    /**
     * Find (/create) any message regarding the Cloud version of Call Now Button.
     *
     * Specifically, message about broken API keys, migrated domains, etc.
     *
     */
    public function get_cloud_notices() {
        global $cnb_user, $cnb_domain, $cnb_subscription_data;
        $cnb_options = get_option( 'cnb' );

        if ( ( new CnbUtils() )->isCloudActive( $cnb_options ) ) {

            if ( is_wp_error( $cnb_user ) ) {
                if ( $cnb_user->get_error_code() === 'CNB_API_NOT_SETUP_YET' ) {
                    // Notice: You're almost there! (enter API key)
                    $this->cnb_settings_get_account_missing_notice();
                } else if ( $cnb_user->get_error_code() === 'CNB_API_KEY_INVALID' ) {
                    // Notice: API key is incorrect
                    $this->cnb_settings_api_key_invalid_notice();
                } else {
                    // Notice: something went wrong
                    $this->cnb_generic_error_notice( $cnb_user );
                }
            }
            $this->render_is_domain_missing( $cnb_domain, $cnb_user );
            $this->render_is_timezone_missing( $cnb_domain );
            $this->render_is_timezone_valid( $cnb_domain );
            $this->render_is_debug_mode_enabled( $cnb_domain );
			$this->render_outstanding_invoice( $cnb_subscription_data );
        }
    }

    private function cnb_settings_get_account_missing_notice() {
        $cnb_utils    = new CnbUtils();
        $register_url = $cnb_utils->get_app_url( 'register', 'upgrade-to-premium-options', 'callnowbutton.com' );
        $url          = $cnb_utils->get_app_url( '', 'manual_activation', 'sign-up-for-api' );

        $message = '<h3 class="title cnb-remove-add-new">Account missing</h3>';
        $message .= '<p class="title cnb-remove-add-new">The connection with NowButtons is enabled but the plugin is not connected to an account yet. Please activate it by connecting to a NowButtons account.</>';
        
        $message .= '<div class="option1-email"><h4>Email activation</h4>';
        $message .= self::cnb_settings_email_activation_input();
        $message .= '</div>';

        
        $message .= '<button class="button button-link" id="option2-apikey">Activate with an API key instead</button>';
        $message .= '<div class="option2-apikey" style="margin-bottom:20px">';
        $message .= '<h4>Activate with API key</h4>';
        $message .= '<ol>';
        $message .= '<li>Login to your <a href="' . esc_url( $url ) . '">NowButtons account</a>. (You can create a (free) account if you don\'t have one yet.)</li>';
        $message .= '<li>Go to your profile info by clicking on the user icon in the top right corner and then click <strong>Create new API key</strong>.</li>';
        $message .= '<li>Copy the API key that appears, paste it into the field below and click <strong>Activate</strong>.</li>';
        $message .= '</ol>';
        $message .= $this->cnb_settings_api_key_input();
        $message .= '</div>';
        $message .= '<button class="button button-link" id="option1-email">Activate via email instead</button>';

        $adminNotices = CnbAdminNotices::get_instance();
        $adminNotices->error( $message );
    }

    private function cnb_settings_api_key_invalid_notice() {
        $cnb_utils = new CnbUtils();
        $url       = $cnb_utils->get_app_url( '', 'manual_activation', 'sign-up-for-api' );
        $message   = '<h3 class="title cnb-remove-add-new">Ooops, that API key doesn\'t seem right</h3>';
        $message   .= '<p>The saved API key is invalid. Let\'s give it another try:</p>';
        $message .= '<div class="option1-email"><h4>Activate by email</h4>';
        $message   .= self::cnb_settings_email_activation_input();
        $message .= '</div>';
        
        $message .= '<p id="option2-apikey"><a href="#">Activate with an API key instead</a></p>';
        $message .= '<div class="option2-apikey" style="margin-bottom:20px">';
        $message .= '<h4>Activate with a new API key</h4>';
        $message .= '<ol>';
        $message .= '<li>Login to your <a href="' . esc_url( $url ) . '">NowButtons account</a>. (You can create a (free) account if you don\'t have one yet.)</li>';
        $message .= '<li>Go to your profile info by clicking on the user icon in the top right corner and then click <strong>Create new API key</strong>.</li>';
        $message .= '<li>Copy the API key that appears, paste it into the field below and click <strong>Activate</strong>.</li>';
        $message .= '</ol>';
        $message .= $this->cnb_settings_api_key_input();
        $message .= '</div>';
        $message .= '<p id="option1-email"><a href="#">Activate via email instead</a></p>';

        $message      .= '<hr><p>If it\'s still not working, we might be experiencing server issues. Please wait a few minutes and try again. You can check our <a target="_blank" href="https://status.callnowbutton.com">status page</a> to be sure.</p>';
        $adminNotices = CnbAdminNotices::get_instance();
        $adminNotices->warning( $message );
    }

    /**
     * Returns an HTML form containing all the fields needed for a Premium signup.
     *
     * The returned string is already pre-HTML-escaped.
     *
     * @return string HTML form with e-mail placeholder and a Submit button
     */
    public static function cnb_settings_email_activation_input() {
        $cnb_utils   = new CnbUtils();
        $terms_url   = $cnb_utils->get_website_url( 'legal/terms/', 'email-activation', 'terms' );
        $privacy_url = $cnb_utils->get_website_url( 'legal/privacy/', 'email-activation', 'privacy' );
        $message     = '<form class="cnb-container cnb_email_activation">';
        $message     .= '<input type="text" required="required" class="cnb_activation_input_field" name="cnb_email_activation_address" placeholder="Your email address" /> ';
        $message     .= get_submit_button( __( 'Create account' ), 'primary', 'cnb_email_activation_submit', false );
        $message     .= '<p class="cnb_email_activation_message"></p>';

        $message .= '<p class="nonessential">By clicking <u>Create account</u> an account will be created with your email address on nowbuttons.com and you agree to our <a href="' . esc_url( $terms_url ) . '" target="_blank">Terms & Conditions</a> and <a href="' . esc_url( $privacy_url ) . '" target="_blank">Privacy statement</a>.</p>';
        $message .= '</form>';

        return $message;
    }

    private function cnb_settings_api_key_input() {
        $message = sprintf( '<form action="%1$s" method="POST" class="cnb-container">', esc_url( admin_url( 'admin-post.php' ) ) );
	    $message .= '<input type="hidden" name="page" value="call-now-button" />';
	    $message .= '<input type="hidden" name="action" value="cnb_apikey_validate_and_update" />';
        $message .= '<div>';
        $message .= '<input type="text" required="required" class="cnb_activation_input_field" name="api_key" placeholder="Paste API key here"/>';
	    $message .= '<input type="hidden" name="_wpnonce" value="' . esc_attr( wp_create_nonce( 'cnb_apikey_validate_and_update' ) ) . '"/>';
        $message .= get_submit_button( __( 'Store API key' ), 'primary', 'submit', false );
        $message .= '</div>';
        $message .= '</form>';

        return $message;
    }

    /**
     * Display notification that the button is active or inactive
     *
     * @param $notices CnbNotice[]
     *
     * @return void
     */
    public function add_button_is_disabled_notice( &$notices ) {
        $cnb_options = get_option( 'cnb' );
        $status      = CnbSettingsController::getStatus( $cnb_options );
        if ( $cnb_options['active'] != 1 && ! empty( $cnb_options['number'] ) && $status != 'cloud' ) {
            $message   = '<p>The Call Now Button is currently <strong>inactive</strong>.';
            $notice    = new CnbNotice( 'warning', $message );
            $notices[] = $notice;
        }
    }

    /**
     * @param $notices CnbNotice[]
     *
     * @return void
     */
    function cnb_button_legacy_enabled_but_no_number_notice( &$notices ) {
        $cnb_options = get_option( 'cnb' );
        $status      = CnbSettingsController::getStatus( $cnb_options );

        if ( $cnb_options['active'] == 1 && $status == 'enabled' && empty( $cnb_options['number'] ) ) {
            $url           = admin_url( 'admin.php' );
            $redirect_link =
                add_query_arg(
                    array(
                        'page' => 'call-now-button',
                    ),
                    $url );
            $redirect_url  = esc_url( $redirect_link );

            $message = '<p>The Call Now Button is currently <strong>active without a phone number</strong>.';
            $message .= 'Change the <i>Button status</i> under <a href="' . $redirect_url . '">My button</a> to disable or enter a phone number.</p>';

            $notice    = new CnbNotice( 'warning', $message );
            $notices[] = $notice;
        }
    }

    function get_changelog_version( $cnb_options ) {
        if ( ! $cnb_options ) {
            return CNB_VERSION;
        }

        if ( ! key_exists( 'changelog_version', $cnb_options ) ) {
            // Get 1 version behind, so new users always get the latest
            $changelog = $this->get_show_changelog_versions();

            return $changelog[1];
        }

        return $cnb_options['changelog_version'];
    }

    /**
     * A list of versions to show the "Your plugin has been updated" message for
     *
     * @return string[]
     */
    function get_show_changelog_versions() {
        return array(
            '1.2.0',
            '1.1.4',
            '1.0.6',
        );
    }

    function get_generic_changelog_message() {
        $cnb_utils         = new CnbUtils();
        $changelog_link    = $cnb_utils->get_website_url( 'wordpress/changelog/', 'update_notice' );
        $changelog_message = '<a href="' . esc_url( $changelog_link ) . '" target="_blank">Click here to see what changed</a>';
        $message           = '<p><span class="dashicons dashicons-yes"></span> ';
        $message           .= 'The plugin has been updated. ';
        $message           .= $changelog_message;
        $message           .= '</p>';

        return $message;
    }

    /**
     * Inform existing users about updates to the button
     *
     * Create a dismissible notice to inform users about changes
     *
     * @param $notices CnbNotice[]
     *
     * @return boolean
     */
    public function upgrade_notice( &$notices = array() ) {
        $cnb_options   = get_option( 'cnb' );
        $cnb_changelog = $this->get_show_changelog_versions();
        $message       = $this->cnb_get_changelog_message( $cnb_changelog, $this->get_changelog_version( $cnb_options ) );

        if ( empty( $message ) ) {
            return false;
        }

        $notices[] = new CnbNotice( 'success', $message, true, $this->cnb_get_upgrade_notice_dismiss_name() );

        return true;
    }

    public function cnb_get_upgrade_notice_dismiss_name() {
        return 'cnb_update_' . CNB_VERSION;
    }

    /**
     * @param $cnb_changelog array
     * @param $cnb_old_version "$cnb_options['changelog_version']" most likely
     *
     * @return string
     */
    private function cnb_get_changelog_message( $cnb_changelog, $cnb_old_version ) {
        foreach ( $cnb_changelog as $value ) {
            if ( version_compare( $value, $cnb_old_version, '>' ) ) {
                return $this->get_generic_changelog_message();
            }
        }

        return '';
    }

    /**
     * @param $notices CnbNotice[]
     *
     * @return void
     */
    public function cnb_show_advanced( &$notices ) {
        if ( ! CnbSettingsController::is_advanced_view() ) {
            return;
        }

        $message   = '<p>Click <a onclick="return cnb_enable_advanced_view(this)" style="cursor: pointer">here</a> to reveal more advanced fields</p>';
        $notices[] = new CnbNotice( 'info', $message );
    }

    public function warn_about_caching_plugins( &$notices ) {
        $cache_handler          = new CacheHandler();
        $active_caching_plugins = $cache_handler->get_active_caching_plugins();
        if ( $active_caching_plugins ) {
            $this->caching_plugin_warning_notice( $active_caching_plugins, $notices );
        }
    }

    private function caching_plugin_warning_notice( $caching_plugin_names, &$notices ) {
        $plugins = get_plugins();
        foreach ( $caching_plugin_names as $caching_plugin_name ) {
            if ( ! is_array( $plugins ) || ! array_key_exists( $caching_plugin_name, $plugins ) ) {
                continue;
            }
            $plugin    = $plugins[ $caching_plugin_name ];
            $notices[] = $this->get_caching_plugin_warning_notice( $plugin );
        }
    }

    /**
     * @param $plugin array expects the array with a single plugin found via get_plugins()
     *
     * @return CnbNotice
     */
    private function get_caching_plugin_warning_notice( $plugin ) {
        $name = $plugin['Name'];

        $message = '<p><span class="dashicons dashicons-warning"></span> ';
        $message .= 'Your website is using a <strong><i>Caching Plugin</i></strong> (' . $name . '). ';
        $message .= "If you're not seeing your button or your changes, make sure you empty your cache first.</p>";

        return new CnbNotice( 'error', $message, true, 'cnb-caching-' . $name );
    }

    public function is_timezone_missing( $domain ) {
        return ( $domain && ! is_wp_error( $domain ) && empty( $domain->timezone ) );
    }

    /**
     * In case Cloud is connected and a User is present, but no Cloud domain could be found.
     *
     * This means either the domain is deleted, the WordPress domain has changed, the environment has changed.
     * Regardless - Cloud cannot operate properly, so we issue an Error and ask a user to reconnect their account.
     *
     * @param $cnb_domain CnbDomain
     * @param $cnb_user CnbUser|WP_Error
     * @return void
     */
    private function render_is_domain_missing ( $cnb_domain, $cnb_user ) {
        $issueWithUser = $cnb_user === null || is_wp_error( $cnb_user);
        $issueWithDomain = $cnb_domain === null || $cnb_domain->id === null || $cnb_domain->name === null;
        if ($issueWithUser) return;
        if (!$issueWithDomain) return;

        $redirect_link =
            add_query_arg(
                array(
                    'page' => 'call-now-button-settings',
                    'tab' => 'account_options',
                ),
                admin_url('admin.php'));
        $redirect_url  = esc_url( $redirect_link );

        $message = sprintf('<p>NowButtons.com Cloud is enabled, but you have no domain configured. This is likely an error. Please <strong>Disconnect</strong> your account in the <a href="%1$s">Account</a> tab and reconnect using your e-mail address.</p>',
            $redirect_url);
        CnbAdminNotices::get_instance()->error( $message, false, 'cnb-domain-missing' );
    }

    /**
     *
     * Also warning if timezone is not yet set
     *
     * @param $domain CnbDomain|WP_Error
     *
     * @return void true is all is alright
     */
    private function render_is_timezone_missing( $domain ) {
        if ( ! ( $this->is_timezone_missing( $domain ) ) ) {
            return;
        }

        $url           = admin_url( 'admin.php' );
        $redirect_link =
            add_query_arg(
                array(
                    'page' => 'call-now-button-settings',
                    'tab'  => 'advanced_options#domain_timezone',
                ),
                $url );
        $redirect_url  = esc_url( $redirect_link );
        $message       = sprintf( '<p>Please set your timezone in the <a href="%1$s">Advanced settings</a> tab to avoid unpredictable behavior when using the scheduler.</p>', $redirect_url );
        CnbAdminNotices::get_instance()->warning( $message, false, 'cnb-timezone-missing' );
    }

    public function is_timezone_valid( $domain ) {
        if ( $domain && ! is_wp_error( $domain ) && ! empty( $domain->timezone ) ) {
            return ( new CnbUtils() )->is_valid_timezone_string( $domain->timezone );
        }

        return true;
    }

    /**
     * @param $domain CnbDomain
     *
     * @return boolean true if everything is already
     */
    public function render_is_timezone_valid( $domain ) {
        if ( $this->is_timezone_valid( $domain ) ) {
            return true;
        }

        $url           = admin_url( 'admin.php' );
        $redirect_link =
            add_query_arg(
                array(
                    'page' => 'call-now-button-settings',
                    'tab'  => 'advanced_options#domain_timezone',
                ),
                $url );
        $redirect_url  = esc_url( $redirect_link );
        $message       = "<p class='cnb-notice-domain-timezone-unsupported'>Please fix your timezone in the ";
        $message       .= '<a href="' . $redirect_url . '">Advanced settings</a> tab ';
        $message       .= 'to avoid unpredictable behavior when using the scheduler.</p>';
        CnbAdminNotices::get_instance()->warning( $message );

        return false;
    }

    /**
     * Add an error if debug mode is turned on
     *
     * @param $domain CnbDomain
     *
     * @return void
     */
    private function render_is_debug_mode_enabled( $domain ) {
        if ( ! $domain || is_wp_error( $domain ) || ! $domain->properties->debug ) {
            return;
        }

        $url           = admin_url( 'admin.php' );
        $redirect_link =
            add_query_arg(
                array(
                    'page' => 'call-now-button-settings',
                    'tab'  => 'advanced_options#domain_properties_debug',
                ),
                $url );
        $redirect_url  = esc_url( $redirect_link );
        $message       = '<p>Your plugin is currently in DEBUG MODE. ';
        $message       .= 'Go to <a href="' . $redirect_url . '">Advanced settings</a> to turn this off.</p>';
        CnbAdminNotices::get_instance()->error( $message );
    }

	/**
	 * @param SubscriptionStatus $cnb_subscription_status
	 *
	 * @return void
	 */
	private function render_outstanding_invoice( $cnb_subscription_status ) {
		if ( ! $cnb_subscription_status || ! $cnb_subscription_status->has_outstanding_payment() ) {
			return;
		}

		wp_enqueue_script( CNB_SLUG . '-billing-portal' );


		$message = '<p style="font-size:18px; font-weight:bold;">Failed payment</p><p>Your PRO subscription is currently <strong>paused</strong> as we were unable to collect your subscription fee of ';
		$message .= esc_html( CnbPlan::get_formatted_amount( $cnb_subscription_status->invoiceAmount / 100.0, $cnb_subscription_status->invoiceCurrency ) );
		$message .= '. <p>Please remit payment at your earliest convenience. All PRO features are reactivated once the overdue invoice has been settled.';
		$message .= '<p><a class="button button-primary button-large" href="' . esc_url( $cnb_subscription_status->invoiceUrl ) . '">Pay now</a></p>';


		CnbAdminNotices::get_instance()->error( $message, false, 'cnb-outstanding-invoice' );
	}

    private function get_cnb_generic_error_notice() {
        $cnb_utils   = new CnbUtils();
        $support_url = $cnb_utils->get_support_url( '', 'notice-error', 'help-center' );

        return '<h3 class="title">Something went wrong!</h3>
            <p>Something has gone wrong and we do not know why...</p>
            <p>As unlikely as it is, our service might be experiencing issues (check <a href="https://status.callnowbutton.com">our status page</a>).</p>
            <p>If you think you\'ve found a bug, please report it at our <a href="' . esc_url( $support_url ) . '" target="_blank">Help Center</a>.';
    }

    private function cnb_generic_error_notice( $user ) {
        $message = $this->get_cnb_generic_error_notice();
        $message .= CnbAdminCloud::cnb_admin_get_error_message_details( $user );

        $adminNotices = CnbAdminNotices::get_instance();
        $adminNotices->warning( $message );
    }
}
