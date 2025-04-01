# Contributing to LodgifyWP

Thank you for your interest in contributing to LodgifyWP! This document provides guidelines and steps for contributing to the project.

## Code of Conduct

By participating in this project, you agree to abide by our [Code of Conduct](CODE_OF_CONDUCT.md).

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in the [Issues](https://github.com/WonkyPixelUK/LodgifyWP/issues) section
2. If not, create a new issue with:
   - A clear title and description
   - Steps to reproduce the bug
   - Expected behavior
   - Actual behavior
   - Screenshots (if applicable)
   - WordPress version and PHP version

### Suggesting Enhancements

1. Check if the enhancement has been suggested in the [Issues](https://github.com/WonkyPixelUK/LodgifyWP/issues) section
2. If not, create a new issue with:
   - A clear title and description
   - Use case and benefits
   - Implementation suggestions (if any)
   - Screenshots or mockups (if applicable)

### Pull Requests

1. Fork the repository
2. Create a new branch for your feature/fix:
   ```bash
   git checkout -b feature/your-feature-name
   # or
   git checkout -b fix/your-fix-name
   ```
3. Make your changes following our coding standards
4. Test your changes thoroughly
5. Commit your changes with clear messages:
   ```bash
   git commit -m "feat: add new feature"
   # or
   git commit -m "fix: resolve specific issue"
   ```
6. Push to your fork:
   ```bash
   git push origin feature/your-feature-name
   ```
7. Create a Pull Request with:
   - A clear title and description
   - Reference to related issues
   - Screenshots (if applicable)
   - Test results

## Development Setup

1. Clone your fork:
   ```bash
   git clone https://github.com/YOUR_USERNAME/LodgifyWP.git
   cd LodgifyWP
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up your development environment:
   - Install WordPress locally
   - Install required plugins (ACF Pro, Timber)
   - Configure your local environment

4. Create a test environment:
   - Set up test data
   - Configure test API keys
   - Set up test email accounts

## Coding Standards

### PHP

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/php/)
- Use meaningful variable and function names
- Add PHPDoc blocks for functions and classes
- Keep functions focused and single-purpose
- Use proper error handling

### JavaScript

- Follow [WordPress JavaScript Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/javascript/)
- Use ES6+ features appropriately
- Document complex functions
- Handle errors gracefully

### SCSS

- Follow [WordPress SCSS Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/css/)
- Use BEM naming convention
- Keep selectors specific and maintainable
- Use variables for repeated values

### Twig Templates

- Follow [Timber/Twig best practices](https://timber.github.io/docs/guides/best-practices/)
- Keep templates DRY
- Use proper escaping
- Organize templates logically

## Testing

1. Test your changes in:
   - Different WordPress versions
   - Different PHP versions
   - Different browsers
   - Mobile devices

2. Run automated tests:
   ```bash
   composer test
   ```

3. Test edge cases and error conditions

## Documentation

1. Update relevant documentation:
   - README.md
   - CHANGELOG.md
   - Code comments
   - PHPDoc blocks

2. Follow documentation standards:
   - Clear and concise language
   - Proper formatting
   - Code examples where appropriate
   - Screenshots for UI changes

## Review Process

1. All pull requests will be reviewed by maintainers
2. Address review comments promptly
3. Keep pull requests focused and manageable
4. Update pull requests based on feedback
5. Ensure all tests pass

## Release Process

1. Version numbers follow [Semantic Versioning](https://semver.org/)
2. Update CHANGELOG.md with your changes
3. Create a release tag
4. Update documentation if needed

## Questions?

If you have questions, please:
1. Check the [documentation](https://github.com/WonkyPixelUK/LodgifyWP/wiki)
2. Search existing issues
3. Create a new issue if needed

Thank you for contributing to LodgifyWP! 