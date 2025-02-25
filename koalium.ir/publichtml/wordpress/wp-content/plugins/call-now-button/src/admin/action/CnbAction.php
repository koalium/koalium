<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use ArrayObject;
use cnb\admin\button\CnbButton;
use cnb\utils\CnbAdminFunctions;
use cnb\utils\CnbUtils;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbAction implements JsonSerializable {
    /**
     * @var string
     */
    public $id;

    /**
     * @var string PHONE, TEXT, etc
     */
    public $actionType;

    /**
     * @var string
     */
    public $actionValue;

    /**
     * @var CnbActionProperties
     */
    public $properties;

    /**
     * @var string
     */
    public $backgroundColor;

    /**
     * @var boolean
     */
    public $iconEnabled;

    public $iconClass;

    public $iconColor;

    public $iconText;

    public $iconType = 'DEFAULT';

    public $iconBackgroundImage;

    public $labelBackgroundColor;

    public $labelText;

    /**
     * @var CnbActionSchedule
     */
    public $schedule;

    /**
     * Should this CnbAction be deleted?
     * @var boolean
     */
    public $delete;

    /**
     * Used internally to associate the Action to a Button
     * @var CnbButton
     */
    public $button;

    /**
     * If a stdClass is passed, it is transformed into a CnbAction.
     * a WP_Error is ignored and returned immediately
     * a null if converted into an (empty) CnbAction
     *
     * @param $object stdClass|array|WP_Error|null
     *
     * @return CnbAction|WP_Error
     */
    public static function fromObject( $object ) {
        if ( is_wp_error( $object ) ) {
            return $object;
        }
		if ($object instanceof CnbAction) {
			return $object;
		}

        $action = new CnbAction();

        $schedule = CnbUtils::getPropertyOrNull( $object, 'schedule' );

		if ($schedule != null) {
			$action->schedule               = new CnbActionSchedule();
			$action->schedule->showAlways   = CnbUtils::getPropertyOrNull( $schedule, 'showAlways' );
			$action->schedule->start        = CnbUtils::getPropertyOrNull( $schedule, 'start' );
			$action->schedule->stop         = CnbUtils::getPropertyOrNull( $schedule, 'stop' );
			$action->schedule->timezone     = CnbUtils::getPropertyOrNull( $schedule, 'timezone' );
			$action->schedule->outsideHours = CnbUtils::getPropertyOrNull( $schedule, 'outsideHours' );
			$daysOfWeek                     = CnbUtils::getPropertyOrNull( $schedule, 'daysOfWeek' );
			if ( isset( $daysOfWeek ) && is_array( $daysOfWeek ) ) {
				$action->schedule->daysOfWeek = ( new CnbAdminFunctions() )->cnb_create_days_of_week_array( $daysOfWeek );
			}
		}

        $action->id              = CnbUtils::getPropertyOrNull( $object, 'id' );
        $action->actionType      = CnbUtils::getPropertyOrNull( $object, 'actionType' );
        $action->actionValue     = CnbUtils::getPropertyOrNull( $object, 'actionValue' );
        $action->backgroundColor = CnbUtils::getPropertyOrNull( $object, 'backgroundColor' );
        $iconEnabled             = CnbUtils::getPropertyOrNull( $object, 'iconEnabled' );
        if ( $iconEnabled === null ) {
            $iconEnabled = true;
        }
        // phpcs:ignore PHPCompatibility.FunctionUse
        $action->iconEnabled          = boolval( $iconEnabled );
        $action->iconClass            = CnbUtils::getPropertyOrNull( $object, 'iconClass' );
        $action->iconColor            = CnbUtils::getPropertyOrNull( $object, 'iconColor' );
        $action->iconText             = CnbUtils::getPropertyOrNull( $object, 'iconText' );
        $action->iconType             = CnbUtils::getPropertyOrNull( $object, 'iconType' );
        $action->iconBackgroundImage  = CnbUtils::getPropertyOrNull( $object, 'iconBackgroundImage' );
        $action->labelBackgroundColor = CnbUtils::getPropertyOrNull( $object, 'labelBackgroundColor' );
        $action->labelText            = CnbUtils::getPropertyOrNull( $object, 'labelText' );

        $action->properties = CnbUtils::getPropertyOrNull( $object, 'properties' );

        // Special cases
        // "Fix" the WHATSAPP/SIGNAL/VIBER values
        $actionValueWhatsappHidden = CnbUtils::getPropertyOrNull( $object, 'actionValueWhatsappHidden' );
        if ( ( $action->actionType === 'WHATSAPP' || $action->actionType === 'SIGNAL' || $action->actionType === 'VIBER' ) && ! empty( $actionValueWhatsappHidden ) ) {
            $action->actionValue = $actionValueWhatsappHidden;
        }

        // Reset the iconText based on type if the iconText is left empty
        if ( isset( $action->iconText ) && empty( $action->iconText ) ) {
            $action->iconText = ( new CnbUtils() )->cnb_actiontype_to_icontext( $action->actionType );
        }

		$error = CnbAction::sanitize_action_value($action);
		if ( $error ) return $error;

        return $action;
    }

	/**
	 * @param $action CnbAction
	 *
	 * @return WP_Error|void
	 */
	private static function sanitize_action_value( $action ) {
		// In case the action type has a value that represents a URL, ensure that the URL is correct
		switch ($action->actionType) {
			case 'LINK':
			case 'IFRAME':
				// phpcs:ignore WordPress.WP
				$sanitized_value = sanitize_url($action->actionValue, array( 'http', 'https' ) );
				if ( empty( $sanitized_value )) {
					return new WP_Error( 'CNB_ACTION_VALUE_INVALID', 'The Full URL for your action contains an invalid URL. Please use a valid URL that starts with http:// or https://' );
				}
				$action->actionValue = $sanitized_value;
				break;
			case 'EMAIL':
				$sanitized_value = sanitize_email($action->actionValue);
				if ( ! is_email( $sanitized_value ) ) {
					return new WP_Error( 'CNB_ACTION_VALUE_INVALID', 'The Email address for your action is invalid. Please use a valid email address.' );
				}
				$action->actionValue = $sanitized_value;
				break;
		}
	}

    /**
     * @param $objects stdClass[]|array[]|WP_Error|null
     *
     * @return CnbAction[]|WP_Error
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
     * @return array
     */
    public function toArray() {
        return array(
            'id'                   => $this->id,
            'actionType'           => $this->actionType,
            'actionValue'          => $this->actionValue,
            'properties'           => $this->properties,
            'backgroundColor'      => $this->backgroundColor,
            'iconEnabled'          => $this->iconEnabled,
            'iconClass'            => $this->iconClass,
            'iconColor'            => $this->iconColor,
            'iconText'             => $this->iconText,
            'iconType'             => $this->iconType,
            'iconBackgroundImage'  => $this->iconBackgroundImage,
            'labelBackgroundColor' => $this->labelBackgroundColor,
            'labelText'            => $this->labelText,
            'schedule'             => $this->schedule,
        );
    }

    /**
     * @return array
     */
	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbActionProperties extends ArrayObject implements JsonSerializable {

    /**
     * @return array
     */
    public function toArray() {
        // Since this is an ArrayObject, we can use its "native" functions
        return $this->getArrayCopy();
    }

    /**
     * @return array
     */
	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}

class CnbActionSchedule implements JsonSerializable {
    /**
     * When `showAlways` is true, the `CnbAction` is always visible and all other
     * elements of the schedule are ignored.
     * @var boolean
     */
    public $showAlways;

    /**
     * Array of booleans, starting at Monday.
     * @var boolean[]
     */
    public $daysOfWeek;

    /**
     * @var string hh:mm
     */
    public $start;

    /**
     * @var string hh:mm
     */
    public $stop;

    /**
     * @var string
     */
    public $timezone;

    /**
     * @var boolean
     */
    public $outsideHours;

    public function toArray() {
        return array(
            'showAlways'   => $this->showAlways,
            'daysOfWeek'   => $this->daysOfWeek,
            'start'        => $this->start,
            'stop'         => $this->stop,
            'timezone'     => $this->timezone,
            'outsideHours' => $this->outsideHours,
        );
    }

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
    public function jsonSerialize() {
        return $this->toArray();
    }
}
