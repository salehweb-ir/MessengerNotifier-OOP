<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/traits/singleton.php';

class mni_free_comment {
    use mni_singleton;

    private function __construct() {
        if ( get_option( 'mni_free_action_comment', 1 ) ) {
            add_action( 'comment_post', [ $this, 'notify_new_comment' ], 10, 3 );
        }
    }

    /**
     * Notify when a new comment is posted
     */
    public function notify_new_comment( $comment_id, $comment_approved, $commentdata ) {

        if ( $comment_approved != 1 ) {
            return; // Notify only approved comments
        }

        $comment = get_comment( $comment_id );
        if ( ! $comment ) {
            return;
        }

        $post_title = get_the_title( $comment->comment_post_ID );
        $author     = sanitize_text_field( $comment->comment_author );
        $content    = sanitize_textarea_field( $comment->comment_content );
        $url        = get_comment_link( $comment_id );

        $message  = "💬 <b>New Comment</b>\n";
        $message .= "📝 Post: {$post_title}\n";
        $message .= "👤 Author: {$author}\n";
        $message .= "🗒️ Content:\n{$content}\n";
        $message .= "🔗 {$url}";

        $hashtag = "#comment";

        $messenger_manager = mni_free_messenger_manager::instance();
        $messenger_manager->send( $message . "\n\n" . $hashtag );
    }
}
