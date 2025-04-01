<?php
/**
 * License management class
 *
 * @package LodgifyWP
 */

defined('ABSPATH') || exit;

/**
 * LodgifyWP_License class
 */
class LodgifyWP_License {

    /**
     * The single instance of the class.
     *
     * @var LodgifyWP_License
     */
    protected static $_instance = null;

    /**
     * License API endpoint
     *
     * @var string
     */
    private $api_url = 'https://wonkypixel.io/wp-json/lodgifywp/v1/license';

    /**
     * Main LodgifyWP_License Instance.
     *
     * @return LodgifyWP_License
     */
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Constructor.
     */
    public function __construct() {
        add_action('admin_init', array($this, 'check_license'));
        add_action('admin_menu', array($this, 'add_license_menu'));
        add_action('admin_notices', array($this, 'admin_notices'));
        add_action('wp_ajax_create_staging_license', array($this, 'create_staging_license'));
    }

    /**
     * Add license menu item
     */
    public function add_license_menu() {
        add_submenu_page(
            'lodgifywp',
            __('License', 'lodgifywp'),
            __('License', 'lodgifywp'),
            'manage_options',
            'lodgifywp-license',
            array($this, 'license_page')
        );
    }

    /**
     * License page callback
     */
    public function license_page() {
        $license = get_option('lodgifywp_license_key');
        $status  = get_option('lodgifywp_license_status');
        $is_staging = get_option('lodgifywp_is_staging_license', false);
        $staging_expiry = get_option('lodgifywp_staging_expiry');
        $agency_info = $this->get_agency_info($license);
        ?>
        <div class="wrap">
            <h2><?php esc_html_e('LodgifyWP License', 'lodgifywp'); ?></h2>
            
            <?php if ($is_staging && $staging_expiry) : ?>
                <div class="notice notice-warning">
                    <p>
                        <?php
                        $days_left = ceil((strtotime($staging_expiry) - time()) / DAY_IN_SECONDS);
                        printf(
                            esc_html__('This is a staging license. It will expire in %d days on %s.', 'lodgifywp'),
                            $days_left,
                            date_i18n(get_option('date_format'), strtotime($staging_expiry))
                        );
                        ?>
                    </p>
                </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php settings_fields('lodgifywp_license'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php esc_html_e('License Key', 'lodgifywp'); ?>
                            </th>
                            <td>
                                <input type="text" 
                                       class="regular-text" 
                                       id="lodgifywp_license_key" 
                                       name="lodgifywp_license_key" 
                                       value="<?php echo esc_attr($license); ?>" 
                                />
                                <?php if ($status === 'valid') : ?>
                                    <span class="description" style="color:green;">
                                        <?php esc_html_e('Active', 'lodgifywp'); ?>
                                    </span>
                                <?php else : ?>
                                    <span class="description" style="color:red;">
                                        <?php esc_html_e('Inactive', 'lodgifywp'); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <?php submit_button(); ?>
            </form>

            <?php if ($status === 'valid' && $agency_info) : ?>
                <div class="agency-info-section">
                    <h3><?php echo esc_html(sprintf(__('Agency: %s', 'lodgifywp'), $agency_info['name'])); ?></h3>
                    
                    <h4><?php esc_html_e('Active Installations', 'lodgifywp'); ?></h4>
                    <table class="wp-list-table widefat fixed striped">
                        <thead>
                            <tr>
                                <th><?php esc_html_e('Domain', 'lodgifywp'); ?></th>
                                <th><?php esc_html_e('License Type', 'lodgifywp'); ?></th>
                                <th><?php esc_html_e('Status', 'lodgifywp'); ?></th>
                                <th><?php esc_html_e('Expiry', 'lodgifywp'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($agency_info['installations'] as $install) : ?>
                                <tr>
                                    <td><?php echo esc_html($install['domain']); ?></td>
                                    <td>
                                        <?php 
                                        echo esc_html(
                                            $install['is_staging'] 
                                                ? __('Staging', 'lodgifywp') 
                                                : __('Production', 'lodgifywp')
                                        ); 
                                        ?>
                                    </td>
                                    <td><?php echo esc_html($install['status']); ?></td>
                                    <td><?php echo esc_html($install['expires']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php if ($agency_info['can_create_staging']) : ?>
                        <div class="staging-license-section" style="margin-top: 30px;">
                            <h4><?php esc_html_e('Create New Staging License', 'lodgifywp'); ?></h4>
                            <p><?php esc_html_e('Create a new 7-day staging license for testing.', 'lodgifywp'); ?></p>
                            <button type="button" class="button button-secondary" id="create-staging-license">
                                <?php esc_html_e('Create Staging License', 'lodgifywp'); ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php elseif (!$is_staging && (!$license || $status !== 'valid')) : ?>
                <div class="staging-license-section" style="margin-top: 30px;">
                    <h3><?php esc_html_e('Create Staging License', 'lodgifywp'); ?></h3>
                    <p><?php esc_html_e('Need to test LodgifyWP? Create a 7-day staging license to try all premium features.', 'lodgifywp'); ?></p>
                    <button type="button" class="button button-secondary" id="create-staging-license">
                        <?php esc_html_e('Create Staging License', 'lodgifywp'); ?>
                    </button>
                </div>
            <?php endif; ?>

            <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#create-staging-license').on('click', function() {
                    var button = $(this);
                    button.prop('disabled', true);
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'create_staging_license',
                            nonce: '<?php echo wp_create_nonce('create_staging_license'); ?>',
                            site_url: '<?php echo esc_url(home_url()); ?>'
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload();
                            } else {
                                alert(response.data.message);
                                button.prop('disabled', false);
                            }
                        },
                        error: function() {
                            alert('<?php esc_html_e('Error creating staging license', 'lodgifywp'); ?>');
                            button.prop('disabled', false);
                        }
                    });
                });
            });
            </script>
        </div>
        <?php
    }

    /**
     * Get agency license information
     *
     * @param string $license_key License key
     * @return array|false Agency information or false if not an agency license
     */
    private function get_agency_info($license_key) {
        if (!$license_key) {
            return false;
        }

        $response = wp_remote_post($this->api_url . '/agency/info', array(
            'timeout' => 15,
            'body' => array(
                'license' => $license_key,
                'url' => home_url(),
            ),
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $data = json_decode(wp_remote_retrieve_body($response), true);
        
        if (!$data || !isset($data['is_agency']) || !$data['is_agency']) {
            return false;
        }

        return array(
            'name' => $data['agency_name'],
            'installations' => $data['installations'],
            'can_create_staging' => $data['can_create_staging'],
        );
    }

    /**
     * Create staging license via AJAX
     */
    public function create_staging_license() {
        check_ajax_referer('create_staging_license', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized'));
        }

        $site_url = $_POST['site_url'];
        
        $response = wp_remote_post($this->api_url . '/staging', array(
            'timeout' => 15,
            'body' => array(
                'url' => $site_url,
            ),
        ));

        if (is_wp_error($response)) {
            wp_send_json_error(array('message' => $response->get_error_message()));
        }

        $result = json_decode(wp_remote_retrieve_body($response));

        if (!$result || !isset($result->license_key)) {
            wp_send_json_error(array('message' => 'Invalid response from license server'));
        }

        update_option('lodgifywp_license_key', $result->license_key);
        update_option('lodgifywp_license_status', 'valid');
        update_option('lodgifywp_is_staging_license', true);
        update_option('lodgifywp_staging_expiry', $result->expires);

        wp_send_json_success(array(
            'license_key' => $result->license_key,
            'expires' => $result->expires
        ));
    }

    /**
     * Check if license is valid
     *
     * @return bool
     */
    public function is_valid() {
        $status = get_option('lodgifywp_license_status');
        
        // Check staging license expiration
        if (get_option('lodgifywp_is_staging_license')) {
            $expiry = get_option('lodgifywp_staging_expiry');
            if ($expiry && strtotime($expiry) < time()) {
                update_option('lodgifywp_license_status', 'invalid');
                update_option('lodgifywp_is_staging_license', false);
                delete_option('lodgifywp_staging_expiry');
                return false;
            }
        }
        
        return $status === 'valid';
    }

    /**
     * Check license with API
     */
    public function check_license() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $license = get_option('lodgifywp_license_key');
        if (!$license) {
            return;
        }

        // Check license every 7 days
        $last_check = get_option('lodgifywp_license_check');
        if ($last_check && time() - $last_check < 7 * DAY_IN_SECONDS) {
            return;
        }

        $response = wp_remote_post($this->api_url . '/verify', array(
            'timeout' => 15,
            'body' => array(
                'license' => $license,
                'url' => home_url(),
            ),
        ));

        if (is_wp_error($response)) {
            return;
        }

        $license_data = json_decode(wp_remote_retrieve_body($response));
        
        if ($license_data && isset($license_data->valid)) {
            update_option('lodgifywp_license_status', $license_data->valid ? 'valid' : 'invalid');
            update_option('lodgifywp_license_check', time());
        }
    }

    /**
     * Show admin notices
     */
    public function admin_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }

        $license = get_option('lodgifywp_license_key');
        $status  = get_option('lodgifywp_license_status');

        if (!$license || $status !== 'valid') {
            ?>
            <div class="notice notice-warning">
                <p>
                    <?php
                    printf(
                        /* translators: %s: License page URL */
                        esc_html__('Please enter your LodgifyWP license key to enable premium features. %s', 'lodgifywp'),
                        '<a href="' . esc_url(admin_url('admin.php?page=lodgifywp-license')) . '">' . 
                        esc_html__('Enter License Key', 'lodgifywp') . '</a>'
                    );
                    ?>
                </p>
            </div>
            <?php
        }
    }

    /**
     * Check if a feature is available in current license
     *
     * @param string $feature Feature to check
     * @return bool
     */
    public function has_feature($feature) {
        // Free features always available
        $free_features = array(
            'property_listing',
            'basic_booking',
            'basic_email',
        );

        if (in_array($feature, $free_features, true)) {
            return true;
        }

        // Premium features require valid license
        $premium_features = array(
            'stripe_payments',
            'calendar_sync',
            'advanced_email',
            'premium_templates',
        );

        if (in_array($feature, $premium_features, true)) {
            return $this->is_valid();
        }

        return false;
    }
} 