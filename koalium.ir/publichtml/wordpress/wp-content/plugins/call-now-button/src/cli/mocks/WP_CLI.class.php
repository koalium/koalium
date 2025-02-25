<?php

// This file should not be "require"d anywhere

// These are not a real classes, but mocks only used during development
// So the IDE is "tricked" into knowing the class and functions without
// having to include the whole WP_CLI suite during development.

if ( ! class_exists( 'WP_CLI' ) ) {
    class WP_CLI {

        public static function error( $string, $die = true ) {
        }

        public static function colorize( $sprintf ) {
            return '';
        }

        public static function success( $string ) {
        }

        public static function log( $string ) {
        }

        public static function add_command( $string, $string1 ) {
        }

        public static function warning( $string ) {
        }
    }
}

if ( ! class_exists( 'WP_CLI_Command' ) ) {
    class WP_CLI_Command {
    }
}
