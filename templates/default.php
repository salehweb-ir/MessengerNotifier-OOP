<?php
/**
 * Default Anonymous Form Template
 *
 * This template is displayed via the [mni_free_anonymous_form] shortcode.
 *
 * @package MNI_FREE\Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="mni-free-page-content">
    <h1><?php esc_html_e( 'Submit Your Message', 'messengernotifier' ); ?></h1>

    <form method="post" class="mni-free-form" enctype="multipart/form-data">
        <?php wp_nonce_field( 'mni_free_nonce_action', 'mni_free_nonce' ); ?>

        <label for="mni-free-message"><?php esc_html_e( 'Your Message', 'messengernotifier' ); ?></label>
        <textarea
            id="mni-free-message"
            name="message"
            required
            placeholder="<?php esc_attr_e( 'Type your message here...', 'messengernotifier' ); ?>"
        ></textarea>

        <input
            type="submit"
            name="mni_free_submit"
            value="<?php esc_attr_e( 'Send Message', 'messengernotifier' ); ?>"
            class="mni-free-button mni-free-button-primary"
        >
    </form>
</div>
