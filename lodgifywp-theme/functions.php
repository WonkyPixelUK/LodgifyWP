<?php
/**
 * LodgifyWP Theme functions and definitions
 */

// Include TGM Plugin Activation
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

/**
 * Register required plugins for this theme
 */
function lodgifywp_register_required_plugins() {
    $plugins = array(
        // Required Plugin: ACF Pro
        array(
            'name'               => 'Advanced Custom Fields PRO',
            'slug'               => 'advanced-custom-fields-pro',
            'source'             => get_template_directory() . '/plugins/advanced-custom-fields-pro.zip',
            'required'           => true,
            'force_activation'   => true,
            'force_deactivation' => false,
        ),
        
        // Required Plugin: Timber
        array(
            'name'               => 'Timber',
            'slug'               => 'timber-library',
            'required'           => true,
            'force_activation'   => true,
            'force_deactivation' => false,
        ),
        
        // Our custom LodgifyWP Plugin
        array(
            'name'               => 'LodgifyWP Plugin',
            'slug'               => 'lodgifywp-plugin',
            'source'             => get_template_directory() . '/plugins/lodgifywp-plugin.zip',
            'required'           => true,
            'force_activation'   => true,
            'force_deactivation' => false,
            'version'            => '1.0.4',
        ),
    );

    $config = array(
        'id'           => 'lodgifywp',
        'default_path' => get_template_directory() . '/plugins/',
        'menu'         => 'tgmpa-install-plugins',
        'parent_slug'  => 'themes.php',
        'capability'   => 'edit_theme_options',
        'has_notices'  => true,
        'dismissable'  => false,
        'dismiss_msg'  => '',
        'is_automatic' => true,
        'message'      => '',
    );

    tgmpa($plugins, $config);
}
add_action('tgmpa_register', 'lodgifywp_register_required_plugins');

/**
 * ACF Configuration
 */

// Set path for ACF JSON sync
add_filter('acf/settings/save_json', 'lodgifywp_acf_json_save_point');
function lodgifywp_acf_json_save_point($path) {
    return get_template_directory() . '/acf-json';
}

// Set path for ACF JSON load
add_filter('acf/settings/load_json', 'lodgifywp_acf_json_load_point');
function lodgifywp_acf_json_load_point($paths) {
    // Remove original path
    unset($paths[0]);
    
    // Add our path
    $paths[] = get_template_directory() . '/acf-json';
    
    return $paths;
}

// Import ACF fields on theme activation
function lodgifywp_import_acf_fields() {
    // Check if ACF is active
    if (!class_exists('ACF')) {
        return;
    }

    // Get all JSON files in ACF JSON directory
    $json_files = glob(get_template_directory() . '/acf-json/*.json');
    
    if (!empty($json_files)) {
        foreach ($json_files as $json_file) {
            $json_content = json_decode(file_get_contents($json_file), true);
            
            // Skip if not a field group
            if (!isset($json_content['key']) || !isset($json_content['title'])) {
                continue;
            }
            
            // Check if field group already exists
            $existing_group = acf_get_field_group($json_content['key']);
            
            if (!$existing_group) {
                // Import the field group
                acf_import_field_group($json_content);
            }
        }
    }
}
add_action('after_switch_theme', 'lodgifywp_import_acf_fields');

// Force ACF to run in local mode
add_filter('acf/settings/show_admin', '__return_true');

/**
 * Theme setup
 */
function lodgifywp_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');
    add_theme_support('html5', array('search-form', 'comment-form', 'comment-list', 'gallery', 'caption'));
    
    // Register menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'lodgifywp'),
        'footer'  => __('Footer Menu', 'lodgifywp'),
    ));
}
add_action('after_setup_theme', 'lodgifywp_theme_setup');

/**
 * Enqueue scripts and styles
 */
function lodgifywp_enqueue_scripts() {
    // Enqueue styles
    wp_enqueue_style('lodgifywp-style', get_stylesheet_uri(), array(), '1.0.4');
    wp_enqueue_style('lodgifywp-main', get_template_directory_uri() . '/assets/css/main.css', array(), '1.0.4');
    
    // Enqueue scripts
    wp_enqueue_script('lodgifywp-main', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), '1.0.4', true);
    
    // Localize script
    wp_localize_script('lodgifywp-main', 'lodgifywpSettings', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('lodgifywp-nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'lodgifywp_enqueue_scripts');

/**
 * Initialize Timber
 */
if (class_exists('Timber')) {
    Timber::$dirname = array('templates', 'views');
    
    class LodgifyWPSite extends TimberSite {
        public function __construct() {
            add_filter('timber_context', array($this, 'add_to_context'));
            parent::__construct();
        }
        
        public function add_to_context($context) {
            $context['menu'] = new TimberMenu('primary');
            $context['footer_menu'] = new TimberMenu('footer');
            $context['site'] = $this;
            return $context;
        }
    }
    
    new LodgifyWPSite();
} 