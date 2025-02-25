<?php

namespace cnb\admin\agency;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class CnbAgencyRouter {
    /**
     * Decides to either render the overview or the upgrade view
     *
     * @return void
     */
    public function render() {
        do_action( 'cnb_init', __METHOD__ );
        $action = ( new CnbUtils() )->get_query_val( 'action', null );
        switch ( $action ) {
            case 'upgrade':
                ( new CnbAgencyViewUpgrade() )->render();
                break;
            default:
                ( new CnbAgencyView() )->render();
                break;
        }
        do_action( 'cnb_finish' );
    }
}
