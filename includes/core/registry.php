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
      public static function page_templates() : array {
          $templates = [];
          
          $dir = MNI_FREE_PATH . '/views/frontend/';
      
          if ( is_dir( $dir ) ) {
              $files = glob( $dir . '*.php' );
      
              foreach ( $files as $file ) {
                  $filename = basename( $file, '.php' );
                  $templates[ $filename ] = ucwords( str_replace( '_', ' ', $filename ) );
              }
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
