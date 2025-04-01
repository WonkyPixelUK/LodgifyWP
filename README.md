# LodgifyWP

A comprehensive WordPress plugin for managing property bookings, with integrated calendar synchronization, Stripe payments, and owner profiles.

## Features

- **Property Management**: Create and manage property listings with detailed information
- **Booking System**: Handle reservations with automated availability checking
- **Payment Processing**: Secure payments via Stripe integration
- **Calendar Integration**: Sync with iCal, Google Calendar, and Microsoft 365
- **Email Notifications**: Automated emails for bookings and confirmations
- **Owner Profiles**: Manage property owner information
- **Flexible Content**: Built with ACF flexible content for maximum customization

## Requirements

- WordPress 5.8 or higher
- PHP 7.4 or higher
- ACF Pro plugin
- Timber plugin
- SSL certificate (for Stripe integration)

## Installation

1. Download the latest release from the [releases page](https://github.com/WonkyPixelUK/LodgifyWP/releases)
2. Upload the plugin ZIP file through the WordPress admin panel or extract it to your `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress
4. Install and activate required plugins (ACF Pro and Timber)
5. Configure the plugin settings

## Configuration

### Stripe Integration

1. Create a [Stripe account](https://stripe.com) if you haven't already
2. Get your API keys from the Stripe Dashboard
3. Go to LodgifyWP Settings > Payments
4. Enter your Stripe API keys (test and live modes available)

### Calendar Integration

1. Navigate to LodgifyWP Settings > Calendar
2. Configure your preferred calendar services:
   - For Google Calendar: Enter your Client ID and Secret
   - For Microsoft 365: Enter your Application ID and Secret
   - For iCal: Add feed URLs directly

### Email Settings

1. Go to LodgifyWP Settings > Emails
2. Customize email templates for:
   - Booking confirmations
   - Payment receipts
   - Admin notifications
3. Test email functionality using the test button

## Usage

### Adding a New Property

1. Go to Properties > Add New
2. Fill in the property details:
   - Basic information (title, description)
   - Pricing and availability
   - Amenities and features
   - Location details
   - House rules
3. Add property images and gallery
4. Set availability calendar
5. Publish the property

### Managing Bookings

1. Access the Bookings menu
2. View all bookings with filters for:
   - Status (pending, confirmed, completed)
   - Date range
   - Property
3. Process payments and update booking status
4. Send manual notifications if needed

### Calendar Management

1. Navigate to the Calendar view
2. Sync with external calendars
3. View availability across all properties
4. Manage booking statuses with color coding
5. Set automated status updates

## Development

### File Structure

```
lodgifywp/
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── inc/
│   ├── class-lodgifywp-property.php
│   ├── class-lodgifywp-booking.php
│   ├── class-lodgifywp-payment.php
│   ├── class-lodgifywp-calendar.php
│   └── class-lodgifywp-owner.php
├── template-parts/
│   ├── property/
│   ├── booking/
│   └── emails/
├── vendor/
├── composer.json
└── lodgifywp.php
```

### Customization

The plugin uses WordPress standards and follows these principles:
- SCSS for styling (no direct CSS)
- Twig templates via Timber
- ACF Flexible Content for layouts
- Action and filter hooks for extensibility

### Adding Custom Features

1. Use WordPress action and filter hooks
2. Extend existing classes as needed
3. Follow the established naming conventions
4. Add new templates to the appropriate directory

## Updates

The plugin includes automatic updates via GitHub:
1. Updates are checked automatically in WordPress
2. New versions can be installed with one click
3. Release notes are available in the WordPress admin

## Support

- [Documentation](https://github.com/WonkyPixelUK/LodgifyWP/wiki)
- [Issue Tracker](https://github.com/WonkyPixelUK/LodgifyWP/issues)
- [Changelog](CHANGELOG.md)

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details. 