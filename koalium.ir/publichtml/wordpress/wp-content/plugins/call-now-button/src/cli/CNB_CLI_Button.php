<?php

namespace cnb\cli;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAdminCloud;
use cnb\admin\api\CnbAppRemote;
use cnb\admin\button\CnbButton;
use cnb\admin\button\CnbButtonOptions;
use WP_CLI;
use WP_CLI_Command;

/**
 * Query the Call Now Button Buttons
 *
 * @since  1.0.6
 * @author Jasper Roel
 *
 * @noinspection PhpUnused (it is used as a WP CLI class)
 */
class CNB_CLI_Button extends WP_CLI_Command {
    /**
     * List all the buttons for this User
     *
     * ## OPTIONS
     *
     * [--format=<format>]
     * : Render output in a particular format.
     * ---
     * default: table
     *
     * options:
     *   - table
     *   - csv
     *   - json
     *   - count
     *   - yaml
     * ---
     *
     * [--fields=<fields>]
     * : Render specific fields. Default is to show all available fields.
     * ---
     * default: id,name,active,type,domain,actions,conditions,options
     *
     * options:
     *   - id
     *   - name
     *   - active
     *   - type
     *   - domain
     *   - actions
     *   - conditions
     *   - options
     * ---
     *
     * ## EXAMPLES
     *
     *     # List all buttons
     *     $ wp cnb button list
     *
     *     # Specify fields
     *     $ wp cnb button list --fields=id,name,active
     *
     *     # Specify output
     *     $ wp cnb button list --output=yaml
     *
     *     # Specify fields and output
     *     $ wp cnb button list --fields=id,name --output=yaml
     *
     * @subcommand list
     */
    public function list_( $args, $assoc_args ) {
        $cnb_remote = new CnbAppRemote();
        $buttons    = $cnb_remote->get_buttons();
        if ( ! is_wp_error( $buttons ) ) {
            // Convert into array
            $items = CnbButton::convertToArray( $buttons );

            $format = WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'table' );
            $fields = WP_CLI\Utils\get_flag_value( $assoc_args, 'fields', array(
                'id',
                'name',
                'active',
                'type',
                'domain',
                'actions',
                'conditions',
                'options',
            ) );
            WP_CLI::log( $fields );
            WP_CLI\Utils\format_items( $format, $items, $fields );
            WP_CLI::log( '' ); // Force a new line since that isn't included in the format_items output
        } else {
            WP_CLI::error( $buttons, false );
        }
    }

    /**
     * Create a Button
     *
     * ## OPTIONS
     *
     * <name>
     * : The name of the Button
     *
     * [--type=<type>]
     * : default: SINGLE
     * ---
     * options:
     *   - SINGLE
     *   - FULL
     *   - MULTI
     * ---
     *
     * [--active=<action>]
     * : default: true
     * ---
     * options:
     *   - true
     *   - false
     * ---
     *
     * [--placement=<placement>]
     * : default: BOTTOM_RIGHT
     * ---
     * options:
     *   - BOTTOM_LEFT
     *   - BOTTOM_CENTER
     *   - BOTTOM_RIGHT
     *   - MIDDLE_LEFT
     *   - MIDDLE_RIGHT
     *   - TOP_LEFT
     *   - TOP_CENTER
     *   - TOP_RIGHT
     *
     * ## EXAMPLES
     *
     *     # Create a button
     *     $ wp cnb button create "Button via WP CLI"
     *
     *     # Create a MULTI button
     *     $ wp cnb button create "Multi Button via WP CLI" --type=MULTI
     *
     *     # Create an inactive FULL button
     *     $ wp cnb button create "Full Button via WP CLI" --type=FULL --active=false
     *
     *     # Create an FULL button with TOP_CENTER placement
     *     $ wp cnb button create "Full Button at top via WP CLI" --type=FULL --placement=TOP_CENTER
     * @return void
     */
    public function create( $args, $assoc_args ) {
        $cnb_remote = new CnbAppRemote();
        // Check of key is provided
        if ( sizeof( $args ) !== 1 ) {
            WP_CLI::error( 'Wrong amount of arguments, 1 (the name) expected' );

            return;
        }

        list ( $button_name ) = $args;

        $notifications = array();

        $default_domain = $cnb_remote->get_wp_domain();

        $button         = new CnbButton();
        $button->name   = $button_name;
        $button->type   = WP_CLI\Utils\get_flag_value( $assoc_args, 'type', 'SINGLE' );
        $button->active = WP_CLI\Utils\get_flag_value( $assoc_args, 'active', 'true' ) == 'true';
        $button->domain = $default_domain;

        $options            = new CnbButtonOptions();
        $options->placement = WP_CLI\Utils\get_flag_value( $assoc_args, 'placement', 'BOTTOM_RIGHT' );
        $button->options    = $options;

        $result = CnbAdminCloud::cnb_create_button( $notifications, $button );
        if ( is_wp_error( $result ) ) {
            WP_CLI::error( $result );
        }
        WP_CLI::success( 'Created ' . $result->name . ' with ID ' . $result->id );

    }

    /**
     * Registers the Call Now Button commands when CLI gets initialized.
     *
     * @noinspection PhpUnused (it is used via cli_init)
     */
    static function cli_register_command() {
        WP_CLI::add_command( 'cnb button', 'cnb\cli\CNB_CLI_Button' );
    }
}

add_action( 'cli_init', '\cnb\cli\CNB_CLI_Button::cli_register_command' );
