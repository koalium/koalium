<?php

namespace cnb\admin\models;

use cnb\admin\button\CnbButton;
use cnb\admin\domain\CnbDomain;
use cnb\notices\CnbNotice;
use WP_Error;

class CnbActivation {


    /**
     * If the API key has been activated in this particular call
     * @var boolean
     */
    public $activation_attempt;
	/**
	 * The OTT key
	 *
	 * @var string
	 */
	public $ott_key;
	/**
	 * The created API key (either direct or via an OTT key)
	 * @var string
	 */
	public $api_key;
    /**
     * If the activated was actually succesfull
     * @var boolean
     */
    public $success;
    /**
     * @var CnbNotice[]
     */
    public $notices = array();
    /**
     * The domain created / used
     * @var CnbDomain|WP_Error
     */
    public $domain;
    /**
     * Indicate if the domain has been created or updated
     * @var string
     */
    public $domain_action;
    /**
     * The migrated button that is created at the API server
     * @var CnbButton|WP_Error
     */
    public $button;

    /**
     * @param bool $activation_attempt
     * @param bool $success
     * @param CnbDomain $domain
     * @param CnbButton $button
     */
    public function __construct( $activation_attempt = false, $success = false, CnbDomain $domain = null, CnbButton $button = null ) {
        $this->activation_attempt = $activation_attempt;
        $this->success            = $success;
        $this->domain             = $domain;
        $this->button             = $button;
    }
}
