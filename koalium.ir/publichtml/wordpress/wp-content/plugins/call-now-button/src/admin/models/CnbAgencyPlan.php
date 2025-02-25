<?php

namespace cnb\admin\models;

use cnb\utils\CnbUtils;
use stdClass;
use WP_Error;

// don't load directly
defined('ABSPATH') || die('-1');

class CnbAgencyPlan {
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $nickname;

    /**
     * @var CnbAgencyPlanPrice[]
     */
    public $prices;
    /**
     * @var string (monthly/yearly)
     */
    public $interval;
    /**
     * @var number
     */
    public $seats;

    /**
     * If a stdClass is passed, it is transformed into a CnbButton.
     * a WP_Error is ignored and return immediately
     * a null if converted into an (empty) CnbButton
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbAgencyPlan|WP_Error
     */
    public static function fromObject($object)
    {
        if (is_wp_error($object)) {
            return $object;
        }

        $plan = new CnbAgencyPlan();
        $plan->id = CnbUtils::getPropertyOrNull($object, 'id');
        $plan->nickname = CnbUtils::getPropertyOrNull($object, 'nickname');
        $plan->interval = CnbUtils::getPropertyOrNull($object, 'interval');
        $plan->seats = intval(CnbUtils::getPropertyOrNull($object, 'seats'));
        $plan->prices = CnbAgencyPlanPrice::fromObjects(CnbUtils::getPropertyOrNull($object, 'prices'));
        $plan->prices = array_reduce($plan->prices, function ($result, $price) {
            $result[$price->currency] = $price;
            return $result;
        }, array());

        return $plan;
    }

    /**
     * @param $objects stdClass[]|WP_Error|null
     *
     * @return CnbAgencyPlan[]|WP_Error
     */
    public static function fromObjects($objects)
    {
        if (is_wp_error($objects)) {
            return $objects;
        }

        return array_map(
            function ($object) {
                return self::fromObject($object);
            },
            $objects
        );
    }

    /**
     * @param float $amount 6.03
     * @param string $currency eur or usd
     *
     * @return string formatted in the proper language format (6.03 $, or €6,03, etc)
     */
    public static function get_formatted_amount($amount, $currency)
    {
        return cnb_get_formatted_amount($amount, $currency);
    }
}

class CnbAgencyPlanPrice {
    /**
     * @var float
     */
    public $price;

    /**
     * @var string should be lowercase, usually 'eur' or 'usd'
     */
    public $currency;

    public static function fromObject($object)
    {
        $price = new CnbAgencyPlanPrice();
        $price->price = floatval(CnbUtils::getPropertyOrNull($object, 'price'));
        $price->currency = CnbUtils::getPropertyOrNull($object, 'currency');
        if ($price->currency) {
            $price->currency = strtolower($price->currency);
        }

        return $price;
    }

    public static function fromObjects($objects)
    {
        if (is_wp_error($objects)) {
            return $objects;
        }

        return array_map(
            function ($object) {
                return self::fromObject($object);
            },
            $objects
        );
    }
}
