<?php
if ( ! defined( 'ABSPATH' ) ) exit;
?>

<div class="mni-wizard">

    <h1>Messenger Notifier – Setup Wizard</h1>

    <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <?php wp_nonce_field( 'mni_wizard_save', 'mni_wizard_nonce' ); ?>
        <input type="hidden" name="action" value="mni_save_wizard">

        <!-- STEP 1: MESSENGERS -->
        <div class="wizard-step active" data-step="1" data-require="checkbox">

            <h2>Step 1: Select Messengers</h2>

            <label>
                <input type="checkbox" class="mni-select-all">
                <strong>Select All</strong>
            </label>

            <hr>

            <?php
            $messengers = [
                'eitaa'   => 'Eitaa',
                'telegram'=> 'Telegram',
                'whatsapp'=> 'WhatsApp',
            ];

            $saved = (array) get_option( 'mni_free_messengers', [] );
            ?>

            <?php foreach ( $messengers as $id => $label ) : ?>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox"
                           class="mni-checkbox"
                           name="settings[messengers][]"
                           value="<?php echo esc_attr( $id ); ?>"
                           <?php checked( in_array( $id, $saved, true ) ); ?>>
                    <?php echo esc_html( $label ); ?>
                </label>
            <?php endforeach; ?>

            <div class="wizard-nav">
                <button type="button" class="wizard-next">Next</button>
            </div>
        </div>

        <!-- STEP 2: ACTIONS -->
        <div class="wizard-step" data-step="2" data-require="checkbox">

            <h2>Step 2: Select Actions</h2>

            <label>
                <input type="checkbox" class="mni-select-all">
                <strong>Select All</strong>
            </label>

            <hr>

            <?php
            $actions = [
                'comment' => 'New Comment',
                'newuser' => 'New User',
                'order'   => 'WooCommerce Order',
            ];

            $saved_actions = (array) get_option( 'mni_free_actions', [] );
            ?>

            <?php foreach ( $actions as $id => $label ) : ?>
                <label style="display:block;margin-bottom:6px;">
                    <input type="checkbox"
                           class="mni-checkbox"
                           name="settings[actions][]"
                           value="<?php echo esc_attr( $id ); ?>"
                           <?php checked( in_array( $id, $saved_actions, true ) ); ?>>
                    <?php echo esc_html( $label ); ?>
                </label>
            <?php endforeach; ?>

            <div class="wizard-nav">
                <button type="button" class="wizard-prev">Previous</button>
                <button type="submit">Save</button>
            </div>
        </div>

    </form>
</div>

<style>
.mni-wizard { max-width: 700px; background:#fff; padding:20px }
.wizard-step { display:none; min-height:300px }
.wizard-step.active { display:block }
.wizard-nav { margin-top:30px; display:flex; justify-content:space-between }
</style>
