<?php

namespace cnb\admin\templates;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class Template_Controller {

	public function get_slug() {
		return CNB_SLUG . '-templates';
	}
}
