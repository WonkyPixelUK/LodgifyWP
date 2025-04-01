<?php
/**
 * Calendar Settings Admin Page Template
 *
 * @package LodgifyWP
 */

// Save settings if form is submitted
if (isset($_POST['lodgifywp_save_calendar_settings'])) {
    check_admin_referer('lodgifywp_calendar_settings');

    // Save API credentials
    update_option('lodgifywp_google_client_id', sanitize_text_field($_POST['google_client_id']));
    update_option('lodgifywp_google_client_secret', sanitize_text_field($_POST['google_client_secret']));
    update_option('lodgifywp_ms365_client_id', sanitize_text_field($_POST['ms365_client_id']));
    update_option('lodgifywp_ms365_client_secret', sanitize_text_field($_POST['ms365_client_secret']));

    // Save booking statuses
    $statuses = array();
    foreach ($_POST['status'] as $key => $status) {
        $statuses[$key] = array(
            'label' => sanitize_text_field($status['label']),
            'color' => sanitize_hex_color($status['color']),
        );
    }
    update_option('lodgifywp_booking_statuses', $statuses);

    echo '<div class="notice notice-success"><p>' . __('Settings saved successfully.', 'lodgifywp') . '</p></div>';
}
?>

<div class="wrap">
    <h1><?php _e('Calendar Settings', 'lodgifywp'); ?></h1>

    <form method="post" action="">
        <?php wp_nonce_field('lodgifywp_calendar_settings'); ?>

        <div class="card">
            <h2><?php _e('Google Calendar Integration', 'lodgifywp'); ?></h2>
            <p class="description">
                <?php _e('Enter your Google Calendar API credentials. You can obtain these from the Google Cloud Console.', 'lodgifywp'); ?>
                <a href="https://console.cloud.google.com/apis/credentials" target="_blank"><?php _e('Learn More', 'lodgifywp'); ?></a>
            </p>
            
            <?php
            $google_connected = get_option('lodgifywp_google_access_token') ? true : false;
            $google_status_class = $google_connected ? 'connected' : 'disconnected';
            $google_status_text = $google_connected ? __('Connected', 'lodgifywp') : __('Disconnected', 'lodgifywp');
            ?>
            
            <div class="connection-status <?php echo $google_status_class; ?>">
                <span class="status-indicator"></span>
                <span class="status-text"><?php echo $google_status_text; ?></span>
            </div>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="google_client_id"><?php _e('Client ID', 'lodgifywp'); ?></label></th>
                    <td>
                        <input type="text" id="google_client_id" name="google_client_id" class="regular-text"
                               value="<?php echo esc_attr($google_client_id); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="google_client_secret"><?php _e('Client Secret', 'lodgifywp'); ?></label></th>
                    <td>
                        <input type="password" id="google_client_secret" name="google_client_secret" class="regular-text"
                               value="<?php echo esc_attr($google_client_secret); ?>">
                    </td>
                </tr>
            </table>
            
            <?php if ($google_client_id && $google_client_secret) : ?>
                <?php
                require_once LODGIFYWP_DIR . '/vendor/google/apiclient/src/Google/Client.php';
                $client = new Google_Client();
                $client->setClientId($google_client_id);
                $client->setClientSecret($google_client_secret);
                $client->setRedirectUri(admin_url('admin.php?page=lodgifywp-calendar-settings&action=google-oauth'));
                $client->setScopes(Google_Service_Calendar::CALENDAR);
                $auth_url = $client->createAuthUrl();
                ?>
                <p>
                    <a href="<?php echo esc_url($auth_url); ?>" class="button button-primary">
                        <?php _e('Connect with Google Calendar', 'lodgifywp'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2><?php _e('Microsoft 365 Integration', 'lodgifywp'); ?></h2>
            <p class="description">
                <?php _e('Enter your Microsoft 365 API credentials. You can obtain these from the Azure Portal.', 'lodgifywp'); ?>
                <a href="https://portal.azure.com/#blade/Microsoft_AAD_RegisteredApps/ApplicationsListBlade" target="_blank"><?php _e('Learn More', 'lodgifywp'); ?></a>
            </p>
            
            <?php
            $ms365_connected = get_option('lodgifywp_ms365_access_token') ? true : false;
            $ms365_status_class = $ms365_connected ? 'connected' : 'disconnected';
            $ms365_status_text = $ms365_connected ? __('Connected', 'lodgifywp') : __('Disconnected', 'lodgifywp');
            ?>
            
            <div class="connection-status <?php echo $ms365_status_class; ?>">
                <span class="status-indicator"></span>
                <span class="status-text"><?php echo $ms365_status_text; ?></span>
            </div>
            
            <table class="form-table">
                <tr>
                    <th scope="row"><label for="ms365_client_id"><?php _e('Client ID', 'lodgifywp'); ?></label></th>
                    <td>
                        <input type="text" id="ms365_client_id" name="ms365_client_id" class="regular-text"
                               value="<?php echo esc_attr($ms365_client_id); ?>">
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="ms365_client_secret"><?php _e('Client Secret', 'lodgifywp'); ?></label></th>
                    <td>
                        <input type="password" id="ms365_client_secret" name="ms365_client_secret" class="regular-text"
                               value="<?php echo esc_attr($ms365_client_secret); ?>">
                    </td>
                </tr>
            </table>
            
            <?php if ($ms365_client_id && $ms365_client_secret) : ?>
                <?php
                $provider = new \League\OAuth2\Client\Provider\GenericProvider([
                    'clientId' => $ms365_client_id,
                    'clientSecret' => $ms365_client_secret,
                    'redirectUri' => admin_url('admin.php?page=lodgifywp-calendar-settings&action=ms365-oauth'),
                    'urlAuthorize' => 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize',
                    'urlAccessToken' => 'https://login.microsoftonline.com/common/oauth2/v2.0/token',
                    'urlResourceOwnerDetails' => '',
                    'scopes' => ['Calendars.ReadWrite'],
                ]);
                $auth_url = $provider->getAuthorizationUrl();
                ?>
                <p>
                    <a href="<?php echo esc_url($auth_url); ?>" class="button button-primary">
                        <?php _e('Connect with Microsoft 365', 'lodgifywp'); ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2><?php _e('Booking Statuses', 'lodgifywp'); ?></h2>
            <p class="description"><?php _e('Configure booking statuses and their colors. These will be used in the calendar view and notifications.', 'lodgifywp'); ?></p>
            
            <div id="booking-statuses">
                <?php foreach ($booking_statuses as $key => $status) : ?>
                    <div class="booking-status">
                        <input type="text" name="status[<?php echo esc_attr($key); ?>][label]"
                               value="<?php echo esc_attr($status['label']); ?>"
                               placeholder="<?php _e('Status Label', 'lodgifywp'); ?>">
                        <input type="color" name="status[<?php echo esc_attr($key); ?>][color]"
                               value="<?php echo esc_attr($status['color']); ?>">
                        <button type="button" class="button remove-status"><?php _e('Remove', 'lodgifywp'); ?></button>
                    </div>
                <?php endforeach; ?>
            </div>

            <button type="button" class="button add-status"><?php _e('Add Status', 'lodgifywp'); ?></button>
        </div>

        <p class="submit">
            <input type="submit" name="lodgifywp_save_calendar_settings" class="button button-primary"
                   value="<?php _e('Save Settings', 'lodgifywp'); ?>">
        </p>
    </form>
</div>

<style>
.card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    margin-top: 20px;
    padding: 20px;
}

