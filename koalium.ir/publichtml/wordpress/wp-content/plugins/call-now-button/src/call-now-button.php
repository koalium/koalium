<?php

// don't load directly
use cnb\CallNowButton;
use cnb\cron\Cron;

defined( 'ABSPATH' ) || die( '-1' );

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/utils/cnb-backwards-compatible.php';

// Only include the WP_CLI suite when it is available
if ( class_exists( 'WP_CLI' ) && class_exists( 'WP_CLI_Command' ) ) {
	require_once __DIR__ . '/cli/CNB_CLI.php';
}

function cnb_add_actions() {
	$call_now_button = new CallNowButton();
	add_action( 'plugins_loaded', array( $call_now_button, 'register_global_actions' ) );
	add_action( 'plugins_loaded', array( $call_now_button, 'register_header_and_footer' ) );
	add_action( 'plugins_loaded', array( $call_now_button, 'register_admin_post_actions' ) );
	add_action( 'plugins_loaded', array( $call_now_button, 'register_ajax_actions' ) );
	add_action( 'plugins_loaded', array( $call_now_button, 'register_cron' ) );

	// Ensure we are excluded from certain Caching plugins
	add_action( 'plugins_loaded', array( 'cnb\cache\CacheHandler', 'exclude' ) );

	// This queues the front-end to be rendered (`wp_loaded` should only fire on the front-end facing site)
	add_action( 'wp_loaded', array( 'cnb\renderer\RendererFactory', 'register' ) );

	$cnb_cron = new Cron();
	add_action( $cnb_cron->get_hook_name(), array( $cnb_cron, 'do_hook' ) );
}

cnb_add_actions();
