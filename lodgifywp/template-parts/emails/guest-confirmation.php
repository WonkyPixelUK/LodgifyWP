<?php
/**
 * Guest confirmation email template
 *
 * @package LodgifyWP
 */

$booking = get_post($booking_id);
$property_id = get_field('property', $booking_id);
$guest_details = get_field('guest_details', $booking_id);
$check_in = get_field('check_in_date', $booking_id);
$check_out = get_field('check_out_date', $booking_id);
$total_price = get_field('total_price', $booking_id);
$property_address = get_field('location_map', $property_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo sprintf(__('Booking Confirmation - %s', 'lodgifywp'), get_the_title($property_id)); ?></title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2c3e50;"><?php _e('Booking Confirmation', 'lodgifywp'); ?></h1>
    </div>

    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <p><?php echo sprintf(__('Dear %s,', 'lodgifywp'), esc_html($guest_details['name'])); ?></p>
        <p><?php _e('Thank you for your booking! Your reservation has been confirmed.', 'lodgifywp'); ?></p>
    </div>

    <div class="details-box">
        <h3><?php _e('Booking Details', 'lodgifywp'); ?></h3>
        <p><strong><?php _e('Check-in:', 'lodgifywp'); ?></strong> <?php echo esc_html($check_in); ?></p>
        <p><strong><?php _e('Check-out:', 'lodgifywp'); ?></strong> <?php echo esc_html($check_out); ?></p>
        <p><strong><?php _e('Total Price:', 'lodgifywp'); ?></strong> <?php echo esc_html($total_price); ?></p>
    </div>

    <?php
    // Get owner details
    $owner_id = get_field('property_owner', $property_id);
    $owner_name = get_the_title($owner_id);
    $owner_phone = get_field('owner_phone', $owner_id);
    $owner_welcome = get_field('owner_welcome_message', $owner_id);
    $owner_photo = get_field('owner_photo', $owner_id);
    $owner_photo_url = $owner_photo ? $owner_photo['sizes']['thumbnail'] : '';
    ?>

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

    <div class="details-box">
        <h3><?php _e('Property Location', 'lodgifywp'); ?></h3>
        <p><?php echo nl2br(esc_html($property_address)); ?></p>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50;"><?php _e('Check-in Instructions', 'lodgifywp'); ?></h2>
        <?php echo wpautop(get_field('check_in_instructions', $property_id)); ?>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50;"><?php _e('House Rules', 'lodgifywp'); ?></h2>
        <?php
        $house_rules = get_field('house_rules', $property_id);
        if ($house_rules) :
        ?>
            <ul style="list-style-type: none; padding: 0;">
                <?php foreach ($house_rules as $rule) : ?>
                    <li style="margin-bottom: 10px;">• <?php echo esc_html($rule['rule']); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>

    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
        <p style="margin-bottom: 10px;"><?php _e('If you have any questions, please don\'t hesitate to contact us:', 'lodgifywp'); ?></p>
        <p style="margin: 0;">
            <?php echo sprintf(__('Email: %s', 'lodgifywp'), get_option('admin_email')); ?><br>
            <?php echo sprintf(__('Phone: %s', 'lodgifywp'), get_option('lodgifywp_contact_phone')); ?>
        </p>
    </div>
</body>
</html> 