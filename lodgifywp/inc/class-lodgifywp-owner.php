<?php
/**
 * Owner Profile Class
 *
 * @package LodgifyWP
 */

class LodgifyWP_Owner {
    /**
     * Initialize the owner profile functionality
     */
    public function init() {
        // Register post type
        add_action('init', array($this, 'register_post_type'));
        
        // Register ACF fields
        add_action('acf/init', array($this, 'register_fields'));
        
        // Add owner field to properties
        add_action('acf/init', array($this, 'add_owner_field_to_property'));
    }

    /**
     * Register owner post type
     */
    public function register_post_type() {
        register_post_type('owner', array(
            'labels' => array(
                'name' => __('Property Owners', 'lodgifywp'),
                'singular_name' => __('Property Owner', 'lodgifywp'),
                'add_new' => __('Add New', 'lodgifywp'),
                'add_new_item' => __('Add New Owner', 'lodgifywp'),
                'edit_item' => __('Edit Owner', 'lodgifywp'),
                'new_item' => __('New Owner', 'lodgifywp'),
                'view_item' => __('View Owner', 'lodgifywp'),
                'search_items' => __('Search Owners', 'lodgifywp'),
                'not_found' => __('No owners found', 'lodgifywp'),
                'not_found_in_trash' => __('No owners found in Trash', 'lodgifywp'),
            ),
            'public' => true,
            'exclude_from_search' => true,
            'publicly_queryable' => false,
            'show_in_menu' => 'edit.php?post_type=property',
            'supports' => array('title'),
            'menu_icon' => 'dashicons-businessperson',
            'capability_type' => 'post',
        ));
    }

    /**
     * Register ACF fields for owner profiles
     */
    public function register_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_owner_details',
            'title' => __('Owner Details', 'lodgifywp'),
            'fields' => array(
                array(
                    'key' => 'field_owner_email',
                    'label' => __('Email', 'lodgifywp'),
                    'name' => 'owner_email',
                    'type' => 'email',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_owner_phone',
                    'label' => __('Phone', 'lodgifywp'),
                    'name' => 'owner_phone',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_owner_photo',
                    'label' => __('Photo', 'lodgifywp'),
                    'name' => 'owner_photo',
                    'type' => 'image',
                    'return_format' => 'array',
                    'preview_size' => 'medium',
                ),
                array(
                    'key' => 'field_owner_bio',
                    'label' => __('Bio', 'lodgifywp'),
                    'name' => 'owner_bio',
                    'type' => 'textarea',
                    'rows' => 4,
                ),
                array(
                    'key' => 'field_owner_welcome_message',
                    'label' => __('Welcome Message', 'lodgifywp'),
                    'name' => 'owner_welcome_message',
                    'type' => 'textarea',
                    'rows' => 3,
                    'instructions' => __('This message will be included in booking confirmations', 'lodgifywp'),
                ),
                array(
                    'key' => 'field_owner_reminder_days',
                    'label' => __('Send Reminder Days Before', 'lodgifywp'),
                    'name' => 'owner_reminder_days',
                    'type' => 'number',
                    'default_value' => 2,
                    'min' => 1,
                    'max' => 14,
                    'instructions' => __('Number of days before check-in to send reminders', 'lodgifywp'),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'owner',
                    ),
                ),
            ),
        ));
    }

    /**
     * Add owner field to property post type
     */
    public function add_owner_field_to_property() {
        acf_add_local_field(array(
            'key' => 'field_property_owner',
            'label' => __('Property Owner', 'lodgifywp'),
            'name' => 'property_owner',
            'type' => 'post_object',
            'post_type' => array('owner'),
            'required' => 1,
            'parent' => 'group_property_details',
        ));
    }
} 