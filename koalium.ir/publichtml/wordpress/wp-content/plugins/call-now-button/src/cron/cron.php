<?php

namespace cnb\cron;

use cnb\admin\api\CnbAppRemote;
use WP_Error;

/**
 * Runs scheduled tasks needed for the operation of the CallNowButton.
 *
 * @since 1.3.8
 */
class Cron {

	/**
	 * This is the hook (action) name, as well as the transient name.
	 *
	 * This is also (hardcoded) in uninstall.php, so if this is change,
	 * be sure to change it there too!
	 *
	 * @var string
	 */
	private $hook_name = 'cnb_wp_info';

	/**
	 * Get the hook (action) / transient name
	 *
	 * @return string
	 */
	public function get_hook_name() {
		return $this->hook_name;
	}

	/**
	 * This pulls the information from the remote API and store it in a transient.
	 * All the "heavy lifting" is done by <code>CnbAppRemote</code> (the pulling *and* storing).
	 *
	 * @return void
	 */
	public function do_hook() {
		// Call the wpinfo hook
		$remote = new CnbAppRemote();
		$remote->get_wp_info();
	}

	/**
	 * Register the hook to be executed every 12 hours.
	 *
	 * @return bool|WP_Error True if successful (or already scheduled) or a WP_Error on error
	 */
	public function register_hook() {
		$result = true;
		$next = wp_next_scheduled( $this->hook_name );

		if ( ! $next ) {
			$result = wp_schedule_event( time(), 'twicedaily', $this->hook_name, array(), true );
		}

		return $result;
	}

	/**
	 * Remove the hook from WordPress.
	 * (Should be) Called on deactivation and uninstallation.
	 *
	 * @return false|int|WP_Error
	 */
	public function unregister_hook() {
		return wp_clear_scheduled_hook( $this->hook_name );
	}
}
