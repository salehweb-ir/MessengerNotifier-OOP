<?php
/**
 * Setup Wizard for Messenger Notifier Free
 *
 * @package MessengerNotifier
 */

namespace MNI_FREE\Includes;

use MNI_FREE\Includes\Messenger_Manager;

defined( 'ABSPATH' ) || exit;

class Wizard {

	/**
	 * Option key to detect if setup is complete.
	 */
	private const OPTION_SETUP_COMPLETE = 'mni_free_setup_complete';

	/**
	 * Init hooks.
	 */
	public function init() {
		register_activation_hook( MNI_FREE_PLUGIN_FILE, [ $this, 'on_activation' ] );
		add_action( 'admin_menu', [ $this, 'register_wizard_page' ] );
		add_action( 'admin_post_mni_free_wizard_save', [ $this, 'save_settings' ] );
	}

	/**
	 * Run on plugin activation.
	 */
	public function on_activation() {
		if ( ! get_option( self::OPTION_SETUP_COMPLETE ) ) {
			add_option( self::OPTION_SETUP_COMPLETE, false );
			wp_safe_redirect( admin_url( 'admin.php?page=mni-free-setup' ) );
			exit;
		}
	}

	/**
	 * Register temporary wizard page.
	 */
	public function register_wizard_page() {
		if ( get_option( self::OPTION_SETUP_COMPLETE ) ) {
			return;
		}

		add_menu_page(
			__( 'Messenger Notifier Setup', 'messengernotifier' ),
			__( 'Messenger Setup', 'messengernotifier' ),
			'manage_options',
			'mni-free-setup',
			[ $this, 'render_setup_page' ],
			'dashicons-format-chat',
			2
		);
	}

	/**
	 * Render setup form.
	 */
	public function render_setup_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$messengers = (new Messenger_Manager)->init_active_messengers();
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Messenger Notifier – Setup Wizard', 'messengernotifier' ); ?></h1>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<?php wp_nonce_field( 'mni_free_wizard_save' ); ?>
				<input type="hidden" name="action" value="mni_free_wizard_save">

				<p><?php esc_html_e( 'Enter your API tokens and IDs for the messengers you want to activate.', 'messengernotifier' ); ?></p>

				<table class="form-table" role="presentation">
					<tbody>
					<?php foreach ( $messengers as $slug => $label ) : ?>
						<tr>
							<th scope="row"><?php echo esc_html( $label ); ?></th>
							<td>
								<label for="token_<?php echo esc_attr( $slug ); ?>">
									<?php esc_html_e( 'API Token:', 'messengernotifier' ); ?>
								</label><br>
								<input type="text" name="token_<?php echo esc_attr( $slug ); ?>" id="token_<?php echo esc_attr( $slug ); ?>" class="regular-text" value="">
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>

				<p>
					<label>
						<input type="checkbox" name="create_anon_page" value="1" checked>
						<?php esc_html_e( 'Create anonymous contact page automatically', 'messengernotifier' ); ?>
					</label>
				</p>

				<?php submit_button( __( 'Finish Setup', 'messengernotifier' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Save the submitted settings.
	 */
	public function save_settings() {
		check_admin_referer( 'mni_free_wizard_save' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Access denied.', 'messengernotifier' ) );
		}

		$messengers = Messenger_Manager::init_active_messengers();

		foreach ( $messengers as $slug => $label ) {
			$token_key = "messengernotifier_token_{$slug}_api";
			$token_val = isset( $_POST[ "token_{$slug}" ] ) ? sanitize_text_field( wp_unslash( $_POST[ "token_{$slug}" ] ) ) : '';
			update_option( $token_key, $token_val );
		}

		if ( ! empty( $_POST['create_anon_page'] ) ) {
			$this->create_anonymous_page();
		}

		update_option( self::OPTION_SETUP_COMPLETE, true );

		wp_safe_redirect( admin_url( 'admin.php?page=mni-free-settings&setup=done' ) );
		exit;
	}

	/**
	 * Create anonymous contact page.
	 */
	private function create_anonymous_page() {
		$page_exists = get_page_by_path( 'anonymous-message' );
		if ( $page_exists ) {
			return;
		}

		$page_id = wp_insert_post( [
			'post_title'   => __( 'Anonymous Message', 'messengernotifier' ),
			'post_name'    => 'anonymous-message',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_content' => '[mni_free_anonymous_form]',
		] );

		if ( $page_id && ! is_wp_error( $page_id ) ) {
			update_option( 'mni_free_anon_page_id', $page_id );
		}
	}
}
