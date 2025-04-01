# LodgifyWP Developer Guide

## Development Environment Setup

### Prerequisites
- Node.js 16.x or higher
- Composer
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Git

### Local Development Setup
1. Clone the repository:
   ```bash
   git clone https://github.com/WonkyPixelUK/LodgifyWP.git
   cd LodgifyWP
   ```

2. Install dependencies:
   ```bash
   # Theme dependencies
   cd lodgifywp-theme
   npm install
   
   # Plugin dependencies
   cd ../lodgifywp
   npm install
   composer install
   ```

3. Start development servers:
   ```bash
   # Theme development
   cd lodgifywp-theme
   npm run watch
   
   # Plugin development
   cd ../lodgifywp
   npm run watch
   ```

## Theme Development

### Directory Structure
```
lodgifywp-theme/
├── acf-json/           # ACF field configurations
├── assets/
│   ├── css/           # Compiled CSS files
│   ├── js/            # JavaScript files
│   ├── images/        # Theme images
│   └── scss/          # SCSS source files
├── inc/               # Theme PHP includes
├── template-parts/     # Reusable template parts
├── templates/         # Page templates
├── views/             # Twig template files
├── functions.php      # Theme functions
└── style.css         # Theme metadata
```

### Template Hierarchy
1. Custom Templates
2. Singular Templates
3. Archive Templates
4. Taxonomy Templates
5. Fallback Templates

### Customization

#### Adding Custom Post Types
```php
// In functions.php or separate file
function register_property_post_type() {
    $args = [
        'public' => true,
        'label'  => 'Properties',
        // ... other arguments
    ];
    register_post_type('property', $args);
}
add_action('init', 'register_property_post_type');
```

#### Adding Custom Fields
1. Use ACF interface
2. Export to JSON
3. Commit to version control

#### Adding Custom Templates
1. Create template file
2. Register in theme
3. Add to template hierarchy

## Plugin Development

### Directory Structure
```
lodgifywp/
├── assets/
│   ├── css/          # Compiled CSS files
│   ├── js/           # JavaScript files
│   └── scss/         # SCSS source files
├── inc/              # Plugin classes
├── templates/        # Template files
├── views/            # Twig template files
└── lodgifywp.php    # Plugin main file
```

### Hooks and Filters

#### Available Actions
```php
// Before booking creation
do_action('lodgifywp_before_booking_create', $booking_data);

// After booking creation
do_action('lodgifywp_after_booking_create', $booking_id, $booking_data);

// Before payment processing
do_action('lodgifywp_before_payment_process', $payment_data);

// After payment processing
do_action('lodgifywp_after_payment_process', $payment_id, $payment_data);

// Calendar sync
do_action('lodgifywp_before_calendar_sync', $calendar_id);
do_action('lodgifywp_after_calendar_sync', $calendar_id, $events);
```

#### Available Filters
```php
// Modify booking data
$booking_data = apply_filters('lodgifywp_booking_data', $booking_data);

// Modify payment amount
$amount = apply_filters('lodgifywp_payment_amount', $amount, $booking_id);

// Modify email content
$content = apply_filters('lodgifywp_email_content', $content, $type);

// Modify calendar sync interval
$interval = apply_filters('lodgifywp_sync_interval', $interval);
```

### API Integration

#### REST API Endpoints
```php
// Register custom endpoints
add_action('rest_api_init', function () {
    register_rest_route('lodgifywp/v1', '/bookings', [
        'methods' => 'GET',
        'callback' => 'get_bookings',
        'permission_callback' => 'check_permissions'
    ]);
});
```

#### External APIs
1. Calendar Integration
2. Payment Processing
3. Email Services

### Database Schema

#### Tables
1. Bookings
2. Payments
3. Calendar Events
4. Settings

#### Example Query
```php
global $wpdb;
$table_name = $wpdb->prefix . 'lodgifywp_bookings';
$results = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT * FROM $table_name WHERE property_id = %d",
        $property_id
    )
);
```

## Testing

### Unit Tests
```bash
# Run PHP unit tests
composer test

# Run JavaScript tests
npm test
```

### Integration Tests
1. Set up test environment
2. Run integration tests
3. Check code coverage

### End-to-End Tests
1. Configure test suite
2. Write test scenarios
3. Run tests

## Build Process

### Theme Build
```bash
cd lodgifywp-theme
npm run build
```

### Plugin Build
```bash
cd lodgifywp
npm run build
composer install --no-dev --optimize-autoloader
```

### Release Process
1. Update version numbers
2. Run builds
3. Create changelog
4. Tag release
5. Deploy

## Best Practices

### Coding Standards
- Follow WordPress Coding Standards
- Use ESLint for JavaScript
- Use Stylelint for SCSS
- Follow PHP PSR-12

### Security
1. Validate inputs
2. Sanitize outputs
3. Use nonces
4. Check permissions
5. Follow WordPress security best practices

### Performance
1. Optimize database queries
2. Cache expensive operations
3. Minimize asset sizes
4. Use transients
5. Follow WordPress performance best practices

## Deployment

### Production Build
```bash
# Build theme
cd lodgifywp-theme
npm run build:production

# Build plugin
cd ../lodgifywp
npm run build:production
```

### Version Control
1. Use semantic versioning
2. Tag releases
3. Maintain changelog
4. Follow Git flow

### Distribution
1. Package theme/plugin
2. Update version numbers
3. Generate documentation
4. Create release notes
5. Deploy to WordPress.org 