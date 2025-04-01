/**
 * Initialize plugin components
 */
function lodgifywp_init() {
    // Initialize Property post type
    $property = new LodgifyWP_Property();
    $property->init();

    // Initialize Booking system
    $booking = new LodgifyWP_Booking();
    $booking->init();

    // Initialize Payment system
    $payment = new LodgifyWP_Payment();
    $payment->init();

    // Initialize Calendar system
    $calendar = new LodgifyWP_Calendar();
    $calendar->init();

    // Initialize Owner profiles
    $owner = new LodgifyWP_Owner();
    $owner->init();

    // Initialize Reminder system
    $reminder = new LodgifyWP_Reminder();
    $reminder->init();
}
add_action('plugins_loaded', 'lodgifywp_init'); 