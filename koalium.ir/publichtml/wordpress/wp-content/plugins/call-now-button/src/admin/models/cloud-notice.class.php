<?php

namespace cnb\admin\models;

use cnb\admin\action\CnbAction;
use cnb\admin\button\CnbButton;
use cnb\utils\CnbUtils;
use stdClass;
use WP_Error;

class ValidationMessageWithId {
	/**
	 * @var string ID of the item (Button, Action, Domain, etc)
	 */
	public $id;
	/**
	 * @var ValidationMessage[]
	 */
	public $messages;

	/**
	 * @param $objects stdClass[]|WP_Error|null
	 *
	 * @return ValidationMessageWithId[]|WP_Error
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

	public static function fromObject( $object ) {
		if ( is_wp_error( $object ) ) {
			return $object;
		}

		$validationMessageWithId = new ValidationMessageWithId();

		$validationMessageWithId->id       = CnbUtils::getPropertyOrNull( $object, 'id' );
		$validationMessageWithId->messages = ValidationMessage::fromObjects( CnbUtils::getPropertyOrNull( $object, 'messages' ) );

		return $validationMessageWithId;
	}

	/**
	 * @param $id string ID of the object to find messages for
	 *
	 * @return ValidationMessage[]
	 */
	public static function get_validation_messages_for( $id ) {
		global $cnb_validation_messages;

		$obj = array_filter( $cnb_validation_messages, function ( $message ) use ( $id ) {
			return $message->id == $id;
		} );
		if ( $obj && count( $obj ) ) {
			return array_pop( $obj )->messages;
		}

		return array();
	}
}

class ValidationMessage {
	/**
	 * @var string INFO, WARNING, ERROR
	 */
	public $type;

	/**
	 * @var string Human-readable message
	 */
	public $message;

	/**
	 * @param $objects stdClass[]|WP_Error|null
	 *
	 * @return ValidationMessage[]|WP_Error
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

	public static function fromObject( $object ) {
		if ( is_wp_error( $object ) ) {
			return $object;
		}
		$validationMessage          = new ValidationMessage();
		$validationMessage->type    = CnbUtils::getPropertyOrNull( $object, 'type' );
		$validationMessage->message = CnbUtils::getPropertyOrNull( $object, 'message' );

		return $validationMessage;
	}

	/**
	 * Get all validation messages for this Button (including Actions and Conditions)
	 *
	 * @param $button CnbButton
	 *
	 * @return ValidationMessage[]
	 */
	public static function get_validation_notices( $button ) {
		$notices = array();
		foreach ( $button->actions as $action ) {
			$notices = array_merge( $notices, ValidationMessageWithId::get_validation_messages_for( $action->id ) );
		}
		foreach ( $button->conditions as $condition ) {
			$notices = array_merge( $notices, ValidationMessageWithId::get_validation_messages_for( $condition->id ) );
		}

		return array_merge( $notices, ValidationMessageWithId::get_validation_messages_for( $button->id ) );
	}

	/**
	 * Get all validation messages for this Button (including Actions and Conditions)
	 *
	 * @param $action CnbAction
	 *
	 * @return ValidationMessage[]
	 */
	public static function get_validation_notices_for_action( $action ) {
		return ValidationMessageWithId::get_validation_messages_for( $action->id );
	}
}

class ValidationHooks {
	/**
	 * This is called via the cnb_validation_notices hook
	 *
	 * @param ValidationMessage|ValidationMessage[] $messages The Validation message(s) to create a notice for
	 * @param bool $wrap Should the message be wrapped in a <p> tag
	 *
	 * @return void
	 */
	function create_notice($messages, $wrap = false) {
		if ( $messages instanceof ValidationMessage ) {
			$this->echo_inline_notice( $messages, $wrap );
			return;
		}
		if ( is_array( $messages ) ) {
			foreach ( $messages as $notice ) {
				$this->echo_inline_notice( $notice, $wrap );
			}
		}
	}

	function echo_inline_notice($notice, $wrap) {
		echo '<div class="notice notice-' . esc_html(strtolower($notice->type)) . ' inline">';
		if ($wrap) echo '<p>';
		echo esc_html($notice->message);
		if ($wrap) echo '</p>';
        echo '</div>';
	}
}
