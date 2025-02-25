<?php

namespace cnb\admin\settings;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class StripeBillingPortal {
    /**
     * @var string
     */
    public $url;

    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $portal      = new StripeBillingPortal();
        $portal->url = CnbUtils::getPropertyOrNull( $object, 'url' );

        return $portal;
    }
}
