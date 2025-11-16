<?php
if (!defined('ABSPATH')) exit;

$wizard = mni_free_wizard::instance();
?>

<div class="mni-wizard">

    <h1><?php _e('Messenger Notifier – Setup Wizard', 'messengernotifier'); ?></h1>

    <?php if (isset($_GET['saved'])): ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e('Settings saved successfully!', 'messengernotifier'); ?></p>
        </div>
    <?php endif; ?>


    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" class="mni-wizard-form">

        <input type="hidden" name="action" value="mni_free_save_wizard">
        <?php wp_nonce_field('mni_free_wizard_nonce'); ?>


        <!-- TABS -->
        <div class="mni-tabs-wrapper">

            <!-- TAB BUTTONS -->
            <div class="mni-tab-buttons">
                <button type="button" data-tab="general"   class="mni-tab-btn active">General</button>
                <button type="button" data-tab="contact"   class="mni-tab-btn">Contact Page</button>
                <button type="button" data-tab="actions"   class="mni-tab-btn">Actions</button>
                <button type="button" data-tab="messenger" class="mni-tab-btn">Messenger</button>
            </div>


            <!-- GENERAL TAB -->
            <div class="mni-tab-content active" id="mni-tab-general">
                <h2>Select Messengers</h2>

                <?php foreach ($wizard->get_messengers() as $key => $messenger): ?>
                    <label class="mni-checkbox">
                        <input type="checkbox"
                               name="messengers[]"
                               value="<?php echo esc_attr($key); ?>"
                               <?php checked(in_array($key, $settings['messengers'] ?? [])); ?>>
                        <?php echo esc_html($messenger['title']); ?>
                    </label>
                <?php endforeach; ?>
            </div>


            <!-- CONTACT TAB -->
            <div class="mni-tab-content" id="mni-tab-contact">
                <h2>Anonymous Contact Page</h2>

                <label>Page Title</label>
                <input type="text" name="contact_title" class="mni-input"
                       value="<?php echo esc_attr($settings['contact_title'] ?? ''); ?>">

                <label>Slug</label>
                <input type="text" name="contact_slug" class="mni-input"
                       value="<?php echo esc_attr($settings['contact_slug'] ?? ''); ?>">
            </div>


            <!-- ACTIONS TAB -->
            <div class="mni-tab-content" id="mni-tab-actions">
                <h2>Enable Actions</h2>

                <?php foreach ($wizard->get_actions() as $key => $label): ?>
                    <label class="mni-checkbox">
                        <input type="checkbox"
                               name="actions[]"
                               value="<?php echo esc_attr($key); ?>"
                               <?php checked(in_array($key, $settings['actions'] ?? [])); ?>>
                        <?php echo esc_html($label); ?>
                    </label>
                <?php endforeach; ?>
            </div>


            <!-- MESSENGERS TAB -->
            <div class="mni-tab-content" id="mni-tab-messenger">
                <h2>Eitaa Settings</h2>

                <label>API Token</label>
                <input type="text" name="eitaa_token" class="mni-input"
                       value="<?php echo esc_attr($settings['eitaa']['token'] ?? ''); ?>">

                <label>Channel ID</label>
                <input type="text" name="eitaa_channel" class="mni-input"
                       value="<?php echo esc_attr($settings['eitaa']['channel'] ?? ''); ?>">
            </div>

        </div>


        <button type="submit" class="button button-primary mni-save-btn">
            Save Settings
        </button>

    </form>
</div>

