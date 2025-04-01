<?php
/**
 * LodgifyWP API class for license management
 */
class LodgifyWP_API {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('rest_api_init', array($this, 'register_endpoints'));
    }

    /**
     * Register REST API endpoints
     */
    public function register_endpoints() {
        register_rest_route('lodgifywp/v1', '/license/verify', array(
            'methods' => 'POST',
            'callback' => array($this, 'verify_license'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('lodgifywp/v1', '/license/purchase', array(
            'methods' => 'POST',
            'callback' => array($this, 'process_purchase'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('lodgifywp/v1', '/license/staging', array(
            'methods' => 'POST',
            'callback' => array($this, 'create_staging_license'),
            'permission_callback' => '__return_true',
        ));

        register_rest_route('lodgifywp/v1', '/agency/info', array(
            'methods' => 'POST',
            'callback' => array($this, 'get_agency_info'),
            'permission_callback' => '__return_true',
        ));
    }

    /**
     * Get agency license information
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function get_agency_info($request) {
        $license = $request->get_param('license');
        
        if (!$license) {
            return new WP_REST_Response(array(
                'is_agency' => false,
                'message' => 'Missing license parameter',
            ), 400);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'lodgifywp_licenses';
        
        // Get the main agency license
        $license_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE license_key = %s AND license_type = 'agency'",
            $license
        ));

        if (!$license_data) {
            return new WP_REST_Response(array(
                'is_agency' => false,
            ), 200);
        }

        // Get all child licenses (including staging)
        $installations = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE agency_id = %s OR license_key = %s ORDER BY created DESC",
            $license_data->agency_id,
            $license
        ));

        $formatted_installations = array();
        foreach ($installations as $install) {
            $domains = maybe_unserialize($install->domains);
            foreach ($domains as $domain) {
                $formatted_installations[] = array(
                    'domain' => $domain,
                    'is_staging' => (bool) $install->is_staging,
                    'status' => $install->status,
                    'expires' => $install->expires,
                );
            }
        }

        return new WP_REST_Response(array(
            'is_agency' => true,
            'agency_name' => $license_data->agency_name,
            'installations' => $formatted_installations,
            'can_create_staging' => true,
        ), 200);
    }

    /**
     * Create staging license
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function create_staging_license($request) {
        $url = $request->get_param('url');
        $parent_license = $request->get_param('parent_license');

        if (!$url) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Missing URL parameter',
            ), 400);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'lodgifywp_licenses';

        // Check if URL already has a staging license
        $existing = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE domains LIKE %s AND is_staging = 1",
            '%' . $wpdb->esc_like($url) . '%'
        ));

        if ($existing) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'A staging license already exists for this domain',
            ), 400);
        }

        // If parent license is provided, get agency information
        $agency_info = null;
        if ($parent_license) {
            $agency_info = $wpdb->get_row($wpdb->prepare(
                "SELECT agency_id, agency_name FROM {$table} WHERE license_key = %s",
                $parent_license
            ));
        }

        // Generate staging license
        $license_key = $this->generate_license_key();
        $expires = date('Y-m-d H:i:s', strtotime('+7 days'));

        $wpdb->insert($table, array(
            'license_key' => $license_key,
            'email' => '',
            'created' => current_time('mysql'),
            'expires' => $expires,
            'domains' => serialize(array($url)),
            'domain_limit' => 1,
            'status' => 'active',
            'is_staging' => 1,
            'agency_id' => $agency_info ? $agency_info->agency_id : null,
            'agency_name' => $agency_info ? $agency_info->agency_name : null,
            'parent_license_key' => $parent_license,
            'license_type' => 'staging'
        ));

        return new WP_REST_Response(array(
            'success' => true,
            'license_key' => $license_key,
            'expires' => $expires,
        ), 200);
    }

    /**
     * Verify license
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function verify_license($request) {
        $license = $request->get_param('license');
        $url = $request->get_param('url');

        if (!$license || !$url) {
            return new WP_REST_Response(array(
                'valid' => false,
                'message' => 'Missing parameters',
            ), 400);
        }

        global $wpdb;
        $table = $wpdb->prefix . 'lodgifywp_licenses';
        
        $license_data = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM {$table} WHERE license_key = %s",
            $license
        ));

        if (!$license_data) {
            return new WP_REST_Response(array(
                'valid' => false,
                'message' => 'Invalid license',
            ), 200);
        }

        // Check if license is expired
        if (strtotime($license_data->expires) < time()) {
            return new WP_REST_Response(array(
                'valid' => false,
                'message' => 'License expired',
            ), 200);
        }

        // Check domain limit
        $domains = maybe_unserialize($license_data->domains);
        if (!in_array($url, $domains) && count($domains) >= $license_data->domain_limit) {
            return new WP_REST_Response(array(
                'valid' => false,
                'message' => 'Domain limit reached',
            ), 200);
        }

        // Add domain if not already registered
        if (!in_array($url, $domains)) {
            $domains[] = $url;
            $wpdb->update(
                $table,
                array('domains' => serialize($domains)),
                array('id' => $license_data->id)
            );
        }

        return new WP_REST_Response(array(
            'valid' => true,
            'message' => 'License valid',
            'expires' => $license_data->expires,
            'is_staging' => (bool) $license_data->is_staging,
        ), 200);
    }

    /**
     * Process license purchase
     *
     * @param WP_REST_Request $request Request object
     * @return WP_REST_Response
     */
    public function process_purchase($request) {
        $email = $request->get_param('email');
        $payment_intent = $request->get_param('payment_intent');
        $agency_name = $request->get_param('agency_name');
        $license_type = $request->get_param('license_type');

        if (!$email || !$payment_intent) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => 'Missing parameters',
            ), 400);
        }

        // Verify payment with Stripe
        try {
            $stripe = new \Stripe\StripeClient(STRIPE_SECRET_KEY);
            $intent = $stripe->paymentIntents->retrieve($payment_intent);
            
            if ($intent->status !== 'succeeded') {
                throw new Exception('Payment not completed');
            }

            // Generate license key and agency ID
            $license_key = $this->generate_license_key();
            $agency_id = $agency_name ? 'agency_' . wp_generate_password(12, false) : null;

            // Store license
            global $wpdb;
            $table = $wpdb->prefix . 'lodgifywp_licenses';
            
            $wpdb->insert($table, array(
                'license_key' => $license_key,
                'email' => $email,
                'created' => current_time('mysql'),
                'expires' => date('Y-m-d H:i:s', strtotime('+1 year')),
                'domains' => serialize(array()),
                'domain_limit' => $license_type === 'agency' ? 999999 : 1,
                'status' => 'active',
                'agency_name' => $agency_name,
                'agency_id' => $agency_id,
                'license_type' => $license_type ?: 'standard'
            ));

            // Send license key email
            $this->send_license_email($email, $license_key, $license_type === 'agency');

            return new WP_REST_Response(array(
                'success' => true,
                'message' => 'License created successfully',
                'license_key' => $license_key,
            ), 200);

        } catch (Exception $e) {
            return new WP_REST_Response(array(
                'success' => false,
                'message' => $e->getMessage(),
            ), 500);
        }
    }

    /**
     * Generate a unique license key
     *
     * @return string
     */
    private function generate_license_key() {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $key = '';
        
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < 4; $j++) {
                $key .= $chars[rand(0, strlen($chars) - 1)];
            }
            if ($i < 3) {
                $key .= '-';
            }
        }

        return $key;
    }

    /**
     * Send license key email
     *
     * @param string $email Email address
     * @param string $license_key License key
     */
    private function send_license_email($email, $license_key, $is_agency = false) {
        $subject = 'Your LodgifyWP License Key';
        
        $message = "Thank you for purchasing LodgifyWP!\n\n";
        $message .= "Your license key is: {$license_key}\n\n";
        $message .= "To activate your license:\n";
        $message .= "1. Go to your WordPress admin panel\n";
        $message .= "2. Navigate to LodgifyWP > License\n";
        $message .= "3. Enter your license key\n\n";
        $message .= "If you need help, please contact support@wonkypixel.co.uk\n";

        if ($is_agency) {
            $message .= "\nThis is an agency license. Please contact the agency for further assistance.\n";
        }

        wp_mail($email, $subject, $message);
    }
}

// Initialize the API
new LodgifyWP_API(); 