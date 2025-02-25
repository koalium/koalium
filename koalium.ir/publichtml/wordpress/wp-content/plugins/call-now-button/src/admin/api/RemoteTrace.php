<?php

namespace cnb\admin\api;

use cnb\utils\Cnb_Sentry;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class RemoteTrace {
    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string|null
     */
    protected $context;
    /**
     * @var \Sentry\Tracing\Span|null
     */
    protected $span;

    /**
     * @var float
     */
    protected $start;
    /**
     * @var float
     */
    protected $end;

    protected $cacheHit = false;


    public function __construct( $endpoint = null, $context = null ) {
        $cnb_remoted_traces = RemoteTracer::getInstance();

        $this->endpoint = $endpoint;
        $this->context = $context;

        $cnb_remoted_traces->addTrace( $this );
        $this->start();
    }

    /**
     * Optional, since a "start" is also calculated during Class creation.
     */
    public function start() {
        $this->start = microtime( true );
        $this->span = Cnb_Sentry::start_span($this->endpoint, $this->context);
    }

    public function end() {
        $this->end = microtime( true );
        Cnb_Sentry::finish_span($this->span);
    }

    /**
     * @return string
     */
    public function getEndpoint() {
        return $this->endpoint;
    }

    /**
     * @param $cacheHit boolean
     */
    public function setCacheHit( $cacheHit ) {
        // phpcs:ignore PHPCompatibility.FunctionUse
        $this->cacheHit = boolval( $cacheHit );
    }

    public function isCacheHit() {
        return $this->cacheHit;
    }

    /**
     * @return string A formatted version of number.
     */
    public function getTime( $precision = 4 ) {
        $diff = $this->end - $this->start;

        return number_format( $diff, $precision );
    }
}
