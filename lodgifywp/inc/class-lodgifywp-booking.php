<?php
/**
 * Booking Class
 *
 * @package LodgifyWP
 */

class LodgifyWP_Booking {
    /**
     * Initialize the class
     */
    public function init() {
        add_action('init', array($this, 'register_post_type'));
        add_action('acf/init', array($this, 'register_fields'));
        add_action('rest_api_init', array($this, 'register_rest_routes'));
    }

    /**
     * Register the Booking post type
     */
    public function register_post_type() {
        $labels = array(
            'name'                  => _x('Bookings', 'Post type general name', 'lodgifywp'),
            'singular_name'         => _x('Booking', 'Post type singular name', 'lodgifywp'),
            'menu_name'             => _x('Bookings', 'Admin Menu text', 'lodgifywp'),
            'add_new'              => __('Add New', 'lodgifywp'),
            'add_new_item'         => __('Add New Booking', 'lodgifywp'),
            'edit_item'            => __('Edit Booking', 'lodgifywp'),
            'new_item'             => __('New Booking', 'lodgifywp'),
            'view_item'            => __('View Booking', 'lodgifywp'),
            'search_items'         => __('Search Bookings', 'lodgifywp'),
            'not_found'            => __('No bookings found', 'lodgifywp'),
            'not_found_in_trash'   => __('No bookings found in Trash', 'lodgifywp'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => false,
            'publicly_queryable' => false,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'booking'),
            'capability_type'    => 'post',
            'has_archive'        => false,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-calendar-alt',
            'supports'           => array('title'),
            'show_in_rest'       => true,
        );

        register_post_type('booking', $args);
    }

