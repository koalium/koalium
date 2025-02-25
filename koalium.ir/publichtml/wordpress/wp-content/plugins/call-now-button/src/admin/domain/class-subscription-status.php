<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\models\CnbPlan;
use cnb\utils\CnbUtils;
use stdClass;
use WP_Error;

class SubscriptionStatus {
	/**
	 * @var string
	 */
	public $domainId;
	/**
	 * @var boolean
	 */
	public $existingSubscriptions;
	/**
	 * @var boolean
	 */
	public $activeTrial;
	/**
	 * @var boolean
	 */
	public $activeSubscription;
	/**
	 * @var string
	 */
	public $invoiceUrl;
	/**
	 * @var double
	 */
	public $invoiceAmount;
	/**
	 * @var string eur or usd
	 */
	public $invoiceCurrency;
	/**
	 * @var string formatted according to PHP/WordPress' locale setting
	 */
	public $invoiceFormatted;
	/**
	 * @var string monthly or yearly
	 */
	public $interval;
	/**
	 * @var string link to the User's personal payment page
	 */
	public $paymentPage;

	/**
	 * If a stdClass is passed, it is transformed into a CnbButton.
	 * a WP_Error is ignored and returned immediately
	 * a null if converted into an (empty) CnbButton
	 *
	 * @param $object stdClass|array|WP_Error|null
	 *
	 * @return SubscriptionStatus|WP_Error|null
	 */
	public static function from_object( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }
		if ( $object === null) {
			return null;
		}

		$payment_link =
			add_query_arg( array(
				'page'   => 'call-now-button-domains',
				'action' => 'payment',
			),
				admin_url( 'admin.php' ) );
		$domain_status = new SubscriptionStatus();
		$domain_status->domainId = CnbUtils::getPropertyOrNull( $object, 'domainId' );
		$domain_status->existingSubscriptions = CnbUtils::getPropertyOrNull( $object, 'existingSubscriptions' );
		$domain_status->activeTrial = CnbUtils::getPropertyOrNull( $object, 'activeTrial' );
		$domain_status->activeSubscription = CnbUtils::getPropertyOrNull( $object, 'activeSubscription' );
		$domain_status->invoiceUrl = CnbUtils::getPropertyOrNull( $object, 'invoiceUrl' );
		$domain_status->invoiceAmount = CnbUtils::getPropertyOrNull( $object, 'invoiceAmount' );
		$domain_status->invoiceCurrency = CnbUtils::getPropertyOrNull( $object, 'invoiceCurrency' );

		$domain_status->invoiceFormatted = CnbPlan::get_formatted_amount($domain_status->invoiceAmount / 100.0, $domain_status->invoiceCurrency);
		$domain_status->interval = CnbUtils::getPropertyOrNull( $object, 'interval' );
		$domain_status->paymentPage = $payment_link;

		return $domain_status;
	}

	public function has_outstanding_payment() {
		$domain = new CnbDomain();
		$domain->id = $this->domainId;
		return $this->_has_outstanding_payment($domain);
	}

	/**
	 * @param CnbDomain $domain
	 *
	 * @return bool
	 */
	private function _has_outstanding_payment( $domain ) {
		// If the domain property doesn't match this SubscriptionStatus, we can't tell, so no outstanding payment
		if ($this->domainId !== $domain->id) return false;
		return (bool)$this->invoiceUrl;
	}

	/**
	 * @param CnbDomain $domain
	 *
	 * @return bool
	 */
	public static function has_outstanding_payment_for_domain( $domain ) {
		/** @var SubscriptionStatus $cnb_subscription_data */
		global $cnb_subscription_data;
		if ($cnb_subscription_data === null) return false;
		return $cnb_subscription_data->_has_outstanding_payment( $domain );
	}
}
