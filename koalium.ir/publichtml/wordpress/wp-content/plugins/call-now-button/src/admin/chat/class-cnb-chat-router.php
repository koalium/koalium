<?php

namespace cnb\admin\chat;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbChatRouter {
	/**
	 * Decides to either render the overview or the edit view
	 *
	 * @return void
	 */
	public function render() {
		do_action( 'cnb_init', __METHOD__ );
		$view = new CnbChatView();
		$view->render();
		do_action( 'cnb_finish' );
	}

	public function get_slug() {
		return CNB_SLUG . '-chat';
	}
}
