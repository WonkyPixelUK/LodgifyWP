<?php
/**
 * Property Post Type Class
 *
 * @package LodgifyWP
 */

class LodgifyWP_Property {
    /**
     * Initialize the class
     */
    public function init() {
        add_action('init', array($this, 'register_post_type'));
        add_action('init', array($this, 'register_taxonomies'));
        add_action('acf/init', array($this, 'register_fields'));
    }

    /**
     * Register the Property post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Properties', 'Post type general name', 'lodgifywp'),
            'singular_name'         => _x('Property', 'Post type singular name', 'lodgifywp'),
            'menu_name'             => _x('Properties', 'Admin Menu text', 'lodgifywp'),
            'add_new'              => __('Add New', 'lodgifywp'),
            'add_new_item'         => __('Add New Property', 'lodgifywp'),
            'edit_item'            => __('Edit Property', 'lodgifywp'),
            'new_item'             => __('New Property', 'lodgifywp'),
            'view_item'            => __('View Property', 'lodgifywp'),
            'search_items'         => __('Search Properties', 'lodgifywp'),
            'not_found'            => __('No properties found', 'lodgifywp'),
            'not_found_in_trash'   => __('No properties found in Trash', 'lodgifywp'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'property'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-building',
            'supports'           => array('title', 'editor', 'thumbnail', 'excerpt'),
            'show_in_rest'       => true,
        );

        register_post_type('property', $args);
    }

    /**
     * Register Property taxonomies
     */
    public function register_taxonomies() {
        // Property Type Taxonomy
        register_taxonomy('property_type', 'property', array(
            'labels' => array(
                'name'              => _x('Property Types', 'taxonomy general name', 'lodgifywp'),
                'singular_name'     => _x('Property Type', 'taxonomy singular name', 'lodgifywp'),
                'menu_name'         => __('Property Types', 'lodgifywp'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'property-type'),
            'show_in_rest'      => true,
        ));

        // Amenities Taxonomy
        register_taxonomy('amenity', 'property', array(
            'labels' => array(
                'name'              => _x('Amenities', 'taxonomy general name', 'lodgifywp'),
                'singular_name'     => _x('Amenity', 'taxonomy singular name', 'lodgifywp'),
                'menu_name'         => __('Amenities', 'lodgifywp'),
            ),
            'hierarchical'      => false,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'amenity'),
            'show_in_rest'      => true,
        ));

        // Location Taxonomy
        register_taxonomy('location', 'property', array(
            'labels' => array(
                'name'              => _x('Locations', 'taxonomy general name', 'lodgifywp'),
                'singular_name'     => _x('Location', 'taxonomy singular name', 'lodgifywp'),
                'menu_name'         => __('Locations', 'lodgifywp'),
            ),
            'hierarchical'      => true,
            'show_ui'           => true,
            'show_admin_column' => true,
            'query_var'         => true,
            'rewrite'           => array('slug' => 'location'),
            'show_in_rest'      => true,
        ));
    }

    /**
     * Register ACF fields for Properties
     */
    public function register_fields() {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_property_details',
                'title' => 'Property Details',
                'fields' => array(
                    array(
                        'key' => 'field_price_per_night',
                        'label' => 'Price per Night',
                        'name' => 'price_per_night',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 'any',
                    ),
                    array(
                        'key' => 'field_max_guests',
                        'label' => 'Maximum Guests',
                        'name' => 'max_guests',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 1,
                    ),
                    array(
                        'key' => 'field_bedrooms',
                        'label' => 'Bedrooms',
                        'name' => 'bedrooms',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                    ),
                    array(
                        'key' => 'field_bathrooms',
                        'label' => 'Bathrooms',
                        'name' => 'bathrooms',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 0,
                        'step' => 'any',
                    ),
                    array(
                        'key' => 'field_property_gallery',
                        'label' => 'Property Gallery',
                        'name' => 'property_gallery',
                        'type' => 'gallery',
                        'required' => 0,
                    ),
                    array(
                        'key' => 'field_check_in_time',
                        'label' => 'Check-in Time',
                        'name' => 'check_in_time',
                        'type' => 'time_picker',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_check_out_time',
                        'label' => 'Check-out Time',
                        'name' => 'check_out_time',
                        'type' => 'time_picker',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_minimum_stay',
                        'label' => 'Minimum Stay (nights)',
                        'name' => 'minimum_stay',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 1,
                        'default_value' => 1,
                    ),
                    array(
                        'key' => 'field_location_map',
                        'label' => 'Property Location',
                        'name' => 'location_map',
                        'type' => 'google_map',
                        'required' => 1,
                    ),
                    array(
                        'key' => 'field_house_rules',
                        'label' => 'House Rules',
                        'name' => 'house_rules',
                        'type' => 'repeater',
                        'required' => 0,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_rule',
                                'label' => 'Rule',
                                'name' => 'rule',
                                'type' => 'text',
                                'required' => 1,
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'property',
                        ),
                    ),
                ),
            ));
        }
    }
} 