# LodgifyWP

A comprehensive WordPress theme and plugin combination for property booking websites.

[![License: GPL v2](https://img.shields.io/badge/License-GPL%20v2-blue.svg)](https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html)

## Documentation

- [Installation Guide](docs/installation.md)
- [Development Guide](docs/development.md)
- [Support Guide](docs/support.md)

## Quick Links

- [Report a Bug](https://github.com/WonkyPixelUK/LodgifyWP/issues)
- [Request a Feature](https://github.com/WonkyPixelUK/LodgifyWP/issues)
- [Contributing Guidelines](CONTRIBUTING.md)
- [Code of Conduct](CODE_OF_CONDUCT.md)

## Features

- Complete booking management system
- Calendar integration (Google Calendar, iCal, Microsoft 365)
- Stripe payment processing
- Automated email notifications
- Property management features
- Customizable templates

## Requirements

- WordPress 6.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Advanced Custom Fields PRO plugin
- Stripe account (for payments)

## Installation

See our detailed [Installation Guide](docs/installation.md) for setup instructions.

## Contributing

We welcome contributions! Please read our [Contributing Guidelines](CONTRIBUTING.md) before submitting a Pull Request.

## Support

Need help? Check our [Support Guide](docs/support.md) or contact us at support@wonkypixel.co.uk.

## License

This project is licensed under the GPL v2 License - see the [LICENSE](LICENSE) file for details.

## Credits

Created and maintained by [WonkyPixel](https://github.com/WonkyPixelUK).

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