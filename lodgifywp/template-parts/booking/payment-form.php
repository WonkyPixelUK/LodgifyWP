<?php
/**
 * Payment form template
 *
 * @package LodgifyWP
 */

$booking_id = get_the_ID();
$total_price = get_field('total_price', $booking_id);
?>

<div class="payment-container">
    <div id="payment-success" style="display: none;" class="alert alert-success">
        <?php _e('Payment successful! Redirecting...', 'lodgifywp'); ?>
    </div>

    <form id="payment-form" data-booking-id="<?php echo esc_attr($booking_id); ?>" data-amount="<?php echo esc_attr($total_price * 100); ?>">
        <div class="form-row">
            <label for="cardholder-name">
                <?php _e('Cardholder Name', 'lodgifywp'); ?>
            </label>
            <input type="text" id="cardholder-name" class="form-control" required>
            <div id="cardholder-errors" class="error-message"></div>
        </div>

        <div class="form-row">
            <label for="card-element">
                <?php _e('Credit or Debit Card', 'lodgifywp'); ?>
            </label>
            <div id="card-element" class="form-control">
                <!-- A Stripe Element will be inserted here. -->
            </div>
            <div id="card-errors" class="error-message"></div>
        </div>

        <div class="form-row payment-summary">
            <h3><?php _e('Payment Summary', 'lodgifywp'); ?></h3>
            <table class="payment-details">
                <tr>
                    <td><?php _e('Total Amount:', 'lodgifywp'); ?></td>
                    <td class="amount"><?php echo sprintf(__('$%s', 'lodgifywp'), number_format($total_price, 2)); ?></td>
                </tr>
            </table>
        </div>

        <div class="form-row submit-row">
            <button type="submit" class="btn btn-primary">
                <?php _e('Pay Now', 'lodgifywp'); ?>
            </button>
        </div>

        <div class="secure-payment-notice">
            <i class="fas fa-lock"></i>
            <span><?php _e('Secure payment processed by Stripe', 'lodgifywp'); ?></span>
        </div>
    </form>
</div>

<style>
    .payment-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
    }

    .form-row {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .form-control {
        width: 100%;
        padding: 12px;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        background-color: white;
    }

    .error-message {
        color: #fa755a;
        margin-top: 8px;
        font-size: 14px;
    }

    .payment-summary {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 4px;
    }

    .payment-details {
        width: 100%;
    }

    .payment-details td {
        padding: 8px 0;
    }

    .payment-details .amount {
        text-align: right;
        font-weight: bold;
    }

    .submit-row {
        text-align: center;
    }

    .btn-primary {
        padding: 12px 24px;
        font-size: 16px;
        min-width: 200px;
    }

    .secure-payment-notice {
        text-align: center;
        color: #6b7c93;
        font-size: 14px;
        margin-top: 20px;
    }

    .secure-payment-notice i {
        margin-right: 8px;
    }

    .alert {
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid transparent;
        border-radius: 4px;
    }

    .alert-success {
        color: #155724;
        background-color: #d4edda;
        border-color: #c3e6cb;
    }
</style> 