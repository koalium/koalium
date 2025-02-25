<?php

namespace cnb\notices;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

/**
 * @var $type string one of info, success, warning, error
 * @var $dismissable boolean false by default
 */
class CnbNotice {
    /**
     * @var string|null info, success, warning, error
     */
    public $type;
    /**
     * @var string
     */
    public $message;
    /**
     * @var boolean
     */
    public $dismissable;
    /**
     * @var string
     */
    public $dismiss_option;
	/**
	 * @var string
	 * @since  v1.2.1
	 */
	public $name;

	/**
	 * @param string $type one of info, success, warning, error
	 * @param string $message
	 * @param boolean $dismissable
	 * @param boolean $dismiss_option
	 * @param string $name
	 */
    public function __construct( $type = null, $message = null, $dismissable = false, $dismiss_option = false, $name = null ) {
        $this->type           = $type;
        $this->message        = $message;
        $this->dismissable    = $dismissable;
        $this->dismiss_option = $dismiss_option;
		$this->name           = $name;
    }
}
