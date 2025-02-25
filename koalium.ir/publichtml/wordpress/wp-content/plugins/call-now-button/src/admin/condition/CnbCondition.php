<?php

namespace cnb\admin\condition;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\button\CnbButton;
use cnb\utils\CnbUtils;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbCondition implements JsonSerializable {
    public $id;
    /**
     * @var string can be URL or GEO
     */
    public $conditionType = 'URL';
    public $filterType;
    /**
     * @var string can be one of SIMPLE, EXACT, REGEX, SUBSTRING (for URL).
     * or COUNTRY_CODE (for GEO)
     */
    public $matchType;
    public $matchValue;

    /**
     * Should this Condition be deleted?
     * @var boolean
     */
    public $delete;

    /**
     * Used internally to associate the Condition to a Button
     * @var CnbButton
     */
    public $button;

    /**
     * If a stdClass is passed, it is transformed into a CnbCondition.
     * a WP_Error is ignored and return immediatly
     * a null if converted into an (empty) CnbCondition
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbCondition|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $condition = new CnbCondition();

        $condition->id            = CnbUtils::getPropertyOrNull( $object, 'id' );
        $condition->conditionType = CnbUtils::getPropertyOrNull( $object, 'conditionType' );
        $condition->filterType    = CnbUtils::getPropertyOrNull( $object, 'filterType' );
        $condition->matchType     = CnbUtils::getPropertyOrNull( $object, 'matchType' );
        $condition->matchValue    = CnbUtils::getPropertyOrNull( $object, 'matchValue' );
        $condition->delete        = CnbUtils::getPropertyOrNull( $object, 'delete' );

        return $condition;
    }

    /**
     * @param $objects stdClass[]|WP_Error|null
     *
     * @return CnbCondition[]|WP_Error
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

    public function toArray() {
        // Note, we do not export "delete", since that is only used internally
        return array(
            'id'            => $this->id,
            'conditionType' => $this->conditionType,
            'filterType'    => $this->filterType,
            'matchType'     => $this->matchType,
            'matchValue'    => $this->matchValue,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
