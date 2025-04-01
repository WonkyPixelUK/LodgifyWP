<?php
/**
 * Calendar Integration Class
 *
 * @package LodgifyWP
 */

class LodgifyWP_Calendar {
    /**
     * Initialize the calendar integration
     */
    public function init() {
        // Register ACF fields for calendar integration
        add_action('acf/init', array($this, 'register_fields'));

        // Register cron job for calendar sync
        add_action('init', array($this, 'register_cron'));
        add_action('lodgifywp_sync_calendars', array($this, 'sync_all_calendars'));

        // Register REST API endpoints
        add_action('rest_api_init', array($this, 'register_rest_routes'));

        // Add admin menu for calendar settings
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Register booking status transitions
        add_action('acf/save_post', array($this, 'handle_booking_status_change'), 20);

        // Register OAuth callbacks
        add_action('admin_init', array($this, 'handle_oauth_callback'));
    }

    /**
     * Add admin menu for calendar settings
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=property',
            __('Calendar Settings', 'lodgifywp'),
            __('Calendar Settings', 'lodgifywp'),
            'manage_options',
            'lodgifywp-calendar-settings',
            array($this, 'render_settings_page')
        );
    }

    /**
     * Render settings page
     */
    public function render_settings_page() {
        // Get saved settings
        $google_client_id = get_option('lodgifywp_google_client_id');
        $google_client_secret = get_option('lodgifywp_google_client_secret');
        $ms365_client_id = get_option('lodgifywp_ms365_client_id');
        $ms365_client_secret = get_option('lodgifywp_ms365_client_secret');
        $booking_statuses = get_option('lodgifywp_booking_statuses', array(
            'pending' => array(
                'label' => 'Pending',
                'color' => '#ffc107',
            ),
            'approved' => array(
                'label' => 'Approved',
                'color' => '#28a745',
            ),
            'rejected' => array(
                'label' => 'Rejected',
                'color' => '#dc3545',
            ),
            'cancelled' => array(
                'label' => 'Cancelled',
                'color' => '#6c757d',
            ),
        ));

        include LODGIFYWP_DIR . '/template-parts/admin/calendar-settings.php';
    }

