<?php

namespace cnb\admin\templates;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;

/**
 * Contains a collection of hardcoded templates to work with (for now).
 *
 * This can later be replaced by an API call or something.
 */
class Templates {

	private $transient_prefix = 'call-now-button_templates_';

	/**
	 *
	 * Get the transient name for the given endpoint, with a prefix specific to this plugin.
	 *
	 * @param $endpoint string
	 *
	 * @return string
	 */
	private function get_transient_name( $endpoint ) {
		return $this->transient_prefix . $endpoint;
	}

	/**
	 * @return Template[]
	 */
	function get_templates() {
		$endpoint = 'https://nowbuttons.com/src/preview/templates.json';

		// If cached, use that
		$cached = get_transient($this->get_transient_name($endpoint));
		if ($cached) return $cached;

		// Get the result and cache it
		$cnb_remote = new CnbAppRemote();
		$result = $cnb_remote->cnb_get($endpoint, false);
		if ($result && !is_wp_error($result)) {
			set_transient($this->get_transient_name($endpoint), $result, DAY_IN_SECONDS);
		}

		return $result;
	}
}
