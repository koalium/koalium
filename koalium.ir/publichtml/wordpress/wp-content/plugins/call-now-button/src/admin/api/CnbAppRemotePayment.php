<?php

namespace cnb\admin\api;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbAppRemotePayment {

    public function cnb_remote_post_subscription( $planId, $domainId, $callbackUri = null ) {
        $callbackUri = $callbackUri === null
            ? get_site_url()
            : $callbackUri;

        $body = array(
            'plan'        => $planId,
            'domain'      => $domainId,
            'callbackUri' => $callbackUri,
        );

        $rest_endpoint = '/v1/subscription/v2';

        return CnbAppRemote::cnb_remote_post( $rest_endpoint, $body );
    }


    public function cnb_remote_post_agency_subscription( $planId, $callbackUri = null ) {
        $callbackUri = $callbackUri === null
            ? get_site_url()
            : $callbackUri;

        $body = array(
            'plan'        => $planId,
            'callbackUri' => $callbackUri,
        );

        $rest_endpoint = '/v1/subscription/proAccount';

        return CnbAppRemote::cnb_remote_post( $rest_endpoint, $body );
    }

    public function cnb_remote_get_subscription_session( $subscriptionSessionId ) {
        $rest_endpoint = '/v1/subscription/session/' . $subscriptionSessionId;

        return CnbAppRemote::cnb_remote_get( $rest_endpoint );
    }
}
