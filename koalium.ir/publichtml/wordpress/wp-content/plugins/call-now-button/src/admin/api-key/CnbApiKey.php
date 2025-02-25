<?php

namespace cnb\admin\apikey;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbApiKey implements JsonSerializable {
    /**
     * @var string
     */
    public $id;
    /**
     * @var string
     */
    public $name;
    /**
     * @var string
     */
    public $key;
    /**
     * Unused
     * @var string
     */
    public $created;
    /**
     * Unused
     * @var string
     */
    public $updateTime;
    /**
     * Unused
     * @var string
     */
    public $lastUsed;

    /**
     * If a stdClass is passed, it is transformed into a CnbApiKey.
     * a WP_Error is ignored and return immediatly
     * a null if converted into an (empty) CnbApiKey
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbApiKey|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $apiKey             = new CnbApiKey();
        $apiKey->id         = CnbUtils::getPropertyOrNull( $object, 'id' );
        $apiKey->name       = CnbUtils::getPropertyOrNull( $object, 'name' );
        $apiKey->key        = CnbUtils::getPropertyOrNull( $object, 'key' );
        $apiKey->created    = CnbUtils::getPropertyOrNull( $object, 'created' );
        $apiKey->updateTime = CnbUtils::getPropertyOrNull( $object, 'updateTime' );
        $apiKey->lastUsed   = CnbUtils::getPropertyOrNull( $object, 'lastUsed' );

        return $apiKey;

    }

    /**
     * @param $objects stdClass[]|WP_Error|null
     *
     * @return CnbApiKey[]|WP_Error
     */
    public static function fromObjects( $objects ) {
        if ( is_wp_error( $objects ) ) {
            return $objects;
        }

        return array_map(
            function ( $object ) {
                return self::fromObject( $object );
            },
            $objects
        );
    }

    public function toArray() {
        return array(
            'id'         => $this->id,
            'name'       => $this->name,
            'key'        => $this->key,
            'created'    => $this->created,
            'updateTime' => $this->updateTime,
            'lastUsed'   => $this->lastUsed,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
