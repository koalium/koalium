<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\api\CnbAppRemotePayment;
use cnb\notices\CnbNotice;

class CnbDomainViewUpgrade {
    function header() {
        $domain = $this->get_domain();
        echo 'Upgrade ' . esc_html( $domain->name ) . ' to PRO';
    }

    /**
     * @return CnbDomain
     */
    private function get_domain() {
        $cnb_remote = new CnbAppRemote();
        $domain_id = filter_input( INPUT_GET, 'id', @FILTER_SANITIZE_STRING );
        $domain    = new CnbDomain();
        if ( strlen( $domain_id ) > 0 && $domain_id != 'new' ) {
            $domain = $cnb_remote->get_domain( $domain_id );
        }

        return $domain;
    }

    /**
     * @param $domain CnbDomain
     *
     * @return CnbNotice
     */
    private function get_upgrade_notice( $domain ) {
        $upgradeStatus     = filter_input( INPUT_GET, 'upgrade', @FILTER_SANITIZE_STRING );
        $checkoutSessionId = filter_input( INPUT_GET, 'checkout_session_id', @FILTER_SANITIZE_STRING );
        $remote_payment_api = new CnbAppRemotePayment();
        if ( $upgradeStatus === 'success?payment=success' ) {
            // Get checkout Session Details
            $session = $remote_payment_api->cnb_remote_get_subscription_session( $checkoutSessionId );
            if ( ! is_wp_error( $session ) ) {
                // This increases the cache ID if needed, since the Domain cache might have changed
                CnbAppRemote::cnb_incr_transient_base();

                return new CnbNotice( 'success', '<p>Your domain <strong>' . esc_html( $domain->name ) . '</strong> has been successfully upgraded to <strong>' . esc_html( $domain->type ) . '</strong>!</p>' );
            } else {
                return new CnbNotice( 'warning', '<p>Something is going on upgrading domain <strong>' . esc_html( $domain->name ) . '</strong>.</p><p>Error: ' . esc_html( $session->get_error_message() ) . '!</p>' );
            }
        }

        return null;
    }

    function render_content() {
        $domain = CnbDomain::setSaneDefault( $this->get_domain() );

        // Bail out in case of error
        if ( is_wp_error( $domain ) ) {
            return;
        }

        // See if the domain is JUST upgraded
        $notice = $this->get_upgrade_notice( $domain );
        if ( $notice ) {
            // And if so, re-fetch the domain
            $domain = CnbDomain::setSaneDefault( $this->get_domain() );
            // Also flush the cache
            do_action( 'cnb_after_button_changed' );
        }
	    wp_enqueue_script( CNB_SLUG . '-domain-upgrade' );
	    wp_enqueue_script( CNB_SLUG . '-billing-portal' );

        // Print the content
	    if ( SubscriptionStatus::has_outstanding_payment_for_domain( $domain ) ) {
			// We bail out of the upgrade and go to payment if there is an outstanding payment
		    (new PaymentView())->render_content();
	    } else if ( $notice && $domain->type != 'PRO' ) {
            // Probably upgraded, but not reflected yet on the API side. Warn about this
            ( new CnbDomainViewUpgradeInProgress() )->render( $domain );
        } else if ( $domain->type == 'PRO' ) {
            ( new CnbDomainViewUpgradeFinished() )->render( $domain, $notice );
        } else {
            ( new CnbDomainViewUpgradeOverview() )->render( $domain );
        }
    }

    public function render() {
        wp_enqueue_script( CNB_SLUG . '-profile' );

        add_action( 'cnb_header_name', array( $this, 'header' ) );
        do_action( 'cnb_header' );
        $this->render_content();
        do_action( 'cnb_footer' );
    }
}
