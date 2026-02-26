<?php
if ( ! defined( 'ABSPATH' ) ) exit;

require_once MNI_FREE_PATH . 'includes/core/registry.php';

class MNI_Free_Wizard_Service {

    public function get_messengers() : array {
        $all = MNI_Free_Registry::messengers();
        return $all;
    }

    public function get_actions() : array {
        $features = MNI_Free_Registry::actions();
        foreach ( $features as $key => $feature ) {
            if (!empty($feature['hook']) && !class_exists($feature['hook'])) {
                unset($features[$key]);
            }
        }
        return $features;
    }
}