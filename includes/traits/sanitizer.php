<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Sanitizer {

    /**
     * Sanitize wizard settings (supports multi-dimensional config)
     */
    public function sanitize_settings(array $input): array {

        $clean = [];
    
        /* ------------------------------
         * Messengers
         * ------------------------------ */
        $clean['messengers'] = [];
        if (!empty($input['messengers']) && is_array($input['messengers'])) {
            foreach ($input['messengers'] as $messenger) {
                $clean['messengers'][] = sanitize_key($messenger);
            }
        }
    
        /* ------------------------------
         * Actions
         * ------------------------------ */
        $clean['actions'] = [];
        if (!empty($input['actions']) && is_array($input['actions'])) {
            foreach ($input['actions'] as $action) {
                $clean['actions'][] = sanitize_key($action);
            }
        }
    
        /* ------------------------------
         * Contact Page
         * ------------------------------ */
        $clean['contact_page'] = [
            'title'    => '',
            'slug'     => '',
            'template' => 'minimal',
        ];
    
        if (!empty($input['contact_page']) && is_array($input['contact_page'])) {
            $clean['contact_page']['title']    = sanitize_text_field($input['contact_page']['title'] ?? '');
            $clean['contact_page']['slug']     = sanitize_title($input['contact_page']['slug'] ?? '');
            $clean['contact_page']['template'] = sanitize_key($input['contact_page']['template'] ?? 'minimal');
        }
    
        /* ------------------------------
         * Messenger Configs
         * ------------------------------ */
        $clean['config'] = [];
    
        if (
            !empty($input['config']) &&
            is_array($input['config']) &&
            !empty($clean['messengers'])
        ) {
            foreach ($clean['messengers'] as $messenger_id) {
                if (!empty($input['config'][$messenger_id]) && is_array($input['config'][$messenger_id])) {
                    $clean['config'][$messenger_id] = [
                        'token'   => sanitize_text_field($input['config'][$messenger_id]['token'] ?? ''),
                        'channel' => sanitize_text_field($input['config'][$messenger_id]['channel'] ?? ''),
                    ];
                }
            }
        }
    
        return $clean;
    }
}
