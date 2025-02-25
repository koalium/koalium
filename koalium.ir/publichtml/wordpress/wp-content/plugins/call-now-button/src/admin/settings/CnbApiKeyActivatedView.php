<?php

namespace cnb\admin\settings;

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\domain\CnbDomainViewEdit;
use cnb\admin\domain\CnbDomainViewUpgradeOverview;
use cnb\admin\models\CnbActivation;
use cnb\admin\models\CnbUser;
use cnb\notices\CnbAdminNotices;
use WP_Error;

class CnbApiKeyActivatedView {
    /**
     * @var CnbActivation
     */
    private $activation;

    function header() {
        echo 'Your NowButtons account';
    }

    public function __construct() {
        $this->activation = get_transient('cnb_activation');
    }

	/**
     * @param $error WP_Error
     *
     * @return void
     */
    private function renderButtonError( $error ) {
        $notice = CnbAdminCloud::cnb_admin_get_error_message( 'create', 'Button', $error );
        CnbAdminNotices::get_instance()->renderNotice( $notice );
    }

    /**
     *
     * @return void
     */
    private function renderButtonCreated() {
        $message = '<p>Your existing button has been migrated.</p>';
        CnbAdminNotices::get_instance()->renderSuccess( $message );
    }

    private function getAllButtonsLink() {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page' => 'call-now-button',
            ),
            $url );
    }

    private function getNewButtonLink() {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page'   => 'call-now-button',
                'action' => 'new',
            ),
            $url );
    }

    private function getSettingsLink() {
        $url = admin_url( 'admin.php' );

        return add_query_arg(
            array(
                'page' => 'call-now-button-settings',
            ),
            $url );
    }

    private function renderButtonInfo() {
        if ( ! $this->activation ) return;

        $button = $this->activation->button;

        if ( is_wp_error( $button ) ) {
            $this->renderButtonError( $button );

            return;
        }

        // If the activation was not successful, don't assume anything about the button
        if ( ! $this->activation->success ) {
            return;
        }

        // If a button is created, tell the user
        if ( $button ) {
            $this->renderButtonCreated();
        }
    }

    /**
     * print SVG wrapped in a div with a cloud symbol and success tick
     *
     * @return void
     */
    public function accountConnectionSvg() {
        echo '<div style="max-width:120px; margin:0 auto;">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M30.5,18a7.45207,7.45207,0,0,1-1.92773,5,6.389,6.389,0,0,1-3.81873,1.93469,7.52112,7.52112,0,0,0,.15118-3.10339,3.19012,3.19012,0,0,0,1.441-.842A4.45541,4.45541,0,0,0,27.5,18a5.00588,5.00588,0,0,0-5-5c-.16486,0-1.01178.10578-1.527.17216a.497.497,0,0,1-.53424-.33408l-.20362-.594A6.16993,6.16993,0,0,0,15.1093,8.03026,5.97887,5.97887,0,0,0,8.665,15.37109l.21353.91336a.5.5,0,0,1-.34225.59245l-.89862.27154A2.97242,2.97242,0,0,0,5.5,20a2.01441,2.01441,0,0,0,.41406,1.22852A2.42324,2.42324,0,0,0,7.70312,22H9.06946a7.55663,7.55663,0,0,0,.19385,3H7.70312a5.35308,5.35308,0,0,1-4.23535-2.03516A5.03407,5.03407,0,0,1,2.5,20a5.93806,5.93806,0,0,1,3.03809-5.19922C5.5127,14.53223,5.5,14.26465,5.5,14a8.99653,8.99653,0,0,1,17.05664-4A8.00931,8.00931,0,0,1,30.5,18ZM23,23a6,6,0,1,1-6-6A6,6,0,0,1,23,23Zm-2.85236-2.23358-.51147-.37879a.29677.29677,0,0,0-.37677.02674l-3.15,2.96063-1.766-.84467a.32278.32278,0,0,0-.37695.09528l-.43353.55444a.29685.29685,0,0,0,.02673.37683l2.23328,2.236a.83043.83043,0,0,0,1.29761-.1L20.2,21.11816A.25358.25358,0,0,0,20.14764,20.76642Z"/></svg>';
        echo '</div>';
    }

	private function renderActivationSuccess($already = false) {
        echo '<div style="text-align: center;">';
        $this->accountConnectionSvg();
        echo '<h2>NowButtons.com account connection ' . ($already ? ' already ' : '') . ' successfully established!</h2>';
        echo '</div>';
    }

    private function renderGetStarted() {
        global $cnb_domain;

        $domain = $this->activation->domain;
        if ( $domain === null ) {
            $domain = $cnb_domain;
        }

        $nonce_field    = wp_nonce_field( 'cnb_update_domain_timezone', '_wpnonce', true, false );
        $timezoneSelect = ( new CnbDomainViewEdit() )->getTimezoneSelect( $domain );
        printf( '
            <div class="cnb-get-started cnb-plan-features cnb-center top-50">
            <h1 class="cnb-center"><strong>Let\'s get started</strong></h1><hr>
            <div class="cnb-flexbox">
              <div class="box">
                <h2>Is this your time zone?</h2>
                <div>
                    %4$s
                    %5$s
                </div>
              </div>
              <div class="box">
                <h2>Manage your buttons</h2>
                <p>
                  <a class="button button-primary" href="%1$s">Create new</a>
                  <a class="button premium-button" href="%2$s">Button overview</a>
                </p>
              </div>
              <div class="box">
                <h2>Check your Settings</h2>
                <p><a class="button premium-button" href="%3$s">Open settings
                  </a></p>
              </div>
            </div>
            </div>',
            esc_url( $this->getNewButtonLink() ),
            esc_url( $this->getAllButtonsLink() ),
            esc_url( $this->getSettingsLink() ),
            // phpcs:ignore WordPress.Security
            $timezoneSelect,
            // phpcs:ignore WordPress.Security
            $nonce_field );
    }

    private function renderOnboarding() {
        $img_add_action   = plugins_url('resources/images/onboarding/add-action.png', CNB_PLUGINS_URL_BASE );
        $img_add_rule     = plugins_url('resources/images/onboarding/add-display-rule.png', CNB_PLUGINS_URL_BASE );
        $img_presentation = plugins_url('resources/images/onboarding/button-presentation.png', CNB_PLUGINS_URL_BASE );
        $img_buttons      = plugins_url('resources/images/onboarding/buttons-overview.png', CNB_PLUGINS_URL_BASE );
        $img_nav_position = plugins_url('resources/images/onboarding/nav-position.png', CNB_PLUGINS_URL_BASE );
        $img_new_button   = plugins_url('resources/images/onboarding/new-button.png', CNB_PLUGINS_URL_BASE );
        $img_preview      = plugins_url('resources/images/onboarding/preview.png', CNB_PLUGINS_URL_BASE );
        $img_visibility   = plugins_url('resources/images/onboarding/visibility-settings.png', CNB_PLUGINS_URL_BASE );
        ?>
        <div class="cnb_onboarding_guide cnb-plan-features cnb-center top-50">
            <h1><strong>Quick start guide</strong></h1>
            <hr>
            <div class="cnb-slides-outer">
                <div class="cnb-center cnb-slide-controls">
                    <button class="button button-primary cnb-slide-prev"><span class="dashicons dashicons-arrow-left-alt2"></span>&nbsp;&nbsp;</button>
                </div>
                <div class="cnb-slides-inner">
                <div class="cnb-slide cnb-slide1 cnb-slide-active">
                    <h2 class="cnb-left">Locating your buttons</h2>
                    <p class="cnb-left">You can find the Buttons in the side nav of your WordPress dashboard.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_nav_position ) ?>" alt="Find your Buttons in the side nav of your WordPress dashboard."></div>

                </div>
                <div class="cnb-slide cnb-slide2">
                    <h2 class="cnb-left">Your buttons overview</h2>
                    <p class="cnb-left">The buttons overview page where you can add, edit and remove buttons.</p> <p class="cnb-left">Click <strong>Add
                            New</strong> at the top to create a new button.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_buttons ) ?>" alt="The buttons overview page where you can add, edit and remove buttons.">
                    </div>

                </div>
                <div class="cnb-slide cnb-slide3">
                    <h2 class="cnb-left">Create a new button</h2>
                    <p class="cnb-left">When creating a new button, start with selecting your button type.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_new_button ) ?>" alt="When creating a new button, start with selecting your button type.">
                    </div>

                </div>
                <div class="cnb-slide cnb-slide4">
                    <h2 class="cnb-left">Add an action to your button</h2>
                    <p class="cnb-left">Every button contains at least one action. The action is what the button does when it
                        is clicked. For example a call button has a Phone action.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_add_action ) ?>" alt="Every button contains one or more actions."></div>

                </div>
                <div class="cnb-slide cnb-slide5">
                    <h2 class="cnb-left">Change the presentation of your button</h2>
                    <p class="cnb-left">In the Presentation tab you can set placement and the colors of your button.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_presentation ) ?>" alt="In the Presentation tab you can set placement, the colors and pick an animation effect for your button.">
                    </div>

                </div>
                <div class="cnb-slide cnb-slide6">
                    <h2 class="cnb-left">Decide where your button should appear</h2>
                    <p class="cnb-left">On the Visibility tab you can decide where your button should appear. Here you also see
                        an overview of all active Display Rules.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_visibility ) ?>" alt="On the Visibility tab you can decide where your button should appear. Here you also see an overview of all active Display Rules.">
                    </div>

                </div>
                <div class="cnb-slide cnb-slide7">
                    <h2 class="cnb-left">Adding Display Rules</h2>
                    <p class="cnb-left">Add display rules to select the pages where the button should appear or not.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_add_rule ) ?>" alt="Add display rules to select the pages where the button should appear or not.">
                    </div>

                </div>
                <div class="cnb-slide cnb-slide8">
                    <h2 class="cnb-left">The preview phone</h2>
                    <p class="cnb-left">A preview phone is always visible to validate your edits. The time in the phone can be
                        changed to test your scheduled actions.</p>
                    <div class="cnb_screenshot">
                    <img src="<?php echo esc_url( $img_preview ) ?>" alt="A preview phone is always visible to validate your edits. The time in the phone can be changed to test your scheduled actions.">
                    </div>

                </div>
                </div>
                <div class="cnb-center cnb-slide-controls">
                <button class="button button-primary cnb-slide-next">&nbsp;<span class="dashicons dashicons-arrow-right-alt2"></span>&nbsp;</button>
                </div>
            </div>
        </div>

        <?php
    }

    /**
     *
     * @return void
     */
    private function renderUpgradeToPro() {
        global $cnb_domain;

        $domain = $this->activation->domain;
        if ( $domain === null ) {
            $domain = $cnb_domain;
        }

        if ( $domain === null || is_wp_error( $domain ) ) {
            // Something went wrong
            return;
        }
        if ( $domain->type === 'PRO' ) {
            // Already upgraded, so skip all of this
            return;
        }
        ?>
        <h1 class="top-50 cnb-center" style="font-weight: bold; font-size: 30px;">Professional features</h1>
        <div class="cnb-welcome-blocks">

            <?php ( new CnbDomainViewUpgradeOverview() )->render_pro_features_nice_view(); ?>

            <br>
            <?php ( new CnbDomainViewUpgradeOverview() )->render_pro_features_extras(); ?>

            <br>
            <h1>Select your <b>PRO</b> plan</h1>
            <br>

            <?php ( new CnbDomainViewUpgradeOverview() )->render_upgrade_form( $domain ); ?>
        </div>
        <div class="cnb-welcome-blocks">
            <h1>Feature comparison</h1>
            <h3>An overview of the features that are included in the Starter and Pro plans.</h3>
            <?php ( new CnbDomainViewUpgradeOverview() )->render_pro_feature_comparison(); ?>

            <br>
            <div class="cnb-block">
                <?php ( new CnbDomainViewUpgradeOverview() )->render_upgrade_form( $domain, '-upgrade-form' ); ?>
            </div>
        </div>
        <?php
    }

    /**
     * @param $user CnbUser
     *
     * @return void
     */
    private function renderActivationFailure( $user ) {
        if ( ! is_wp_error( $user ) ) {
            echo '<div style="text-align: center">';
            $this->accountConnectionSvg();
            echo '<h2>NowButtons.com account connection already established.</h2>';
            echo '</div>';

            return;
        }

        echo '<h1>You tried to establish a connection with NowButtons.com, but something went wrong.</h1>';
        echo '<p>If you\'re trying to change to a different account/email address there might still be an old API key stored in the plugin.</p><p>To remove the old API key, go to <b>Settings</b> > <b>Account</b> tab > Click the <b>Disconnect account</b> link and click <b></b>Save Changes</b>. Now try to activate NowButtons again.</p>';
    }

    private function renderActivationStatus() {
        $cnb_remote = new CnbAppRemote();
        $user = $cnb_remote->get_user();

        if ( $this->activation && $this->activation->notices) {
            foreach ($this->activation->notices as $notice) {
                CnbAdminNotices::get_instance()->renderNotice( $notice );
            }
        }

        if ( $this->activation && $this->activation->success ) {
            $this->renderActivationSuccess();
        }

        if ( $this->activation && ( ! $this->activation->success && is_wp_error( $this->activation->domain ) ) ) {
            $notice = CnbAdminCloud::cnb_admin_get_error_message( 'update', 'domain', $this->activation->domain );
            CnbAdminNotices::get_instance()->renderNotice( $notice );
        }

        if ( $this->activation && ( $this->activation->success || ! is_wp_error( $user ) ) ) {
            $this->renderGetStarted();
            $this->renderOnboarding();
            echo '<hr class="top-50 bottom-50">';            
            $this->renderUpgradeToPro();
        }

        if ( ! $this->activation && ! is_wp_error( $user ) ) {
	        $this->renderActivationSuccess(true);
        }

	    if ( ! $this->activation && is_wp_error( $user ) ) {
            $this->renderActivationFailure( $user );
        }
    }

    public function render() {
        add_action( 'cnb_header_name', array( $this, 'header' ) );
        wp_enqueue_script( CNB_SLUG . '-settings-activated' );
        wp_enqueue_script( CNB_SLUG . '-profile' );
        wp_enqueue_script( CNB_SLUG . '-domain-upgrade' );
        wp_enqueue_script( CNB_SLUG . '-timezone-picker-fix' );

        // Hide "timezone warning" - that is already covered in the "Is this your timezone?" question
        add_filter('cnb_admin_notice_filter', function ($notice) {
            if ($notice && $notice->name === 'cnb-timezone-missing') return null;
            return $notice;
        });

        do_action( 'cnb_header' );

        $this->renderActivationStatus();

        // Link to Button (if present)
        $this->renderButtonInfo();

        do_action( 'cnb_footer' );
    }

    /**
     * @param CnbActivation $activation
     *
     * @return void
     */
    public function setActivation( $activation ) {
        $this->activation = $activation;
    }
}
