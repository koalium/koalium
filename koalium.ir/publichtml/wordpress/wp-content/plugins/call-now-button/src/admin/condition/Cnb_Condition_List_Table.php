<?php

namespace cnb\admin\condition;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use cnb\admin\api\CnbAppRemote;
use cnb\admin\button\CnbButton;
use cnb\admin\settings\CnbSettingsController;
use cnb\utils\CnbUtils;
use WP_Error;
use WP_List_Table;

class Cnb_Condition_List_Table extends WP_List_Table {

    /**
     * CallNowButton Condition object
     *
     * @since v0.5.5
     * @var CnbButton
     */
    public $button;

    /**
     * @var CnbUtils
     */
    private $cnb_utils;

    /**
     * Constructor, we override the parent to pass our own arguments
     * We usually focus on three parameters: singular and plural labels, as well as whether the class supports AJAX.
     *
     * @param CnbButton $button
     */
    function __construct( $button = null ) {
        parent::__construct( array(
            'singular' => 'cnb_list_condition', //Singular label
            'plural'   => 'cnb_list_conditions', //plural label, also this well be one of the table css class
            'ajax'     => false, //We won't support Ajax for this table
            'screen'   => 'call-now-button-conditions', // Screen name for bulk actions, etc
        ) );

        $this->cnb_utils = new CnbUtils();
        if ( $button instanceof CnbButton ) {
            $this->button = $button;
        }
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        $cnb_options = get_option( 'cnb' );
        $columns     = array(
            'cb'            => '<input type="checkbox">',
            'id'            => __( 'ID' ),
            'filterType'    => __( 'Filter type' ),
            'conditionType' => __( 'Type' ),
            'matchType'     => __( 'Match type' ),
            'matchValue'    => __( 'Match value' ),
        );
        if ( $this->button ) {
            unset( $columns['cb'] );
            unset( $columns['conditionType'] );

        }

        if ( CnbSettingsController::is_advanced_view() ) {
            $columns['conditionButton'] = __( 'Button' );
        }

        return $columns;
    }

    function get_sortable_columns() {
        return array(
            'conditionType' => array( 'conditionType', false ),
            'filterType'    => array( 'filterType', false ),
            'matchType'     => array( 'matchType', false ),
            'matchValue'    => array( 'matchValue', false ),
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
        $current_page = intval( $this->cnb_utils->get_query_val( 'paged', 1 ) );

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
            $this->_column_headers = array( $columns, $hidden_columns, $sortable_columns, 'conditionType' );

            /* -- Register the items -- */
            $data        = array_slice( $data, $offset, $per_page );
            $this->items = $data;
        }

        return null;
    }

    /**
     * @param CnbCondition $item
     * @param $column_name
     *
     * @return string|void
     */
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
                // Also add the ID input
                $id    = '<input type="hidden" name="conditions[][id]" value="' . esc_attr( $item->id ) . '" />';
                $value = ! empty( $item->id ) ? esc_html( $item->id ) : '<em>No value</em>';

                return $id . $value;
            case 'filterType':
                $actions  = $this->get_quick_actions( $item );
                $edit_url = $this->get_edit_link( $item );
                $value    = ucfirst( strtolower( $item->filterType ) );

                return sprintf(
                    '%1$s %2$s',
                    '<strong><a class="row-title" href="' . $edit_url . '">' . esc_html( $value ) . '</a></strong>',
                    $this->row_actions( $actions ) );
            case 'matchType':
                switch ( $item->matchType ) {
                    case 'SIMPLE':
                        return 'Page path starts with';
                    case 'EXACT':
                        return 'Page URL is';
                    case 'SUBSTRING':
                        return 'Page URL contains';
                    case 'REGEX':
                        return 'Page URL matches RegEx';
	                case 'COUNTRY_CODE':
		                return 'Country code is';
	                case 'AGENTS_AVAILABLE':
		                return 'Agents are available';
	                default:

                }

                return '<code>' . esc_html( $item->matchType ) . '</code>';
            case 'matchValue':
				switch ( $item->conditionType ) {
					case 'URL':
					case 'GEO':
						$matchValue = $item->matchValue;
						if ( $item->matchType === 'REGEX' ) {
							$matchValue = '<code>' . $matchValue . '</code>';
						}
						return ! empty( $matchValue ) ? esc_html( $matchValue ) : '<em>No value</em>';
					case 'CHAT':
						return '<em>Not applicable</em>';
				}
                return ! empty( $item->matchValue ) ? esc_html( $item->matchValue ) : '<em>No value</em>';
            case 'conditionButton':
                $url    = admin_url( 'admin.php' );
                $button = $item->button;
                if ( $button && $button->id && ! is_wp_error( $button ) ) {
                    $new_link =
                        add_query_arg(
                            array(
                                'page'   => 'call-now-button',
                                'action' => 'edit',
                                'tab'    => 'visibility',
                                'id'     => $button->id,
                            ),
                            $url );
                    $new_url  = esc_url_raw( $new_link );

                    return '<a href="' . $new_url . '">' . esc_html( $button->name ) . '</a>';
                }

