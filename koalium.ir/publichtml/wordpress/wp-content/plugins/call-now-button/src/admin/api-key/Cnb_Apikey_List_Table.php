<?php

namespace cnb\admin\apikey;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use cnb\admin\api\CnbAppRemote;
use cnb\utils\CnbUtils;
use WP_Error;
use WP_List_Table;

class Cnb_Apikey_List_Table extends WP_List_Table {

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
            'singular' => 'cnb_list_apikey', //Singular label
            'plural'   => 'cnb_list_apikeys', //plural label, also this well be one of the table css class
            'ajax'     => false, //We don't support Ajax for this table
            'screen'   => 'call-now-button-apikeys', // Screen name for bulk actions, etc
        ) );

        $this->cnb_utils = new CnbUtils();
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        return array(
            'cb'         => '<input type="checkbox">',
            'id'         => __( 'ID' ),
            'name'       => __( 'Name' ),
            'created'    => __( 'Created' ),
            'lastUsed'   => __( 'Last used' ),
            'updateTime' => __( 'Last updated' ),
        );
    }

    function get_sortable_columns() {
        return array(
            'name'       => array( 'name', false ),
            'created'    => array( 'created', false ),
            'lastUsed'   => array( 'lastUsed', false ),
            'updateTime' => array( 'updateTime', false ),
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
     * @param CnbApiKey $item
     * @param $column_name
     *
     * @return string
     */
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
                return '<code>' . esc_html( $item->id ) . '</code>';
            case 'name':
                return '<strong>' . esc_html( $item->name ) . '</strong>';
            case 'created':
            case 'lastUsed':
            case 'updateTime':
                return esc_html( ( new CnbUtils() )->cnb_timestamp_to_string( $item->$column_name ) );
            default:
                return '<em>Unknown column ' . esc_html( $column_name ) . '</em>';
        }
    }

    private function get_data() {
        $cnb_remote = new CnbAppRemote();
        return $cnb_remote->get_apikeys();
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @param CnbApiKey $a
     * @param CnbApiKey $b
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
     * @param CnbApiKey $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            esc_html( $item->id )
        );
    }

    function get_bulk_actions() {
        return array(
            'delete' => __( 'Delete' ),
        );
    }

    function no_items() {
        esc_html_e( 'No API keys found.' );
    }
}
