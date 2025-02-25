<?php
// This file should not be "require"d anywhere

// These are not a real classes or functions, but mocks only used during development
// So the IDE is "tricked" into knowing the class and functions without
// having to include the whole WP_CLI suite during development.

namespace WP_CLI\Utils;

function get_flag_value( $array, $value, $default ) {
    return '';
}

function format_items( $format, $items, $fields ) {
}
