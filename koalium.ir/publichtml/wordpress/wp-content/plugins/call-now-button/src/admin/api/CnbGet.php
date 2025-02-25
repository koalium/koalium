<?php

namespace cnb\admin\api;

/**
 * Used only by CnbAppRemote
 * @private
 */
class CnbGet {
    protected $defaultExpiration = 300; // 5 * MINUTE_IN_SECONDS = 5 * 60 = 300
    protected $isCacheHit = false;
    private $useCache = false;
	private $transient_prefix = 'call-now-button_CnbGet_';

    public function __construct() {
        $cnb_options = get_option( 'cnb' );
        if ( $cnb_options['api_caching'] === 1 ) {
            $this->useCache = true;
        }
    }

    protected function add( $url, $response ) {
        set_transient( $this->transient_prefix . CnbAppRemote::cnb_get_transient_base() . $url, $response, $this->defaultExpiration );

        return $response;
    }

    public function isLastCallCached() {
        return $this->isCacheHit;
    }

    public function get( $url, $args ) {
        if ( $this->useCache ) {
            $cache = get_transient( $this->transient_prefix . CnbAppRemote::cnb_get_transient_base() . $url );
            if ( $cache ) {
                $this->isCacheHit = true;

                return $cache;
            }
        }
        $this->isCacheHit = false;
        $response         = wp_remote_get( $url, $args );

        return $this->add( $url, $response );
    }
}
