<?php

namespace cnb\admin\magictoken;

use cnb\admin\api\CnbChatApi;

class CnbMagicTokenController {
	public function create_chat_token() {
		$chat_api = new CnbChatApi();
		return $chat_api->get_magic_token();
	}
}
