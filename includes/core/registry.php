<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Registry {

    /* ---------- Messengers ---------- */
    public static function messengers() : array {
          return [
            'eitaa' => [
              'label' => __( 'Eitaa', 'messengernotifier' ),
              'class' => 'Eitaa::class',
            ],
            'bale' => [
              'label' => __( 'Bale', 'messengernotifier' ),
              'class' => 'Bale::class',
            ],
          ];
    }

    /**!SECTION
     * available actions in plugin
     */
    public static function actions() : array {
      return [
          'comment' => [
              'label'    => __( 'New Comment', 'messengernotifier' ),
              'file'     => 'includes/features/comment.php',
              'class'    => 'mni_free_feature_comment',
              'hook' => null,
          ],
          'new_user' => [
              'label'    => __( 'New User Registration', 'messengernotifier' ),
              'file'     => 'includes/features/newuser.php',
              'class'    => 'mni_free_feature_newuser',
              'hook' => null,
          ],
          'ordercompleted' => [
              'label'    => __( 'WooCommerce Order Completed', 'messengernotifier' ),
              'file'     => 'includes/features/woocommerce/ordercompleted.php',
              'class'    => 'mni_free_feature_ordercompleted',
              'hook' => 'woocommerce_loaded',
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
      
        // sorting the templates
        ksort( $templates );
      
        // bring default at first if exists 
        if ( isset( $templates['default'] ) ) {
      
            $default = [ 'default' => $templates['default'] ];
            unset( $templates['default'] );
      
            $templates = $default + $templates;
        }
      
        return $templates;
      }

    // return all required settings
    public static function all() : array {
       $all_settings_data = [
        'messengers'     => self::messengers(),
        'actions'        => self::actions(),
        'page_templates' => self::page_templates(),
      ];
      
      return $all_settings_data;
    }
}
