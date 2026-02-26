<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$service     = new MNI_Free_Settings_Service();
$messengers  = $service->get_messengers();
$actions     = $service->get_actions();
$contact     = $service->get_contact_page();
$templates   = MNI_Free_Registry::page_templates();
new MNI_Free_Settings_Controller();

$page_id     = $contact['id'] ?? 0;
$page_obj    = $page_id ? get_post( $page_id ) : null;
$current_tpl = $page_id ? get_post_meta( $page_id, 'mni_contact_template', true ) : 'default';
?>

<div class="wrap">
    <h1>Messenger Notifier — Settings</h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p>Settings saved successfully.</p>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="mni_free_save_settings">
        <?php wp_nonce_field( 'mni_free_settings_save' ); ?>

        <h2 class="nav-tab-wrapper">
            <a href="#tab-contact" class="nav-tab nav-tab-active">Contact Page</a>
            <a href="#tab-messengers" class="nav-tab">Messengers</a>
            <a href="#tab-actions" class="nav-tab">Actions</a>
            <a href="#tab-config" class="nav-tab">Configuration</a>
        </h2>

        <!-- ================= Contact Page Card ================= -->
        <div id="tab-contact" class="mni-tab-content" style="display:block;margin-top:20px;">
            <div class="postbox-header">
                <h2 class="hndle">Anonymous Contact Page</h2>
            </div>

            <div class="inside">

                <?php if ( $page_obj ) : ?>

                    <p>
                        <strong>Title:</strong>
                        <?php echo esc_html( $page_obj->post_title ); ?>
                    </p>

                    <p>
                        <strong>Slug:</strong>
                        <?php echo esc_html( $page_obj->post_name ); ?>
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <?php echo esc_html( ucfirst( $page_obj->post_status ) ); ?>
                    </p>

                    <p>
                        <a class="button button-secondary"
                           href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>">
                            Edit Page
                        </a>

                        <a class="button"
                           target="_blank"
                           href="<?php echo esc_url( get_permalink( $page_id ) ); ?>">
                            View Page
                        </a>
                    </p>

                <?php else : ?>

                    <p style="color:#b32d2e;">
                        Contact page not found. Please run the wizard again.
                    </p>

                <?php endif; ?>

                <hr>

                <table class="form-table">
                    <tr>
                        <th scope="row">Page Template</th>
                        <td>
                            <select name="settings[contact_page][template]">

                                <?php foreach ( $templates as $id => $label ) : ?>
                                    <option value="<?php echo esc_attr( $id ); ?>"
                                        <?php selected( $current_tpl, $id ); ?>>
                                        <?php echo esc_html( $label ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>

                <input type="hidden"
                       name="settings[contact_page][id]"
                       value="<?php echo esc_attr( $page_id ); ?>">

            </div>
        </div>

        <!-- ================= Messengers ================= -->
        <div id="tab-messengers" class="mni-tab-content" style="display:none;margin-top:20px;">
            <div class="postbox-header">
                <h2 class="hndle">Messengers</h2>
            </div>
            <div class="inside">

                <?php foreach ( $messengers as $id => $m ) : ?>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox"
                               name="settings[messengers][]"
                               value="<?php echo esc_attr( $id ); ?>"
                               <?php checked( $m['active'] ?? false ); ?>>
                        <strong><?php echo esc_html( $m['label'] ); ?></strong>
                    </label>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- ================= Actions ================= -->
        <div id="tab-actions" class="mni-tab-content" style="display:none;margin-top:20px;">
            <div class="postbox-header">
                <h2 class="hndle">Actions</h2>
            </div>
            <div class="inside">

                <?php foreach ( $actions as $id => $a ) : ?>
                    <label style="display:block;margin-bottom:8px;">
                        <input type="checkbox"
                               name="settings[actions][]"
                               value="<?php echo esc_attr( $id ); ?>"
                               <?php checked( $a['active'] ?? false ); ?>>
                        <?php echo esc_html( $a['label'] ); ?>
                    </label>
                <?php endforeach; ?>

            </div>
        </div>

        <!-- ================= Config ================= -->
        <div id="tab-config" class="mni-tab-content" style="display:none;margin-top:20px;">
            <div class="postbox-header">
                <h2 class="hndle">Messenger Configuration</h2>
            </div>
            <div class="inside">

                <?php foreach ( $messengers as $id => $m ) :
                    if ( ! $m['active'] ?? false ) {
                        continue;
                    }
                    $cfg = $service->get_config( $id );
                ?>
                    <h4><?php echo esc_html( $m['label'] ); ?></h4>

                    <table class="form-table">
                        <tr>
                            <th>API Token</th>
                            <td>
                                <input type="text"
                                       class="regular-text"
                                       name="settings[config][<?php echo esc_attr( $id ); ?>][token]"
                                       value="<?php echo esc_attr( $cfg['token'] ?? '' ); ?>">
                            </td>
                        </tr>
                        <tr>
                            <th>Channel ID</th>
                            <td>
                                <input type="text"
                                       class="regular-text"
                                       name="settings[config][<?php echo esc_attr( $id ); ?>][channel]"
                                       value="<?php echo esc_attr( $cfg['channel'] ?? '' ); ?>">
                            </td>
                        </tr>
                    </table>

                    <hr>

                <?php endforeach; ?>

            </div>
        </div>

        <?php submit_button(); ?>

    </form>
</div>


<script>
document.addEventListener('DOMContentLoaded', function () {
    const tabs = document.querySelectorAll('.nav-tab');
    const contents = document.querySelectorAll('.mni-tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', function (e) {
            e.preventDefault();

            // activate the clicked tab
            tabs.forEach(t => t.classList.remove('nav-tab-active'));
            tab.classList.add('nav-tab-active');

            // show corresponding content
            contents.forEach(c => c.style.display = 'none');
            document.querySelector(tab.getAttribute('href')).style.display = 'block';
        });
    });
});
</script>
