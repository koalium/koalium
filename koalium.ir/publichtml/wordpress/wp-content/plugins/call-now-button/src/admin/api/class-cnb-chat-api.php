<?php

namespace cnb\admin\api;

use cnb\admin\magictoken\CnbMagicToken;

class CnbChatApi {
	public function get_magic_token() {
		$rest_endpoint = '/v1/magic-token';
		$body = array(
			'email' => true,
			'target' => 'CHAT_APP',
		);

		$response = CnbAppRemote::cnb_remote_post($rest_endpoint, $body);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Parse into MagicToken
		return CnbMagicToken::fromObject( $response );
	}
}
