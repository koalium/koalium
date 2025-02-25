<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\notices\CnbAdminNotices;

class CnbDomainViewUpgradeInProgress {

    /**
     * @param $domain CnbDomain
     *
     * @return void
     */
    function render( $domain ) {
        $message = '<p>We are processing the upgrade for <strong>' . esc_html( $domain->name ) . '</strong>, please hold on.</p>';
        $message .= '<p>This page will refresh in 2 seconds...</p>';
        CnbAdminNotices::get_instance()->renderWarning( $message );
        echo '<script>setTimeout(window.location.reload.bind(window.location), 2000);</script>';
    }
}
