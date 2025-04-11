# Laravel Vibes Documentation

This documentation covers the Laravel Vibes package components, with a focus on TheAgency class and related functionality.

## Table of Contents

- [TheAgency Documentation](TheAgency.md) - Complete documentation for TheAgency class
- [Class Diagram](ClassDiagram.md) - Structural diagram showing the relationships between classes
- [Usage Examples](UsageExamples.md) - Practical examples of using TheAgency in Laravel applications

## Core Concepts

Laravel Vibes is a package for implementing the Machine Control Protocol (MCP) server in Laravel applications. It enables AI agents to interact with your application through a structured API, leveraging various primitives like tools, resources, prompts, samples, and roots.

### TheAgency

TheAgency is the central orchestrator that manages all primitive handlers in the Laravel Vibes ecosystem. It provides methods to add, remove, and access these primitives, enabling AI agents to interact with your Laravel application.

### Primitive Handlers

Primitive handlers are the building blocks of the MCP implementation. They can be:

- **Tools**: Functions that AI agents can call to perform actions
- **Resources**: Data sources that AI agents can query
- **Prompts**: Templates for AI agent interactions
- **Samples**: Configuration for AI model behavior
- **Roots**: Entry points for custom workflows

## Getting Started

See the [main README](../README.md) for installation instructions and basic setup.

For usage examples, check the [Usage Examples](UsageExamples.md) document.

## Contributing

When extending or modifying TheAgency or related classes, please follow these guidelines:

1. Maintain consistent PHPDoc comments for all methods and properties
2. Follow Laravel's coding style and conventions
3. Add appropriate unit tests for new functionality
4. Update documentation to reflect changes 