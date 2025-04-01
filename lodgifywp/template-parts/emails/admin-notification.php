<?php
/**
 * Admin notification email template
 *
 * @package LodgifyWP
 */

$booking = get_post($booking_id);
$property_id = get_field('property', $booking_id);
$guest_details = get_field('guest_details', $booking_id);
$check_in = get_field('check_in_date', $booking_id);
$check_out = get_field('check_out_date', $booking_id);
$total_price = get_field('total_price', $booking_id);
$payment_status = get_field('payment_status', $booking_id);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo sprintf(__('New Booking - %s', 'lodgifywp'), get_the_title($property_id)); ?></title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="text-align: center; margin-bottom: 30px;">
        <h1 style="color: #2c3e50;"><?php _e('New Booking Received', 'lodgifywp'); ?></h1>
    </div>

    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <p><?php _e('A new booking has been received with the following details:', 'lodgifywp'); ?></p>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50;"><?php _e('Booking Details', 'lodgifywp'); ?></h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Booking ID:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;">#<?php echo $booking_id; ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Property:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo get_the_title($property_id); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Check-in:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo date_i18n(get_option('date_format'), strtotime($check_in)); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Check-out:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo date_i18n(get_option('date_format'), strtotime($check_out)); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Total Price:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo sprintf(__('$%s', 'lodgifywp'), number_format($total_price, 2)); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Payment Status:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($payment_status); ?></td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="color: #2c3e50;"><?php _e('Guest Information', 'lodgifywp'); ?></h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Name:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($guest_details['name']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Email:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($guest_details['email']); ?></td>
            </tr>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Phone:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($guest_details['phone']); ?></td>
            </tr>
            <?php if (!empty($guest_details['special_requests'])) : ?>
            <tr>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><strong><?php _e('Special Requests:', 'lodgifywp'); ?></strong></td>
                <td style="padding: 10px; border-bottom: 1px solid #eee;"><?php echo esc_html($guest_details['special_requests']); ?></td>
            </tr>
            <?php endif; ?>
        </table>
    </div>

    <div style="background-color: #f5f5f5; padding: 20px; border-radius: 8px;">
        <p style="margin-bottom: 10px;"><?php _e('You can manage this booking in the WordPress admin panel:', 'lodgifywp'); ?></p>
        <p style="margin: 0;">
            <a href="<?php echo esc_url(admin_url('post.php?post=' . $booking_id . '&action=edit')); ?>" style="color: #2c3e50; text-decoration: underline;">
                <?php _e('View Booking Details', 'lodgifywp'); ?>
            </a>
        </p>
    </div>
</body>
</html> 