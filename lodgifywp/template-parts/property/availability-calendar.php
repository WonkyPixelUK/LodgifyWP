<?php
/**
 * Property availability calendar template
 *
 * @package LodgifyWP
 */

$property_id = get_the_ID();
$bookings = get_posts(array(
    'post_type' => 'booking',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'property',
            'value' => $property_id,
        ),
        array(
            'key' => 'check_out_date',
            'value' => date('Y-m-d'),
            'compare' => '>=',
            'type' => 'DATE',
        ),
    ),
));

$booking_dates = array();
foreach ($bookings as $booking) {
    $check_in = get_field('check_in_date', $booking->ID);
    $check_out = get_field('check_out_date', $booking->ID);
    $status = get_field('booking_status', $booking->ID);
    
    $current_date = new DateTime($check_in);
    $end_date = new DateTime($check_out);
    
    while ($current_date < $end_date) {
        $date_string = $current_date->format('Y-m-d');
        $booking_dates[$date_string] = array(
            'booking_id' => $booking->ID,
            'status' => $status,
        );
        $current_date->modify('+1 day');
    }
}

// Get booking statuses and their colors
$booking_statuses = get_option('lodgifywp_booking_statuses', array(
    'pending' => array(
        'label' => 'Pending',
        'color' => '#ffc107',
    ),
    'approved' => array(
        'label' => 'Approved',
        'color' => '#28a745',
    ),
    'rejected' => array(
        'label' => 'Rejected',
        'color' => '#dc3545',
    ),
    'cancelled' => array(
        'label' => 'Cancelled',
        'color' => '#6c757d',
    ),
));
?>

<div class="availability-calendar">
    <h3><?php _e('Availability Calendar', 'lodgifywp'); ?></h3>
    
    <div class="calendar-container">
        <div class="calendar-navigation">
            <button class="prev-month" type="button">
                <i class="fas fa-chevron-left"></i>
            </button>
            <span class="current-month"></span>
            <button class="next-month" type="button">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <div class="calendar-grid">
            <div class="calendar-header">
                <div><?php _e('Sun', 'lodgifywp'); ?></div>
                <div><?php _e('Mon', 'lodgifywp'); ?></div>
                <div><?php _e('Tue', 'lodgifywp'); ?></div>
                <div><?php _e('Wed', 'lodgifywp'); ?></div>
                <div><?php _e('Thu', 'lodgifywp'); ?></div>
                <div><?php _e('Fri', 'lodgifywp'); ?></div>
                <div><?php _e('Sat', 'lodgifywp'); ?></div>
            </div>
            <div class="calendar-days"></div>
        </div>
        
        <div class="calendar-legend">
            <div class="legend-item">
                <span class="legend-color available"></span>
                <span class="legend-text"><?php _e('Available', 'lodgifywp'); ?></span>
            </div>
            <?php foreach ($booking_statuses as $key => $status) : ?>
                <div class="legend-item">
                    <span class="legend-color" style="background-color: <?php echo esc_attr($status['color']); ?>"></span>
                    <span class="legend-text"><?php echo esc_html($status['label']); ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bookingDates = <?php echo json_encode($booking_dates); ?>;
    const bookingStatuses = <?php echo json_encode($booking_statuses); ?>;
    const calendarContainer = document.querySelector('.calendar-container');
    const currentMonthElement = calendarContainer.querySelector('.current-month');
    const calendarDays = calendarContainer.querySelector('.calendar-days');
    const prevMonthButton = calendarContainer.querySelector('.prev-month');
    const nextMonthButton = calendarContainer.querySelector('.next-month');
    
    let currentDate = new Date();
    
    function renderCalendar(date) {
        const firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
        const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
        
        currentMonthElement.textContent = date.toLocaleString('default', { 
            month: 'long', 
            year: 'numeric' 
        });
        
        let calendarHTML = '';
        
        // Add empty cells for days before the first day of the month
        for (let i = 0; i < firstDay.getDay(); i++) {
            calendarHTML += '<div class="calendar-day empty"></div>';
        }
        
        // Add days of the month
        for (let day = 1; day <= lastDay.getDate(); day++) {
            const currentDateString = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const bookingInfo = bookingDates[currentDateString];
            const isPast = new Date(currentDateString) < new Date(new Date().toDateString());
            
            let dayClasses = ['calendar-day'];
            let dayStyles = '';
            let statusLabel = '';
            
            if (isPast) {
                dayClasses.push('past');
            } else if (bookingInfo) {
                dayClasses.push('booked');
                const status = bookingStatuses[bookingInfo.status];
                if (status) {
                    dayStyles = `background-color: ${status.color};`;
                    statusLabel = status.label;
                }
            }
            
            calendarHTML += `
                <div class="${dayClasses.join(' ')}" style="${dayStyles}" data-booking-id="${bookingInfo ? bookingInfo.booking_id : ''}" title="${statusLabel}">
                    <span class="day-number">${day}</span>
                </div>
            `;
        }
        
        calendarDays.innerHTML = calendarHTML;
    }
    
    // Event listeners for navigation
    prevMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });
    
    nextMonthButton.addEventListener('click', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });
    
    // Event listener for clicking on a booked day
    calendarDays.addEventListener('click', (event) => {
        const dayElement = event.target.closest('.calendar-day');
        if (dayElement && dayElement.dataset.bookingId) {
            window.location.href = `<?php echo admin_url('post.php?action=edit&post='); ?>${dayElement.dataset.bookingId}`;
        }
    });
    
    // Initial render
    renderCalendar(currentDate);
});
</script>

<style>
.availability-calendar {
    margin: 30px 0;
}

.calendar-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 20px;
}

.calendar-navigation {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
}

.calendar-navigation button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    color: #2c3e50;
}

.calendar-navigation button:hover {
    color: #3498db;
}

.current-month {
    font-size: 18px;
    font-weight: 500;
}

.calendar-grid {
    margin-bottom: 20px;
}

.calendar-header {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    text-align: center;
    font-weight: 500;
    margin-bottom: 10px;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 4px;
    cursor: pointer;
    position: relative;
    transition: transform 0.2s;
}

.calendar-day:not(.empty):hover {
    transform: scale(1.1);
    z-index: 1;
}

.calendar-day:not(.empty) {
    background-color: #f8f9fa;
}

.calendar-day.booked {
    color: white;
}

.calendar-day.past {
    background-color: #eee;
    color: #999;
    cursor: not-allowed;
}

.day-number {
    font-size: 14px;
}

.calendar-legend {
    display: flex;
    gap: 20px;
    justify-content: center;
    flex-wrap: wrap;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 4px;
}

.legend-color.available {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
}

.legend-text {
    font-size: 14px;
    color: #6c757d;
}
</style> 