    /**
     * Register ACF fields for Bookings
     */
    public function register_fields() {
        if (function_exists('acf_add_local_field_group')) {
            acf_add_local_field_group(array(
                'key' => 'group_booking_details',
                'title' => 'Booking Details',
                'fields' => array(
                    array(
                        'key' => 'field_property',
                        'label' => 'Property',
                        'name' => 'property',
                        'type' => 'post_object',
                        'required' => 1,
                        'post_type' => array('property'),
                        'return_format' => 'id',
                    ),
                    array(
                        'key' => 'field_check_in_date',
                        'label' => 'Check-in Date',
                        'name' => 'check_in_date',
                        'type' => 'date_picker',
                        'required' => 1,
                        'display_format' => 'd/m/Y',
                        'return_format' => 'Y-m-d',
                    ),
                    array(
                        'key' => 'field_check_out_date',
                        'label' => 'Check-out Date',
                        'name' => 'check_out_date',
                        'type' => 'date_picker',
                        'required' => 1,
                        'display_format' => 'd/m/Y',
                        'return_format' => 'Y-m-d',
                    ),
                    array(
                        'key' => 'field_guests',
                        'label' => 'Number of Guests',
                        'name' => 'guests',
                        'type' => 'number',
                        'required' => 1,
                        'min' => 1,
                    ),
                    array(
                        'key' => 'field_total_price',
                        'label' => 'Total Price',
                        'name' => 'total_price',
                        'type' => 'number',
                        'required' => 1,
                        'readonly' => 1,
                    ),
                    array(
                        'key' => 'field_status',
                        'label' => 'Status',
                        'name' => 'status',
                        'type' => 'select',
                        'required' => 1,
                        'choices' => array(
                            'pending' => 'Pending',
                            'confirmed' => 'Confirmed',
                            'cancelled' => 'Cancelled',
                            'completed' => 'Completed',
                        ),
                        'default_value' => 'pending',
                    ),
                    array(
                        'key' => 'field_guest_details',
                        'label' => 'Guest Details',
                        'name' => 'guest_details',
                        'type' => 'group',
                        'required' => 1,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_guest_name',
                                'label' => 'Name',
                                'name' => 'name',
                                'type' => 'text',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_guest_email',
                                'label' => 'Email',
                                'name' => 'email',
                                'type' => 'email',
                                'required' => 1,
                            ),
                            array(
                                'key' => 'field_guest_phone',
                                'label' => 'Phone',
                                'name' => 'phone',
                                'type' => 'text',
                                'required' => 1,
                            ),
                        ),
                    ),
                    array(
                        'key' => 'field_payment_details',
                        'label' => 'Payment Details',
                        'name' => 'payment_details',
                        'type' => 'group',
                        'required' => 0,
                        'sub_fields' => array(
                            array(
                                'key' => 'field_payment_id',
                                'label' => 'Payment ID',
                                'name' => 'payment_id',
                                'type' => 'text',
                                'readonly' => 1,
                            ),
                            array(
                                'key' => 'field_payment_status',
                                'label' => 'Payment Status',
                                'name' => 'payment_status',
                                'type' => 'select',
                                'choices' => array(
                                    'pending' => 'Pending',
                                    'paid' => 'Paid',
                                    'failed' => 'Failed',
                                    'refunded' => 'Refunded',
                                ),
                                'readonly' => 1,
                            ),
                        ),
                    ),
                ),
                'location' => array(
                    array(
                        array(
                            'param' => 'post_type',
                            'operator' => '==',
                            'value' => 'booking',
                        ),
                    ),
                ),
            ));
        }
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('lodgifywp/v1', '/check-availability', array(
            'methods' => 'GET',
            'callback' => array($this, 'check_availability'),
            'permission_callback' => '__return_true',
            'args' => array(
                'property_id' => array(
                    'required' => true,
                    'type' => 'integer',
                ),
                'check_in' => array(
                    'required' => true,
                    'type' => 'string',
                    'format' => 'date',
                ),
                'check_out' => array(
                    'required' => true,
                    'type' => 'string',
                    'format' => 'date',
                ),
                'guests' => array(
                    'required' => true,
                    'type' => 'integer',
                    'minimum' => 1,
                ),
            ),
        ));

        register_rest_route('lodgifywp/v1', '/create-booking', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_booking'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Check property availability
     */
    public function check_availability($request) {
        $property_id = $request->get_param('property_id');
        $check_in = $request->get_param('check_in');
        $check_out = $request->get_param('check_out');
        $guests = $request->get_param('guests');

        // Check if property exists
        $property = get_post($property_id);
        if (!$property || $property->post_type !== 'property') {
            return new WP_Error('property_not_found', 'Property not found', array('status' => 404));
        }

        // Check if dates are valid
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $today = new DateTime();

        if ($check_in_date < $today) {
            return new WP_Error('invalid_dates', 'Check-in date must be in the future', array('status' => 400));
        }

        if ($check_out_date <= $check_in_date) {
            return new WP_Error('invalid_dates', 'Check-out date must be after check-in date', array('status' => 400));
        }

        // Check if property can accommodate the number of guests
        $max_guests = get_field('max_guests', $property_id);
        if ($guests > $max_guests) {
            return new WP_Error('too_many_guests', 'Too many guests for this property', array('status' => 400));
        }

        // Check if property is available for the selected dates
        $is_available = $this->is_property_available($property_id, $check_in, $check_out);
        if (!$is_available) {
            return new WP_Error('not_available', 'Property is not available for the selected dates', array('status' => 400));
        }

        // Calculate total price
        $price_per_night = get_field('price_per_night', $property_id);
        $nights = $check_in_date->diff($check_out_date)->days;
        $total_price = $price_per_night * $nights;

        return array(
            'available' => true,
            'nights' => $nights,
            'price_per_night' => $price_per_night,
            'total_price' => $total_price,
        );
    }

    /**
     * Check if property is available for given dates
     */
    private function is_property_available($property_id, $check_in, $check_out) {
        $args = array(
            'post_type' => 'booking',
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'property',
                    'value' => $property_id,
                ),
                array(
                    'key' => 'status',
                    'value' => array('pending', 'confirmed'),
                    'compare' => 'IN',
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'check_in_date',
                            'value' => $check_in,
                            'compare' => '<=',
                            'type' => 'DATE',
                        ),
                        array(
                            'key' => 'check_out_date',
                            'value' => $check_in,
                            'compare' => '>',
                            'type' => 'DATE',
                        ),
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'check_in_date',
                            'value' => $check_out,
                            'compare' => '<',
                            'type' => 'DATE',
                        ),
                        array(
                            'key' => 'check_out_date',
                            'value' => $check_out,
                            'compare' => '>=',
                            'type' => 'DATE',
                        ),
                    ),
                ),
            ),
        );

        $bookings = get_posts($args);
        return empty($bookings);
    }

    /**
     * Create a new booking
     */
    public function create_booking($request) {
        $property_id = $request->get_param('property_id');
        $check_in = $request->get_param('check_in');
        $check_out = $request->get_param('check_out');
        $guests = $request->get_param('guests');
        $guest_details = $request->get_param('guest_details');

        // Validate availability first
        $availability = $this->check_availability(new WP_REST_Request('GET', '/lodgifywp/v1/check-availability'));
        if (is_wp_error($availability)) {
            return $availability;
        }

        // Create booking
        $booking_data = array(
            'post_title' => sprintf('Booking for %s - %s', $guest_details['name'], $check_in),
            'post_type' => 'booking',
            'post_status' => 'publish',
        );

        $booking_id = wp_insert_post($booking_data);

        if (is_wp_error($booking_id)) {
            return new WP_Error('booking_creation_failed', 'Failed to create booking', array('status' => 500));
        }

        // Update booking fields
        update_field('property', $property_id, $booking_id);
        update_field('check_in_date', $check_in, $booking_id);
        update_field('check_out_date', $check_out, $booking_id);
        update_field('guests', $guests, $booking_id);
        update_field('total_price', $availability['total_price'], $booking_id);
        update_field('status', 'pending', $booking_id);
        update_field('guest_details', $guest_details, $booking_id);

        return array(
            'booking_id' => $booking_id,
            'status' => 'pending',
            'total_price' => $availability['total_price'],
        );
    }
} 