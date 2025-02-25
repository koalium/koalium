<?php

namespace cnb\admin\settings;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class StripeRequestBillingPortalResponse {
    /**
     * @var boolean
     */
    public $success;

    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $response      = new StripeRequestBillingPortalResponse();
        $response->success = CnbUtils::getPropertyOrNull( $object, 'success' );

        return $response;
    }
}
