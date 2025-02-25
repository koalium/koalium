<?php

namespace cnb\admin\domain;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use cnb\admin\api\CnbAppRemote;
use cnb\utils\CnbUtils;
use WP_Error;
use WP_List_Table;

class Cnb_Domain_List_Table extends WP_List_Table {

    /**
     * @var CnbUtils
     */
    private $cnb_utils;

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct() {
        parent::__construct( array(
            'singular' => 'cnb_list_domain', //Singular label
            'plural'   => 'cnb_list_domains', //plural label, also this well be one of the table css class
            'ajax'     => false, //We won't support Ajax for this table
            'screen'   => 'call-now-button-domains', // Screen name for bulk actions, etc
        ) );

        $this->cnb_utils = new CnbUtils();
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        return array(
            'cb'              => '<input type="checkbox">',
            'id'              => __( 'ID' ),
            'name'            => __( 'Name' ),
            'type'            => __( 'Type' ),
            'expires'         => __( 'Expires' ),
            'renew'           => __( 'Renew automatically' ),
            'timezone'        => __( 'Timezone' ),
            'trackGA'         => __( 'Google Analytics' ),
            'trackConversion' => __( 'Google Ads' ),
        );
    }

    function get_sortable_columns() {
        return array(
            'name' => array( 'name', false ),
            'type' => array( 'type', false ),
        );
    }

    function get_hidden_columns() {
        return array( 'id' );
    }

    function prepare_items() {
        /* -- Preparing your query -- */
        $data = $this->get_data();

        if ( $data instanceof WP_Error ) {
            return $data;
        }

        /* -- Ordering parameters -- */
        //Parameters that are going to be used to order the result
        usort( $data, array( &$this, 'sort_data' ) );

        /* -- Pagination parameters -- */
        //Number of elements in your table?
        $totalitems = count( $data ); //return the total number of affected rows
        $per_page   = 20; //How many to display per page?
        //Which page is this?
        $current_page = (int) $this->cnb_utils->get_query_val( 'paged', '1' );

        //Page Number
        if ( empty( $current_page ) || ! is_numeric( $current_page ) || $current_page <= 0 ) {
            $current_page = 1;
        }

        //How many pages do we have in total?
        $totalpages = ceil( $totalitems / $per_page ); //adjust the query to take pagination into account
        if ( ! empty( $current_page ) && ! empty( $per_page ) ) {
            $offset = ( $current_page - 1 ) * $per_page;

            /* -- Register the pagination -- */
            $this->set_pagination_args( array(
                'total_items' => $totalitems,
                'total_pages' => $totalpages,
                'per_page'    => $per_page,
            ) );
            //The pagination links are automatically built according to those parameters

            /* -- Register the Columns -- */
            $columns               = $this->get_columns();
            $hidden_columns        = $this->get_hidden_columns();
            $sortable_columns      = $this->get_sortable_columns();
            $this->_column_headers = array( $columns, $hidden_columns, $sortable_columns, 'name' );

            /* -- Register the items -- */
            $data        = array_slice( $data, $offset, $per_page );
            $this->items = $data;
        }

        return null;
    }

    /**
     * @param $item CnbDomain
     * @param $column_name string
     *
     * @return string
     */
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
                return '<code>' . esc_html( $item->id ) . '</code>';
            case 'type':
                switch ( $item->type ) {
                    case 'FREE':
                        return 'Free';
                    case 'PRO':
                        return 'Pro';
                    case 'STARTER':
                        return 'Starter';
                    default:
                        return esc_html( $item->type );
                }
            case 'renew':
            case 'trackGA':
            case 'trackConversion':
                return $item->$column_name ? __( 'Enabled' ) : __( 'Disabled' );
            case 'timezone':
            case 'expires':
                return esc_html( $item->$column_name );

            default:
                return '<em>Unknown column ' . esc_html( $column_name ) . '</em>';
        }
    }

    private function get_data() {
        global $cnb_domains;
        return $cnb_domains;
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @param $a CnbDomain
     * @param $b CnbDomain
     *
     * @return int
     */
    private function sort_data( $a, $b ) {
        // If orderby is set, use this as the sort column
        $orderby = $this->cnb_utils->get_query_val( 'orderby', 'name' );
        // If order is set use this as the order
        $order = $this->cnb_utils->get_query_val( 'order', 'asc' );

        $result = strcmp( $a->$orderby, $b->$orderby );

        if ( $order === 'asc' ) {
            return $result;
        }

        return - $result;
    }

    /**
     * Custom action for `cb` columns (checkboxes)
     *
     * @param $item CnbDomain
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            esc_attr( $item->id )
        );
    }

    /**
     * @param $item CnbDomain
     *
     * @return string
     */
    function column_name( $item ) {
        $actions = array();
        // Let's build a link
        $url             = admin_url( 'admin.php' );
        $edit_link       =
            add_query_arg(
                array( 'page' => 'call-now-button-domains', 'action' => 'edit', 'id' => $item->id ),
                $url );
        $edit_url        = esc_url( $edit_link );
        $actions['edit'] = '<a href="' . $edit_url . '">' . __( 'Edit' ) . '</a>';

        $delete_link       = wp_nonce_url(
            add_query_arg( array(
                'page'   => 'call-now-button-domains',
                'action' => 'cnb_delete_domain',
                'id'     => $item->id,
            ), admin_url( 'admin-post.php' ) ),
            'cnb_delete_domain' );
        $delete_url        = esc_url( $delete_link );
        $actions['delete'] = '<a href="' . $delete_url . '">' . __( 'Delete' ) . '</a>';

        // If the type is not PRO, offer an upgrade
        if ( $item->type !== 'PRO' ) {
            $upgrade_link       =
                add_query_arg( array(
                    'page'   => 'call-now-button-domains',
                    'action' => 'upgrade',
                    'id'     => $item->id,
                ),
                    $url );
            $upgrade_url        = esc_url( $upgrade_link );
            $actions['upgrade'] = '<a href="' . $upgrade_url . '" style="color: orange">Upgrade!</a>';
        }

        return sprintf(
            '%1$s %2$s',
            '<strong><a class="row-title" href="' . $edit_url . '">' . esc_html( $item->name ) . '</a></strong>',
            $this->row_actions( $actions )
        );
    }

    function get_bulk_actions() {
        return array(
            'delete' => __( 'Delete' ),
        );
    }

    function no_items() {
        esc_html_e( 'No domains found.' );
    }
}
