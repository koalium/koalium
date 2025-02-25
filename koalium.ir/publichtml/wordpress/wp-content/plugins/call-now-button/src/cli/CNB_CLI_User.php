<?php

namespace cnb\cli;

// don't load directly
defined( 'ABSPATH' ) || die( '-1' );

use cnb\admin\api\CnbAppRemote;
use cnb\admin\models\CnbUser;
use WP_CLI;
use WP_CLI_Command;
use WP_Error;

/**
 * Query the Call Now Button User
 *
 * @since  1.0.6
 * @author Jasper Roel
 *
 * @noinspection PhpUnused (it is used as a WP CLI class)
 */
class CNB_CLI_User extends WP_CLI_Command {
    /**
     * Returns the logged-in User
     *
     * ## EXAMPLES
     *
     *     # Get the current logged-in User
     *     $ wp cnb user get
     *     ID: 67a54fd0-b048-4522-8e23-ddfeffa6b129
     *     Name: Jasper Roel
     *     Email: jasper@studiostacks.com
     *
     * @return CnbUser|WP_Error
     */
    public function get() {
        $cnb_remote = new CnbAppRemote();
        $user = $cnb_remote->get_user();
        if ( ! is_wp_error( $user ) ) {
            WP_CLI::log( sprintf( 'ID: %s', $user->id ) );
            WP_CLI::log( sprintf( 'Name: %s', $user->name ) );
            WP_CLI::log(
                sprintf( 'Email: %s',
                    WP_CLI::colorize(
                        sprintf( '%%G%s%%n', $user->email )
                    )
                )
            );
        } else {
            WP_CLI::error( $user, false );
        }

        return $user;
    }

    /**
     * Registers the Call Now Button commands when CLI gets initialized.
     *
     * @noinspection PhpUnused (it is used via cli_init)
     */
    static function cli_register_command() {
        WP_CLI::add_command( 'cnb user', 'cnb\cli\CNB_CLI_User' );
    }
}

add_action( 'cli_init', '\cnb\cli\CNB_CLI_User::cli_register_command' );
