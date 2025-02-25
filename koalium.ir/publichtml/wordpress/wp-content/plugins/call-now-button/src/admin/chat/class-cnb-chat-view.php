<?php

namespace cnb\admin\chat;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\models\CnbUser;

class CnbChatView {

	public function render() {
		add_filter('cnb_header_wrapper_classes', function($classes) {
			return array_diff($classes, array( 'wrap' ));
		});

		do_action( 'cnb_header' );

		wp_enqueue_script( CNB_SLUG . '-chat' );

		$this->iframe_content();

		// Hides the "Thank you for creating with..." in the admin footer
		add_filter('admin_footer_text', '__return_empty_string');
		// Hides the "Version x.x" in the admin footer
		add_filter('update_footer', '__return_empty_string');

		// Hides the entire NowButtons footer
		add_filter('cnb_show_footer', '__return_false');

		do_action( 'cnb_footer' );
	}

	function iframe_content() {
		/** @type CnbUser $cnb_user */
		global $cnb_user;
		$app_remote = new CnbAppRemote();
		$chat_url = $app_remote->get_chat_url() . '/auth/login';
		echo "<iframe class='cnb-chat-window' src='" . esc_url( add_query_arg( array( 'env_from' => 'WordPress', 'login_hint' => rawurlencode( $cnb_user->email ) ), $chat_url ) ) . "'></iframe>";
	}
}
