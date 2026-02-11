<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="mni-template-minimal">
    <form method="post" class="mni-contact-form">
        <?php wp_nonce_field( 'mni_send_message', 'mni_nonce' ); ?>

        <textarea name="mni_message" placeholder="Write your message..." required></textarea>

        <button type="submit">
            <?php esc_html_e( 'Send', 'messengernotifier' ); ?>
        </button>
    </form>
</div>
