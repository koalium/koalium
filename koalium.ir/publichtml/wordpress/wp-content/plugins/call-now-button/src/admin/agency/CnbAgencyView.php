<?php

namespace cnb\admin\agency;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\domain\CnbProAccountBlocks;

class CnbAgencyView {
    /**
     * Decides to either render the overview or the upgrade view
     *
     * @return void
     */
    public function render() {
        // TODO Find active currency (and actually use this function)
        $x = new CnbProAccountBlocks();
        $x->render_pro_account_block();
    }
}
