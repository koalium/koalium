<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;

class PaymentView {
	public function header() {
		global $cnb_domain;
		echo 'Your subscription fee for ' . esc_html( $cnb_domain->name ) . ' is overdue';
	}

    private function get_subscription_data() {
	    global $cnb_domain, $cnb_subscription_data;
	    if ($cnb_subscription_data === null ) {
		    $remote = new CnbAppRemote();
		    return $remote->get_subscription_status( $cnb_domain->id);
	    }
        return $cnb_subscription_data;
    }

    public function render_content() {
	    $subscription_data = $this->get_subscription_data();
	    if ( $subscription_data && $subscription_data->activeSubscription && $subscription_data->invoiceUrl ) { ?>
            <div class="cnb-welcome-blocks">
                <div class="cnb-block">
                    <h1 style="font-weight: bold; font-size: 30px;">Failed payment</h1>
                    <p>Your PRO subscription is currently <strong>paused</strong> as we were unable to collect your subscription fee of <?php echo esc_html( $subscription_data->invoiceFormatted ) ?>.</p>
                    
                    <p>As a result buttons containing any PRO features are currently not displayed and you have no access to PRO features, including:</p>
                    
                        <p>✨ Multibuttons
                        ✨&nbsp;Multi-action&nbsp;Buttonbars
                        ✨&nbsp;Scheduling<br>
                        ✨&nbsp;Custom&nbsp;icons
                        ✨&nbsp;Multiple&nbsp;buttons&nbsp;per&nbsp;page
                        ✨&nbsp;And&nbsp;much&nbsp;more</p>
                    
                    
                    
                    <a class="button button-primary"
                        href="<?php echo esc_url( $subscription_data->invoiceUrl ) ?>">
                        Pay now
                    </a>
                    <p><a href="<?php echo esc_url( $subscription_data->invoiceUrl ) ?>">Pay now</a> to restore all PRO features.</p>
                </div>
            </div>
	    <?php } ?>

        <div class="cnb-welcome-blocks">
            <div class="cnb-block">
                Visit your <a href="#" onclick="return cnb_goto_billing_portal()">Billing portal</a> to see all your
                invoices and subscriptions.
            </div>
        </div>

	    <?php
    }
	public function render() {
		// For the billing portal
		wp_enqueue_script( CNB_SLUG . '-settings' );
		wp_enqueue_script( CNB_SLUG . '-billing-portal' );

        // Remove the notice, this payment page will explain it further
		add_filter( 'cnb_admin_notice_filter', function ( $notice ) {
			if ( $notice && $notice->name === 'cnb-outstanding-invoice' ) return null;
			return $notice;
		} );

		add_action( 'cnb_header_name', array( $this, 'header' ) );
		do_action( 'cnb_header' );
        $this->render_content();
		do_action( 'cnb_footer' );
	}
}
