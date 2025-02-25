<?php

namespace cnb\admin\magictoken;

use cnb\utils\CnbUtils;
use DateTime;
use JsonSerializable;
use stdClass;
use WP_Error;

class CnbMagicToken implements JsonSerializable {
	/**
	 * @var string UUID of the User
	 */
	public $id;

	/**
	 * @var DateTime|null Date this token was created
	 */
	public $createdDate;

	/**
	 * @var boolean If this token has been consumed. If so, see usedDate for when
	 */
	public $used;

	/**
	 * @var DateTime|null Date this token was consumed
	 */
	public $usedDate;

	/**
	 * @param $object stdClass|array|WP_Error|null
	 *
	 * @return CnbMagicToken|WP_Error
	 */
	public static function fromObject( $object ) {
		if ( is_wp_error( $object ) ) {
			return $object;
		}
		$token = new CnbMagicToken();
		$token->id = CnbUtils::getPropertyOrNull( $object, 'id' );
		$createdDate = CnbUtils::getPropertyOrNull( $object, 'createdDate' );
		if ( $createdDate !== null ) {
			$token->createdDate = new DateTime( $createdDate );
		}
		$token->used = CnbUtils::getPropertyOrNull( $object, 'used' );
		$usedDate = CnbUtils::getPropertyOrNull( $object, 'usedDate' );
		if ( $usedDate !== null ) {
			$token->usedDate = new DateTime( $usedDate );
		}

		return $token;
	}

	public function toArray() {
		return array(
			'id' => $this->id,
			'createdDate' => $this->createdDate ? $this->createdDate->format(DateTime::ATOM) : null,
			'used' => $this->used,
			'usedDate' => $this->usedDate ? $this->usedDate->format(DateTime::ATOM) : null,
		);
	}

	/** @noinspection PhpLanguageLevelInspection */
	#[\ReturnTypeWillChange]
	public function jsonSerialize() {
		return $this->toArray();
	}
}
