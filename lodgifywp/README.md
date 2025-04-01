# LodgifyWP Theme

A modern WordPress theme for managing holiday property bookings, featuring Stripe integration, iCal synchronization, and a beautiful responsive design.

## Theme Structure

```
.
├── assets/                 # Theme assets
│   ├── css/               # Compiled CSS files
│   │   └── style.css      # Main compiled stylesheet
│   ├── js/                # JavaScript files
│   │   └── main.js        # Main JavaScript file
│   ├── scss/              # SCSS source files
│   │   ├── abstracts/     # Variables, mixins, functions
│   │   ├── base/          # Reset, typography, base styles
│   │   ├── components/    # UI components
│   │   ├── layout/        # Layout styles
│   │   ├── pages/         # Page-specific styles
│   │   └── main.scss      # Main SCSS file
│   └── images/            # Theme images
│
├── inc/                   # Core functionality
│   ├── class-lodgifywp-setup.php
│   ├── class-lodgifywp-scripts.php
│   ├── class-lodgifywp-widgets.php
│   ├── class-lodgifywp-customizer.php
│   └── class-lodgifywp-acf.php
│
├── template-parts/        # Reusable template parts
│   ├── content/           # Content templates
│   ├── components/        # UI components
│   │   ├── header/       # Header components
│   │   └── footer/       # Footer components
│   ├── properties/       # Property components
│   └── search/          # Search components
│
├── templates/            # Page templates
│   └── page-templates/   # Custom page templates
│
├── 404.php              # 404 error template
├── archive.php          # Archive template
├── composer.json        # Composer dependencies
├── footer.php           # Footer template
├── functions.php        # Theme functions
├── header.php           # Header template
├── index.php            # Main template file
├── package.json         # NPM dependencies
├── page.php            # Page template
├── README.md           # Theme documentation
├── search.php          # Search template
├── single.php          # Single post template
└── style.css           # Theme information
```

## Core Files

- `style.css`: Theme information and identification
- `functions.php`: Theme setup, scripts, and functionality
- `index.php`: Main template file
- `header.php`: Header template
- `footer.php`: Footer template

## Template Files

- `single.php`: Single post template
- `page.php`: Page template
- `archive.php`: Archive template
- `search.php`: Search template
- `404.php`: 404 error template

## Development

### Prerequisites

- Node.js and NPM
- Composer
- WordPress 5.8+
- PHP 7.4+
- Advanced Custom Fields PRO
- Timber/Twig

### Getting Started

1. Clone the repository:
```bash
git clone [repository-url] wp-content/themes/lodgifywp
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install NPM dependencies:
```bash
npm install
```

4. Start development:
```bash
npm run dev
```

5. Build for production:
```bash
npm run build
```

## Features

- Responsive design
- Property booking system
- Stripe integration
- iCal synchronization
- Advanced Custom Fields integration
- Flexible content blocks
- Custom post types and taxonomies
- Timber/Twig templating

## Coding Standards

This theme follows WordPress coding standards and best practices. To check your code:

```bash
composer run phpcs
npm run lint
```

## License

This theme is licensed under the GPL v2 or later. 