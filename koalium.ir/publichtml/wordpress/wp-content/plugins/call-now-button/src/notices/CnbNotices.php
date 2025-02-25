<?php

namespace cnb\notices;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbNotices {
    // PHP 5.4 does not support arrays as const, wait for PHP 5.6 of 7 for this
    const TYPES = 'error,warning,info,success,blackfriday';

    /**
     * @var CnbNotice[]
     */
    public $error = array();
    /**
     * @var CnbNotice[]
     */
    public $warning = array();
    /**
     * @var CnbNotice[]
     */
    public $info = array();
    /**
     * @var CnbNotice[]
     */
    public $success = array();/**
     * @var CnbNotice[]
     */
    public $blackfriday = array();
}
