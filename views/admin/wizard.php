<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$wizard = mni_free_wizard::instance();
$settings = $wizard->get_settings();

$action_name  = 'mni_free_save_wizard';
$nonce_action = 'mni_free_wizard_action';
$nonce_name   = 'mni_free_wizard_nonce';
?>

<div class="mni-wizard-card">

    <h1><?php esc_html_e( 'Messenger Notifier — Setup Wizard', 'messengernotifier' ); ?></h1>

    <?php if ( isset( $_GET['saved'] ) ) : ?>
        <div class="notice notice-success is-dismissible">
            <p><?php esc_html_e( 'Settings saved successfully.', 'messengernotifier' ); ?></p>
        </div>
    <?php endif; ?>

    <form id="mni-wizard-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
        <input type="hidden" name="action" value="<?php echo esc_attr( $action_name ); ?>">
        <?php wp_nonce_field( $nonce_action, $nonce_name ); ?>

        <!-- Step indicators -->
        <div class="mni-steps">
            <div class="mni-step-indicator active" data-step="1"><?php esc_html_e( 'General', 'messengernotifier' ); ?></div>
            <div class="mni-step-indicator" data-step="2"><?php esc_html_e( 'Anonymous contact page', 'messengernotifier' ); ?></div>
            <div class="mni-step-indicator" data-step="3"><?php esc_html_e( 'Actions & Messenger settings', 'messengernotifier' ); ?></div>
        </div>

        <!-- STEP 1 -->
        <div class="mni-step" id="mni-step-1">
            <h2><?php esc_html_e( 'General', 'messengernotifier' ); ?></h2>

            <p><strong><?php esc_html_e( 'Select messengers', 'messengernotifier' ); ?></strong></p>

            <?php foreach ( $wizard->get_messengers() as $key => $m ) : ?>
                <label class="mni-checkbox">
                    <input type="checkbox" name="messengers[]" value="<?php echo esc_attr( $key ); ?>"
                        <?php checked( in_array( $key, (array) $settings['messengers'], true ) ); ?>>
                    <?php echo esc_html( $m['label'] ?? $m['title'] ?? $key ); ?>
                </label>
            <?php endforeach; ?>

            <p>
                <button type="button" class="button button-primary mni-next">
                    <?php esc_html_e( 'Next', 'messengernotifier' ); ?>
                </button>
            </p>
        </div>

        <!-- STEP 2 -->
        <div class="mni-step" id="mni-step-2" style="display:none;">
            <h2><?php esc_html_e( 'Anonymous contact page', 'messengernotifier' ); ?></h2>

            <label><?php esc_html_e( 'Page title', 'messengernotifier' ); ?></label>
            <input type="text" name="contact_title" class="mni-input"
                   value="<?php echo esc_attr( $settings['contact_title'] ?? '' ); ?>">

            <label><?php esc_html_e( 'Page slug', 'messengernotifier' ); ?></label>
            <input type="text" name="contact_slug" class="mni-input"
                   value="<?php echo esc_attr( $settings['contact_slug'] ?? '' ); ?>">

            <p>
                <button type="button" class="button mni-back"><?php esc_html_e( 'Back', 'messengernotifier' ); ?></button>
                <button type="button" class="button button-primary mni-next"><?php esc_html_e( 'Next', 'messengernotifier' ); ?></button>
            </p>
        </div>

        <!-- STEP 3 -->
        <div class="mni-step" id="mni-step-3" style="display:none;">
            <h2><?php esc_html_e( 'Actions & Messenger settings', 'messengernotifier' ); ?></h2>

            <p><strong><?php esc_html_e( 'Enable Actions', 'messengernotifier' ); ?></strong></p>

            <?php foreach ( $wizard->get_actions() as $k => $label ) : ?>
                <label class="mni-checkbox">
                    <input type="checkbox" name="actions[]" value="<?php echo esc_attr( $k ); ?>"
                        <?php checked( in_array( $k, (array) $settings['actions'], true ) ); ?>>
                    <?php echo esc_html( $label ); ?>
                </label>
            <?php endforeach; ?>

            <hr>

            <h3><?php esc_html_e( 'Eitaa settings', 'messengernotifier' ); ?></h3>

            <label><?php esc_html_e( 'API Token', 'messengernotifier' ); ?></label>
            <input type="text" name="eitaa_token" class="mni-input"
                   value="<?php echo esc_attr( $settings['eitaa']['token'] ?? '' ); ?>">

            <label><?php esc_html_e( 'Channel ID', 'messengernotifier' ); ?></label>
            <input type="text" name="eitaa_channel" class="mni-input"
                   value="<?php echo esc_attr( $settings['eitaa']['channel'] ?? '' ); ?>">

            <p>
                <button type="button" class="button mni-back"><?php esc_html_e( 'Back', 'messengernotifier' ); ?></button>
                <button type="submit" class="button button-primary"><?php esc_html_e( 'Save & Finish', 'messengernotifier' ); ?></button>
            </p>
        </div>
    </form>
</div>

<!-- WIZARD CSS -->
<style>
.mni-wizard-card { max-width:940px; background:#fff; padding:22px; margin-top:20px; border-radius:8px; }
.mni-steps { display:flex; gap:10px; margin-bottom:18px; }
.mni-step-indicator { /* width:34px; height:34px; */ border-radius:15%; padding: 15px; background:#eee; display:flex; align-items:center; justify-content:center; font-weight:700; cursor:pointer; }
.mni-step-indicator.active { background:#2271b1; color:#fff; }
.mni-input { width:100%; padding:8px; margin:6px 0 14px; border-radius:4px; border:1px solid #ccd0d4; }
.mni-checkbox { display:block; margin:6px 0; }
</style>

<!-- WIZARD JS -->
<script>
document.addEventListener('DOMContentLoaded', function(){
    const steps = 3;
    let current = 1;

    function showStep(n){
        for(let i=1;i<=steps;i++){
            document.getElementById('mni-step-'+i).style.display = (i===n?'':'none');
            document.querySelector('.mni-step-indicator[data-step="'+i+'"]').classList.toggle('active', i===n);
        }
        current = n;
    }

    document.querySelectorAll('.mni-next').forEach(btn=>{
        btn.addEventListener('click', ()=>{ if(current<steps) showStep(current+1); });
    });

    document.querySelectorAll('.mni-back').forEach(btn=>{
        btn.addEventListener('click', ()=>{ if(current>1) showStep(current-1); });
    });

    document.querySelectorAll('.mni-step-indicator').forEach(ind=>{
        ind.addEventListener('click', ()=> showStep(parseInt(ind.dataset.step)));
    });

    showStep(1);
});
</script>
