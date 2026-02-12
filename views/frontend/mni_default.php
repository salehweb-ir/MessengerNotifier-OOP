<?php
    if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap mni-free-contact-form">
    <form id="mni-anonymous-contact-form" class="mni-free-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('mni-form_nonce_action', 'mni-form_nonce'); ?>
        <label for="mni-free-message"><?php esc_html_e('Your Message', 'messengernotifier'); ?></label>
        <textarea id="mni-free-message" class="mni-textarea" name="message" required></textarea>
        <input type="submit" name="submit" class="mni-button mni-button-primary" value="<?php esc_html_e('Submit', 'messengernotifier'); ?>">
    </form>
</div>