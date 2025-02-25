<?php

namespace cnb\admin\chat;

use cnb\admin\magictoken\CnbMagicTokenController;
use cnb\admin\models\CnbUser;
use cnb\admin\user\CnbUserCache;

class CnbChatController {
	public function has_chat_enabled() {
		$user_cache = new CnbUserCache();
		$cnb_user = $user_cache->get_user_data();
		if ( $cnb_user instanceof CnbUser && $cnb_user->has_role('ROLE_CHAT_USER') ) {
			return true;
		}
		return false;
	}

	public function create_chat_token_ajax() {
		do_action( 'cnb_init', __METHOD__ );

		$chat_api = new CnbMagicTokenController();
		$token = $chat_api->create_chat_token();

		wp_send_json( array(
			'status'  => 'success',
			'token' => $token,
		) );

		do_action( 'cnb_finish' );

	}
}
