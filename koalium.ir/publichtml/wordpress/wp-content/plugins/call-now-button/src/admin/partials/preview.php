<?php

namespace cnb\admin\partials;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use stdClass;

class Preview {
	function register_preview_data() {
		global $cnb_user;
		wp_localize_script(
			CNB_SLUG . '-preview',
			'cnb_preview_data',
			array(
				'apiRoot' => CnbAppRemote::cnb_get_api_base(),
				'cssLocation' => CnbAppRemote::cnb_get_static_base(),
				'options' => new stdClass(),
				'user' => $cnb_user,
) );
	}
}
