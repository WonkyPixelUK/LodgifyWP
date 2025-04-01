# LodgifyWP

A comprehensive WordPress property booking system consisting of a theme and plugin working together to provide a complete solution for property management and bookings.

## Features

### Theme Features
- Modern, responsive design
- Timber/Twig templating
- SCSS styling
- ACF Flexible content blocks
- Automatic plugin dependency management
- Built-in ACF field synchronization

### Plugin Features
- Property management system
- Booking system with calendar
- Payment processing (Stripe)
- Calendar synchronization (iCal, Google Calendar, Microsoft 365)
- Email notifications
- Owner profiles

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- SSL certificate (for Stripe integration)
- Node.js and npm (for development)
- Composer (for development)

## Installation

1. Download the latest release from the [releases page](https://github.com/WonkyPixelUK/LodgifyWP/releases)
2. Install and activate the LodgifyWP Theme
3. The theme will automatically prompt you to install and activate:
   - Advanced Custom Fields PRO
   - Timber
   - LodgifyWP Plugin

## Development Setup

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
   cd ../lodgifywp-plugin
   composer install
   ```

3. Build assets:
   ```bash
   # Theme assets
   cd ../lodgifywp-theme
   npm run build
   
   # Plugin assets
   cd ../lodgifywp-plugin
   npm run build
   ```

## Project Structure

```
LodgifyWP/
├── lodgifywp-theme/           # Theme files
│   ├── acf-json/             # ACF field group JSON files
│   ├── assets/              # Theme assets (SCSS, JS, images)
│   ├── templates/           # Twig templates
│   ├── functions.php        # Theme functions
│   └── style.css           # Theme stylesheet
│
└── lodgifywp-plugin/         # Plugin files
    ├── inc/                # Plugin classes
    ├── assets/            # Plugin assets
    └── lodgifywp.php     # Main plugin file
```

## Version Control

The project uses automatic version control through Git hooks:

- Versions follow semantic versioning (MAJOR.MINOR.PATCH)
- The pre-commit hook automatically increments the PATCH version
- Version numbers are synchronized across all relevant files

### Manual Version Updates

For MAJOR or MINOR version updates:
1. Edit version numbers manually in:
   - `lodgifywp-theme/style.css`
   - `lodgifywp-theme/functions.php`
   - `lodgifywp-plugin/lodgifywp.php`
   - `composer.json`
2. Commit changes

### Automatic Updates (PATCH)

Simply commit your changes:
```bash
git add .
git commit -m "your message"
```
The pre-commit hook will:
1. Increment PATCH version
2. Update all version numbers
3. Stage the changes
4. Include them in your commit

## ACF Field Management

ACF fields are version controlled and automatically synchronized:

1. Fields are stored as JSON in `lodgifywp-theme/acf-json/`
2. Changes made in WordPress admin are automatically saved to JSON
3. JSON files are version controlled
4. Fields are automatically imported when the theme is activated

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on:
- Code of Conduct
- Development process
- Pull Request process
- Coding standards

## License

This project is licensed under the GPL v2 or later - see the [LICENSE](LICENSE) file for details.

## Support

- [Documentation](https://github.com/WonkyPixelUK/LodgifyWP/wiki)
- [Issue Tracker](https://github.com/WonkyPixelUK/LodgifyWP/issues)
- [Changelog](CHANGELOG.md) 