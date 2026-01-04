<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class mni_logger {

    public static function log( $message, $context = [] ) {

        if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
            return;
        }

        $output = is_array( $message )
            ? print_r( $message, true )
            : $message;

        if ( ! empty( $context ) ) {
            $output .= ' | ' . print_r( $context, true );
        }

        error_log( '[MNI] ' . $output );
    }
}
