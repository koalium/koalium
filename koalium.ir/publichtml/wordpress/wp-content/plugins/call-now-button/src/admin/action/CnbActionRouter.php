<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class CnbActionRouter {
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
                ( new CnbActionViewEdit() )->render();
                break;
            default:
                ( new CnbActionView() )->render();
                break;
        }
        do_action( 'cnb_finish' );
    }
}
