<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wizard = new MNI_Free_Wizard_Service();
$all_messengers = $wizard->get_messengers();
$saved_db_settings = $wizard->get_messenger_settings_from_db();
$all_actions = $wizard->get_actions();

?>

<div class="mni-wizard">

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mni_wizard_save', 'mni_wizard_nonce' ); ?>
<input type="hidden" name="action" value="mni_save_wizard">

<!-- STEP 1 -->
<div class="wizard-step active" data-step="1" data-require="checkbox">
    <h2>Step 1: Select Messengers</h2>

    <label>
        <input type="checkbox" class="mni-select-all">
        <strong>Select All</strong>
    </label>
    <hr>

    <?php foreach ( $all_messengers as $id => $info ) : ?>
        <label style="display:block">
            <input type="checkbox"
                   class="mni-checkbox mni-messenger"
                   name="settings[messengers][]"
                   value="<?php echo esc_attr( $id ); ?>">
            <?php error_log($info); echo esc_html( $info ); ?>
        </label>
    <?php endforeach; ?>

    <div class="wizard-nav">
        <button type="button" class="wizard-next">Next</button>
    </div>
</div>

<!-- STEP 2 -->
<div class="wizard-step" data-step="2" data-require="checkbox">
    <h2>Step 2: Select Actions</h2>

    <label>
        <input type="checkbox" class="mni-select-all">
        <strong>Select All</strong>
    </label>
    <hr>

    <?php foreach ( $all_actions as $act_id => $label ) : ?>
        <label style="display:block">
            <input type="checkbox"
                   class="mni-checkbox mni-action"
                   name="settings[actions][]"
                   value="<?php echo esc_attr( $id ); ?>">
            <?php echo esc_html( $label ); ?>
        </label>
    <?php endforeach; ?>

    <div class="wizard-nav">
        <button type="button" class="wizard-prev">Previous</button>
        <button type="button" class="wizard-next">Next</button>
    </div>
</div>

<!-- STEP 3 -->
<div class="wizard-step" data-step="3">
    <h2>Step 3: Messenger Configuration</h2>

    <div id="mni-messenger-configs">
        <!-- JS injects configs here -->
    </div>

    <div class="wizard-nav">
        <button type="button" class="wizard-prev">Previous</button>
        <button type="submit">Save Settings</button>
    </div>
</div>

</form>
</div>

<style>
.mni-wizard { max-width:720px;background:#fff;padding:20px }
.wizard-step { display:none; min-height:300px }
.wizard-step.active { display:block }
.wizard-nav { margin-top:30px; display:flex; justify-content:space-between }
</style>
