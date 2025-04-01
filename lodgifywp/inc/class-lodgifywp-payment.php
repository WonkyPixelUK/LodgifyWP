<?php
/**
 * Payment Class
 *
 * @package LodgifyWP
 */

class LodgifyWP_Payment {
    private $stripe;

    /**
     * Initialize the class
     */
    public function init() {
        if (!class_exists('Stripe\Stripe')) {
            require_once LODGIFYWP_DIR . '/vendor/autoload.php';
        }

        \Stripe\Stripe::setApiKey(get_option('lodgifywp_stripe_secret_key'));
        
        add_action('rest_api_init', array($this, 'register_rest_routes'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_ajax_lodgifywp_process_payment', array($this, 'process_payment'));
        add_action('wp_ajax_nopriv_lodgifywp_process_payment', array($this, 'process_payment'));
    }

    /**
     * Register REST API routes
     */
    public function register_rest_routes() {
        register_rest_route('lodgifywp/v1', '/create-payment-intent', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_payment_intent'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Enqueue necessary scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script('stripe-js', 'https://js.stripe.com/v3/', array(), null, true);
        wp_enqueue_script('lodgifywp-payment', LODGIFYWP_URI . '/assets/js/payment.js', array('jquery', 'stripe-js'), LODGIFYWP_VERSION, true);
        
        wp_localize_script('lodgifywp-payment', 'lodgifywpPayment', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'stripe_key' => get_option('lodgifywp_stripe_publishable_key'),
            'nonce' => wp_create_nonce('lodgifywp-payment'),
        ));
    }

    /**
     * Create a payment intent
     */
    public function create_payment_intent($request) {
        $booking_id = $request->get_param('booking_id');
        $amount = $request->get_param('amount');

        if (!$booking_id || !$amount) {
            return new WP_Error('missing_parameters', 'Missing required parameters', array('status' => 400));
        }

        try {
            $intent = \Stripe\PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents
                'currency' => 'usd',
                'metadata' => [
                    'booking_id' => $booking_id,
                ],
            ]);

            return array(
                'client_secret' => $intent->client_secret,
            );
        } catch (\Exception $e) {
            return new WP_Error('stripe_error', $e->getMessage(), array('status' => 500));
        }
    }

    /**
     * Process the payment
     */
    public function process_payment() {
        check_ajax_referer('lodgifywp-payment', 'nonce');

        $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
        $payment_intent_id = isset($_POST['payment_intent_id']) ? sanitize_text_field($_POST['payment_intent_id']) : '';

        if (!$booking_id || !$payment_intent_id) {
            wp_send_json_error(array('message' => 'Missing required parameters'));
        }

        try {
            $intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);

            if ($intent->status === 'succeeded') {
                // Update booking payment status
                update_field('payment_details_payment_id', $payment_intent_id, $booking_id);
                update_field('payment_details_payment_status', 'paid', $booking_id);
                update_field('status', 'confirmed', $booking_id);

                // Send confirmation emails
                $this->send_confirmation_emails($booking_id);

                wp_send_json_success(array(
                    'message' => 'Payment processed successfully',
                    'booking_id' => $booking_id,
                ));
            } else {
                wp_send_json_error(array('message' => 'Payment failed'));
            }
        } catch (\Exception $e) {
            wp_send_json_error(array('message' => $e->getMessage()));
        }
    }

    /**
     * Send confirmation emails
     */
    private function send_confirmation_emails($booking_id) {
        $booking = get_post($booking_id);
        $property_id = get_field('property', $booking_id);
        $guest_details = get_field('guest_details', $booking_id);
        $check_in = get_field('check_in_date', $booking_id);
        $check_out = get_field('check_out_date', $booking_id);
        $total_price = get_field('total_price', $booking_id);

        // Guest email
        $guest_subject = sprintf(__('Booking Confirmation - %s', 'lodgifywp'), get_the_title($property_id));
        $guest_message = $this->get_guest_email_content($booking_id);
        wp_mail($guest_details['email'], $guest_subject, $guest_message);

        // Admin email
        $admin_email = get_option('admin_email');
        $admin_subject = sprintf(__('New Booking - %s', 'lodgifywp'), get_the_title($property_id));
        $admin_message = $this->get_admin_email_content($booking_id);
        wp_mail($admin_email, $admin_subject, $admin_message);
    }

    /**
     * Get guest email content
     */
    private function get_guest_email_content($booking_id) {
        ob_start();
        include LODGIFYWP_DIR . '/template-parts/emails/guest-confirmation.php';
        return ob_get_clean();
    }

    /**
     * Get admin email content
     */
    private function get_admin_email_content($booking_id) {
        ob_start();
        include LODGIFYWP_DIR . '/template-parts/emails/admin-notification.php';
        return ob_get_clean();
    }
} 