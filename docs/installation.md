# LodgifyWP Setup Guide

## Prerequisites
- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Advanced Custom Fields PRO plugin
- Stripe account (for payments)

## Installation

### Theme Installation
1. Download the latest version of the LodgifyWP theme
2. Go to WordPress Admin > Appearance > Themes > Add New > Upload Theme
3. Choose the downloaded zip file and click "Install Now"
4. After installation, click "Activate"

### Plugin Installation
1. Download the latest version of the LodgifyWP plugin
2. Go to WordPress Admin > Plugins > Add New > Upload Plugin
3. Choose the downloaded zip file and click "Install Now"
4. After installation, click "Activate"

## Initial Configuration

### 1. Required Plugins
After activation, you'll be prompted to install and activate required plugins:
- Advanced Custom Fields PRO
- Timber (for Twig templating)

### 2. Theme Settings
1. Go to Appearance > Customize
2. Configure the following sections:
   - Site Identity (logo, site title, favicon)
   - Colors and Typography
   - Header and Footer options
   - Social Media links

### 3. Plugin Settings
1. Navigate to LodgifyWP > Settings in the WordPress admin
2. Configure the following:
   - General Settings
   - Payment Settings (Stripe keys)
   - Email Templates
   - Booking Rules

## Setting Up Properties

### Creating a New Property
1. Go to Properties > Add New
2. Fill in the basic information:
   - Property Title
   - Description
   - Featured Image
3. Add property details using the custom fields:
   - Location
   - Pricing
   - Amenities
   - House Rules
   - Check-in/out times
4. Set availability calendar
5. Publish the property

### Calendar Integration

#### Google Calendar
1. Go to LodgifyWP > Calendar Settings
2. Click "Connect with Google Calendar"
3. Follow the OAuth authentication process
4. Select the calendar to sync with
5. Choose sync direction (import/export/both)

#### iCal Integration
1. Go to Properties > Edit Property
2. Scroll to "Calendar Integration" section
3. Add external calendar URLs (Airbnb, Booking.com, etc.)
4. Set sync frequency
5. Save changes

#### Microsoft 365 Calendar
1. Go to LodgifyWP > Calendar Settings
2. Click "Connect with Microsoft 365"
3. Follow the OAuth authentication process
4. Select the calendar to sync with
5. Choose sync direction (import/export/both)

## Payment Setup

### Stripe Configuration
1. Create a Stripe account if you don't have one
2. Go to LodgifyWP > Settings > Payments
3. Enter your Stripe API keys:
   - Publishable Key
   - Secret Key
4. Configure payment settings:
   - Currency
   - Deposit percentage
   - Cancellation policy
   - Refund rules

## Email Configuration
1. Go to LodgifyWP > Settings > Emails
2. Configure email templates:
   - Booking confirmation
   - Payment receipt
   - Reminder emails
   - Admin notifications
3. Customize email sender details
4. Test email delivery

## Customization

### Theme Customization
1. Use WordPress Customizer for basic styling
2. Advanced customization through SCSS files:
   ```bash
   cd wp-content/themes/lodgifywp-theme
   npm install
   npm run watch
   ```

### Plugin Customization
1. Override templates in your theme:
   - Copy templates from `wp-content/plugins/lodgifywp/templates/`
   - Paste to `wp-content/themes/your-theme/lodgifywp/`
2. Use provided hooks and filters (see Developer Guide)

## Troubleshooting

### Common Issues
1. Calendar sync not working:
   - Check API credentials
   - Verify calendar permissions
   - Check server cron jobs

2. Payment issues:
   - Verify Stripe API keys
   - Check SSL certificate
   - Review error logs

3. Email delivery problems:
   - Configure SMTP settings
   - Check spam settings
   - Verify email templates

### Getting Support
- Visit our [Support Forum](https://github.com/WonkyPixelUK/LodgifyWP/discussions)
- Submit issues on [GitHub](https://github.com/WonkyPixelUK/LodgifyWP/issues)
- Contact support at support@wonkypixel.co.uk 