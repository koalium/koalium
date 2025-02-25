<?php

namespace cnb\coupons;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;
use stdClass;
use WP_Error;

class CnbPromotionCodeRestrictions {
	/**
	 * @var boolean
	 */
	public $firstTimeTransaction;
	/**
	 * @var float
	 */
	public $minimumAmount;

	/**
	 * If a stdClass is passed, it is transformed into a CnbPromotionCodeRestrictions.
	 * a WP_Error is ignored and returned immediately
	 * a null is converted into an (empty) CnbPromotionCodeRestrictions
	 *
	 * @param $object stdClass|array|WP_Error|null
	 *
	 * @return CnbPromotionCodeRestrictions|WP_Error
	 */
	public static function fromObject( $object ) {
		if ( is_wp_error( $object ) ) {
			return $object;
		}

		$restrictions             = new CnbPromotionCodeRestrictions();
		$restrictions->firstTimeTransaction         = CnbUtils::getPropertyOrNull( $object, 'firstTimeTransaction' );
		$restrictions->minimumAmount   = CnbUtils::getPropertyOrNull( $object, 'minimumAmount' );

		return $restrictions;
	}
}
