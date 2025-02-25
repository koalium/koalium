<?php

namespace cnb\admin\api;

// don't load directly
use cnb\utils\CnbUtils;
use stdClass;
use WP_Error;

defined( 'ABSPATH' ) || die( '-1' );

class CnbDeleteResult {
    /**
     * @var boolean
     */
    public $success;

    /**
     * @var string
     */
    public $id;

    /**
     * Any of the API result objects (CnbUser, CnbButton, etc)
     *
     * @var object|WP_Error
     */
    public $object;

    /**
     * If a stdClass is passed, it is transformed into a CnbDeleteResult.
     * a WP_Error is ignored and return immediatly
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbDeleteResult
     */
    public static function fromObject( $object ) {
        $result = new CnbDeleteResult();
        if ( is_wp_error( $object ) ) {
            $result->object = $object;
            return $result;
        }

        // phpcs:ignore PHPCompatibility.FunctionUse
        $result->success = boolval( CnbUtils::getPropertyOrNull( $object, 'success' ) );
        $result->id      = CnbUtils::getPropertyOrNull( $object, 'id' );
        $result->object  = CnbUtils::getPropertyOrNull( $object, 'object' );

        return $result;
    }

    /**
     * if this returns true, the #object contains a proper object (and not a WP_Error)
     * which can be cast into a CallNowButton type.
     *
     * @return bool
     */
    public function is_success() {
        return $this->success == true && ! is_wp_error( $this->object );
    }

    /**
     * @return WP_Error|null
     */
    public function get_error() {
        if ( is_wp_error( $this->object ) ) {
            return $this->object;
        }

        return null;
    }
}
