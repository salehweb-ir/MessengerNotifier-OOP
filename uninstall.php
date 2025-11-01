<?php
/**
 * Uninstall script for Messenger Notifier (Free)
 *
 * @package MessengerNotifierFree
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit; // Prevent direct access
}

/**
 * Because uninstall.php is executed independently,
 * we can’t rely on the plugin’s loader or namespace here.
 *
 * So we’ll write everything procedural and minimal.
 */

$option_keys = json_decode( MNI_FREE_OPTION_KEYS, true );

// Check if uninstall is triggered via WP Admin form
if ( isset( $_POST['mni_free_confirm_uninstall'] ) ) {
	check_admin_referer( 'mni_free_uninstall_action', 'mni_free_uninstall_nonce' );

	$user_choice = sanitize_text_field( wp_unslash( $_POST['mni_free_keep_data'] ?? 'yes' ) );

	if ( 'no' === $user_choice ) {

		// Delete plugin options
		foreach ( $option_keys as $key ) {
			delete_option( $key );
		}

		// Remove anonymous message page if exists
		$page_id = get_option( 'mni_free_pageid' );
		if ( $page_id && get_post( $page_id ) ) {
			wp_delete_post( $page_id, true );
		}

		// Feedback
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-success is-dismissible"><p>'
				. esc_html__( 'Messenger Notifier data deleted successfully.', 'messengernotifier' )
				. '</p></div>';
		} );
	}
	return;
}

/**
 * If uninstall is accessed manually (via WP Admin > Plugins > Delete),
 * show the confirmation form.
 */
if ( current_user_can( 'activate_plugins' ) ) :
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Uninstall Messenger Notifier', 'messengernotifier' ); ?></h1>
		<p><?php esc_html_e( 'Would you like to keep or remove your plugin data?', 'messengernotifier' ); ?></p>

		<form method="post">
			<?php wp_nonce_field( 'mni_free_uninstall_action', 'mni_free_uninstall_nonce' ); ?>

			<p>
				<label>
					<input type="radio" name="mni_free_keep_data" value="yes" checked>
					<?php esc_html_e( 'Keep my plugin settings and anonymous message page.', 'messengernotifier' ); ?>
				</label><br>
				<label>
					<input type="radio" name="mni_free_keep_data" value="no">
					<?php esc_html_e( 'Remove all plugin data and pages permanently.', 'messengernotifier' ); ?>
				</label>
			</p>

			<p>
				<input type="submit" class="button button-primary" name="mni_free_confirm_uninstall" value="<?php esc_attr_e( 'Confirm Uninstall', 'messengernotifier' ); ?>">
			</p>
		</form>
	</div>
	<?php
endif;
