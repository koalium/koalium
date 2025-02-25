<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbDomain implements JsonSerializable {

    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;
    /**
     * @var string MONTHLY/YEARLY
     */
    public $interval;
    /**
     * @var string ACTIVE/TRIALING
     */
    public $status;

    /**
     * @var string STARTER/PRO/FREE
     */
    public $type;
    public $expires;
    /**
     * @var boolean
     */
    public $renew;
    public $timezone;
    /**
     * @var boolean
     */
    public $trackGA;
    /**
     * @var boolean
     */
    public $trackConversion;
    /**
     * @var CnbDomainProperties
     */
    public $properties;
	/**
	 * @var string[]
	 */
	public $aliases;

    /**
     * If a stdClass is passed, it is transformed into a CnbDomain.
     * a WP_Error is ignored and return immediatly
     * a null if converted into an (empty) CnbDomain
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbDomain|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $domain = new CnbDomain();

        $domain->id              = CnbUtils::getPropertyOrNull( $object, 'id' );
        $domain->name            = CnbUtils::getPropertyOrNull( $object, 'name' );
        $domain->interval        = CnbUtils::getPropertyOrNull( $object, 'interval' );
        $domain->status          = CnbUtils::getPropertyOrNull( $object, 'status' );
        $domain->timezone        = CnbUtils::getPropertyOrNull( $object, 'timezone' );
        $domain->type            = CnbUtils::getPropertyOrNull( $object, 'type' );
        $properties              = CnbUtils::getPropertyOrNull( $object, 'properties' );
        $domain->properties      = CnbDomainProperties::fromObject( $properties );
        $domain->trackGA         = CnbUtils::getPropertyOrNull( $object, 'trackGA' );
        $domain->trackConversion = CnbUtils::getPropertyOrNull( $object, 'trackConversion' );
        $domain->renew           = CnbUtils::getPropertyOrNull( $object, 'renew' );
        $domain->expires         = CnbUtils::getPropertyOrNull( $object, 'expires' );

        // Convert into booleans
        $domain->trackGA         = filter_var( $domain->trackGA, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
        $domain->trackConversion = filter_var( $domain->trackConversion, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
        $domain->renew           = filter_var( $domain->renew, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

		$domain->aliases         = CnbUtils::getPropertyOr( $object, 'aliases', array() );

        return $domain;
    }

    /**
     * @param $objects stdClass[]|WP_Error|null
     *
     * @return CnbDomain[]|WP_Error
     */
    public static function fromObjects( $objects ) {
        if ( is_wp_error( $objects ) ) {
            return $objects;
        }
        if ( $objects === null ) {
            return null;
        }

        return array_map(
            function ( $object ) {
                return self::fromObject( $object );
            },
            $objects
        );
    }

    /**
     *
     * This changes the object itself, settings some sane defaults in case those are missing
     *
     * @param $domain CnbDomain|null
     * @param $domain_id number|null
     *
     * @returns CnbDomain
     */
    public static function setSaneDefault( $domain = null, $domain_id = null ) {
        if ( is_wp_error( $domain ) ) {
            return $domain;
        }

        if ( $domain === null ) {
            $domain = new CnbDomain();
        }

	    if ( $domain_id !== null && strlen( $domain_id ) > 0 && $domain_id == 'new' && empty( $domain->id ) ) {
            $domain->id = null;
        }
        if ( empty( $domain->timezone ) ) {
            $domain->timezone = null;
        }
        if ( empty( $domain->type ) ) {
            $domain->type = 'STARTER';
        }
        if ( empty( $domain->properties ) ) {
            $domain->properties = new CnbDomainProperties();
        }
        if ( empty( $domain->properties->scale ) ) {
            $domain->properties->scale = '1';
        }
        if ( empty( $domain->properties->debug ) ) {
            $domain->properties->debug = false;
        }
        if ( empty( $domain->properties->zindex ) ) {
            $domain->properties->zindex = 2147483647;
        }

        if ( empty( $domain->name ) ) {
            $domain->name = null;
        }
        if ( ! isset( $domain->trackGA ) ) {
            $domain->trackGA = false;
        }
        if ( ! isset( $domain->trackConversion ) ) {
            $domain->trackConversion = false;
        }
	    if ( ! isset( $domain->aliases ) ) {
		    $domain->aliases = array();
	    }

        return $domain;
    }

    public function toArray() {
        return array(
            'id'              => $this->id,
            'name'            => $this->name,
            'interval'        => $this->interval,
            'status'          => $this->status,
            'timezone'        => $this->timezone,
            'type'            => $this->type,
            'properties'      => $this->properties->toArray(),
            'trackGA'         => $this->trackGA,
            'trackConversion' => $this->trackConversion,
            'renew'           => $this->renew,
            'aliases'         => $this->aliases,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbDomainProperties implements JsonSerializable {
    /**
     * @var number 0.7 to 1.3 (normally 1)
     */
    public $scale;

    /**
     * @var boolean
     */
    public $debug;

    /**
     * @var number|string ("auto" is also allowed)
     */
    public $zindex;

    /**
     * @var string "true" or "false"
     */
    public $allowMultipleButtons;

    /**
     * If a stdClass is passed, it is transformed into a CnbDomainProperties.
     * a WP_Error is ignored and return immediatly
     * a null if converted into an (empty) CnbDomain
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbDomainProperties|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $properties                       = new CnbDomainProperties();
        $properties->scale                = CnbUtils::getPropertyOrNull( $object, 'scale' );
        $properties->debug                = CnbUtils::getPropertyOrNull( $object, 'debug' );
        $properties->zindex               = CnbUtils::getPropertyOrNull( $object, 'zindex' );
        $properties->allowMultipleButtons = CnbUtils::getPropertyOrNull( $object, 'allowMultipleButtons' );

        $properties->debug                = filter_var( $properties->debug, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
        $properties->allowMultipleButtons = filter_var( $properties->allowMultipleButtons, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );

        return $properties;
    }

    public function toArray() {
        return array(
            'scale'                => $this->scale,
            'debug'                => $this->debug,
            'zindex'               => $this->zindex,
            'allowMultipleButtons' => $this->allowMultipleButtons,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
