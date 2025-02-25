<?php

namespace cnb\admin\agency;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\api\CnbAppRemotePayment;
use cnb\admin\models\CnbAgencyPlan;
use cnb\notices\CnbNotice;
use cnb\admin\domain\CnbProAccountBlocks;

class CnbAgencyViewUpgrade {    
    function header() {
        echo 'Upgrading your account to a PRO account';
    }

    public function render() {
        wp_enqueue_script( CNB_SLUG . '-profile' );
	    wp_enqueue_script( CNB_SLUG . '-domain-upgrade' );

        add_action( 'cnb_header_name', array( $this, 'header' ) );
        do_action( 'cnb_header' );
        $this->render_content();
        do_action( 'cnb_footer' );
    }
    /**
     * Decides to either render the overview or the upgrade view
     *
     * @return void
     */
    public function render_content() {
        $notice = $this->get_upgrade_notice( );
        if ( $notice ) {
            // flush the cache
            do_action('cnb_after_button_changed');
        }

        if ( $notice && $notice->type === 'success' ) {
            echo '<p>Your account has been successfully upgraded to a <strong>PRO account</strong>!</p>';
        }
        if ( $notice && $notice->type === 'warning' ) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>Something is going on upgrading to a  <strong>PRO account</strong>.</p>';
            echo '<p>Error: ' . esc_html( $notice->message ) . '!</p>';
            echo '</div>';
        }
        $upgradeStatus     = filter_input( INPUT_GET, 'upgrade', @FILTER_SANITIZE_STRING );

	    /** @var CnbAgencyPlan[] $cnb_agency_plans */
	    global $cnb_agency_plans;

	    $agency_20_plans = array_filter($cnb_agency_plans, function ($plan) {
		    return $plan->seats === 20 && $plan->interval === 'monthly';
	    });
	    $agency_20_plan = array_shift($agency_20_plans);

