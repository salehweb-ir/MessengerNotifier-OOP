<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class MNI_Free_Sanitizer {

    /**
     * Sanitize settings array (Wizard + Settings page compatible)
     */
    public function sanitize_settings( array $input ): array {

        $clean = [];

        /* ------------------------------
         * Messengers
         * ------------------------------ */
        $clean['messengers'] = [];

        if ( ! empty( $input['messengers'] ) && is_array( $input['messengers'] ) ) {
            foreach ( $input['messengers'] as $messenger ) {
                $clean['messengers'][] = sanitize_key( $messenger );
            }
        }

        $clean['messengers'] = array_unique( $clean['messengers'] );


        /* ------------------------------
         * Actions
         * ------------------------------ */
        $clean['actions'] = [];

        if ( ! empty( $input['actions'] ) && is_array( $input['actions'] ) ) {
            foreach ( $input['actions'] as $action ) {
                $clean['actions'][] = sanitize_key( $action );
            }
        }

        $clean['actions'] = array_unique( $clean['actions'] );


        /* ------------------------------
         * Contact Page
         * ------------------------------ */
        $clean['contact_page'] = [
            'id'       => 0,
            'title'    => '',
            'slug'     => '',
            'template' => '',
        ];

        if ( ! empty( $input['contact_page'] ) && is_array( $input['contact_page'] ) ) {

            // Page ID (برای وقتی از صفحه تنظیمات ذخیره می‌شود)
            if ( isset( $input['contact_page']['id'] ) ) {
                $clean['contact_page']['id'] = absint( $input['contact_page']['id'] );
            }

            // Title (فقط در Wizard اهمیت دارد)
            if ( isset( $input['contact_page']['title'] ) ) {
                $clean['contact_page']['title'] = sanitize_text_field(
                    $input['contact_page']['title']
                );
            }

            // Slug
            if ( isset( $input['contact_page']['slug'] ) ) {
                $slug = sanitize_title( $input['contact_page']['slug'] );
                $clean['contact_page']['slug'] = $slug ?: 'nashenas';
            }

            // Template
            if ( isset( $input['contact_page']['template'] ) ) {
                $clean['contact_page']['template'] = sanitize_text_field(
                    $input['contact_page']['template']
                );
            }
        }


        /* ------------------------------
         * Messenger Configs
         * ------------------------------ */
        $clean['config'] = [];

        if (
            ! empty( $input['config'] ) &&
            is_array( $input['config'] ) &&
            ! empty( $clean['messengers'] )
        ) {
            foreach ( $clean['messengers'] as $messenger_id ) {

                if (
                    ! empty( $input['config'][ $messenger_id ] ) &&
                    is_array( $input['config'][ $messenger_id ] )
                ) {

                    $clean['config'][ $messenger_id ] = [
                        'token'   => sanitize_text_field(
                            $input['config'][ $messenger_id ]['token'] ?? ''
                        ),
                        'channel' => sanitize_text_field(
                            $input['config'][ $messenger_id ]['channel'] ?? ''
                        ),
                    ];
                }
            }
        }

        return $clean;
    }
}