    /**
     * Register ACF fields for calendar integration
     */
    public function register_fields() {
        acf_add_local_field_group(array(
            'key' => 'group_calendar_integration',
            'title' => __('Calendar Integration', 'lodgifywp'),
            'fields' => array(
                array(
                    'key' => 'field_calendar_sync',
                    'label' => __('Calendar Sync', 'lodgifywp'),
                    'name' => 'calendar_sync',
                    'type' => 'repeater',
                    'layout' => 'table',
                    'button_label' => __('Add Calendar', 'lodgifywp'),
                    'sub_fields' => array(
                        array(
                            'key' => 'field_calendar_name',
                            'label' => __('Calendar Name', 'lodgifywp'),
                            'name' => 'calendar_name',
                            'type' => 'text',
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_calendar_type',
                            'label' => __('Calendar Type', 'lodgifywp'),
                            'name' => 'calendar_type',
                            'type' => 'select',
                            'choices' => array(
                                'ical' => 'iCal',
                                'google' => 'Google Calendar',
                                'ms365' => 'Microsoft 365',
                                'airbnb' => 'Airbnb',
                                'booking' => 'Booking.com',
                                'vrbo' => 'VRBO',
                            ),
                            'required' => 1,
                        ),
                        array(
                            'key' => 'field_calendar_url',
                            'label' => __('Calendar URL/ID', 'lodgifywp'),
                            'name' => 'calendar_url',
                            'type' => 'text',
                            'required' => 1,
                            'instructions' => __('For iCal: enter the URL. For Google/Microsoft: enter the calendar ID', 'lodgifywp'),
                        ),
                        array(
                            'key' => 'field_sync_direction',
                            'label' => __('Sync Direction', 'lodgifywp'),
                            'name' => 'sync_direction',
                            'type' => 'select',
                            'choices' => array(
                                'import' => 'Import Only',
                                'export' => 'Export Only',
                                'both' => 'Two-way Sync',
                            ),
                            'default_value' => 'both',
                        ),
                        array(
                            'key' => 'field_auto_status',
                            'label' => __('Auto Status', 'lodgifywp'),
                            'name' => 'auto_status',
                            'type' => 'select',
                            'choices' => array(
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'none' => 'No Automatic Status',
                            ),
                            'default_value' => 'pending',
                        ),
                    ),
                ),
                array(
                    'key' => 'field_booking_status',
                    'label' => __('Booking Status', 'lodgifywp'),
                    'name' => 'booking_status',
                    'type' => 'select',
                    'choices' => array(
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled',
                    ),
                    'default_value' => 'pending',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_status_color',
                    'label' => __('Status Color', 'lodgifywp'),
                    'name' => 'status_color',
                    'type' => 'color_picker',
                    'required' => 0,
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

    /**
     * Register cron job for calendar sync
     */
    public function register_cron() {
        if (!wp_next_scheduled('lodgifywp_sync_calendars')) {
            wp_schedule_event(time(), 'hourly', 'lodgifywp_sync_calendars');
        }
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('lodgifywp/v1', '/sync-calendar/(?P<property_id>\d+)', array(
            'methods' => 'POST',
            'callback' => array($this, 'sync_calendar_endpoint'),
            'permission_callback' => function() {
                return current_user_can('edit_posts');
            },
        ));
    }

    /**
     * REST API endpoint for manual calendar sync
     */
    public function sync_calendar_endpoint($request) {
        $property_id = $request->get_param('property_id');
        
        try {
            $this->sync_property_calendars($property_id);
            return new WP_REST_Response(array(
                'success' => true,
                'message' => __('Calendar synchronized successfully', 'lodgifywp'),
            ), 200);
        } catch (Exception $e) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), 500);
        }
    }

    /**
     * Sync all property calendars
     */
    public function sync_all_calendars() {
        $properties = get_posts(array(
            'post_type' => 'property',
            'posts_per_page' => -1,
        ));

        foreach ($properties as $property) {
            $this->sync_property_calendars($property->ID);
        }
    }

    /**
     * Sync calendars for a specific property
     */
    public function sync_property_calendars($property_id) {
        $calendars = get_field('calendar_sync', $property_id);
        
        if (!$calendars) {
            return;
        }

        foreach ($calendars as $calendar) {
            try {
                $this->sync_single_calendar($property_id, $calendar);
            } catch (Exception $e) {
                error_log(sprintf(
                    'Calendar sync failed for property %d: %s',
                    $property_id,
                    $e->getMessage()
                ));
            }
        }
    }

    /**
     * Sync a single calendar
     */
    private function sync_single_calendar($property_id, $calendar) {
        // Get calendar data
        $ical_data = $this->fetch_calendar_data($calendar['calendar_url']);
        
        // Parse calendar data
        $events = $this->parse_ical_data($ical_data);
        
        // Update bookings
        $this->update_bookings($property_id, $events, $calendar['calendar_name']);
    }

    /**
     * Fetch calendar data from URL
     */
    private function fetch_calendar_data($url) {
        $response = wp_remote_get($url);
        
        if (is_wp_error($response)) {
            throw new Exception($response->get_error_message());
        }
        
        return wp_remote_retrieve_body($response);
    }

    /**
     * Parse iCal data
     */
    private function parse_ical_data($ical_data) {
        $events = array();
        $lines = explode("\n", $ical_data);
        $event = null;

        foreach ($lines as $line) {
            $line = trim($line);
            
            if ($line === 'BEGIN:VEVENT') {
                $event = array();
            } elseif ($line === 'END:VEVENT' && $event) {
                $events[] = $event;
                $event = null;
            } elseif ($event !== null) {
                if (strpos($line, 'DTSTART') === 0) {
                    $event['start'] = $this->parse_ical_date($line);
                } elseif (strpos($line, 'DTEND') === 0) {
                    $event['end'] = $this->parse_ical_date($line);
                } elseif (strpos($line, 'SUMMARY') === 0) {
                    $event['summary'] = substr($line, 8);
                }
            }
        }

        return $events;
    }

    /**
     * Parse iCal date
     */
    private function parse_ical_date($line) {
        preg_match('/\d{8}/', $line, $matches);
        if (isset($matches[0])) {
            return DateTime::createFromFormat('Ymd', $matches[0])->format('Y-m-d');
        }
        return null;
    }

    /**
     * Update bookings based on calendar events
     */
    private function update_bookings($property_id, $events, $calendar_name) {
        foreach ($events as $event) {
            // Check if booking already exists
            $existing_booking = $this->find_existing_booking($property_id, $event['start'], $event['end']);
            
            if (!$existing_booking) {
                // Create new booking
                $booking_data = array(
                    'post_title' => sprintf(
                        __('External Booking from %s', 'lodgifywp'),
                        $calendar_name
                    ),
                    'post_type' => 'booking',
                    'post_status' => 'publish',
                );
                
                $booking_id = wp_insert_post($booking_data);
                
                if (!is_wp_error($booking_id)) {
                    update_field('property', $property_id, $booking_id);
                    update_field('check_in_date', $event['start'], $booking_id);
                    update_field('check_out_date', $event['end'], $booking_id);
                    update_field('booking_source', $calendar_name, $booking_id);
                    update_field('is_external_booking', true, $booking_id);
                }
            }
        }
    }

    /**
     * Find existing booking
     */
    private function find_existing_booking($property_id, $start_date, $end_date) {
        $bookings = get_posts(array(
            'post_type' => 'booking',
            'meta_query' => array(
                array(
                    'key' => 'property',
                    'value' => $property_id,
                ),
                array(
                    'key' => 'check_in_date',
                    'value' => $start_date,
                ),
                array(
                    'key' => 'check_out_date',
                    'value' => $end_date,
                ),
            ),
        ));

        return !empty($bookings) ? $bookings[0] : null;
    }

    /**
     * Handle booking status changes
     */
    public function handle_booking_status_change($post_id) {
        if (get_post_type($post_id) !== 'booking') {
            return;
        }

        $old_status = get_post_meta($post_id, 'booking_status', true);
        $new_status = get_field('booking_status', $post_id);

        if ($old_status !== $new_status) {
            // Update calendar events
            $this->update_calendar_events($post_id, $new_status);

            // Send notifications
            $this->send_status_notifications($post_id, $old_status, $new_status);
        }
    }

    /**
     * Update calendar events
     */
    private function update_calendar_events($booking_id, $status) {
        $property_id = get_field('property', $booking_id);
        $calendars = get_field('calendar_sync', $property_id);

        if (!$calendars) {
            return;
        }

        foreach ($calendars as $calendar) {
            switch ($calendar['calendar_type']) {
                case 'google':
                    $this->update_google_calendar($booking_id, $status, $calendar);
                    break;
                case 'ms365':
                    $this->update_ms365_calendar($booking_id, $status, $calendar);
                    break;
            }
        }
    }

    /**
     * Update Google Calendar event
     */
    private function update_google_calendar($booking_id, $status, $calendar) {
        require_once LODGIFYWP_DIR . '/vendor/google/apiclient/src/Google/Client.php';
        
        $client = new Google_Client();
        $client->setClientId(get_option('lodgifywp_google_client_id'));
        $client->setClientSecret(get_option('lodgifywp_google_client_secret'));
        $client->setRedirectUri(admin_url('admin.php?page=lodgifywp-calendar-settings&action=google-oauth'));
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        
        // Get access token
        $access_token = get_option('lodgifywp_google_access_token');
        if ($access_token) {
            $client->setAccessToken($access_token);
            
            if ($client->isAccessTokenExpired()) {
                $refresh_token = get_option('lodgifywp_google_refresh_token');
                if ($refresh_token) {
                    $client->fetchAccessTokenWithRefreshToken($refresh_token);
                    update_option('lodgifywp_google_access_token', $client->getAccessToken());
                }
            }
        }
        
        if (!$client->getAccessToken()) {
            return;
        }
        
        $service = new Google_Service_Calendar($client);
        
        // Get booking details
        $check_in = get_field('check_in_date', $booking_id);
        $check_out = get_field('check_out_date', $booking_id);
        $property_id = get_field('property', $booking_id);
        $property_title = get_the_title($property_id);
        
        $event = new Google_Service_Calendar_Event(array(
            'summary' => sprintf(
                __('Booking: %s (%s)', 'lodgifywp'),
                $property_title,
                $status
            ),
            'start' => array(
                'date' => $check_in,
            ),
            'end' => array(
                'date' => $check_out,
            ),
            'description' => sprintf(
                __('Booking ID: %d\nStatus: %s', 'lodgifywp'),
                $booking_id,
                $status
            ),
        ));
        
        try {
            $service->events->insert($calendar['calendar_url'], $event);
        } catch (Exception $e) {
            error_log(sprintf(
                'Google Calendar update failed for booking %d: %s',
                $booking_id,
                $e->getMessage()
            ));
        }
    }

    /**
     * Update Microsoft 365 Calendar event
     */
    private function update_ms365_calendar($booking_id, $status, $calendar) {
        require_once LODGIFYWP_DIR . '/vendor/microsoft/microsoft-graph/src/Graph.php';
        
        $graph = new Microsoft\Graph\Graph();
        $access_token = get_option('lodgifywp_ms365_access_token');
        
        if (!$access_token) {
            return;
        }
        
        $graph->setAccessToken($access_token);
        
        // Get booking details
        $check_in = get_field('check_in_date', $booking_id);
        $check_out = get_field('check_out_date', $booking_id);
        $property_id = get_field('property', $booking_id);
        $property_title = get_the_title($property_id);
        
        $event = array(
            'subject' => sprintf(
                __('Booking: %s (%s)', 'lodgifywp'),
                $property_title,
                $status
            ),
            'start' => array(
                'dateTime' => $check_in,
                'timeZone' => 'UTC',
            ),
            'end' => array(
                'dateTime' => $check_out,
                'timeZone' => 'UTC',
            ),
            'body' => array(
                'contentType' => 'text',
                'content' => sprintf(
                    __('Booking ID: %d\nStatus: %s', 'lodgifywp'),
                    $booking_id,
                    $status
                ),
            ),
        );
        
        try {
            $graph->createRequest('POST', '/me/calendars/' . $calendar['calendar_url'] . '/events')
                ->attachBody($event)
                ->execute();
        } catch (Exception $e) {
            error_log(sprintf(
                'Microsoft 365 Calendar update failed for booking %d: %s',
                $booking_id,
                $e->getMessage()
            ));
        }
    }

    /**
     * Send status change notifications
     */
    private function send_status_notifications($booking_id, $old_status, $new_status) {
        $guest_details = get_field('guest_details', $booking_id);
        $property_id = get_field('property', $booking_id);
        $property_title = get_the_title($property_id);

        // Guest notification
        $guest_subject = sprintf(
            __('Booking Status Update - %s', 'lodgifywp'),
            $property_title
        );
        $guest_message = sprintf(
            __('Your booking status has been updated from %s to %s.', 'lodgifywp'),
            $old_status,
            $new_status
        );
        wp_mail($guest_details['email'], $guest_subject, $guest_message);

        // Admin notification
        $admin_email = get_option('admin_email');
        $admin_subject = sprintf(
            __('Booking Status Changed - %s', 'lodgifywp'),
            $property_title
        );
        $admin_message = sprintf(
            __('Booking #%d status changed from %s to %s.', 'lodgifywp'),
            $booking_id,
            $old_status,
            $new_status
        );
        wp_mail($admin_email, $admin_subject, $admin_message);
    }

    /**
     * Handle OAuth callbacks
     */
    public function handle_oauth_callback() {
        if (!isset($_GET['page']) || $_GET['page'] !== 'lodgifywp-calendar-settings') {
            return;
        }

        if (isset($_GET['action']) && $_GET['action'] === 'google-oauth') {
            $this->handle_google_oauth();
        } elseif (isset($_GET['action']) && $_GET['action'] === 'ms365-oauth') {
            $this->handle_ms365_oauth();
        }
    }

    /**
     * Handle Google OAuth
     */
    private function handle_google_oauth() {
        require_once LODGIFYWP_DIR . '/vendor/google/apiclient/src/Google/Client.php';
        
        $client = new Google_Client();
        $client->setClientId(get_option('lodgifywp_google_client_id'));
        $client->setClientSecret(get_option('lodgifywp_google_client_secret'));
        $client->setRedirectUri(admin_url('admin.php?page=lodgifywp-calendar-settings&action=google-oauth'));
        $client->setScopes(Google_Service_Calendar::CALENDAR);
        
        if (isset($_GET['code'])) {
            $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
            update_option('lodgifywp_google_access_token', $token);
            update_option('lodgifywp_google_refresh_token', $token['refresh_token']);
            wp_redirect(admin_url('admin.php?page=lodgifywp-calendar-settings&google-connected=1'));
            exit;
        }
    }

    /**
     * Handle Microsoft 365 OAuth
     */
    private function handle_ms365_oauth() {
        require_once LODGIFYWP_DIR . '/vendor/microsoft/microsoft-graph/src/Graph.php';
        
        $provider = new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => get_option('lodgifywp_ms365_client_id'),
            'clientSecret' => get_option('lodgifywp_ms365_client_secret'),
            'redirectUri' => admin_url('admin.php?page=lodgifywp-calendar-settings&action=ms365-oauth'),
            'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
            'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
            'urlResourceOwnerDetails' => '',
            'scopes' => ['Calendars.ReadWrite'],
        ]);
        
        if (isset($_GET['code'])) {
            $token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code'],
            ]);
            update_option('lodgifywp_ms365_access_token', $token->getToken());
            update_option('lodgifywp_ms365_refresh_token', $token->getRefreshToken());
            wp_redirect(admin_url('admin.php?page=lodgifywp-calendar-settings&ms365-connected=1'));
            exit;
        }
    }
} 