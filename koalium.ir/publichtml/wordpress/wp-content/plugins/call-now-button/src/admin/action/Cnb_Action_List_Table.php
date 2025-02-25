<?php

namespace cnb\admin\action;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

use cnb\admin\api\CnbAppRemote;
use cnb\admin\button\CnbButton;
use cnb\admin\settings\CnbSettingsController;
use cnb\utils\CnbAdminFunctions;
use cnb\utils\CnbUtils;
use DateTime;
use WP_Error;
use WP_List_Table;
use WP_Locale;

class Cnb_Action_List_Table extends WP_List_Table {

    /**
     * CallNowButton Button object
     *
     * @since v0.5.1
     * @var object
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
     * @param $button CnbButton (can contain at least a single key called 'button')
     */
    function __construct( $button = null ) {
        parent::__construct( array(
            'singular' => 'cnb_list_action', //Singular label
            'plural'   => 'cnb_list_actions', //plural label, also this well be one of the table css class
            'ajax'     => true, // We support it, see _js_vars(),
            'screen'   => 'call-now-button-actions', // Screen name for bulk actions, etc
        ) );

        $this->cnb_utils = new CnbUtils();
        $this->button    = $button;
    }

    /**
     * Define the columns that are going to be used in the table
     * @return array $columns, the array of columns to use with the table
     */
    function get_columns() {
        $cnb_options = get_option( 'cnb' );
        $columns     = array(
            'cb'          => '<input type="checkbox">',
            'draggable'   => '',
            'id'          => __( 'ID' ),
            'actionType'  => __( 'Type' ),
            'actionValue' => __( 'Value' ),
            'labelText'   => __( 'Label' ),
            'schedule'    => __( 'Schedule' ),
        );
        if ( $this->button ) {
            unset( $columns['cb'] );
        } else {
            unset( $columns['draggable'] );
        }

	    if ( $this->button && $this->button->type === 'DOTS' ) {
		    unset( $columns['labelText'] );
	    }

	    if ( CnbSettingsController::is_advanced_view() ) {
            $columns['actionButton'] = __( 'Button' );
        }

        return $columns;
    }

    function get_sortable_columns() {
        // No sorting for the actions list for buttons
        // since they are returned in /actual/ order they appear in, we do not allow changing
        // that via sortable columns
        if ( $this->button ) {
            return array();
        }

        return array(
            'actionType'  => array( 'actionType', false ),
            'actionValue' => array( 'actionValue', false ),
            'labelText'   => array( 'labelText', false ),
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
        if ( ! $this->button ) {
            if ( $this->cnb_utils->get_query_val( 'orderby' ) ) {
                usort( $data, array( &$this, 'sort_data' ) );
            }
        }

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
            $this->_column_headers = array( $columns, $hidden_columns, $sortable_columns, 'actionType' );

            /* -- Register the items -- */
            $data        = array_slice( $data, $offset, $per_page );
            $this->items = $data;
        }

        return null;
    }

    /**
     * @param $item CnbAction
     * @param $column_name
     *
     * @return string
     */
    function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'id':
                // Also add the ID input
                $id    = '<input type="hidden" name="actions[][id]" value="' . esc_attr( $item->id ) . '" />';
                $value = ! empty( $item->id ) ? esc_html( $item->id ) : '<em>No value</em>';

