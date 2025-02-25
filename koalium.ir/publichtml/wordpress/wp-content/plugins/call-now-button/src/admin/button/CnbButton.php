<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\action\CnbAction;
use cnb\admin\condition\CnbCondition;
use cnb\admin\domain\CnbDomain;
use cnb\utils\CnbUtils;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbButton implements JsonSerializable {

    /**
     * @var string
     */
    public $id;

    /**
     * @var boolean
     */
    public $active;

    /**
     * @var string
     */
    public $name;

    /**
     * One of SINGLE, MULTI, FULL or DOTS
     * @var string
     */
    public $type;

    /**
     * @var CnbDomain
     */
    public $domain;

    /**
     * Depending on the circumstance, this is either an array of CnbAction
     * or (when submitting to the API), reduced to an array of strings (CnbAction->id's)
     *
     * @var CnbAction[]|string[]
     */
    public $actions;

    /**
     * Depending on the circumstance, this is either an array of CnbCondition
     * or (when submitting to the API), reduced to an array of strings (CnbAction->id's)
     *
     * @var CnbCondition[]|string[]
     */
    public $conditions;

    /**
     * @var CnbButtonOptions
     */
    public $options;

    /**
     * @var CnbMultiButtonOptions
     */
    public $multiButtonOptions;

    /**
     * @param $domain CnbDomain
     *
     * @return CnbButton
     */
    public static function createDummyButton( $domain = null ) {
        $button                       = new CnbButton();
        $button->id                   = 'new';
        $button->active               = true;
        $button->name                 = '';
        $button->type                 = 'SINGLE';
        $button->domain               = $domain;
        $button->actions              = array();
        $button->conditions           = array();
        $button->options              = new CnbButtonOptions();
        $button->options->placement   = 'BOTTOM_RIGHT';
        $button->options->displayMode = 'ALWAYS';

        return $button;
    }

    /**
     * Ensure the position is valid for FULL
     *
     * @param $button CnbButton
     *
     * @return void
     */
    private static function validate( $button ) {
        if ( strtoupper( $button->type ) !== 'FULL' ) {
            return;
        }

        // If empty, set a default for FULL
        if ( empty( $button->options->placement ) ) {
            $button->options->placement = 'BOTTOM_CENTER';
        }

        // If some non-valid version, set a default for FULL
        if ( $button->options->placement !== 'BOTTOM_CENTER' && $button->options->placement !== 'TOP_CENTER' ) {
            $button->options->placement = 'BOTTOM_CENTER';
        }
    }

    public function toArray( $convertToStringArray = true ) {
        // Actions should be string[]
        $actions = array();
        if ( is_array( $this->actions ) ) {
            $actions = ( new CnbUtils() )->cnb_array_column( $this->actions, 'id' );
        }
        $actions = array_filter( $actions, function ( $action ) {
            return ! empty( $action ) && $action !== 'new';
        } );

        // Conditions should be string[]
        $conditions = array();
        if ( is_array( $this->conditions ) ) {
            $conditions = ( new CnbUtils() )->cnb_array_column( $this->conditions, 'id' );
        }
        $conditions = array_filter( $conditions, function ( $condition ) {
            return ! empty( $condition );
        } );

        // Empty options should not be an array, but a null (so we do not confuse the API server
        $options = isset( $this->options ) ? $this->options->toArray() : null;

        // domain should be string (the CnbDomain ID)
        $domain = $this->domain ? $this->domain->id : $this->domain;

        // This basically "undoes" the above and returns actual objects instead of strings
        if ( ! $convertToStringArray ) {
            $actions    = $this->actions;
            $conditions = $this->conditions;
            $domain     = $this->domain;
        }

        return array(
            'id'                 => $this->id,
            'active'             => $this->active,
            'name'               => $this->name,
            'type'               => $this->type,
            'domain'             => $domain,
            'actions'            => $actions,
            'conditions'         => $conditions,
            'options'            => $options,
            'multiButtonOptions' => $this->multiButtonOptions,
        );
    }

    /**
     * If a stdClass is passed, it is transformed into a CnbButton.
     * a WP_Error is ignored and return immediately
     * a null if converted into an (empty) CnbButton
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbButton|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }

        $button     = new CnbButton();
        $button->id = CnbUtils::getPropertyOrNull( $object, 'id' );
        // phpcs:ignore PHPCompatibility.FunctionUse
        $button->active     = boolval( CnbUtils::getPropertyOrNull( $object, 'active' ) );
        $button->name       = CnbUtils::getPropertyOrNull( $object, 'name' );
        $button->type       = CnbUtils::getPropertyOrNull( $object, 'type' );
        $button->domain     = CnbUtils::getPropertyOrNull( $object, 'domain' );
        $button->actions    = CnbUtils::getPropertyOrNull( $object, 'actions' );
        $button->conditions = CnbUtils::getPropertyOrNull( $object, 'conditions' );
        $options            = CnbUtils::getPropertyOrNull( $object, 'options' );
        $button->options    = CnbButtonOptions::fromObject( $options );
        $multiButtonOptions = CnbUtils::getPropertyOrNull( $object, 'multiButtonOptions' );
        if ( $multiButtonOptions != null ) {
            $button->multiButtonOptions = CnbMultiButtonOptions::fromObject( $multiButtonOptions );
        }

        if ( gettype( $button->domain ) === 'string' ) {
            $domainId           = $button->domain;
            $button->domain     = new CnbDomain();
            $button->domain->id = $domainId;
        }

        // Convert "stdClass" actions into Action classes
        $button->actions = CnbAction::fromObjects( $button->actions );

	    self::validate( $button );

        return $button;
    }

    /**
     * @param $objects stdClass[]|WP_Error|null
     *
     * @return CnbButton[]|WP_Error
     */
    public static function fromObjects( $objects ) {
        if ( is_wp_error( $objects ) ) {
            return $objects;
        }

        return array_map(
            function ( $object ) {
                return CnbButton::fromObject( $object );
            },
            $objects
        );
    }

    /**
     * Convert an array of CnbButtons into arrays, useful for CLI viewing
     *
     * @param $buttons stdClass[]|CnbButton[]
     *
     * @return array
     */
    public static function convertToArray( $buttons ) {
        return array_map(
            function ( $button ) {
                $button = $button instanceof CnbButton ? $button : self::fromObject( $button );

                return ( $button instanceof CnbButton ) ? $button->toArray() : array();
            }, $buttons
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

/**
 * The Options for a CnbButton are varied and can differ
 */
class CnbButtonOptions implements JsonSerializable {
    /**
     *   - BOTTOM_LEFT
     *   - BOTTOM_CENTER
     *   - BOTTOM_RIGHT
     *   - MIDDLE_LEFT
     *   - MIDDLE_RIGHT
     *   - TOP_LEFT
     *   - TOP_CENTER
     *   - TOP_RIGHT
     * @var string
     */
    public $placement;
    public $position; // DEFAULT, FIXED, ABSOLUTE
    public $animation; // NONE, TADA, SHAKE, SONAR_LIGHT, SONAR_DARK
    public $iconBackgroundColor;
    public $iconColor;
    public $displayMode; // MOBILE_ONLY, DESKTOP_ONLY, ALWAYS
    /**
     * @var CnbScrollOptions
     */
    public $scroll;
	/**
	 * Free text field for additional CSS Classes
	 * PRO only
	 *
	 * @var string
	 */
	public $cssClasses;

    public static function getAnimationTypes() {
        return array(
            'NONE'        => 'None',
            'TADA'        => 'Tada',
            'SHAKE'       => 'Shake',
            'SONAR_LIGHT' => 'Sonar light (for dark backgrounds)',
            'SONAR_DARK'  => 'Sonar dark (for light backgrounds)',
        );
    }

    public function toArray() {
        return array(
            'placement'           => $this->placement,
            'position'            => $this->position,
            'animation'           => $this->animation,
            'iconBackgroundColor' => $this->iconBackgroundColor,
            'iconColor'           => $this->iconColor,
            'displayMode'         => $this->displayMode,
            'scroll'              => $this->scroll,
	        'cssClasses'          => $this->cssClasses,
        );
    }

    public static function fromObject( $object ) {
        $options                      = new CnbButtonOptions();
        $options->placement           = CnbUtils::getPropertyOrNull( $object, 'placement' );
        $options->position            = CnbUtils::getPropertyOrNull( $object, 'position' );
        $options->animation           = CnbUtils::getPropertyOrNull( $object, 'animation' );
        $options->iconBackgroundColor = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundColor' );
        $options->iconColor           = CnbUtils::getPropertyOrNull( $object, 'iconColor' );
        $options->displayMode         = CnbUtils::getPropertyOrNull( $object, 'displayMode' );
        $scrollOptions                = CnbUtils::getPropertyOrNull( $object, 'scroll' );
        if ( $scrollOptions != null ) {
            $options->scroll = CnbScrollOptions::fromObject( $scrollOptions );
        }
	    $options->cssClasses          = CnbUtils::getPropertyOrNull( $object, 'cssClasses' );

        return $options;
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbMultiButtonOptions implements JsonSerializable {
    public $id;

    // Deprecated, replaced by $iconColorOpen
    public $iconColor;
    // Deprecated, replaced by $iconBackgroundColorOpen
    public $iconBackgroundColor;

    public $iconTypeOpen;
    public $iconTextOpen;
    public $iconBackgroundImageOpen;
    public $iconClassOpen;
    public $iconColorOpen;
    public $iconBackgroundColorOpen;

    public $iconTypeClose;
    public $iconTextClose;
    public $iconBackgroundImageClose;
    public $iconClassClose;
    public $iconColorClose;
    public $iconBackgroundColorClose;

    public $labelTextOpen;
    public $labelBackgroundColorOpen;
    public $labelTextColorOpen;

    public $labelTextClose;
    public $labelBackgroundColorClose;
    public $labelTextColorClose;

	public $defaultState;

    public function toArray() {
        return array(
            'id'                  => ! empty( $this->id ) ? $this->id : null,
            'iconColor'           => $this->iconColor,
            'iconBackgroundColor' => $this->iconBackgroundColor,

            'iconTypeOpen'            => $this->iconTypeOpen,
            'iconTextOpen'            => $this->iconTextOpen,
            'iconBackgroundImageOpen' => $this->iconBackgroundImageOpen,
            'iconClassOpen'           => $this->iconClassOpen,
            'iconColorOpen'           => $this->iconColorOpen,
            'iconBackgroundColorOpen' => $this->iconBackgroundColorOpen,

            'iconTypeClose'            => $this->iconTypeClose,
            'iconTextClose'            => $this->iconTextClose,
            'iconBackgroundImageClose' => $this->iconBackgroundImageClose,
            'iconClassClose'           => $this->iconClassClose,
            'iconColorClose'           => $this->iconColorClose,
            'iconBackgroundColorClose' => $this->iconBackgroundColorClose,

            'labelTextOpen'            => $this->labelTextOpen, // PRO only
            'labelBackgroundColorOpen' => $this->labelBackgroundColorOpen, // PRO only
            'labelTextColorOpen'       => $this->labelTextColorOpen, // PRO only

            'labelTextClose'            => $this->labelTextClose, // PRO only
            'labelBackgroundColorClose' => $this->labelBackgroundColorClose, // PRO only
            'labelTextColorClose'       => $this->labelTextColorClose, // PRO only,

	        'defaultState'              => $this->defaultState, // PRO only,
        );
    }

    public static function fromObject( $object ) {
        $options                      = new CnbMultiButtonOptions();
        $options->id                  = CnbUtils::getPropertyOrNull( $object, 'id' );
        $options->iconColor           = CnbUtils::getPropertyOrNull( $object, 'iconColor' );
        $options->iconBackgroundColor = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundColor' );

        $options->iconTypeOpen            = CnbUtils::getPropertyOrNull( $object, 'iconTypeOpen' );
        $options->iconTextOpen            = CnbUtils::getPropertyOrNull( $object, 'iconTextOpen' );
        $options->iconBackgroundImageOpen = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundImageOpen' );
        $options->iconClassOpen           = CnbUtils::getPropertyOrNull( $object, 'iconClassOpen' );
        $options->iconColorOpen           = CnbUtils::getPropertyOrNull( $object, 'iconColorOpen' );
        $options->iconBackgroundColorOpen = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundColorOpen' );

        $options->iconTypeClose            = CnbUtils::getPropertyOrNull( $object, 'iconTypeClose' );
        $options->iconTextClose            = CnbUtils::getPropertyOrNull( $object, 'iconTextClose' );
        $options->iconBackgroundImageClose = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundImageClose' );
        $options->iconClassClose           = CnbUtils::getPropertyOrNull( $object, 'iconClassClose' );
        $options->iconColorClose           = CnbUtils::getPropertyOrNull( $object, 'iconColorClose' );
        $options->iconBackgroundColorClose = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundColorClose' );

        $options->labelTextOpen            = CnbUtils::getPropertyOrNull( $object, 'labelTextOpen' );
        $options->labelBackgroundColorOpen = CnbUtils::getPropertyOrNull( $object, 'labelBackgroundColorOpen' );
        $options->labelTextColorOpen = CnbUtils::getPropertyOrNull( $object, 'labelTextColorOpen' );

        $options->labelTextClose            = CnbUtils::getPropertyOrNull( $object, 'labelTextClose' );
        $options->labelBackgroundColorClose = CnbUtils::getPropertyOrNull( $object, 'labelBackgroundColorClose' );
        $options->labelTextColorClose = CnbUtils::getPropertyOrNull( $object, 'labelTextColorClose' );

		$options->defaultState = CnbUtils::getPropertyOrNull( $object, 'defaultState' );

        return $options;
    }

	public static function getDefaultStates() {
		return array(
			'CLOSED'   => 'Closed (default)',
			'EXPANDED' => 'Expanded',
		);
	}
	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbScrollOptions implements JsonSerializable {
    /**
     * 0-Inf, but should be a positive integer
     * @var number
     */
    public $revealAtHeight;

    /**
     * 0-Inf, but should be a positive integer
     * @var number
     */
    public $hideAtHeight;

    /**
     * Indicates if this element should be hidden again after it is visible
     * @var boolean
     */
    public $neverHide;

    public function toArray() {
        return array(
            'revealAtHeight' => $this->revealAtHeight,
            'hideAtHeight'   => $this->hideAtHeight,
            'neverHide'      => $this->neverHide,
        );
    }

    public static function fromObject( $object ) {
        $options                 = new CnbScrollOptions();
        $options->revealAtHeight = intval( CnbUtils::getPropertyOrNull( $object, 'revealAtHeight' ) );
        $options->hideAtHeight   = intval( CnbUtils::getPropertyOrNull( $object, 'hideAtHeight' ) );
        // phpcs:ignore PHPCompatibility.FunctionUse
        $options->neverHide = boolval( CnbUtils::getPropertyOrNull( $object, 'neverHide' ) );

        return $options;
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
