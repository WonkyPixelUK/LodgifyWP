# LodgifyWP - WordPress Property Booking System

A comprehensive WordPress plugin for managing property bookings, with integrated calendar synchronization, Stripe payments, and owner profiles.

## Features

### Property Management
- Custom post type for properties
- Property categorization (type, amenities, location)
- Advanced Custom Fields integration for property details
- Price per night, maximum guests, bedrooms, bathrooms
- Property gallery and location management
- Check-in/check-out times and minimum stay requirements

### Booking System
- Secure booking management
- Guest information collection
- Automated status updates
- Email notifications
- Payment processing via Stripe
- Booking calendar with availability display

### Owner Profiles
- Custom post type for property owners
- Detailed owner profiles with:
  - Contact information
  - Profile photo
  - Biography
  - Welcome message for guests
  - Customizable reminder settings

### Calendar Integration
- Synchronization with:
  - Google Calendar
  - Microsoft 365
  - iCal
- Two-way sync capabilities
- Real-time availability updates
- Conflict prevention

### Automated Reminders
- Configurable reminder schedules
- Separate notifications for:
  - Guests (upcoming stay details)
  - Owners (upcoming guest arrival)
- Customizable email templates
- Pre-arrival checklists for owners

### Payment Processing
- Stripe integration
- Secure payment handling
- Automated booking status updates
- Payment confirmation emails

## Technical Details

### Built With
- WordPress
- Advanced Custom Fields (ACF)
- Timber/Twig for templating
- SCSS for styling
- Stripe API for payments
- Google Calendar API
- Microsoft Graph API

### File Structure
```
lodgifywp/
├── assets/
│   ├── scss/
│   │   ├── abstracts/
│   │   ├── base/
│   │   ├── blocks/
│   │   └── main.scss
│   ├── js/
│   └── css/
├── inc/
│   ├── class-lodgifywp-property.php
│   ├── class-lodgifywp-booking.php
│   ├── class-lodgifywp-payment.php
│   ├── class-lodgifywp-calendar.php
│   ├── class-lodgifywp-owner.php
│   └── class-lodgifywp-reminder.php
├── views/
│   └── emails/
├── template-parts/
└── lodgifywp.php
```

## Installation

1. Clone the repository:
```bash
git clone https://github.com/WonkyPixelUK/LodgifyWP.git
```

2. Install dependencies:
```bash
composer install
npm install
```

3. Configure API Keys:
- Set up Stripe API keys
- Configure Google Calendar API credentials
- Set up Microsoft 365 API credentials

4. Activate the plugin in WordPress admin panel

## Configuration

### Required Plugins
- Advanced Custom Fields PRO
- Timber

### API Setup

#### Stripe
1. Create a Stripe account
2. Obtain API keys from Stripe Dashboard
3. Add keys to WordPress settings

#### Google Calendar
1. Create a project in Google Cloud Console
2. Enable Google Calendar API
3. Create OAuth 2.0 credentials
4. Configure redirect URI
5. Add credentials to plugin settings

#### Microsoft 365
1. Register application in Azure Portal
2. Configure OAuth 2.0 settings
3. Add required permissions
4. Configure redirect URI
5. Add credentials to plugin settings

## Usage

### Adding Properties
1. Navigate to Properties > Add New
2. Fill in property details
3. Set pricing and availability
4. Assign property owner
5. Configure booking settings

### Managing Owners
1. Go to Properties > Property Owners
2. Create owner profiles
3. Configure reminder preferences
4. Add welcome messages and photos

### Booking Management
1. View all bookings under Bookings menu
2. Process payments
3. Manage calendar availability
4. Send notifications

### Calendar Sync
1. Configure calendar integration in settings
2. Connect desired calendar services
3. Set sync frequency
4. Monitor sync status

## Development

### SCSS Compilation
```bash
npm run watch
```

### Building for Production
```bash
npm run build
```

## Support

For support, please create an issue in the GitHub repository or contact the development team.

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the GPL v2 or later - see the LICENSE file for details.

## Acknowledgments

- WordPress
- Advanced Custom Fields
- Timber
- Stripe
- Google Calendar API
- Microsoft Graph API 