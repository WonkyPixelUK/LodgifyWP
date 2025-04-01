/**
 * Stripe payment integration
 *
 * @package LodgifyWP
 */

(function($) {
    'use strict';

    // Initialize Stripe
    const stripe = Stripe(lodgifyWPStripe.publishableKey);
    const elements = stripe.elements();

    // Create card element
    const card = elements.create('card', {
        style: {
            base: {
                color: '#32325d',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        }
    });

    // Mount card element
    card.mount('#card-element');

    // Handle real-time validation errors
    card.addEventListener('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    // Handle form submission
    const form = document.getElementById('payment-form');
    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        // Disable the submit button to prevent multiple submissions
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = lodgifyWPStripe.processingText;

        try {
            // Create payment intent
            const response = await fetch(lodgifyWPStripe.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'create_payment_intent',
                    nonce: lodgifyWPStripe.nonce,
                    booking_id: form.dataset.bookingId,
                    amount: form.dataset.amount
                })
            });

            const data = await response.json();

            if (!data.success) {
                throw new Error(data.data.message || 'Payment intent creation failed');
            }

            // Confirm card payment
            const { paymentIntent, error: confirmError } = await stripe.confirmCardPayment(
                data.data.client_secret,
                {
                    payment_method: {
                        card: card,
                        billing_details: {
                            name: document.getElementById('cardholder-name').value
                        }
                    }
                }
            );

            if (confirmError) {
                throw new Error(confirmError.message);
            }

            // Payment successful
            if (paymentIntent.status === 'succeeded') {
                // Process the successful payment on the server
                const processResponse = await fetch(lodgifyWPStripe.ajaxurl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        action: 'process_payment',
                        nonce: lodgifyWPStripe.nonce,
                        booking_id: form.dataset.bookingId,
                        payment_intent_id: paymentIntent.id
                    })
                });

                const processData = await processResponse.json();

                if (!processData.success) {
                    throw new Error(processData.data.message || 'Payment processing failed');
                }

                // Show success message and redirect
                document.getElementById('payment-success').style.display = 'block';
                document.getElementById('payment-form').style.display = 'none';

                // Redirect after a short delay
                setTimeout(() => {
                    window.location.href = processData.data.redirect_url;
                }, 2000);
            }
        } catch (error) {
            // Display error message
            const errorElement = document.getElementById('card-errors');
            errorElement.textContent = error.message;

            // Re-enable the submit button
            submitButton.disabled = false;
            submitButton.innerHTML = lodgifyWPStripe.submitText;
        }
    });

    // Handle cardholder name input
    const cardholderName = document.getElementById('cardholder-name');
    cardholderName.addEventListener('change', function(event) {
        const error = document.getElementById('cardholder-errors');
        if (!event.target.value.trim()) {
            error.textContent = lodgifyWPStripe.nameRequired;
        } else {
            error.textContent = '';
        }
    });

})(jQuery); 