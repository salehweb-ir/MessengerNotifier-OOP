<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Trait: mni_singleton
 *
 * Provides a simple singleton implementation.
 *
 * Usage:
 *   class my_class {
 *       use mni_singleton;
 *       private function __construct() { ... }
 *   }
 *   my_class::instance();
 */

if ( ! trait_exists( 'mni_singleton' ) ) {
    trait mni_singleton {

        /**
         * Hold instances.
         *
         * @var array
         */
        private static $instances = [];

        /**
         * Returns singleton instance.
         *
         * @return static
         */
        public static function instance() {
            $called = static::class;
            if ( ! isset( self::$instances[ $called ] ) ) {
                self::$instances[ $called ] = new static();
            }
            return self::$instances[ $called ];
        }

        /**
         * Prevent cloning.
         *
         * Keep as private (no final) to avoid PHP warnings.
         */
        private function __clone() {
            // Intentionally left blank to prevent cloning.
        }

        /**
         * Prevent unserialize.
         *
         * Magic __wakeup MUST be public. Throw to prevent unserialization.
         */
        public function __wakeup() {
            // If someone tries to unserialize, stop it.
            throw new \Exception( 'Cannot unserialize singleton ' . static::class );
        }
    }
}