                return $id . $value;
            case 'draggable':
                return '<svg height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M11 18c0 1.1-.9 2-2 2s-2-.9-2-2 .9-2 2-2 2 .9 2 2zm-2-8c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0-6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm6 4c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>';
            case 'actionValue':
                return $this->get_action_value( $item );
            case 'labelText':
                return ! empty( $item->labelText ) ? esc_html( $item->labelText ) : '<em>No value</em>';
            case 'actionButton':
                $url    = admin_url( 'admin.php' );
                $button = $item->button;
                if ( $button && $button->id && ! is_wp_error( $button ) ) {
                    $new_link =
                        add_query_arg(
                            array(
                                'page'   => 'call-now-button',
                                'action' => 'edit',
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

	/**
	 * @param $action CnbAction
	 *
	 * @return string
	 */
	private function get_action_value( $action ) {
		if ($action->actionType === 'CHAT' ) return ' -';
		return ! empty( $action->actionValue ) ? esc_html( $action->actionValue ) : '<em>No value</em>';
	}

    private function get_data() {
        $cnb_remote = new CnbAppRemote();
        $actions     = array();
        if ( $this->button === null ) {
            $actions = $cnb_remote->get_actions();
        } else {
            // Find ActionIDs for Button
            $button = $this->button;
            if ( $button instanceof WP_Error ) {
                return $button;
            }

            if ( $button->actions != null ) {
                $actions = $button->actions;
            }
        }

        if ( $actions instanceof WP_Error ) {
            return $actions;
        }

        foreach ( $actions as $action ) {
            if ( CnbSettingsController::is_advanced_view() ) {
                $button         = $cnb_remote->get_button_for_action( $action->id );
                $action->button = $button;
            }
        }

        return $actions;
    }

    /**
     * Allows you to sort the data by the variables set in the $_GET
     *
     * @param $a CnbAction
     * @param $b CnbAction
     *
     * @return int
     */
    private function sort_data( $a, $b ) {
        // If orderby is set, use this as the sort column
        $orderby = $this->cnb_utils->get_query_val( 'orderby', 'actionValue' );
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
     * @param CnbAction $item
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
     * @param $item CnbAction
     *
     * @return string|void
     * @noinspection PhpUnused
     */
    function column_schedule( $item ) {
        /**
         * @global WP_Locale $wp_locale WordPress date and time locale object.
         */
        global $wp_locale;

        if ( ! $item || ! $item->schedule || $item->schedule->showAlways === null || $item->schedule->showAlways ) {
            return __( 'Always visible' );
        }

        $cnb_days_of_week_order = ( new CnbActionViewEdit() )->get_order_of_days();

        $schedule = $item->schedule;

        $days       = '';
        $daysOfWeek = array_unique( $schedule->daysOfWeek );
        if ( count( $daysOfWeek ) === 1 ) {
            // All disabled or enabled
            if ( $daysOfWeek[0] === true ) {
                $days = __( 'All days' );
            } else {
                // Alligator alert: normally this is no possible, but adding for completeness
                $days = __( 'No days' );
            }
        } else {
            // Special case: weekdays (and weekends?)
            // First 5 are true, last 2 are false == weekdays
            // Get first 5, filter for false/null, count
            $weekdays = ( count( array_filter( array_slice( $schedule->daysOfWeek, 0, 5 ) ) ) === 5 &&
                            count( array_filter( array_slice( $schedule->daysOfWeek, 5, 2 ) ) ) === 0 );
            $weekend  = ( count( array_filter( array_slice( $schedule->daysOfWeek, 0, 5 ) ) ) === 0 &&
                            count( array_filter( array_slice( $schedule->daysOfWeek, 5, 2 ) ) ) === 2 );
            if ( $weekdays ) {
                $days = __( 'Weekdays only' );
            } else if ( $weekend ) {
                $days = __( 'Weekend only' );
            } else {
                // Print days it's enabled
                $split = '';
                foreach ( $cnb_days_of_week_order as $cnb_day_of_week ) {
                    $api_server_index = ( new CnbActionViewEdit() )->wp_locale_day_to_daysofweek_array_index( $cnb_day_of_week );
                    $day              = $wp_locale->get_weekday( $cnb_day_of_week );
                    if ( $schedule->daysOfWeek[ $api_server_index ] ) {
                        $days  .= $split . $day;
                        $split = ', ';
                    }
                }
            }
        }
        $wp_time_format = get_option( 'time_format' );

        $start = $schedule->start;
        $stop  = $schedule->stop;

        $date     = new DateTime();
        $wp_start = '00:00';
        if ( $start ) {
            $hour = explode( ':', $start )[0];
            $min  = explode( ':', $start )[1];
            $date->setTime( $hour, $min );
            $wp_start = date_i18n( $wp_time_format, $date->getTimestamp() );
        }

        $wp_stop = '23:59';
        if ( $stop ) {
            $hour = explode( ':', $stop )[0];
            $min  = explode( ':', $stop )[1];
            $date->setTime( $hour, $min );
            $wp_stop = date_i18n( $wp_time_format, $date->getTimestamp() );
        }

        // print time
        if ( $start == '00:00' && $stop === '23:59' ) {
            $time = '<em>' . __( 'All day' ) . '</em>';
        } else {
            $time = esc_html( $wp_start ) . ' - ' . esc_html( $wp_stop );
        }

        if ( $schedule->outsideHours ) {
            $time = 'Before ' . esc_html( $wp_start ) . ', after ' . esc_html( $wp_stop );
        }

        return sprintf(
            '%1$s %2$s',
            '<div class="cnb-scheduler-days">' . $days . '</div>',
            '<div class="time">' . $time . '</div>'
        );
    }

    /**
     * @param $item CnbAction
     *
     * @return string
     * @noinspection PhpUnused
     */
    function column_actionType( $item ) {
        $bid = $this->button !== null ? $this->button->id : null;
        $tab = $this->button !== null ? 'basic_options' : null;

        $actions = array();
        // Let's build a link
        $url             = admin_url( 'admin.php' );
        $edit_link       =
            add_query_arg(
                array(
                    'page'   => 'call-now-button-actions',
                    'action' => 'edit',
                    'id'     => $item->id,
                    'bid'    => $bid,
                    'tab'    => $tab,
                ),
                $url );
        $edit_url        = esc_url( $edit_link );
        $actions['edit'] = '<a href="' . $edit_url . '">Edit</a>';

        if ( $this->button ) {
            $nonce             = wp_create_nonce( 'cnb_delete_action' );
            $screen            = get_current_screen();
            $args              = array(
                'action'   => 'cnb_delete_action',
                'id'       => $item->id,
                'bid'      => $bid,
                '_wpnonce' => $nonce,
                'refer'    => $screen->parent_file,
            );
            $delete_url        = esc_url( add_query_arg( $args, admin_url( 'admin-ajax.php' ) ) );
            $actions['delete'] = '<a data-ajax="true" data-id="' . $item->id . '" data-bid="' . $bid . '" data-wpnonce="' . $nonce . '" href="' . $delete_url . '">' . __( 'Delete' ) . '</a>';
        } else {
            $delete_link       = wp_nonce_url(
                add_query_arg( array(
                    'page'   => 'call-now-button-actions',
                    'action' => 'cnb_delete_action',
                    'id'     => $item->id,
                    'bid'    => $bid,
                ), admin_url( 'admin-post.php' ) ),
                'cnb_delete_action' );
            $delete_url        = esc_url( $delete_link );
            $actions['delete'] = '<a href="' . $delete_url . '">' . __( 'Delete' ) . '</a>';
        }

        $actionTypes = ( new CnbAdminFunctions() )->cnb_get_action_types();
        $value       = ! empty( $item->actionType ) ? esc_html( $actionTypes[ $item->actionType ]->name ) : '<em>No value</em>';

        return sprintf(
            '%1$s %2$s',
            '<strong><a class="row-title" href="' . $edit_url . '">' . $value . '</a></strong>',
            $this->row_actions( $actions )
        );
    }

    function get_bulk_actions() {
        // Hide Bulk Actions if we're on the Button edit page
        if ( $this->button ) {
            return array();
        }

        return array(
            'delete' => 'Delete',
        );
    }

    public function _js_vars() {
        $args = array(
            'class'  => get_class( $this ),
            'screen' => array(
                'id'   => $this->screen->id,
                'base' => $this->screen->base,
            ),
            'button' => $this->button,
            'data'   => $this->get_data(),
        );

        /** @noinspection BadExpressionStatementJS */
        printf( "<script type='text/javascript'>list_args = %s;</script>\n", wp_json_encode( $args ) );
    }

    function no_items() {
        esc_html_e( 'This button has no actions yet. Let\'s add one!' );
    }

    function get_column_count() {
        $count = parent::get_column_count();
        $data  = $this->get_data();
        // We hide the draggable column if there are less than 2 items, so we need to account for that.
        if ( $this->button && count( $data ) < 2 ) {
            return $count - 1;
        }

        return $count;
    }
}
