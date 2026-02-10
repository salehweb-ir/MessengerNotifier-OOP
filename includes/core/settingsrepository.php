<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Settings_Repository {

    private static function get_all() : array {
        return (array) get_option( 'mni_free_settings', [] );
    }

    public static function active_messengers() : array {
        return self::get_all()['messengers'] ?? [];
    }

    public static function active_actions() : array {
        return self::get_all()['actions'] ?? [];
    }

    public static function config( string $key ) : array {
        return self::get_all()['config'][ $key ] ?? [];
    }

    public static function all() : array {
        return self::get_all();
    }
}
