<?php

namespace cnb\cli;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAdminCloud;
use WP_CLI;
use WP_CLI_Command;

/**
 * Adds, removes and fetches Call Now Buttons API keys
 *
 * @since  1.0.6
 * @author Jasper Roel
 *
 * @noinspection PhpUnused (it is used as a WP CLI class)
 */
class CNB_CLI_Api extends WP_CLI_Command {
    private $adminCloud;

    public function __construct() {
        $this->adminCloud = new CnbAdminCloud();
    }

    /**
     * Gets the stored API key
     *
     * It will only show the first part by default, so it can be safely passed to Support
     *
     * ## OPTIONS
     *
     * [--reveal]
     * : Shows the full API key
     *
     * ## Examples
     *
     *     # Get the API key
     *     $ wp cnb api get
     *     Success: 0a45e023-1242-4410-a17a-xxxxxxxxxxxx
     *
     *     # Get the API key and reveal it
     *     $ wp cnb api get --reveal
     *     Success: 0a45e023-1242-4410-a17a-123456789012
     *
     * @param $args array [optional]
     * @param $assoc_args array [optional]
     *
     *
     * @return void
     *
     * @noinspection PhpUnusedParameterInspection ($args is required for a CLI command)
     */
    function get( $args = array(), $assoc_args = array() ) {

        $options = get_option( 'cnb' );
        if ( is_array( $options ) && key_exists( 'api_key', $options ) ) {
            $key = $options['api_key'];
            if ( ! isset( $assoc_args['reveal'] ) ) {
                $key = $this->obfuscate_key( $options['api_key'] );
            }
            WP_CLI::success( $key );

            return;
        }
        WP_CLI::warning( 'API key not found' );
    }

    /**
     * Set the provided API key
     *
     * ## OPTIONS
     *
     * <api_key>
     * : The API key to set.
     *
     * [--set-if-valid]
     * : Only store the API key if valid
     *
     * ## EXAMPLES
     *
     *     # Set an API key
     *     $ wp cnb api set 0a45e023-...
     *
     *     # Set an API key (but only if the key is tested and valid)
     *     $ wp cnb api set 0a45e023-... --set-if-valid
     *
     * @param $args array should contain 1 entry, only the API key
     * @param $assoc_args array [optional]
     *
     * @return void
     *
     */
    public function set( $args, $assoc_args ) {
        // Check of key is provided
        if ( sizeof( $args ) !== 1 ) {
            WP_CLI::error( 'Wrong amount of arguments, 1 (the API key) expected' );

            return;
        }

        $api_key = $args[0];
        WP_CLI::log( sprintf( 'Key provided: %s',
            WP_CLI::colorize( sprintf( '%%G%s%%n', $api_key ) )
        ) );

        if ( isset( $assoc_args['set-if-valid'] ) ) {
            WP_CLI::log( 'Testing key...' );
            $is_valid = $this->test_api_key( $api_key );
            if ( $is_valid ) {
                $options            = array();
                $options['api_key'] = $api_key;
                update_option( 'cnb', $options );
                WP_CLI::success( 'API key updated' );
            } else {
                WP_CLI::error( 'API key incorrect, nothing updated' );
            }

            return;
        }

        // No options provided, just set the key
        $options            = array();
        $options['api_key'] = $api_key;
        update_option( 'cnb', $options );
        WP_CLI::success( 'API key updated' );
    }

    /**
     * Test the provided API key
     *
     * ## OPTIONS
     *
     * <api_key>
     * : The API key to test.
     *
     * ## EXAMPLES
     *
     *     # Test an API key
     *     $ wp cnb api test 0a45e023-...
     *
     *
     * @param $args {array} should contain 1 entry, only the API key
     *
     * @return boolean true if valid
     *
     */
    public function test( $args ) {
        // Check of key is provided
        if ( sizeof( $args ) !== 1 ) {
            WP_CLI::error( 'Wrong amount of arguments, 1 (the API key) expected' );

            return false;
        }
        $api_key = $args[0];

        WP_CLI::log( 'Testing key...' );
        $is_valid = $this->test_api_key( $api_key );
        if ( $is_valid ) {
            WP_CLI::success( 'API key valid' );
        } else {
            WP_CLI::error( 'API key invalid' );
        }

        return $is_valid;
    }

    /**
     * @param $api_key string the API key to test
     *
     * @return boolean true if the key is valid
     */
    private function test_api_key( $api_key ) {
        return $this->adminCloud->is_api_key_valid( $api_key );
    }

    /**
     * @param $api_key string any API key
     *
     * @return string Obfucscated string (last part of key removed)
     */
    private function obfuscate_key( $api_key ) {
        $hide_chars = 12;

        return substr( $api_key, 0, - $hide_chars )
                . str_repeat( 'x', $hide_chars );
    }

    /**
     * Registers the Call Now Button commands when CLI gets initialized.
     *
     * @noinspection PhpUnused (it is used via cli_init)
     */
    static function cli_register_command() {
        WP_CLI::add_command( 'cnb api', 'cnb\cli\CNB_CLI_Api' );
    }
}

add_action( 'cli_init', '\cnb\cli\CNB_CLI_Api::cli_register_command' );
