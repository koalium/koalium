<?php
namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbActionType {

	/**
	 * @param string $type
	 * @param string $name
	 * @param string[] $plans
	 */
	public function __construct( $type, $name, $plans ) {
		$this->type  = $type;
		$this->name  = $name;
		$this->plans = $plans;
	}

	/**
	 * PHONE, EMAIL, etc
	 * @var string
	 */
	public $type;

	/**
	 * Display name, such as "💬 Phone", "🔗 Link", etc
	 *
	 * @var string
	 */
	public $name;

	/**
	 * Domain types where this ActionType is available
	 * @var string[]
	 */
	public $plans;
}
