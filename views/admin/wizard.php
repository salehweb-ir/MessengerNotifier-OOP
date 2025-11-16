<div class="wrap mni-wizard">

    <h1>Plugin Setup Wizard</h1>

    <form id="mni_wizard_form" method="post" action="">

        <!-- Hidden field to pass selected messengers to step 3 -->
        <input type="hidden" id="mni_selected_messengers" name="mni_selected_messengers" value="">

        <!-- STEP 1 -------------------------------------------------------- -->
        <div class="mni-step" data-step="1">
            <h2>General Settings</h2>

            <p>Select the messengers you want to activate:</p>

            <label><input type="checkbox" class="mni-messenger-check" value="eitaa"> Eitaa</label><br>
            <label><input type="checkbox" class="mni-messenger-check" value="telegram"> Telegram</label><br>
            <label><input type="checkbox" class="mni-messenger-check" value="bale"> Bale</label><br>
            <label><input type="checkbox" class="mni-messenger-check" value="igap"> iGap</label><br>
            <label><input type="checkbox" class="mni-messenger-check" value="soroush"> Soroush</label><br>

            <p style="color:#d00;font-weight:bold;margin-top:10px;">
                At least one messenger must be active.
            </p>

            <div class="mni-tab-buttons">
                <button type="button" class="button button-primary mni-next-step">Next</button>
            </div>
        </div>

        <!-- STEP 2 -------------------------------------------------------- -->
        <div class="mni-step" data-step="2" style="display:none;">
            <h2>Actions & Behavior</h2>
            <p>Configure how the notifier behaves.</p>

            <textarea style="width:100%;height:200px;">(Some action settings...)</textarea>

            <div class="mni-tab-buttons">
                <button type="button" class="button mni-prev-step">Back</button>
                <button type="button" class="button button-primary mni-next-step">Next</button>
            </div>
        </div>

        <!-- STEP 3 -------------------------------------------------------- -->
        <div class="mni-step" data-step="3" style="display:none;">
            <h2>Messenger Settings</h2>

            <!-- Messenger tabs will be created dynamically in JS -->
            <div id="mni_messenger_tabs"></div>

            <div class="mni-tab-buttons">
                <button type="button" class="button mni-prev-step">Back</button>
                <button type="submit" class="button button-primary">Finish</button>
            </div>
        </div>

    </form>
</div>
