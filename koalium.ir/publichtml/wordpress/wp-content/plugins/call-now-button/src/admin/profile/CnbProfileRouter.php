<?php

namespace cnb\admin\profile;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

class CnbProfileRouter {
    public function render() {
        do_action( 'cnb_init', __METHOD__ );
        (new CnbProfileEdit())->render();
        do_action( 'cnb_finish' );
    }
}
