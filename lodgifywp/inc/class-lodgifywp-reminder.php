<?php
/**
 * Reminder System Class
 *
 * @package LodgifyWP
 */

use Timber\Timber;

class LodgifyWP_Reminder {
    /**
     * Initialize the reminder system
     */
    public function init() {
        // Schedule daily reminder check
        add_action('init', array($this, 'schedule_reminder_check'));
        add_action('lodgifywp_check_reminders', array($this, 'check_reminders'));
    }

    /**
     * Schedule daily reminder check
     */
    public function schedule_reminder_check() {
        if (!wp_next_scheduled('lodgifywp_check_reminders')) {
            wp_schedule_event(strtotime('tomorrow midnight'), 'daily', 'lodgifywp_check_reminders');
        }
    }

    /**
     * Check for reminders that need to be sent
     */
    public function check_reminders() {
        $bookings = get_posts(array(
            'post_type' => 'booking',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => 'check_in_date',
                    'value' => date('Y-m-d'),
                    'compare' => '>',
                    'type' => 'DATE',
                ),
                array(
                    'key' => 'reminder_sent',
                    'compare' => 'NOT EXISTS',
                ),
            ),
        ));

        foreach ($bookings as $booking) {
            $this->process_booking_reminder($booking->ID);
        }
    }

    /**
     * Process reminder for a specific booking
     */
    private function process_booking_reminder($booking_id) {
        $check_in_date = get_field('check_in_date', $booking_id);
        $property_id = get_field('property', $booking_id);
        $owner_id = get_field('property_owner', $property_id);
        $reminder_days = get_field('owner_reminder_days', $owner_id);
        
        if (!$reminder_days) {
            $reminder_days = 2; // Default to 2 days if not set
        }

        $days_until_checkin = (strtotime($check_in_date) - time()) / DAY_IN_SECONDS;

        if ($days_until_checkin <= $reminder_days) {
            $this->send_guest_reminder($booking_id);
            $this->send_owner_reminder($booking_id);
            update_post_meta($booking_id, 'reminder_sent', true);
        }
    }

    /**
     * Send reminder to guest
     */
    private function send_guest_reminder($booking_id) {
        $guest_details = get_field('guest_details', $booking_id);
        $property_id = get_field('property', $booking_id);
        $owner_id = get_field('property_owner', $property_id);

        $context = array(
            'email_title' => sprintf(
                __('Reminder: Your stay at %s is coming up!', 'lodgifywp'),
                get_the_title($property_id)
            ),
            'guest' => $guest_details,
            'property' => array(
                'title' => get_the_title($property_id),
            ),
            'check_in_date' => get_field('check_in_date', $booking_id),
            'check_in_time' => get_field('check_in_time', $property_id),
            'property_address' => get_field('property_address', $property_id),
            'owner' => array(
                'name' => get_the_title($owner_id),
                'phone' => get_field('owner_phone', $owner_id),
                'photo' => get_field('owner_photo', $owner_id),
                'welcome_message' => get_field('owner_welcome_message', $owner_id),
            ),
            'booking_url' => get_permalink($booking_id),
        );

        $message = Timber::compile('emails/guest-reminder.twig', $context);
        
        wp_mail(
            $guest_details['email'],
            $context['email_title'],
            $message,
            array('Content-Type: text/html; charset=UTF-8')
        );
    }

    /**
     * Send reminder to owner
     */
    private function send_owner_reminder($booking_id) {
        $property_id = get_field('property', $booking_id);
        $owner_id = get_field('property_owner', $property_id);
        $guest_details = get_field('guest_details', $booking_id);

        $context = array(
            'email_title' => sprintf(
                __('Reminder: Upcoming guest arrival at %s', 'lodgifywp'),
                get_the_title($property_id)
            ),
            'owner' => array(
                'name' => get_the_title($owner_id),
            ),
            'guest' => $guest_details,
            'guest_count' => get_field('guest_count', $booking_id),
            'check_in_date' => get_field('check_in_date', $booking_id),
            'check_out_date' => get_field('check_out_date', $booking_id),
            'booking_edit_url' => get_edit_post_link($booking_id),
        );

        $message = Timber::compile('emails/owner-reminder.twig', $context);
        
        wp_mail(
            get_field('owner_email', $owner_id),
            $context['email_title'],
            $message,
            array('Content-Type: text/html; charset=UTF-8')
        );
    }
} 