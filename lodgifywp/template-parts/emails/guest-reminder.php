<?php
/**
 * Guest Reminder Email Template
 *
 * @package LodgifyWP
 */

// Get owner photo
$owner_photo = get_field('owner_photo', $owner_id);
$owner_photo_url = $owner_photo ? $owner_photo['sizes']['thumbnail'] : '';
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
        .owner-info {
            display: flex;
            align-items: center;
            margin: 20px 0;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        .owner-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-right: 15px;
        }
        .owner-details {
            flex: 1;
        }
        .welcome-message {
            font-style: italic;
            margin: 20px 0;
            padding: 15px;
            background: #fff3cd;
            border-left: 4px solid #ffc107;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php _e('Your Stay is Coming Up!', 'lodgifywp'); ?></h1>
        </div>

        <p><?php printf(
            __('Dear %s,', 'lodgifywp'),
            esc_html($guest_details['name'])
        ); ?></p>

        <p><?php printf(
            __('We hope you\'re excited about your upcoming stay at %s! Here\'s a reminder of your booking details:', 'lodgifywp'),
            esc_html($property_title)
        ); ?></p>

        <div class="details-box">
            <h3><?php _e('Booking Details', 'lodgifywp'); ?></h3>
            <p><strong><?php _e('Check-in:', 'lodgifywp'); ?></strong> <?php echo esc_html($check_in_date); ?> at <?php echo esc_html($check_in_time); ?></p>
            <p><strong><?php _e('Property Address:', 'lodgifywp'); ?></strong><br><?php echo nl2br(esc_html($property_address)); ?></p>
        </div>

        <div class="owner-info">
            <?php if ($owner_photo_url) : ?>
                <img src="<?php echo esc_url($owner_photo_url); ?>" alt="<?php echo esc_attr($owner_name); ?>" class="owner-photo">
            <?php endif; ?>
            <div class="owner-details">
                <h3><?php _e('Your Host', 'lodgifywp'); ?></h3>
                <p><strong><?php echo esc_html($owner_name); ?></strong></p>
                <p><?php _e('Phone:', 'lodgifywp'); ?> <?php echo esc_html($owner_phone); ?></p>
            </div>
        </div>

        <?php if ($owner_welcome) : ?>
            <div class="welcome-message">
                <?php echo wpautop(esc_html($owner_welcome)); ?>
            </div>
        <?php endif; ?>

        <p><?php _e('Need to get in touch? You can reach your host using the contact details above.', 'lodgifywp'); ?></p>

        <p><?php _e('We look forward to welcoming you!', 'lodgifywp'); ?></p>

        <a href="<?php echo esc_url(get_permalink($booking_id)); ?>" class="button">
            <?php _e('View Booking Details', 'lodgifywp'); ?>
        </a>
    </div>
</body>
</html> 