<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\utils\CnbUtils;

class CnbDomainView {

    private $cnb_utils;

    public function __construct() {
        $this->cnb_utils = new CnbUtils();
    }

    function header() {
        echo 'Domains ';
    }

    /**
     * Only add the "Add new" action in the overview part
     *
     * @return void
     */
    function add_modal() {
        $id     = $this->cnb_utils->get_query_val( 'id', null );
        $action = $this->cnb_utils->get_query_val( 'action', null );
        if ( $id === null || ( $action != 'new' && $action != 'edit' ) ) {
            // Create link
            $url      = admin_url( 'admin.php' );
            $new_link =
                add_query_arg(
                    array(
                        'page'   => 'call-now-button-domains',
                        'action' => 'new',
                        'id'     => 'new',
                    ),
                    $url );

            echo '<a href="' . esc_url( $new_link ) . '" class="page-title-action">Add New</a>';
        }
    }

    function render() {
        $wp_list_table = new Cnb_Domain_List_Table();
        $data          = $wp_list_table->prepare_items();

        add_action( 'cnb_header_name', array( $this, 'header' ) );

        if ( ! is_wp_error( $data ) ) {
            add_action( 'cnb_after_header', array( $this, 'add_modal' ) );
        }

        wp_enqueue_script( CNB_SLUG . '-form-bulk-rewrite' );

        do_action( 'cnb_header' );

        printf( '<form class="cnb_list_event" action="%s" method="post">', esc_url( admin_url( 'admin-post.php' ) ) );
        echo '<input type="hidden" name="page" value="call-now-button-domains" />';
        echo '<input type="hidden" name="action" value="cnb_domains_bulk" />';
        $wp_list_table->display();
        echo '</form>';
        do_action( 'cnb_footer' );
    }
}
