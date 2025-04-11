# Contributing to Laravel Vibes

Thank you for considering contributing to Laravel Vibes! This document outlines the guidelines and workflows for contributing to this package.

## Table of Contents

- [Development Workflow](#development-workflow)
- [Code Style Requirements](#code-style-requirements)
- [Testing Requirements](#testing-requirements)
- [Pull Request Process](#pull-request-process)
- [Documentation Standards](#documentation-standards)
- [Reporting Bugs](#reporting-bugs)
- [Feature Requests](#feature-requests)

## Development Workflow

### Getting Started

1. **Fork the Repository**: Start by forking the [Laravel Vibes repository](https://github.com/projectsaturnstudios/laravel-vibes) to your GitHub account.

2. **Clone Your Fork**: Clone your fork to your local development environment:
   ```bash
   git clone https://github.com/YOUR-USERNAME/laravel-vibes.git
   cd laravel-vibes
   ```

3. **Install Dependencies**: Install Composer dependencies:
   ```bash
   composer install
   ```

4. **Set Up Development Environment**:
   - Create a Laravel test application or use an existing one
   - Set up a symbolic link or repository reference in your test application
   - Configure your test application to use your development version of Laravel Vibes

### Development Process

1. **Create a Branch**: Always work in a feature branch:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Implement Your Changes**: Develop your feature or fix using the code style and guidelines in this document.

3. **Write Tests**: Add or update tests to ensure code quality and prevent regressions.

4. **Run Tests**: Make sure all tests pass:
   ```bash
   composer test
   ```

5. **Check Code Style**: Ensure your code follows the style guidelines:
   ```bash
   composer check-style
   ```

6. **Document Your Changes**: Update or add documentation for any new features or changes.

7. **Commit Your Changes**: Use clear, descriptive commit messages:
   ```bash
   git commit -m "Feature: Add new XYZ capability to ABC component"
   ```

8. **Push to Your Fork**: Push your changes to your GitHub repository:
   ```bash
   git push origin feature/your-feature-name
   ```

9. **Submit a Pull Request**: Create a pull request from your fork to the main repository.

## Code Style Requirements

Laravel Vibes follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard and the [Laravel coding style](https://laravel.com/docs/master/contributions#coding-style).

### General Guidelines

- Use 4 spaces for indentation (no tabs)
- Use `StudlyCaps` for class names
- Use `camelCase` for method and variable names
- Use proper type hints and return type declarations
- Keep methods focused and reasonably sized
- Prefer explicit visibility declarations (`public`, `protected`, `private`)
- Use PHP 8.2+ features where appropriate (readonly classes, enums, etc.)

### Laravel Vibes Specific Guidelines

- Primitive handlers should implement the `PrimitiveHandler` interface
- Tools should extend the `VibeTool` base class
- Always include comprehensive PHPDoc comments
- Include `@since` tags in PHPDoc comments to indicate when features were added

### Code Style Checking

The project uses Laravel Pint for code style enforcement:

```bash
# Check code style
composer check-style

# Fix code style issues automatically
composer fix-style
```

## Testing Requirements

All contributions must include appropriate tests. Laravel Vibes uses PHPUnit for testing.

### Testing Guidelines

1. **Test Coverage**: Aim for comprehensive test coverage of all new code.
2. **Unit Tests**: Write unit tests for individual components.
3. **Integration Tests**: Write integration tests for components that interact with each other.
4. **Feature Tests**: Write feature tests for complete features.
5. **Test Edge Cases**: Include tests for error conditions and edge cases.

### Types of Tests Needed

- **Tool Tests**: Each tool should have tests for validation, execution, and error handling
- **Middleware Tests**: Middleware components should be tested independently
- **SSE Connection Tests**: Server-Sent Events functionality should include connection tests
- **TheAgency Integration Tests**: Test integration with the central TheAgency component

### Running Tests

```bash
# Run all tests
composer test

# Run specific test suite
composer test -- --testsuite=Unit

# Generate test coverage report
composer test-coverage
```

## Pull Request Process

1. **PR Title Format**: Use clear, descriptive titles that summarize the change
   - Format: `[Type]: Brief description`
   - Examples: `[Feature]: Add new resource primitive`, `[Fix]: Resolve SSE connection timeout`

2. **PR Description**: Include a detailed description with:
   - What problem the PR solves
   - How the solution works
   - Any breaking changes
   - Screenshots or examples (if applicable)

3. **Link Related Issues**: Reference any related issues using GitHub keywords
   - Example: `Fixes #123` or `Related to #456`

4. **PR Checklist**: Ensure your PR meets these requirements:
   - [ ] Tests added/updated for all new code
   - [ ] Documentation updated to reflect changes
   - [ ] Code follows style guidelines
   - [ ] All tests pass
   - [ ] No breaking changes (or documented if unavoidable)

5. **Code Review Process**:
   - A maintainer will review your PR
   - Address any feedback or requested changes
   - Once approved, a maintainer will merge your PR

6. **After Merge**:
   - Delete your feature branch
   - Pull the latest changes from the upstream repository

## Documentation Standards

Good documentation is essential for Laravel Vibes. All contributions should include appropriate documentation.

### Documentation Guidelines

1. **PHPDoc Comments**: All classes and methods should have comprehensive PHPDoc comments:
   ```php
   /**
    * Handles the execution of a tool by an AI agent.
    *
    * Validates the input parameters against the tool's schema,
    * executes the tool, and formats the response.
    *
    * @param string $toolName The name of the tool to execute
    * @param array $params The parameters to pass to the tool
    * @return array The formatted tool execution results
    * @throws ToolNotFoundException If the requested tool doesn't exist
    * @throws InvalidToolParameters If the parameters are invalid
    * @since 0.4.0
    */
   ```

2. **README Updates**: Update the main README.md file for significant new features.

3. **Documentation Files**: Create or update dedicated documentation files in the `docs/` directory.

4. **Example Code**: Include example code showing how to use your feature.

5. **Markdown Format**: Use Markdown for all documentation files.

## Reporting Bugs

1. **Use the Issue Tracker**: Report bugs using the GitHub issue tracker.

2. **Bug Report Format**:
   - Descriptive title
   - Steps to reproduce
   - Expected vs. actual behavior
   - Screenshots (if applicable)
   - Environment details (PHP version, Laravel version, etc.)
   - Possible solution (if you have one)

3. **Security Vulnerabilities**: Do NOT report security vulnerabilities via public issues. Email security@projectsaturnstudios.com instead.

## Feature Requests

1. **Use the Issue Tracker**: Submit feature requests using the GitHub issue tracker.

2. **Feature Request Format**:
   - Descriptive title
   - Clear description of the desired feature
   - Use case or problem it solves
   - Any alternatives you've considered
   - Implementation ideas (if applicable)

Thank you for contributing to Laravel Vibes! 