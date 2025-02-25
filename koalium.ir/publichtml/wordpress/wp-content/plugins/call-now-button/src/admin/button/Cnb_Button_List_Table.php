<?php

namespace cnb\admin\button;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use cnb\admin\action\CnbAction;
use cnb\admin\api\CnbAdminCloud;
use cnb\admin\models\ValidationMessage;
use cnb\admin\settings\CnbSettingsController;
use cnb\utils\CnbAdminFunctions;
use cnb\utils\CnbUtils;
use WP_Error;
use WP_List_Table;

class Cnb_Button_List_Table extends WP_List_Table {

    /**
     * Used as a local caching variable to avoid multiple calls to the external datasource (i.e. API calls)
     *
     * @var CnbButton[]
     */
    private $data;

    /**
     * @var CnbUtils
     */
    private $cnb_utils;

    private $options = array(
        'filter_buttons_for_domain' => null,
    );

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     */
    function __construct() {
        parent::__construct( array(
            'singular' => 'cnb_list_button', //Singular label
            'plural'   => 'cnb_list_buttons', //plural label, also this well be one of the table css class
            'ajax'     => false, //We won't support Ajax for this table
            'screen'   => 'call-now-button-buttons', // Screen name for bulk actions, etc
        ) );

        $this->cnb_utils = new CnbUtils();
    }

    function setOption( $optionName, $optionValue ) {
        $this->options[ $optionName ] = $optionValue;
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        return array(
            'cb'          => '<input type="checkbox">',
            'id'          => __( 'ID' ),
            'name'        => __( 'Name' ),
            'type'        => __( 'Type' ),
            'actions'     => __( 'Actions' ),
            'displaymode' => __( 'Display' ),
        );
    }

    function get_sortable_columns() {
        return array(
            'name'  => array( 'name', false ),
            'type'  => array( 'type', false ),
            'title' => array( 'title', false ),
        );
    }

    function get_hidden_columns() {
        return array( 'id' );
    }

    /**
     * "views" is the top selector of a List Table (all, active, pending) etc
     *
     * @return string[]
     */
    function get_views() {
        // Let's count
        $data = $this->get_data();

        // In case of error (CNB not setup yet), return an empty view
        if ( $data instanceof WP_Error ) {
            return array();
        }
        $all_count        = count( $data );
        $all_count_str    = '<span class="count">(' . $all_count . ')</span>';
        $active_count     = count( array_filter( $data, function ( $el ) {
            return $el->active === true;
        } ) );
        $active_count_str = '<span class="count">(' . $active_count . ')</span>';

        // Let's build a link
        $url = admin_url( 'admin.php' );

        // Which one is current?
        $current_view_is_active = $this->cnb_utils->get_query_val( 'view' ) === 'active';
        $all_link               =
            add_query_arg(
                array( 'page' => 'call-now-button' ),
                $url );

        $active_link =
            add_query_arg(
                array( 'page' => 'call-now-button', 'view' => 'active' ),
                $url );

        return array(
            'all'    => "<a href='" . esc_url( $all_link ) . "' " . ( ! $current_view_is_active ? "class='current'" : '' ) . "'>" . __( 'All' ) . $all_count_str . '</a>',
            'active' => "<a href='" . esc_url( $active_link ) . "' " . ( $current_view_is_active ? "class='current'" : '' ) . "'>" . __( 'Active' ) . $active_count_str . '</a>',
        );
    }

