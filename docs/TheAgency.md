# TheAgency Class Documentation

`TheAgency` is the central orchestrator of the Laravel Vibes package, managing various MCP (Machine Control Protocol) primitives including tools, resources, prompts, samples, and roots.

## Overview

TheAgency serves as the registry and manager for all primitive handlers in the Laravel Vibes ecosystem. It provides methods to add, remove, and access these primitives, enabling AI agents to interact with your Laravel application through the Machine Control Protocol.

## Class Declaration

```php
namespace ProjectSaturnStudios\Vibes;

class TheAgency
{
    use HasTools, HasResources, HasPrompts, HasSamples, HasRoots;
    
    // Methods and properties...
}
```

## Traits Used

TheAgency incorporates functionality through several traits:

- `HasTools`: Manages tool primitives that AI agents can use
- `HasResources`: Manages resource primitives for data retrieval
- `HasPrompts`: Manages prompt primitives for AI agent interactions
- `HasSamples`: Manages sample primitives for model configuration
- `HasRoots`: Manages root primitives for custom workflow entry points

## Properties

| Property | Type | Description |
|----------|------|-------------|
| `$catchExceptions` | bool | Determines whether exceptions should be caught during primitive handler operations |

## Methods

### Constructor

```php
public function __construct(array $config)
```

Creates a new instance of TheAgency with the provided configuration.

**Parameters:**
- `$config` (array): Configuration array containing service options

### addPrimitiveHandler

```php
public function addPrimitiveHandler($primitiveHandlerClass) : void
```

Adds a primitive handler to the appropriate collection based on its type.

**Parameters:**
- `$primitiveHandlerClass` (string|object): The class name or instance of the primitive handler

**Throws:**
- `InvalidPrimitiveHandler`: When the provided class is not a valid primitive handler

### removePrimitiveHandler

```php
public function removePrimitiveHandler(string $primitiveHandlerClass): self
```

Removes a primitive handler from the appropriate collection based on its type.

**Parameters:**
- `$primitiveHandlerClass` (string): The class name of the primitive handler to remove

**Returns:**
- `self`: The instance of TheAgency for method chaining

**Throws:**
- `InvalidPrimitiveHandler`: When the provided class is not a valid primitive handler

### addPrimitiveHandlers

```php
public function addPrimitiveHandlers(array $primitiveHandlers): self
```

Adds multiple primitive handlers at once.

**Parameters:**
- `$primitiveHandlers` (array): Array of primitive handler class names or instances

**Returns:**
- `self`: The instance of TheAgency for method chaining

### make

```php
public static function make() : static
```

Creates a new instance of TheAgency with default configuration from the `vibes` config.

**Returns:**
- `static`: A new instance of TheAgency

## Usage Examples

### Basic Registration

```php
// Get the agency singleton
$agency = app(TheAgency::class);

// Register tools
$agency->addTools([
    WeatherTool::class,
    TranslationTool::class,
]);
```

### Creating a Custom Agency Instance

```php
$customConfig = [
    'service_info' => [
        'catch_exceptions' => true,
    ],
];

$agency = new TheAgency($customConfig);
```

### Using the Factory Method

```php
// Uses the configuration from config/vibes.php
$agency = TheAgency::make();
```

## Extending TheAgency

To extend TheAgency with additional functionality:

```php
class ExtendedAgency extends TheAgency
{
    public function someCustomMethod()
    {
        // Custom implementation
    }
    
    // Override existing methods as needed
}
```

## Related Classes

- `VibeTool`: Base class for implementing tools
- `PrimitiveHandlerCollection`: Collection class for managing primitive handlers
- `InvalidPrimitiveHandler`: Exception class for invalid primitive handlers

## Notes

- TheAgency is typically registered as a singleton in the Laravel container
- Auto-discovery mechanisms can be used to automatically register primitives
- Caching is available for better performance in production 