.booking-status {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.booking-status input[type="text"] {
    flex: 1;
}

.booking-status input[type="color"] {
    width: 50px;
    padding: 0;
    height: 30px;
}

.connection-status {
    display: flex;
    align-items: center;
    gap: 8px;
    margin: 15px 0;
    padding: 10px;
    border-radius: 4px;
    background: #f8f9fa;
}

.status-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.connected .status-indicator {
    background-color: #28a745;
}

.disconnected .status-indicator {
    background-color: #dc3545;
}

.status-text {
    font-weight: 500;
}

.connected .status-text {
    color: #28a745;
}

.disconnected .status-text {
    color: #dc3545;
}
</style>

<script>
jQuery(document).ready(function($) {
    // Add new status
    $('.add-status').on('click', function() {
        const key = Date.now();
        const html = `
            <div class="booking-status">
                <input type="text" name="status[${key}][label]" placeholder="<?php _e('Status Label', 'lodgifywp'); ?>">
                <input type="color" name="status[${key}][color]" value="#000000">
                <button type="button" class="button remove-status"><?php _e('Remove', 'lodgifywp'); ?></button>
            </div>
        `;
        $('#booking-statuses').append(html);
    });

    // Remove status
    $(document).on('click', '.remove-status', function() {
        $(this).closest('.booking-status').remove();
    });
});
</script> 