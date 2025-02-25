<?php

namespace cnb\admin\apikey;

// don't load directly
use cnb\admin\settings\CnbApiKeyActivatedView;
use cnb\utils\CnbUtils;

defined( 'ABSPATH' ) || die( '-1' );

/**
 * Somewhat simple router, here to ensure we stay in line with the rest.
 *
 * Since APIKey creation is handled via admin-post, the only thing we do here is render the overview.
 */
class CnbApiKeyRouter {
    public function render() {
        do_action( 'cnb_init', __METHOD__ );
	    $page = ( new CnbUtils() )->get_query_val( 'page', null );
		switch ( $page ) {
			case 'call-now-button-activated':
				( new CnbApiKeyActivatedView() )->render();
				break;
			case 'call-now-button-apikeys':
			default:
				( new CnbApiKeyView() )->render();
				break;
		}

        do_action( 'cnb_finish' );
    }
}
