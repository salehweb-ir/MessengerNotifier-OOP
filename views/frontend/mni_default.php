<?php
    if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="wrap mni-free-default-form">
    <form id="mni-default-anonymous-form" class="mni-free-form" method="post" enctype="multipart/form-data">
        <?php wp_nonce_field('mni-form_nonce_action', 'mni-form_nonce'); ?>
        <label for="mni-free-default-message"><?php esc_html_e('Your Message
        (default)', 'messengernotifier'); ?></label>
        <textarea id="mni-free-default-message" class="mni-textarea" name="message" required style="width: 100%;"></textarea>
        <input type="submit" name="submit" class="mni-button mni-button-primary" value="<?php esc_html_e('Submit', 'messengernotifier'); ?>">
    </form>
</div>