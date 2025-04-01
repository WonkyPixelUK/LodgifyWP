<?php
/**
 * Plugin Name: LodgifyWP Plugin
 * Plugin URI: https://github.com/WonkyPixelUK/LodgifyWP
 * Description: Property booking and management system for WordPress. Requires LodgifyWP Theme.
 * Version: 1.0.0
 * Author: WonkyPixel
 * Author URI: https://wonkypixel.co.uk
 * License: GPL v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: lodgifywp
 * Domain Path: /languages
 * Requires at least: 6.0
 * Requires PHP: 7.4
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('LODGIFYWP_VERSION', '1.0.1');
define('LODGIFYWP_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LODGIFYWP_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Check if the LodgifyWP theme is active
 */
function lodgifywp_check_theme_dependency() {
    $theme = wp_get_theme();
    if ('LodgifyWP' !== $theme->name && 'LodgifyWP' !== $theme->parent_theme) {
        add_action('admin_notices', 'lodgifywp_theme_missing_notice');
        deactivate_plugins(plugin_basename(__FILE__));
    }
}
add_action('admin_init', 'lodgifywp_check_theme_dependency');

/**
 * Admin notice for missing theme dependency
 */
function lodgifywp_theme_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('LodgifyWP Plugin requires the LodgifyWP Theme to be installed and active. Please install and activate the theme first.', 'lodgifywp'); ?></p>
    </div>
    <?php
}

/**
 * The core plugin class
 */
require_once LODGIFYWP_PLUGIN_DIR . 'inc/class-lodgifywp.php';

/**
 * Begins execution of the plugin
 */
function run_lodgifywp() {
    $plugin = new LodgifyWP();
    $plugin->run();
}
run_lodgifywp(); 