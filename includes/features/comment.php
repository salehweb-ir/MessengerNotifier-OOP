<?php
if ( ! defined( 'ABSPATH' ) ) exit;


require_once MNI_FREE_PATH . 'includes/traits/singleton.php';
require_once MNI_FREE_PATH . 'includes/messengers/manager.php';

class mni_free_feature_comment {
    use mni_singleton;

    private function __construct() {
        add_action( 'comment_post', [ $this, 'handle_new_comment' ], 10, 2 );
    }

    /**
     * Handle new comment notification
     */
    public function handle_new_comment( $comment_ID, $comment_approved ) {

        $comment = get_comment( $comment_ID );
        if ( ! $comment ) {
            return;
        }

        $post = get_post( $comment->comment_post_ID );
        if ( ! $post ) {
            return;
        }

        $message = $this->build_message( $comment, $post, $comment_approved );

        // Send via Messenger Manager
        mni_free_messenger_manager::instance()->send(
            $message,
            __( '#comment', 'messengernotifier' )
        );

        error_log($message);
    }

    /**
     * Build comment notification message
     */
    private function build_message( WP_Comment $comment, WP_Post $post, $comment_approved ) : string {

        $status_map = [
            1      => "✅ " . __( 'Approved', 'messengernotifier' ),
            0      => "⏳ " . __( 'Pending Moderation', 'messengernotifier' ),
            'spam' => "🚫 " . __( 'Marked as Spam', 'messengernotifier' ),
        ];

        $status_text = $status_map[ $comment_approved ]
            ?? "❓ " . __( 'Unknown Status', 'messengernotifier' );

        $message  = "🆕💬 " . sprintf(
            __( 'New Comment on: %s', 'messengernotifier' ),
            get_the_title( $post )
        ) . "\n\n";

        $message .= "🔔 " . __( 'A new comment has been posted on your site.', 'messengernotifier' ) . "\n\n";
        $message .= "👤 " . sprintf(
            __( 'Author: %s', 'messengernotifier' ),
            $comment->comment_author
        ) . "\n";

        $message .= "📌 " . sprintf(
            __( 'Status: %s', 'messengernotifier' ),
            $status_text
        ) . "\n\n";

        $message .= "💬 " . __( 'Comment:', 'messengernotifier' ) . "\n";
        $message .= wp_strip_all_tags( $comment->comment_content ) . "\n\n";

        $message .= "🔗 " . sprintf(
            __( 'View Comment: %s', 'messengernotifier' ),
            get_comment_link( $comment )
        ) . "\n\n";

        // Moderation actions for pending comments
        if ( (int) $comment_approved === 0 ) {

            $message .= "⚠️ " . __( 'This comment is awaiting moderation.', 'messengernotifier' ) . "\n";

            $message .= "✅ " . sprintf(
                __( 'Approve: %s', 'messengernotifier' ),
                admin_url( "comment.php?action=approve&c={$comment->comment_ID}" )
            ) . "\n";

            $message .= "🚫 " . sprintf(
                __( 'Mark as Spam: %s', 'messengernotifier' ),
                admin_url( "comment.php?action=spam&c={$comment->comment_ID}" )
            ) . "\n";

            $message .= "🗑️ " . sprintf(
                __( 'Trash: %s', 'messengernotifier' ),
                admin_url( "comment.php?action=trash&c={$comment->comment_ID}" )
            ) . "\n";
        }

        error_log($message);

        return $message;
    }
}
