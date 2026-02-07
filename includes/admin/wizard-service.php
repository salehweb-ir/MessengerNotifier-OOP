<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class MNI_Free_Wizard_Service {

    /**
     * Available messengers
     */
    public function get_messengers() : array {
        return [
            'eitaa' => [
                'label' =>  __( 'eitaa', 'messengernotifier' ),
                'name'  =>  'Eitaa',
            ],
            'bale' => [
                'label' =>  __( 'bale', 'messengernotifier' ),
                'name'  =>  'Bale',
            ],

            // future messengers
        ];
    }

    /**
     * Get saved settings from DB
     */
    public function get_messenger_settings_from_db() : array {
        return get_option( 'mni_free_settings', [] );
    }

    /**
     * Available actions / features
     */
    public function get_actions() : array {
        return [
            'comment'        => __( 'New Comment', 'messengernotifier' ),
            'new_user'       => __( 'New User Registration', 'messengernotifier' ),
            'ordercompleted' => __( 'WooCommerce Order Completed', 'messengernotifier' ),
        ];
    }
}
?>