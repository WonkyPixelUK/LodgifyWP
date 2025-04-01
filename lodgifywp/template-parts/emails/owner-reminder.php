<?php
/**
 * Owner Reminder Email Template
 *
 * @package LodgifyWP
 */
?>

<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .guest-info {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .details-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
        }
        .checklist {
            background: #e9ecef;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .checklist ul {
            list-style-type: none;
            padding: 0;
        }
        .checklist li {
            margin-bottom: 10px;
            padding-left: 25px;
            position: relative;
        }
        .checklist li:before {
            content: "☐";
            position: absolute;
            left: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php _e('Upcoming Guest Arrival', 'lodgifywp'); ?></h1>
        </div>

        <p><?php printf(
            __('Hello %s,', 'lodgifywp'),
            get_the_title($owner_id)
        ); ?></p>

        <p><?php _e('This is a reminder that you have an upcoming guest arrival at your property.', 'lodgifywp'); ?></p>

        <div class="guest-info">
            <h3><?php _e('Guest Information', 'lodgifywp'); ?></h3>
            <p><strong><?php _e('Name:', 'lodgifywp'); ?></strong> <?php echo esc_html($guest_details['name']); ?></p>
            <p><strong><?php _e('Phone:', 'lodgifywp'); ?></strong> <?php echo esc_html($guest_details['phone']); ?></p>
            <p><strong><?php _e('Email:', 'lodgifywp'); ?></strong> <?php echo esc_html($guest_details['email']); ?></p>
            <p><strong><?php _e('Number of Guests:', 'lodgifywp'); ?></strong> <?php echo esc_html($guest_count); ?></p>
        </div>

        <div class="details-box">
            <h3><?php _e('Stay Details', 'lodgifywp'); ?></h3>
            <p><strong><?php _e('Check-in:', 'lodgifywp'); ?></strong> <?php echo esc_html($check_in_date); ?></p>
            <p><strong><?php _e('Check-out:', 'lodgifywp'); ?></strong> <?php echo esc_html($check_out_date); ?></p>
        </div>

        <div class="checklist">
            <h3><?php _e('Pre-arrival Checklist', 'lodgifywp'); ?></h3>
            <ul>
                <li><?php _e('Clean and prepare the property', 'lodgifywp'); ?></li>
                <li><?php _e('Check all amenities are working', 'lodgifywp'); ?></li>
                <li><?php _e('Ensure fresh linens and towels are available', 'lodgifywp'); ?></li>
                <li><?php _e('Prepare welcome package (if applicable)', 'lodgifywp'); ?></li>
                <li><?php _e('Check heating/cooling systems', 'lodgifywp'); ?></li>
                <li><?php _e('Test all keys/access codes', 'lodgifywp'); ?></li>
                <li><?php _e('Update house manual if needed', 'lodgifywp'); ?></li>
            </ul>
        </div>

        <p><?php _e('Please ensure everything is ready for your guest\'s arrival.', 'lodgifywp'); ?></p>

        <a href="<?php echo esc_url(get_edit_post_link($booking_id)); ?>" class="button">
            <?php _e('View Booking Details', 'lodgifywp'); ?>
        </a>
    </div>
</body>
</html> 