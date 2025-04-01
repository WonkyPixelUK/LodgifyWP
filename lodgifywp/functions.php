<?php

if (!defined('ABSPATH')) {
    exit;
}

// Composer autoload
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Initialize Timber
if (class_exists('Timber')) {
    Timber::init();
}

// Include required files
require_once get_template_directory() . '/inc/acf-fields.php';
require_once get_template_directory() . '/inc/class-ical-integration.php';

// Theme Setup
function house_booking_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
}
add_action('after_setup_theme', 'house_booking_setup');

// Enqueue scripts and styles
function house_booking_scripts() {
    wp_enqueue_style('house-booking-style', get_stylesheet_uri());
    wp_enqueue_script('house-booking-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0', true);
    
    // Localize script for AJAX
    wp_localize_script('house-booking-main', 'houseBookingAjax', array(
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('house-booking-nonce')
    ));
}
add_action('wp_enqueue_scripts', 'house_booking_scripts');

// Register Custom Post Type for Bookings
function register_booking_post_type() {
    $labels = array(
        'name' => 'Bookings',
        'singular_name' => 'Booking',
        'menu_name' => 'Bookings',
        'add_new' => 'Add New',
        'add_new_item' => 'Add New Booking',
        'edit_item' => 'Edit Booking',
        'new_item' => 'New Booking',
        'view_item' => 'View Booking',
        'search_items' => 'Search Bookings',
        'not_found' => 'No bookings found',
        'not_found_in_trash' => 'No bookings found in Trash',
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'supports' => array('title', 'editor', 'custom-fields'),
        'menu_icon' => 'dashicons-calendar-alt',
    );

    register_post_type('booking', $args);
}
add_action('init', 'register_booking_post_type');

// Add Theme Settings Page
function add_theme_settings_page() {
    add_menu_page(
        'Theme Settings',
        'Theme Settings',
        'manage_options',
        'theme-settings',
        'render_theme_settings_page',
        'dashicons-admin-customizer',
        60
    );
}
add_action('admin_menu', 'add_theme_settings_page');

// Render Theme Settings Page
function render_theme_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        
        <?php
        // Get iCal settings
        $ical_settings = get_field('ical_settings', 'option');
        if ($ical_settings && isset($ical_settings['enabled']) && $ical_settings['enabled']) {
            ?>
            <div class="ical-sync-section">
                <h2>iCal Integration</h2>
                <p>Click the button below to manually sync iCal feeds:</p>
                <button type="button" id="sync-ical-feeds" class="button button-primary">
                    Sync iCal Feeds
                </button>
                <span id="sync-status" style="margin-left: 10px;"></span>
            </div>
            <script>
            jQuery(document).ready(function($) {
                $('#sync-ical-feeds').on('click', function() {
                    var button = $(this);
                    var status = $('#sync-status');
                    
                    button.prop('disabled', true);
                    status.html('Syncing...');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'sync_ical_feeds',
                            nonce: '<?php echo wp_create_nonce('house-booking-nonce'); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                status.html('Sync completed successfully!');
                            } else {
                                status.html('Error: ' + response.data);
                            }
                        },
                        error: function() {
                            status.html('Error: Failed to sync feeds');
                        },
                        complete: function() {
                            button.prop('disabled', false);
                        }
                    });
                });
            });
            </script>
            <?php
        }
        ?>
        
        <form action="options.php" method="post">
            <?php
            settings_fields('theme_settings');
            do_settings_sections('theme-settings');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}

// Register Theme Settings
function register_theme_settings() {
    register_setting('theme_settings', 'site_logo');
    register_setting('theme_settings', 'site_email');
    register_setting('theme_settings', 'site_phone');
    register_setting('theme_settings', 'social_media');
    register_setting('theme_settings', 'trustpilot_url');
}
add_action('admin_init', 'register_theme_settings');

// Add Timber Context
function add_to_timber_context($context) {
    $context['site_logo'] = get_field('site_logo', 'option');
    $context['site_email'] = get_field('site_email', 'option');
    $context['site_phone'] = get_field('site_phone', 'option');
    $context['social_media'] = get_field('social_media', 'option');
    $context['trustpilot_url'] = get_field('trustpilot_url', 'option');
    return $context;
}
add_filter('timber/context', 'add_to_timber_context');

function house_booking_system_enqueue_scripts() {
    // ... existing enqueues ...

    // Fancybox for gallery lightbox
    wp_enqueue_style(
        'fancybox',
        'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css',
        array(),
        '5.0.0'
    );

    wp_enqueue_script(
        'fancybox',
        'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js',
        array(),
        '5.0.0',
        true
    );

    // Masonry for gallery layout
    wp_enqueue_script(
        'masonry',
        'https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js',
        array(),
        '4.2.2',
        true
    );

    wp_enqueue_script(
        'imagesloaded',
        'https://unpkg.com/imagesloaded@5/imagesloaded.pkgd.min.js',
        array('masonry'),
        '5.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'house_booking_system_enqueue_scripts'); 