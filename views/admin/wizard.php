<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wizard = new MNI_Free_Wizard_Service();
$available = $wizard->get_messengers();
$saved_db_settings = $wizard->get_messenger_settings_from_db();
$available_actions = $wizard->get_actions();

$wc_active = class_exists( 'WooCommerce' );
?>

<div class="wrap mni-wizard-wrapper">
    <h1><?php esc_html_e( 'Messenger Notifier — Setup Wizard', 'messengernotifier' ); ?></h1>

    <form id="mni-wizard-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="mni_free_save_wizard">
        <?php wp_nonce_field( 'mni_free_wizard_save' ); ?>

        <!-- STEP 1 -->
        <div class="mni-step is-active" data-step="1">
            <h2><?php esc_html_e( 'Select Messengers', 'messengernotifier' ); ?></h2>

            <label class="mni-select-all">
                <input type="checkbox" id="mni-select-all-messengers">
                <?php esc_html_e( 'Select All', 'messengernotifier' ); ?>
            </label>

            <div class="mni-checkbox-list">
                <?php foreach ( $available as $id => $info ) : ?>
                    <label>
                        <input type="checkbox"
                               class="mni-messenger-checkbox"
                               name="messengers[]"
                               value="<?php echo esc_attr( $id ); ?>">
                        <?php echo esc_html( $info['label'] ); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="mni-tab-buttons">
                <button type="button" class="button button-primary mni-next-step" disabled>
                    <?php esc_html_e( 'Next', 'messengernotifier' ); ?>
                </button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div class="mni-step" data-step="2">
            <h2><?php esc_html_e( 'Select Actions', 'messengernotifier' ); ?></h2>

            <label class="mni-select-all">
                <input type="checkbox" id="mni-select-all-actions">
                <?php esc_html_e( 'Select All', 'messengernotifier' ); ?>
            </label>

            <div class="mni-checkbox-list">
                <?php foreach ( $available_actions as $act_id => $label ) : ?>
                    <?php
                        $is_wc = str_contains( $act_id, 'woocommerce' );
                        $disabled = ( $is_wc && ! $wc_active );
                    ?>
                    <label class="<?php echo $disabled ? 'mni-disabled' : ''; ?>">
                        <input type="checkbox"
                               class="mni-action-checkbox"
                               name="enabled_actions[]"
                               value="<?php echo esc_attr( $act_id ); ?>"
                               <?php disabled( $disabled ); ?>>
                        <?php echo esc_html( $label ); ?>
                        <?php if ( $disabled ) : ?>
                            <em>(WooCommerce inactive)</em>
                        <?php endif; ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="mni-tab-buttons">
                <button type="button" class="button mni-prev-step"><?php esc_html_e( 'Back', 'messengernotifier' ); ?></button>
                <button type="button" class="button button-primary mni-next-step" disabled>
                    <?php esc_html_e( 'Next', 'messengernotifier' ); ?>
                </button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="mni-step" data-step="3">
            <h2><?php esc_html_e( 'Messenger Configuration', 'messengernotifier' ); ?></h2>

            <div id="mni_messenger_tabs_container">
                <!-- populated by JS -->
            </div>

            <div class="mni-tab-buttons">
                <button type="button" class="button mni-prev-step"><?php esc_html_e( 'Back', 'messengernotifier' ); ?></button>
                <button type="submit" class="button button-primary">
                    <?php esc_html_e( 'Save & Finish', 'messengernotifier' ); ?>
                </button>
            </div>
        </div>

    </form>
</div>
