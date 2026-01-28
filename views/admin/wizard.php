<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/** @var mni_free_wizard $wizard */
$wizard = new MNI_Free_Wizard_Service();
$available = $wizard->get_messengers();
$saved_db_settings = $wizard->get_messenger_settings_from_db();
$available_actions = $wizard->get_actions();
?>

<div class="wrap mni-wizard-wrapper">
    <h1><?php esc_html_e( 'Messenger Notifier — Setup Wizard', 'messengernotifier' ); ?></h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Settings saved successfully.', 'messengernotifier' ); ?></p>
        </div>
    <?php endif; ?>

    <form id="mni-wizard-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" id="mni_selected_messengers" name="mni_selected_messengers" value="">
        <input type="hidden" name="action" value="mni_free_save_wizard">
        <?php wp_nonce_field( 'mni_free_wizard_save' ); ?>

        <!-- STEP 1 -->
        <div class="mni-step" data-step="1">
            <h2><?php esc_html_e( 'General — Select Messengers', 'messengernotifier' ); ?></h2>
            <p><?php esc_html_e( 'Choose which messengers to activate. At least one must remain active.', 'messengernotifier' ); ?></p>

            <div class="mni-checkbox-list">

                <!-- check all checkbox  -->
                <label class="mni-check-all">
                    <input type="checkbox" id="mni-check-all-messengers">
                    <?php esc_html_e( 'Select all messengers', 'messengernotifier' ); ?>
                </label>

                <?php foreach ( $available as $id => $info ) : ?>
                    <label>
                        <input  type="checkbox"
                                class="mni-messenger-checkbox"
                                name="settings[messengers][]"
                                value="<?php echo esc_attr( $id ); ?>"
                                <?php checked( in_array( $id, (array) get_option( 'mni_free_messengers', array( 'eitaa' ) ), true ) ); ?>>
                        <?php echo esc_html( $info['label'] );?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="mni-tab-buttons">
                <button type="button" class="button button-primary mni-next-step"><?php esc_html_e( 'Next', 'messengernotifier' ); ?></button>
            </div>
        </div>

        <!-- STEP 2 -->
        <div class="mni-step" data-step="2" style="display:none;">
            <h2><?php esc_html_e( 'Actions', 'messengernotifier' ); ?></h2>
            <p><?php esc_html_e( 'Choose which WordPress events should send notifications.', 'messengernotifier' ); ?></p>

            <div class="mni-checkbox-list">

                <!-- check all checkbox -->
                <label class="mni-check-all">
                    <input type="checkbox" id="mni-check-all-actions">
                    <?php esc_html_e( 'Select all actions', 'messengernotifier' ); ?>
                </label>

                <?php foreach ( $available_actions as $act_id => $label ) : ?>
                    <label>
                        <input  type="checkbox"
                                class="mni-action-checkbox"
                                name="settings[actions][]"
                                value="<?php echo esc_attr( $act_id ); ?>"
                                <?php checked( in_array( $act_id, (array) get_option( 'mni_free_enabled_actions', array() ), true ) ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="mni-tab-buttons">
                <button type="button" class="button mni-prev-step"><?php esc_html_e( 'Back', 'messengernotifier' ); ?></button>
                <button type="button" class="button button-primary mni-next-step"><?php esc_html_e( 'Next', 'messengernotifier' ); ?></button>
            </div>
        </div>

        <!-- STEP 3 -->
        <div class="mni-step" data-step="3" style="display:none;">
            <h2><?php esc_html_e( 'Messenger Settings', 'messengernotifier' ); ?></h2>
            <p><?php esc_html_e( 'Only messengers you selected in step 1 appear here (before saving).', 'messengernotifier' ); ?></p>

            <div id="mni_messenger_tabs_container">
                <!-- JS will populate tabs & content based on the hidden JSON field -->
                <!-- For accessibility, we also output saved DB settings for active messengers as fallback -->
                <?php
                $db_active = (array) get_option( 'mni_free_messengers', array( 'eitaa' ) );
                foreach ( $db_active as $msgr ) :
                    if ( ! isset( $available[ $msgr ] ) ) continue;
                    $s = isset( $saved_db_settings[ $msgr ] ) ? $saved_db_settings[ $msgr ] : array();
                ?>
                    <div class="mni-messenger-panel" data-msgr="<?php echo esc_attr( $msgr ); ?>">
                        <h3><?php echo esc_html( $available[ $msgr ]['label'] ); ?></h3>

                        <label><?php esc_html_e( 'API Token', 'messengernotifier' ); ?></label>
                        <input type="text" name="messenger_settings[<?php echo esc_attr( $msgr ); ?>][token]"
                               value="<?php echo esc_attr( $s['token'] ?? '' ); ?>" class="mni-input">

                        <label><?php esc_html_e( 'Channel ID', 'messengernotifier' ); ?></label>
                        <input type="text" name="messenger_settings[<?php echo esc_attr( $msgr ); ?>][channel]"
                               value="<?php echo esc_attr( $s['channel'] ?? '' ); ?>" class="mni-input">

                        <label><?php esc_html_e( 'Test Message (optional)', 'messengernotifier' ); ?></label>
                        <textarea name="messenger_settings[<?php echo esc_attr( $msgr ); ?>][test]" class="mni-textarea"></textarea>

                        <p>
                            <button type="button" class="button mni-test-api" data-msgr="<?php echo esc_attr( $msgr ); ?>">
                                <?php esc_html_e( 'Test API', 'messengernotifier' ); ?>
                            </button>
                            <span class="mni-test-result" id="mni-test-<?php echo esc_attr( $msgr ); ?>"></span>
                        </p>

                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mni-tab-buttons">
                <button type="button" class="button mni-prev-step"><?php esc_html_e( 'Back', 'messengernotifier' ); ?></button>
                <button type="submit" class="button button-primary"><?php esc_html_e( 'Save & Finish', 'messengernotifier' ); ?></button>
            </div>
        </div>

    </form>
</div>