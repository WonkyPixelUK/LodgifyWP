// Gallery Thumbnail Click Handler
document.addEventListener('DOMContentLoaded', function() {
    const galleryThumbs = document.querySelectorAll('.gallery-thumb');
    const galleryMain = document.querySelector('.gallery-main');

    if (galleryThumbs && galleryMain) {
        galleryThumbs.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const newImage = this.style.backgroundImage;
                galleryMain.style.backgroundImage = newImage;
            });
        });
    }

    // Date Range Picker
    const checkInInput = document.getElementById('check-in');
    const checkOutInput = document.getElementById('check-out');

    if (checkInInput && checkOutInput) {
        const today = new Date().toISOString().split('T')[0];
        checkInInput.min = today;

        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const minCheckOut = new Date(checkInDate);
            minCheckOut.setDate(checkInDate.getDate() + 1);
            checkOutInput.min = minCheckOut.toISOString().split('T')[0];
            
            if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = '';
            }
        });
    }

    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const mainNav = document.querySelector('.main-navigation');

    if (menuToggle && mainNav) {
        menuToggle.addEventListener('click', function() {
            mainNav.classList.toggle('active');
            this.setAttribute('aria-expanded', 
                this.getAttribute('aria-expanded') === 'true' ? 'false' : 'true'
            );
        });
    }

    // Smooth Scroll for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Initialize Google Maps
    if (typeof google !== 'undefined' && document.getElementById('property-map')) {
        initMap();
    }
});

// Form Validation
function validateBookingForm(form) {
    const checkIn = new Date(form.check_in.value);
    const checkOut = new Date(form.check_out.value);
    const guests = parseInt(form.guests.value);
    const name = form.name.value.trim();
    const email = form.email.value.trim();
    const phone = form.phone.value.trim();

    let errors = [];

    if (!checkIn || !checkOut) {
        errors.push('Please select both check-in and check-out dates');
    } else if (checkOut <= checkIn) {
        errors.push('Check-out date must be after check-in date');
    }

    if (guests < 1 || guests > 10) {
        errors.push('Please select a valid number of guests');
    }

    if (!name) {
        errors.push('Please enter your name');
    }

    if (!email || !isValidEmail(email)) {
        errors.push('Please enter a valid email address');
    }

    if (!phone || !isValidPhone(phone)) {
        errors.push('Please enter a valid phone number');
    }

    return errors;
}

// Helper Functions
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

function isValidPhone(phone) {
    const re = /^\+?[\d\s-]{10,}$/;
    return re.test(phone);
}

// Stripe Payment Integration
async function handlePayment(bookingData) {
    try {
        const response = await fetch(houseBookingAjax.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'create_payment_intent',
                nonce: houseBookingAjax.nonce,
                ...bookingData
            })
        });

        const data = await response.json();
        
        if (data.success) {
            // Initialize Stripe
            const stripe = Stripe(data.publishableKey);
            
            // Confirm the payment
            const result = await stripe.confirmCardPayment(data.clientSecret, {
                payment_method: {
                    card: elements.getElement('card'),
                }
            });

            if (result.error) {
                throw new Error(result.error.message);
            }

            return result.paymentIntent;
        } else {
            throw new Error(data.message);
        }
    } catch (error) {
        console.error('Payment error:', error);
        throw error;
    }
}

// iCal Integration
async function syncCalendar() {
    try {
        const response = await fetch(houseBookingAjax.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'sync_calendar',
                nonce: houseBookingAjax.nonce
            })
        });

        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message);
        }

        return data;
    } catch (error) {
        console.error('Calendar sync error:', error);
        throw error;
    }
} 