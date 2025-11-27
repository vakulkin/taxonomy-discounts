<?php
/**
 * Bootstrap for PHPUnit tests
 */

// Define test constants
define('WP_TESTS_DIR', getenv('WP_TESTS_DIR') ?: '/tmp/wordpress-tests-lib');

// Load WordPress test functions if available
if (file_exists(WP_TESTS_DIR . '/includes/functions.php')) {
    require_once WP_TESTS_DIR . '/includes/functions.php';
}

// Load the plugin (commented out for unit tests to avoid WP dependencies)
// require_once dirname(__DIR__) . '/taxonomy-discounts.php';

// Mock ACF functions if not available
if (!function_exists('acf_add_local_field_group')) {
    function acf_add_local_field_group($field_group) {
        // Mock implementation
    }
}

if (!function_exists('get_field')) {
    function get_field($field_name, $post_id = false) {
        // Mock implementation - return test data
        static $mock_data = [];
        return $mock_data[$field_name] ?? null;
    }
}

// Mock WooCommerce functions
if (!class_exists('WC_Product')) {
    class WC_Product {
        protected $id;
        protected $type = 'simple';

        public function __construct($id = 1) {
            $this->id = $id;
        }

        public function get_id() {
            return $this->id;
        }

        public function is_type($type) {
            return $this->type === $type;
        }

        public function get_regular_price() {
            return 100.0;
        }
    }
}

// Mock WordPress functions
if (!function_exists('wp_get_post_terms')) {
    function wp_get_post_terms($post_id, $taxonomy, $args = []) {
        // Mock implementation
        return [1, 2]; // Return some term IDs
    }
}

if (!function_exists('get_posts')) {
    function get_posts($args) {
        // Mock implementation - return empty array for tests
        return [];
    }
}

if (!function_exists('add_action')) {
    function add_action($hook, $callback, $priority = 10, $args = 1) {
        // Mock implementation
    }
}

if (!function_exists('add_filter')) {
    function add_filter($hook, $callback, $priority = 10, $args = 1) {
        // Mock implementation
    }
}

if (!function_exists('register_post_type')) {
    function register_post_type($post_type, $args) {
        // Mock implementation
    }
}