<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class CnbDomainRouter {
    /**
     * Decides to either render the overview or the edit view
     *
     * @return void
     */
    public function render() {
        do_action( 'cnb_init', __METHOD__ );
        $action = ( new CnbUtils() )->get_query_val( 'action', null );
        switch ( $action ) {
            case 'new':
            case 'edit':
                ( new CnbDomainViewEdit() )->render();
                break;
            case 'upgrade':
                ( new CnbDomainViewUpgrade() )->render();
                break;
	        case 'payment':
		        ( new PaymentView() )->render();
		        break;
            default:
                ( new CnbDomainView() )->render();
                break;
        }
        do_action( 'cnb_finish' );
    }
}
