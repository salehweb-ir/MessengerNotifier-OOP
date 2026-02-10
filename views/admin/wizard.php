<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wizard = new MNI_Free_Wizard_Service();
$all_messengers = $wizard->get_messengers();
$all_actions = $wizard->get_actions();
?>

<div class="mni-wizard">

<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
<?php wp_nonce_field( 'mni_free_wizard_save' ); ?>
<input type="hidden" id="mni_selected_messengers" name="mni_selected_messengers" value="">
<input type="hidden" id="mni_selected_actions" name="mni_selected_actions" value="">
<input type="hidden" name="action" value="mni_free_save_wizard">

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
            <?php echo esc_html( $info['label'] ); ?>
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

    <?php foreach ( $all_actions as $act_id => $action ) : ?>
          <?php $available = mni_is_requirement_met( $action['requires'] ); ?>
      
          <label style="display:block; opacity: <?php echo $available ? '1' : '0.5'; ?>">
              <input type="checkbox"
                     class="mni-checkbox mni-action"
                     name="settings[actions][]"
                     value="<?php echo esc_attr( $act_id ); ?>"
                     <?php disabled( ! $available ); ?>>
      
              <?php echo esc_html( $action['label'] ); ?>
      
              <?php if ( ! $available ) : ?>
                  <small style="color:#c00">
                      (WooCommerce is not active)
                  </small>
              <?php endif; ?>
          </label>
      <?php endforeach; ?>


    <div class="wizard-nav">
        <button type="button" class="wizard-prev">Previous</button>
        <button type="button" class="wizard-next">Next</button>
    </div>
</div>

<!-- STEP 3 -->
<div class="wizard-step" data-step="3">
    <h2><?php esc_html_e( 'Anonymous Message Page', 'messengernotifier' ); ?></h2>
    <p><?php esc_html_e( 'Configure the anonymous message page.', 'messengernotifier' ); ?></p>

    <label><?php esc_html_e( 'Page Title', 'messengernotifier' ); ?></label>
    <input type="text"
           name="settings[contact_page][title]"
           value="<?php echo esc_attr( $page_cfg['title'] ?? '' ); ?>"
           class="mni-input">

    <label><?php esc_html_e( 'Page Slug', 'messengernotifier' ); ?></label>
    <input type="text"
           name="settings[contact_page][slug]"
           value="<?php echo esc_attr( $page_cfg['slug'] ?? '' ); ?>"
           class="mni-input">

    <label><?php esc_html_e( 'Template', 'messengernotifier' ); ?></label>
    <select name="settings[contact_page][template]" class="mni-select">
    
        <?php
        // گرفتن آرایه template ها
        $templates = MNI_Free_Registry::page_templates();
    
        foreach ( $templates as $key => $label ) : ?>
            <option value="<?php echo esc_attr( $key ); ?>">
                <?php echo esc_html( $label ); ?>
            </option>
        <?php endforeach; ?>
    </select>


    <div class="wizard-nav">
        <button type="button" class="wizard-prev">Previous</button>
        <button type="button" class="wizard-next">Next</button>
    </div>
</div>


<!-- STEP 4 -->
<div class="wizard-step" data-step="4">
    <h2>Step 3: Messenger Configuration</h2>

    <div id="mni-messenger-configs">
        <!-- JS injects configs here -->
    </div>

    <div class="wizard-nav">
        <button type="button" class="wizard-prev">Previous</button>
        <?php submit_button(); ?>
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