	    if ( $upgradeStatus === 'success?payment=cancelled' ) {
            wp_enqueue_script( CNB_SLUG . '-tally' ); ?>
            
            <div class="cnb-welcome-blocks  cnb_font_light">
                <h1>Not sure if the PRO Account <strong class="cnb-green">is right for you?</strong></h1>
            </div>
            <div class="cnb-welcome-blocks  cnb_font_light">
                <h1>Let's stack up the benefits</h1>
                <div class="cnb-left">
                    <p class="font-18">If you manage more than 10 websites, the PRO Account is the way to go!</p>
                    <ul class="cnb-list font-16 cnb-checklist">
                        <li><strong>Manage Up to 20 Websites</strong> from a single account</li>
                        <li><strong>Instant PRO Features</strong> across all your sites</li>
                        <li><strong>Save 55%</strong> compared to individual site licenses</li>
                        <li><strong>Add or Remove Sites</strong> anytime - full flexibility to adapt as your business grows</li>
                        <li><strong>Easy administration</strong> with a single invoice per month of year</li>
                        <li><strong>An extra 17% off</strong> with annual billing</li>
                        <li><strong>Excellent support</strong> to help you become successful</li>
                    </ul>
                </div>
            </div>
            <div class="cnb_align_center">
                <a class="button button-primary button-green button-large font-18" onclick="cnb_get_agency_checkout('<?php echo esc_js( $agency_20_plan->id ) ?>')" href="#">Complete Checkout</a></div>
            <div class="cnb-welcome-blocks  cnb_font_light">
                <h1>Need PRO for <strong class="cnb-green">more than 20 domains?</strong></h1>
                <div class="cnb-left">
                    <p class="font-18">If you manage more than 20 websites, we offer expanded PRO Account options:</p>
                    <ul class="cnb-list font-16 cnb-checklist">
                        <li><strong>50 websites</strong> from €/$ 1.33 per site</li>
                        <li><strong>100 websites</strong> from €/$ 0.83 per site </li>
                        <li><strong>1000 websites</strong> from €/$ 0.21 per site </li>
                    </ul>
                    <p class="font-18">Contact us if you need a bigger bundle: <a href="mailto:hello@nowbuttons.com">hello@nowbuttons.com</a></p>
                </div>
            </div>

            <div class="cnb-welcome-blocks  cnb_font_light">
                <h1>Why agencies choose the PRO Account</h1>
                <div class="">
                    <p class="font-18">The PRO Account transforms how you manage client websites. Instead of juggling multiple licenses, everything is centralized and streamlined. The cost savings alone makes it a no-brainer for any agency.</p>
                </div>
            </div>
            <div class="cnb_align_center"><a class="button button-primary button-green button-large font-18" onclick="cnb_get_agency_checkout('<?php echo esc_js( $agency_20_plan->id ) ?>')" href="#">Complete Checkout</a></div>
            <div class="cnb-welcome-blocks  cnb_font_light">
                <h1>Questions or Special Requirements?</strong></h1>
                <div class="cnb-left">
                    <p class="font-18">Our team specializes in supporting agencies and multi-site businesses. Let's discuss your specific needs:</p>
                    <ul class="cnb-list font-16 cnb-checklist">
                        <li>Custom domain limits</li>
                        <li>Enterprise solutions </li>
                        <li>Volume pricing </li>
                        <li>Training and onboarding</li>
                    </ul>
                    <iframe data-tally-src="https://tally.so/embed/wdZLdV?alignLeft=1&hideTitle=1&transparentBackground=1&dynamicHeight=1" loading="lazy" width="100%" height="228" frameborder="0" marginheight="0" marginwidth="0" title="Abandoned PRO Account checkout"></iframe>
                        <script>jQuery(function () {cnb_show_tally_abandoned_checkout()})</script>
                    
                </div>
            </div>
            <div class="cnb-welcome-blocks  cnb_font_light">
                <h1>Our promise</strong></h1>
                <div class="cnb-left">
                    
                    <ul class="cnb-list font-16 cnb-checklist">
                        <li>Affordable pricing</li>
                        <li>Dedicated agency support</li>
                        <li>Regular feature updates </li>
                        <li>Priority ticket handling </li>
                    </ul>
                    <p class="font-18 font-italic cnb_align_center">Ready to give your client's website a boost? Your PRO Account is just one click away.</p>
                    <div class="cnb_align_center"><a class="button button-primary button-green button-large font-18" onclick="cnb_get_agency_checkout('<?php echo esc_js( $agency_20_plan->id ) ?>')" href="#">Complete Checkout</a></div>
                </div>
            </div>
            <br><br>
            <div class="cnb-message notice"><p class="cnb-error-message"></p></div>
        <?php }
    }

    /**
     * @return CnbNotice
     */
    private function get_upgrade_notice( ) {
        $upgradeStatus     = filter_input( INPUT_GET, 'upgrade', @FILTER_SANITIZE_STRING );
        $checkoutSessionId = filter_input( INPUT_GET, 'checkout_session_id', @FILTER_SANITIZE_STRING );
        $remote_payment_api = new CnbAppRemotePayment();
        if ( $upgradeStatus === 'success?payment=success' ) {
            // Get checkout Session Details
            $session = $remote_payment_api->cnb_remote_get_subscription_session( $checkoutSessionId );
            if ( ! is_wp_error( $session ) ) {
                // This increases the cache ID if needed, since the Domain cache might have changed
                CnbAppRemote::cnb_incr_transient_base();

                return new CnbNotice( 'success', '<p>Your account has been successfully upgraded to a <strong>PRO account</strong>!</p>' );
            } else {
                return new CnbNotice( 'warning', '<p>Something is going on upgrading to a  <strong>PRO account</strong>.</p><p>Error: ' . esc_html( $session->get_error_message() ) . '!</p>' );
            }
        }

        return null;
    }
}
