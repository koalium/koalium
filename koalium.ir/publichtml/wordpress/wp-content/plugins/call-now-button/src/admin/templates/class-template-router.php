<?php

namespace cnb\admin\templates;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class Template_Router {

	public function render() {
		do_action( 'cnb_init', __METHOD__ );
		$view = new Template_View();
		$view->render();
		do_action( 'cnb_finish' );
	}
}
