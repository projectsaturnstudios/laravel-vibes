# TheAgency Usage Examples

This document provides practical examples of how to use TheAgency class in your Laravel applications.

## Basic Usage

### Creating an Instance

The recommended way to get an instance of TheAgency is through Laravel's service container:

```php
use ProjectSaturnStudios\Vibes\TheAgency;

// In a controller or service:
$agency = app(TheAgency::class);

// Alternative using dependency injection in a controller:
public function handle(TheAgency $agency)
{
    // Use $agency here
}
```

### Using the Factory Method

You can also use the static `make()` method to create a new instance with the default configuration:

```php
use ProjectSaturnStudios\Vibes\TheAgency;

$agency = TheAgency::make();
```

## Working with Tools

### Registering a Single Tool

```php
use App\MCP\Tools\WeatherTool;

$agency->addTool(WeatherTool::class);

// Or with an instance:
$weatherTool = new WeatherTool();
$agency->addTool($weatherTool);
```

### Registering Multiple Tools

```php
use App\MCP\Tools\WeatherTool;
use App\MCP\Tools\TranslationTool;
use App\MCP\Tools\DataVisualizationTool;

$agency->addTools([
    WeatherTool::class,
    TranslationTool::class,
    DataVisualizationTool::class,
]);
```

### Retrieving Tools

```php
// Get all registered tools
$allTools = $agency->getTools();

// Get a specific tool by name
$weatherTool = $agency->getTool('weather');

if ($weatherTool) {
    // Use the tool
}
```

### Removing a Tool

```php
use App\MCP\Tools\WeatherTool;

$agency->removeTool(WeatherTool::class);

// Or with an instance:
$weatherTool = new WeatherTool();
$agency->removeTool($weatherTool);
```

## Service Provider Registration

For a clean approach, register tools and other primitives in a dedicated service provider:

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ProjectSaturnStudios\Vibes\TheAgency;
use App\MCP\Tools\WeatherTool;
use App\MCP\Tools\TranslationTool;

class MCPServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // Get the agency singleton
        $agency = app(TheAgency::class);
        
        // Register tools
        $agency->addTools([
            WeatherTool::class,
            TranslationTool::class,
        ]);
    }
}
```

Register your service provider in `config/app.php`:

```php
'providers' => [
    // Other service providers
    App\Providers\MCPServiceProvider::class,
],
```

## Creating a Custom Tool

Here's an example of creating a custom tool that can be registered with TheAgency:

```php
<?php

namespace App\MCP\Tools;

use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

class WeatherTool extends VibeTool
{
    protected string $name = 'weather';

    public static function getMetadata(): array
    {
        return [
            'name' => 'weather',
            'description' => 'Get weather information for a location',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'location' => [
                        'type' => 'string',
                        'description' => 'City name or coordinates'
                    ],
                    'units' => [
                        'type' => 'string',
                        'enum' => ['metric', 'imperial'],
                        'description' => 'Temperature units'
                    ]
                ],
                'required' => ['location']
            ]
        ];
    }
    
    /**
     * Handle the tool execution
     *
     * @param string $location The location to get weather for
     * @param string $units The temperature units (metric or imperial)
     * @return array Weather data
     */
    public function handle(string $location, string $units = 'metric'): array
    {
        // Implement weather API call logic here
        
        return [
            'location' => $location,
            'temperature' => 22,
            'conditions' => 'sunny',
            'units' => $units,
        ];
    }
}
```

## Working with Primitive Handlers

### Adding a Generic Primitive Handler

```php
use App\MCP\Tools\CustomTool;

$agency->addPrimitiveHandler(CustomTool::class);
```

### Removing a Primitive Handler

```php
use App\MCP\Tools\CustomTool;

$agency->removePrimitiveHandler(CustomTool::class);
```

### Adding Multiple Primitive Handlers

```php
use App\MCP\Tools\CustomTool;
use App\MCP\Resources\UserResource;

$agency->addPrimitiveHandlers([
    CustomTool::class,
    UserResource::class,
]);
```

## Accessing in Blade Views

You can access TheAgency in Blade views through a view composer:

```php
// In a service provider:
public function boot()
{
    View::composer('*', function ($view) {
        $view->with('agency', app(TheAgency::class));
    });
}
```

Then in your Blade view:

```blade
@if($agency->getTool('weather'))
    <!-- Weather tool is available -->
    <weather-widget></weather-widget>
@endif
```

## Testing with TheAgency

When writing tests, you can create a mock instance of TheAgency:

```php
use ProjectSaturnStudios\Vibes\TheAgency;
use App\MCP\Tools\WeatherTool;

public function test_agency_can_register_tools()
{
    // Create a new agency instance for testing
    $agency = new TheAgency(['service_info' => ['catch_exceptions' => false]]);
    
    // Add a tool
    $agency->addTool(WeatherTool::class);
    
    // Assert the tool was registered
    $this->assertNotNull($agency->getTool('weather'));
}
``` 