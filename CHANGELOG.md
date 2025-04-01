# Changelog

All notable changes to the LodgifyWP project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.1.0] - 2024-03-21

### Changed
- Restructured project to separate theme and plugin components
- Implemented automatic plugin installation through theme
- Added automatic version control through Git hooks
- Improved documentation and project organization

### Added
- Theme Features:
  - ACF field synchronization
  - Automatic plugin dependency management
  - Timber/Twig templating integration
  - SCSS styling structure
  - Built-in plugin installer
- Development Tools:
  - Git pre-commit hook for version management
  - Automated version synchronization
  - Build process for theme and plugin
- Documentation:
  - Comprehensive README
  - Contributing guidelines
  - Code of conduct
  - Issue and PR templates

## [1.0.0] - 2024-03-21

### Added
- Initial release of LodgifyWP plugin
- Property management system
  - Custom post type for properties
  - Property type, amenity, and location taxonomies
  - ACF fields for property details (price, guests, bedrooms, etc.)
  - Property gallery support
  - Check-in/check-out time settings
  - House rules management

- Booking system
  - Custom post type for bookings
  - Availability checking
  - Guest information collection
  - Booking status management
  - REST API endpoints for availability and booking creation

- Payment integration (Stripe)
  - Secure payment processing
  - Payment intent creation
  - Client-side card element
  - Payment status tracking
  - Confirmation handling

- Calendar integration
  - Support for iCal feeds
  - Google Calendar integration
  - Microsoft 365 Calendar integration
  - Two-way sync capabilities
  - Automated booking status labels
  - Calendar settings management

- Email notifications
  - Guest booking confirmation emails
  - Admin notification emails
  - Customizable email templates
  - Booking details inclusion
  - Payment status updates

- Frontend features
  - Availability calendar display
  - Payment form with Stripe integration
  - Property details display
  - Booking form integration

- Admin features
  - Calendar settings page
  - Booking status management
  - Integration configuration
  - Property management interface

### Technical Features
- WordPress standards compliant
- ACF Flexible Content integration
- SCSS styling structure
- Twig/Timber templating
- Automated updates via GitHub
- Composer dependency management 