                return '<em>N/A</em>';
            default:
                return '<em>Unknown column ' . esc_html( $column_name ) . '</em>';
        }
    }

    private function get_data() {
        $cnb_options = get_option( 'cnb' );
        $cnb_remote = new CnbAppRemote();
        if ( $this->button === null ) {
            $entities = $cnb_remote->get_conditions();
        } else {
            // Find ConditionIDs for Button
            $button = $this->button;
            if ( $button instanceof WP_Error ) {
                return $button;
            }
            $entities = $button->conditions;
        }

        if ( $entities instanceof WP_Error ) {
            return $entities;
        }

        foreach ( $entities as $entity ) {
            if ( CnbSettingsController::is_advanced_view() ) {
                $button         = $cnb_remote->get_button_for_condition( $entity->id );
                $entity->button = $button;
            }
        }

        return $entities;
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @param CnbCondition $a
     * @param CnbCondition $b
     *
     * @return int
     */
    private function sort_data( $a, $b ) {
        // If orderby is set, use this as the sort column
        $orderby = $this->cnb_utils->get_query_val( 'orderby', 'conditionType' );
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
     * @param CnbCondition $item
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

    private function get_edit_link( $condition ) {
        $bid       = $this->button !== null ? $this->button->id : null;
        $tab       = $this->button !== null ? 'visibility' : null;
        $url       = admin_url( 'admin.php' );
        $edit_link =
            add_query_arg(
                array(
                    'page'   => 'call-now-button-conditions',
                    'action' => 'edit',
                    'id'     => $condition->id,
                    'bid'    => $bid,
                    'tab'    => $tab,
                ),
                $url );

        return esc_url( $edit_link );
    }

    /**
     * @param $condition CnbCondition
     *
     * @return string[]
     */
    private function get_quick_actions( $condition ) {
        $bid = $this->button !== null ? $this->button->id : null;

        // Let's build a link
        $url      = admin_url( 'admin.php' );
        $edit_url = $this->get_edit_link( $condition );
        $actions  = array(
            'edit' => '<a href="' . $edit_url . '">Edit</a>',
        );

        if ( $this->button ) {
            $nonce             = wp_create_nonce( 'cnb_delete_condition' );
            $screen            = get_current_screen();
            $args              = array(
                'action'   => 'cnb_delete_condition',
                'id'       => $condition->id,
                'bid'      => $bid,
                '_wpnonce' => $nonce,
                'refer'    => $screen->parent_file,
            );
            $delete_url        = esc_url( add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) );
            $actions['delete'] = '<a data-ajax="true" data-id="' . $condition->id . '" data-bid="' . $bid . '" data-wpnonce="' . $nonce . '" href="' . $delete_url . '">' . __( 'Delete' ) . '</a>';
        } else {
            $delete_link       = wp_nonce_url(
                add_query_arg( array(
                    'page'   => 'call-now-button-conditions',
                    'action' => 'cnb_delete_condition',
                    'id'     => $condition->id,
                    'bid'    => $bid,
                ), admin_url( 'admin-post.php' ) ),
                'cnb_delete_condition' );
            $delete_url        = esc_url( $delete_link );
            $actions['delete'] = '<a href="' . $delete_url . '">' . __( 'Delete' ) . '</a>';
        }

        return $actions;
    }

    /**
     * @param CnbCondition $item
     *
     * @return string
     * @noinspection PhpUnused
     */
    function column_conditionType( $item ) {
        return ! empty( $item->conditionType ) ? esc_html( $item->conditionType ) : '<em>No value</em>';
    }

    function get_bulk_actions() {
        // Note: bulk actions are hard to implement as well, since the table is already nested in the buttonViewEdit form
        // which breaks the action-post action for Condition bulk actions.
        // Hide Bulk Actions if we're on the Button edit page
        if ( $this->button ) {
            return array();
        }

        return array(
            'delete' => 'Delete',
        );
    }

    function no_items() {
        if ( $this->button ) {
            echo '<p class="cnb_paragraph">You have no display rules set up. This means that your button will show on all pages.</p>';
            echo '<p class="cnb_paragraph">Click the <code>Add display rule</code> button above to limit the appearance. You can freely mix and match rules to meet your requirements.</p>';

            return;
        }
        esc_html_e( 'No display rules found.' );

    }
}
