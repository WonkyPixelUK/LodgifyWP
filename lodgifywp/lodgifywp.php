<?php
/**
 * Plugin Name: LodgifyWP
 * Plugin URI: https://github.com/WonkyPixelUK/LodgifyWP
 * Description: A comprehensive WordPress plugin for managing property bookings, with integrated calendar synchronization, Stripe payments, and owner profiles.
 * Version: 1.0.0
 * Author: WonkyPixel
 * Author URI: https://wonkypixel.co.uk
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: lodgifywp
 * Domain Path: /languages
 * Requires at least: 5.8
 * Requires PHP: 7.4
 *
 * @package LodgifyWP
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Plugin version
define('LODGIFYWP_VERSION', '1.0.0');

// Plugin directory path
define('LODGIFYWP_DIR', plugin_dir_path(__FILE__));

// Plugin directory URL
define('LODGIFYWP_URL', plugin_dir_url(__FILE__));

// Require Composer autoloader
require_once LODGIFYWP_DIR . 'vendor/autoload.php';

/**
 * Initialize update checker
 */
function lodgifywp_init_updater() {
    // Include the update checker library
    require_once LODGIFYWP_DIR . 'vendor/yahnis-elsts/plugin-update-checker/plugin-update-checker.php';

    // Initialize the update checker
    $updateChecker = Puc_v4_Factory::buildUpdateChecker(
        'https://github.com/WonkyPixelUK/LodgifyWP/',
        __FILE__,
        'lodgifywp'
    );

    // Set the branch that contains the stable release
    $updateChecker->setBranch('master');

    // Optional: Enable release assets
    $updateChecker->getVcsApi()->enableReleaseAssets();
}
add_action('init', 'lodgifywp_init_updater');

/**
 * Initialize plugin components
 */
function lodgifywp_init() {
    // Initialize Property post type
    $property = new LodgifyWP_Property();
    $property->init();

    // Initialize Booking system
    $booking = new LodgifyWP_Booking();
    $booking->init();

    // Initialize Payment system
    $payment = new LodgifyWP_Payment();
    $payment->init();

    // Initialize Calendar system
    $calendar = new LodgifyWP_Calendar();
    $calendar->init();

    // Initialize Owner profiles
    $owner = new LodgifyWP_Owner();
    $owner->init();

    // Initialize Reminder system
    $reminder = new LodgifyWP_Reminder();
    $reminder->init();
}
add_action('plugins_loaded', 'lodgifywp_init'); 