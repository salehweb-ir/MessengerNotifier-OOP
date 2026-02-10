<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$service     = new MNI_Free_Settings_Service();
$messengers  =  $service->get_messengers();
$actions     =  $service->get_actions();
$contact     =  $service->get_contact_page();
$templates   =  MNI_Free_Registry::page_templates();
new MNI_Free_Settings_Controller();
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Messenger Notifier — Settings', 'messengernotifier' ); ?></h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Settings saved successfully.', 'messengernotifier' ); ?></p>
        </div>
    <?php endif; ?>

    <h2 class="nav-tab-wrapper">
        <a href="#contact" class="nav-tab nav-tab-active"><?php esc_html_e( 'Anonymous Page', 'messengernotifier' ); ?></a>
        <a href="#messengers" class="nav-tab"><?php esc_html_e( 'Messengers', 'messengernotifier' ); ?></a>
        <a href="#actions" class="nav-tab"><?php esc_html_e( 'Actions', 'messengernotifier' ); ?></a>
        <a href="#config" class="nav-tab"><?php esc_html_e( 'Configuration', 'messengernotifier' ); ?></a>
    </h2>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="mni_free_save_settings">
        <?php wp_nonce_field( 'mni_free_settings_save' ); ?>

        <!-- ================= Contact Page ================= -->
        <div id="contact" class="mni-settings-section">

            <h2><?php esc_html_e( 'Anonymous Contact Page', 'messengernotifier' ); ?></h2>

            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Page Title', 'messengernotifier' ); ?></th>
                    <td>
                        <input type="text"
                               name="settings[contact_page][title]"
                               value="<?php echo esc_attr( $contact['title'] ); ?>"
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Page Slug', 'messengernotifier' ); ?></th>
                    <td>
                        <input type="text"
                               name="settings[contact_page][slug]"
                               value="<?php echo esc_attr( $contact['slug'] ); ?>"
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php esc_html_e( 'Page Template', 'messengernotifier' ); ?></th>
                    <td>
                        <select name="settings[contact_page][template]">
                            <?php foreach ( $templates as $id => $label ) : ?>
                                <option value="<?php echo esc_attr( $id ); ?>"
                                    <?php selected( $contact['template'], $id ); ?>>
                                    <?php echo esc_html( $label ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

        </div>

        <!-- ================= Messengers ================= -->
        <div id="messengers" class="mni-settings-section" style="margin-top:40px;">

            <h2><?php esc_html_e( 'Messengers', 'messengernotifier' ); ?></h2>

            <?php foreach ( $messengers as $id => $m ) : ?>
                <label style="display:block;margin-bottom:6px;">
                  <input type="checkbox"
                     class="mni-messenger-toggle"
                     data-messenger="<?php echo esc_attr( $id ); ?>"
                     name="settings[messengers][]"
                     value="<?php echo esc_attr( $id ); ?>"
                     <?php checked( $m['active'] ?? false ); ?>>
                     <strong><?php echo esc_html( $m['label'] ); ?></strong>
                </label>
            <?php endforeach; ?>

        </div>

        <!-- ================= Actions ================= -->
        <div id="actions" class="mni-settings-section" style="margin-top:40px;">

            <h2><?php esc_html_e( 'Actions', 'messengernotifier' ); ?></h2>

            <?php foreach ( $actions as $id => $a ) : ?>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox"
                           name="settings[actions][]"
                           value="<?php echo esc_attr( $id ); ?>"
                           <?php checked( $a['active'] ?? false ); ?>>
                    <?php echo esc_html( $a['label'] ); ?>
                </label>
            <?php endforeach; ?>

        </div>

        <!-- ================= Configuration ================= -->
        <div id="config" class="mni-settings-section" style="margin-top:40px;">

            <h2><?php esc_html_e( 'Messenger Configuration', 'messengernotifier' ); ?></h2>

            <?php foreach ( $messengers as $id => $m ) :
                $cfg = $service->get_config( $id );
            ?>
                <fieldset class="mni-messenger-config"
                  data-messenger="<?php echo esc_attr( $id ); ?>"
                  style="border:1px solid #ccd0d4;padding:15px;margin-bottom:20px;">
                    <legend><strong><?php echo esc_html( $m['label'] ); ?></strong></legend>

                    <table class="form-table">
                        <tr>
                            <th><?php esc_html_e( 'API Token', 'messengernotifier' ); ?></th>
                            <td>
                                <input type="text"
                                       name="settings[config][<?php echo esc_attr( $id ); ?>][token]"
                                       value="<?php echo esc_attr( $cfg['token'] ?? '' ); ?>"
                                       class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e( 'Channel ID', 'messengernotifier' ); ?></th>
                            <td>
                                <input type="text"
                                       name="settings[config][<?php echo esc_attr( $id ); ?>][channel]"
                                       value="<?php echo esc_attr( $cfg['channel'] ?? '' ); ?>"
                                       class="regular-text">
                            </td>
                        </tr>
                    </table>
                </fieldset>
            <?php endforeach; ?>

        </div>
        <?php submit_button(); ?>
    </form>
</div>
