<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;

// Define a simple stub class for testing inheritance
#[MCPTool('simple_inherited', 'Simple Test Tool')]
class SimpleInheritedTool extends VibeTool
{
    protected string $name = 'simple_inherited';

    public static function input_schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'test_param' => ['type' => 'string']
            ]
        ];
    }

    public static function getMetadata(): array
    {
        return ['name' => 'simple_inherited', 'description' => 'Simple Test Tool'];
    }

    // Maybe add a method specific to this subclass
    public function getSpecificValue(): string
    {
        return 'specific';
    }
}

// Define a class that overrides a method from SimpleInheritedTool
#[MCPTool('overriding_tool', 'Overrides Simple Tool')]
class OverridingTool extends SimpleInheritedTool
{
    protected string $name = 'overriding_tool';

    public static function getMetadata(): array
    {
        return ['name' => 'overriding_tool', 'description' => 'Overrides Simple Tool'];
    }

    // Override the parent method
    public function getSpecificValue(): string
    {
        return 'overridden-' . parent::getSpecificValue();
    }
}

// Define a class with a constructor
#[MCPTool('constructor_tool', 'Tool with constructor')]
class ToolWithConstructor extends VibeTool
{
    protected string $name = 'constructor_tool';
    public string $constructedValue;

    public static function input_schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'value' => ['type' => 'string']
            ],
            'required' => ['value']
        ];
    }

    public function __construct(string $value)
    {
        // Maybe parent::__construct() is needed if VibeTool or Data had one?
        // For spatie/laravel-data, often not needed for simple properties.
        $this->constructedValue = $value;
    }

    public static function getMetadata(): array
    {
        return ['name' => 'constructor_tool', 'description' => 'Tool with constructor'];
    }
}

class VibeToolInheritanceTest extends TestCase
{
    #[Test]
    public function test_basic_inheritance_and_metadata_implementation()
    {
        $tool = new SimpleInheritedTool();

        // Check inheritance
        $this->assertInstanceOf(VibeTool::class, $tool);

        // Check metadata implementation
        $metadata = SimpleInheritedTool::getMetadata();
        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('name', $metadata);
        $this->assertEquals('simple_inherited', $metadata['name']);

        // Check name property access (inherited or defined)
        $this->assertEquals('simple_inherited', $tool->getName());

        // Check subclass-specific method
        $this->assertEquals('specific', $tool->getSpecificValue());
    }

    #[Test]
    public function test_method_overriding()
    {
        $tool = new OverridingTool();

        // Check inheritance chain
        $this->assertInstanceOf(SimpleInheritedTool::class, $tool);
        $this->assertInstanceOf(VibeTool::class, $tool);

        // Check overridden method value
        $this->assertEquals('overridden-specific', $tool->getSpecificValue());

        // Check that other properties/methods are still accessible
        $this->assertEquals('overriding_tool', $tool->getName());
        $metadata = OverridingTool::getMetadata();
        $this->assertEquals('overriding_tool', $metadata['name']);
    }

    #[Test]
    public function test_constructor_inheritance()
    {
        $value = 'test-value';
        $tool = new ToolWithConstructor($value);

        $this->assertInstanceOf(VibeTool::class, $tool);
        $this->assertEquals('constructor_tool', $tool->getName());
        $this->assertEquals($value, $tool->constructedValue);

        // Verify metadata still works
        $metadata = ToolWithConstructor::getMetadata();
        $this->assertEquals('constructor_tool', $metadata['name']);
    }

    #[Test]
    public function test_metadata_is_not_inherited_by_default()
    {
        // Metadata from the parent class
        $parentMetadata = SimpleInheritedTool::getMetadata();
        $this->assertArrayHasKey('description', $parentMetadata);
        $this->assertEquals('Simple Test Tool', $parentMetadata['description']);

        // Metadata from the child class that extends SimpleInheritedTool
        $childMetadata = OverridingTool::getMetadata();
        $this->assertArrayHasKey('description', $childMetadata);
        // Assert that the description is the one defined in OverridingTool, not SimpleInheritedTool
        $this->assertEquals('Overrides Simple Tool', $childMetadata['description']);
        $this->assertNotEquals($parentMetadata['description'], $childMetadata['description']);
    }

    // Add more tests for overriding, abstract methods, constructors etc.
}
