<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = get_option( 'mni_free_settings', [] );
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Messenger Notifier Settings', 'messengernotifier' ); ?></h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Settings saved.', 'messengernotifier' ); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="mni_free_save_settings">
        <?php wp_nonce_field( 'mni_free_save_settings' ); ?>

        <!-- Messengers -->
        <h2><?php esc_html_e( 'Active Messengers', 'messengernotifier' ); ?></h2>

        <?php foreach ( ( $settings['messengers'] ?? [] ) as $messenger ) : ?>
            <label style="display:block">
                <input type="checkbox"
                       name="settings[messengers][]"
                       value="<?php echo esc_attr( $messenger ); ?>"
                       checked>
                <?php echo esc_html( ucfirst( $messenger ) ); ?>
            </label>
        <?php endforeach; ?>

        <!-- Actions -->
        <h2><?php esc_html_e( 'Enabled Actions', 'messengernotifier' ); ?></h2>

        <?php foreach ( ( $settings['actions'] ?? [] ) as $action ) : ?>
            <label style="display:block">
                <input type="checkbox"
                       name="settings[actions][]"
                       value="<?php echo esc_attr( $action ); ?>"
                       checked>
                <?php echo esc_html( ucfirst( $action ) ); ?>
            </label>
        <?php endforeach; ?>

        <!-- Config -->
        <h2><?php esc_html_e( 'Messenger Configuration', 'messengernotifier' ); ?></h2>

        <?php foreach ( ( $settings['config'] ?? [] ) as $msgr => $conf ) : ?>
            <h3><?php echo esc_html( ucfirst( $msgr ) ); ?></h3>

            <label>Token</label><br>
            <input type="text"
                   name="settings[config][<?php echo esc_attr( $msgr ); ?>][token]"
                   value="<?php echo esc_attr( $conf['token'] ?? '' ); ?>"
                   class="regular-text"><br><br>

            <label>Channel</label><br>
            <input type="text"
                   name="settings[config][<?php echo esc_attr( $msgr ); ?>][channel]"
                   value="<?php echo esc_attr( $conf['channel'] ?? '' ); ?>"
                   class="regular-text">
        <?php endforeach; ?>

        <p>
            <button type="submit" class="button button-primary">
                <?php esc_html_e( 'Save Settings', 'messengernotifier' ); ?>
            </button>
        </p>

    </form>
</div>