    function prepare_items() {
        /* -- Preparing your query -- */
        $data = $this->get_data();

        if ( $data instanceof WP_Error ) {
            return $data;
        }

        /* -- Filtering parameters -- */
        $current_view_is_active = $this->cnb_utils->get_query_val( 'view' ) === 'active';
        if ( $current_view_is_active ) {
            $data = array_filter( $data, function ( $el ) {
                return $el->active === true;
            } );
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
     * @param $item CnbButton
     * @param $column_name string
     *
     * @return string|void
     */
    function column_default( $item, $column_name ) {
	    $adminFunctions = new CnbAdminFunctions();
        switch ( $column_name ) {
            case 'id':
                return '<code>' . esc_html( $item->id ) . '</code>';
            case 'name':
            case 'type':
                switch ( $item->type ) {
                    case 'SINGLE':
                    case 'FULL':
	                case 'MULTI':
	                case 'DOTS':
                        $button_types   = $adminFunctions->cnb_get_button_types();

	                    $flower = !empty( $item->options->cssClasses ) && str_contains($item->options->cssClasses, 'cnb-multi-flower');
                        return $button_types[ $item->type ] . ($flower ? ' (flower)' : '');
                    default:
                        return esc_html( $item->type );
                }
            case 'displaymode':
				$display_modes = $adminFunctions->get_display_modes();
                switch ( $item->options->displayMode ) {
                    case 'MOBILE_ONLY':
	                case 'DESKTOP_ONLY':
	                case 'ALWAYS':
                        return $display_modes[ $item->options->displayMode];
                    default:
                        return $display_modes['ALWAYS'];
                }
            default:
                return '<em>Unknown column ' . esc_html( $column_name ) . '</em>';
        }
    }

    /**
     * @param $item CnbButton
     *
     * @return string
     * @noinspection PhpUnused
     */
    function column_actions( $item ) {
        $items     = '';
        $domain    = '';
        $actionMsg = '';
        $count     = 0;

        // Action info
        if ( $item->actions ) {
            $count = count( $item->actions );
        }

        if ( $count === 0 ) {
            $items .= '<em>No action yet</em>';
        }

        // Action detail
        $actions = CnbAdminCloud::cnb_wp_get_actions_for_button( $item );
        foreach ( $actions as $action ) {
            $actionTypes = ( new CnbAdminFunctions() )->cnb_get_action_types();
			// In case the action type is not found, display an error message
            if ( !array_key_exists($action->actionType, $actionTypes) ) {
	            $actionMsg   .= '<em> ' . esc_html($action->actionType) . ' is invalid</em><br />';
				continue;
            }
            $actionType  = $actionTypes[ $action->actionType ];
            $actionMsg   .= esc_html($actionType->name) . $this->get_action_value( $action ) . '<br />';
        }
        $diff = $count - count( $actions );
        if ( $diff > 0 ) {
            $actionMsg .= "<em>Plus $diff more...</em><br />";
        }

        // Domain info
        if ( CnbSettingsController::is_advanced_view() ) {
            $domain = '<br />Domain: <code>' . esc_html( $item->domain->name ) . '</code>';
        }

        return "$items$actionMsg$domain";
    }

	/**
	 * @param $action CnbAction
	 *
	 * @return string
	 */
	private function get_action_value( $action ) {
		if ($action->actionType === 'CHAT') return '';

		$actionValue = ! empty( $action->actionValue ) ? esc_html( $action->actionValue ) : '<em>No value</em>';
		return ' (' . $actionValue . ')';
	}

    /**
     * @return CnbButton[]|WP_Error
     */
    private function get_data() {
        global $cnb_buttons;
        if ( is_array( $this->data ) ) {
            return $this->data;
        }
        $buttons = $cnb_buttons;

        if ( $buttons instanceof WP_Error ) {
            $this->data = $buttons;

            return $buttons;
        }
        if ( $buttons === null ) {
            $buttons = array();
        }

        // Filter for current or all domains
        $filterOnDomainId = $this->options['filter_buttons_for_domain'];
        if ( $filterOnDomainId ) {
            $buttons = array_filter( $buttons, function ( $el ) use ( $filterOnDomainId ) {
                return $el->domain->id === $filterOnDomainId;
            } );
        }

        $this->data = $buttons;

        return $buttons;
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @param $a CnbButton
     * @param $b CnbButton
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
     * @param CnbButton $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="%1$s[]" value="%2$s" />',
            $this->_args['singular'],
            $item->id
        );
    }

    /**
     * @param $item CnbButton
     *
     * @return string
     */
    function column_name( $item ) {
        // Let's build a link
        $url       = admin_url( 'admin.php' );
        $edit_link =
            add_query_arg(
                array(
                    'page'   => 'call-now-button',
                    'action' => 'edit',
                    'type'   => strtolower( $item->type ),
                    'id'     => $item->id,
                ),
                $url );
        $edit_url  = esc_url( $edit_link );

        $actions             = array(
            'edit' => '<a href="' . $edit_url . '">' . __( 'Edit' ) . '</a>',
        );
        $enable_disable_link = wp_nonce_url(
            add_query_arg(
                array(
                    'page'   => 'call-now-button',
                    'action' => $item->active == true ? 'disable' : 'enable',
                    'id'     => $item->id,
                ),
                $url ),
            'cnb_enable_disable_button' );
        $enable_disable_url  = esc_url( $enable_disable_link );

        if ( $item->active == true ) {
            $actions['disable'] = '<a href="' . $enable_disable_url . '">' . __( 'Disable' ) . '</a>';
        } else {
            $actions['enable'] = '<a href="' . $enable_disable_url . '">' . __( 'Enable' ) . '</a>';
        }
        $delete_link       = wp_nonce_url(
            add_query_arg( array(
                'page'   => 'call-now-button',
                'action' => 'cnb_delete_button',
                'id'     => $item->id,
            ), admin_url( 'admin-post.php' ) ),
            'cnb_delete_button' );
        $delete_url        = esc_url( $delete_link );
        $actions['delete'] = '<a href="' . $delete_url . '">' . __( 'Delete' ) . '</a>';

        $inactive_str = '';
        if ( ! $item->active ) {
            $inactive_str = ' — <span class="post-state">' . __( 'Inactive' ) . '</span>';
        }

        $notices = ValidationMessage::get_validation_notices($item);
	    do_action('cnb_validation_notices', $notices);

        return sprintf(
            '%1$s %2$s',
            '<strong><a class="row-title" href="' . $edit_url . '">' . esc_html( $item->name ) . '</a>' . $inactive_str . '</strong>',
            $this->row_actions( $actions )
        );
    }

    function get_bulk_actions() {
        return array(
            'enable'  => __( 'Enable' ),
            'disable' => __( 'Disable' ),
            'delete'  => __( 'Delete' ),
        );
    }

    function no_items() {
        esc_html_e( 'No buttons found. ' );
        if ( ! is_wp_error( $this->data ) ) {
            ( new CnbButtonView() )->render_lets_create_one_link();
        }
    }
}
