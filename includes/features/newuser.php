<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_newuser {
    use mni_singleton;

    private function __construct() {
        add_action( 'user_register', [ $this, 'notify_new_user' ], 10, 1 );
    }

    /**
     * Notify when a new user registers
     */
    public function notify_new_user( $user_id ) {
        $user = get_userdata( $user_id );
        if ( ! $user ) {
            return;
        }

        $username = sanitize_text_field( $user->user_login );
        $email    = sanitize_email( $user->user_email );

        $message  = "👤 <b>New User Registered</b>\n";
        $message .= "🧑 Username: {$username}\n";
        $message .= "📧 Email: {$email}\n";

        $hashtag  = "#newuser";

        $messenger_manager = mni_free_messenger_manager::instance();
        $messenger_manager->send( $message . "\n\n" . $hashtag );
    }
}
