<?php

namespace cnb\coupons;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;
use stdClass;
use WP_Error;

class CnbPromotionCode {

    /**
     * @var string
     */
    public $code;
    /**
     * @var string
     */
    public $name;
    /**
     * @var float
     */
    public $amountOff;
    /**
     * @var float
     */
    public $percentOff;
    /**
     * @var int (date in epoch?)
     */
    public $redeemBy;
    /**
     * @var int (date in epoch?)
     */
    public $redeemByDate;
    /**
     * @var string (repeating, once, forever)
     */
    public $duration;
    /**
     * @var int
     */
    public $durationInMonths;
    /**
     * @var CnbPromotionCodeRestrictions
     */
    public $restrictions;

    public function get_discount() {
        if ( $this->percentOff ) {
            return $this->percentOff . '%';
        }
        // Since this is returned as an int in cents (1000 == 10 EUR/USD)
        // we convert it into full EUR/USD amounts, rounded down to be sure
        return floor( $this->amountOff / 100 ) . '&euro;/$';
    }

    public function get_restrictions() {
        $restrictions = '';
        if ( $this->restrictions->firstTimeTransaction ) {
            $restrictions .= 'The discount applies to first-time orders only.';
        }

        if ( $this->duration === 'forever' ) {
            $restrictions .= 'This discount applies to all your future invoices.';
        }
        if ( $this->duration === 'once' ) {
            $restrictions .= 'This discount is only applied to your first invoice.';
        }
        if ( $this->duration === 'repeating' ) {
            if ( $this->durationInMonths == 12 ) {
                $restrictions .= 'This discount applies for 1 year.';
            } else {
                $restrictions .= 'This discount applies for ' . $this->durationInMonths . ' months.';
            }
        }
        if ( $this->redeemByDate ) {
            $restrictions .= 'Redeem before ' . $this->redeemByDate . '.';
        }

        return $restrictions;
    }

    /**
     * @return string
     */
    public function get_plan() {
        return $this->restrictions->minimumAmount > 1000 ? 'when choosing Yearly billing' : 'on all plans';
    }

    /**
     * @return string
     */
    public function get_period() {
        $output = '';
        if ( $this->duration == 'forever' ) {
            $output .= 'for the entire length of your subscription';
        } elseif ( $this->duration == 'once' ) {
            $output .= 'your first bill';
        } elseif ( $this->restrictions->minimumAmount > 1000 && ceil( $this->durationInMonths / 12 ) > 1 ) {
            $output .= 'for the first ' . ceil( $this->durationInMonths / 12 ) . ' years';
        } elseif ( $this->restrictions->minimumAmount > 1000 && ceil( $this->durationInMonths / 12 ) == 1 ) {
            $output .= 'for the first year';
        } else {
            $output .= 'for the first ' . $this->durationInMonths . ' months';
        }

        return $output;
    }

    /**
     * Returns 0d 00h 00m 00s
     *
     * In case DateTime does not exist (PHP < 5.2.0), we return nothing and let Javascript handle it.
     * @return string
     */
    /** @noinspection PhpFullyQualifiedNameUsageInspection */
    public function get_redeem_by() {
        if ( version_compare( PHP_VERSION, '5.2.0', '<' ) ) {
            return '0d 00h 00m 00s';
        }

        $output = '';
        if ( $this->redeemBy > 0 && $this->redeemBy < PHP_INT_MAX ) {
            $now       = new \DateTime( 'now' );
            $redeem_by = ( new \DateTime() )->setTimestamp( $this->redeemBy );
            $diff      = $now->diff( $redeem_by );

            $output .= sprintf( '%dd ', $diff->d );
            $output .= sprintf( '%02dh ', $diff->h );
            $output .= sprintf( '%02dm ', $diff->m );
            $output .= sprintf( '%02ds', $diff->s );
        }

        return $output;
    }

    /**
     * If a stdClass is passed, it is transformed into a CnbPromotionCode.
     * a WP_Error is ignored and returned immediately
     * a null is converted into an (empty) CnbPromotionCode
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbPromotionCode|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }
		if ($object === null) {
			return null;
		}

        $promo_code                   = new CnbPromotionCode();
        $promo_code->code             = CnbUtils::getPropertyOrNull( $object, 'code' );
        $promo_code->name             = CnbUtils::getPropertyOrNull( $object, 'name' );
        $promo_code->amountOff        = CnbUtils::getPropertyOrNull( $object, 'amountOff' );
        $promo_code->percentOff       = CnbUtils::getPropertyOrNull( $object, 'percentOff' );
        $promo_code->redeemBy         = CnbUtils::getPropertyOrNull( $object, 'redeemBy' );
        $promo_code->duration         = CnbUtils::getPropertyOrNull( $object, 'duration' );
        $promo_code->durationInMonths = CnbUtils::getPropertyOrNull( $object, 'durationInMonths' );
        $promo_code->restrictions     = CnbPromotionCodeRestrictions::fromObject( CnbUtils::getPropertyOrNull( $object, 'restrictions' ) );

        // Convert date
        if ( $promo_code->redeemBy > 0 && $promo_code->redeemBy < PHP_INT_MAX ) {
            // That is a date we can parse
            $promo_code->redeemByDate = date( 'F d, Y', $promo_code->redeemBy );
        }

        return $promo_code;
    }
}
