<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Registry {

    /* ---------- Messengers ---------- */
    public static function messengers() : array {
          return [
            'eitaa' => [
              'label' => __( 'Eitaa', 'messengernotifier' ),
            ],
             'bale' => [
              'label' => __( 'Bale', 'messengernotifier' ),
            ],
          ];
    }

    /* ---------- Actions / Features ---------- */
    public static function actions() : array {
        return [
            'comment' => [
                'label' => __( 'New Comment', 'messengernotifier' ),
                'requires' => null,
            ],
            'new_user' => [
                'label' => __( 'New User Registration', 'messengernotifier' ),
                'requires' => null,
            ],
            'ordercompleted' => [
                'label' => __( 'WooCommerce Order Completed', 'messengernotifier' ),
                'requires' => 'woocommerce',
            ],
        ];
    }

    /* ---------- Page Templates (Frontend) ---------- */
      public static function page_templates(): array {
        $path = MNI_FREE_PATH . 'views/frontend/css/';
        $files = glob( $path . '*.css' );
      
        $templates = [];
      
        if ( ! $files ) {
            return $templates;
        }
      
        foreach ( $files as $file ) {
      
            $slug = basename( $file, '.css' );
            $templates[ $slug ] = ucwords( str_replace( '-', ' ', $slug ) );
        }
      
        // مرتب‌سازی الفبایی
        ksort( $templates );
      
        // اگر default وجود داشت، بیار اول
        if ( isset( $templates['default'] ) ) {
      
            $default = [ 'default' => $templates['default'] ];
            unset( $templates['default'] );
      
            $templates = $default + $templates;
        }
      
        return $templates;
      }

      
      

    
    public static function all() : array {
       $all_settings_data = [
        'messengers'     => self::messengers(),
        'actions'        => self::actions(),
        'page_templates' => self::page_templates(),
      ];
      
      error_log("all settings data: " . print_r($all_settings_data,true));
      return $all_settings_data;
    }
}
