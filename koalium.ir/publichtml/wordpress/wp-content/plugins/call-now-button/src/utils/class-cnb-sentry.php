<?php

namespace cnb\utils;

use cnb\admin\api\RemoteTrace;
use cnb\CnbFooter;
use Error;
use Sentry\SentrySdk;
use Sentry\Tracing\TransactionContext;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

/**
 * This sets up the Sentry integration for CallNowButton only.
 *
 * See https://packagist.org/packages/sentry/sentry
 */
class Cnb_Sentry {

    /**
     * DSN used by CallNowButton to collect WordPress statistics
     *
     * @var string
     */
    private $dsn = 'https://272863c3742949d9a7f828ddb221bc01@o432725.ingest.sentry.io/6507738';

    private $trace;
    /**
     * Initialize Sentry IF:
     * - PHP > 7.2 (required by Sentry)
     * - Nobody else integrated Sentry themselves (checked via the existence of a class called Sentry\SentrySdk)
     * - The user gave their permission to collect errors and metrics
     *
     * @param $op_name string name of operation, usually __METHOD__
     *
     * @return void
     */
    function init($op_name = 'sender') {
        try {
            global $cnb_sentry_init;
            if ( $cnb_sentry_init ) {
                return;
            }

            $cnb_options = get_option( 'cnb' );
            // Only do with permission AND PHP > 7 (since that's what Sentry (/composer) requires)
            // Verify this number is the same as used in src/vendor/composer/platform_check.php
            if ( version_compare( PHP_VERSION, '7.2.5', '>=' )
                && ! class_exists( 'Sentry\SentrySdk' )
                && key_exists( 'error_reporting', $cnb_options )
                && $cnb_options['error_reporting'] ) {
                $this->init_real( $op_name );
            }
        } catch (Error $e) {
            // Do not interrupt or break plugin functionality if Sentry for whatever reason does not load
            $cnb_sentry_init = true;
        }
    }

    private function init_real($op_name) {
        global $cnb_sentry_init;
        if ( $cnb_sentry_init ) {
            return;
        }
        require_once __DIR__ . '/../vendor/autoload.php';
        \Sentry\init(
            array(
                'dsn'                => $this->dsn,
                'release'            => CNB_VERSION,
                'environment'        => WP_DEBUG ? 'development' : 'production',
            ) );

        self::setup_global_transaction($op_name);
        $cnb_sentry_init = true;
    }

    private function set_scope() {
        \Sentry\configureScope( function ( $scope ) {
            global $wp_version;

            $scope->setContext(
                'WordPress', array(
                'version' => $wp_version,
            ));
        } );
    }

    /**
     * Finish the transaction, this submits the transaction and its span to Sentry
     *
     * @return void
     */
    public function finish() {
        global $cnb_transaction;
        if ( !$cnb_transaction ) {
            return;
        }
        $this->trace = new RemoteTrace($this->get_endpoint(), __METHOD__);
        $this->set_scope();
        $cnb_transaction->finish();
        $this->print_trace_for_footer();

    }

    private function get_endpoint() {
        $dsn = \Sentry\Dsn::createFromString($this->dsn);
        return $dsn->getScheme() . '://' . $dsn->getHost();
    }

    private function print_trace_for_footer() {
        $this->trace->end();
        $footer = new CnbFooter();
        if ($footer->is_show_traces()) {
            $footer->print_traces( array( $this->trace ) );
        }
    }

    private function setup_global_transaction($op_name) {
        global $cnb_transaction;
        if ($cnb_transaction !== null) {
            // Already setup!
            return;
        }

        try {
            // Setup context for the full transaction
            $transactionContext = new TransactionContext();

            $transactionContext->setName( 'cnb_wp_plugin' );
            $transactionContext->setOp( $op_name );

            // See if we can split $op_name by :: once
            $parts = explode('::', $op_name, 2);
            if (count($parts) === 2) {
                list($name, $op) = $parts;
                $transactionContext->setName( $name );
                $transactionContext->setOp( $op );
            }

            // Start the transaction
            $cnb_transaction = \Sentry\startTransaction( $transactionContext );

            // Set the current transaction as the current span so we can retrieve it later
            SentrySdk::getCurrentHub()->setSpan( $cnb_transaction );
        } catch ( Error $exception ) {
            \Sentry\captureException($exception);
        }
    }

    /**
     * @return \Sentry\Tracing\Span
     */
    public static function start_span($name, $context) {
        global $cnb_transaction;
        if (!$cnb_transaction) {
            return null;
        }

        try {
            // Setup the context for the expensive operation span
            $spanContext = new \Sentry\Tracing\SpanContext();
            $spanContext->setOp( $context );

            // Start the span
            $span = $cnb_transaction->startChild( $spanContext );
            $span->setDescription( $name );

            // Set the current span to the span we just started
            //SentrySdk::getCurrentHub()->setSpan( $span );

            return $span;
        } catch ( Error $exception ) {
            try {
                \Sentry\captureException($exception);
            } catch ( Error $exception2 ) {
                // NOOP
            }

        }
        return null;
    }

    public static function finish_span($span) {
        global $cnb_transaction;
        if (!$cnb_transaction || !$span) {
            return;
        }

        // Finish the span
        $span->finish();
    }
}
