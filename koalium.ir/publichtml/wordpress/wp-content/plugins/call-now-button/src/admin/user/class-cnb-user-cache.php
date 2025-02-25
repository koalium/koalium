<?php

namespace cnb\admin\user;

use cnb\admin\models\CnbUser;

/**
 * This stores the current user data in a transient, so that we don't have to
 * make a call to the remote API every time we need the user data.
 */
class CnbUserCache {
	/**
	 * @var string The name of the transient that stores the CnbUser data.

	 */
	private $cache_name = 'cnb_user_cache';

	/**
	 * Stores the CnbUser to a local (transient) store, so that #get_subscription_data
	 * can retrieve it.
	 *
	 * @param $cnb_user_data CnbUser
	 *
	 * @return void
	 */
	public function save_user_data($cnb_user_data) {
		if ( $cnb_user_data && !is_wp_error($cnb_user_data)) {
			set_transient( $this->cache_name, $cnb_user_data, DAY_IN_SECONDS );
		}
	}

	/**
	 * Get the CnbUser without a call to the remote API, instead relying on the
	 * local transient store.
	 *
	 * If the cache is unavailable, this will return false.
	 *
	 * @return false|CnbUser
	 */
	public function get_user_data() {
		return get_transient($this->cache_name);
	}
}
