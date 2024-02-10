<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
class Plugin_Logger {

    public static function log_to_debug( $message ) {
        if ( defined('WP_DEBUG') && WP_DEBUG ) {
            if ( defined('WP_DEBUG_LOG') && WP_DEBUG_LOG ) {
                error_log( print_r( $message, true ) );
            }
        }
    }
}
