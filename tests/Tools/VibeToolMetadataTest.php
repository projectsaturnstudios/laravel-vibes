<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;

// Include the file with SimpleInheritedTool and OverridingTool definitions
require_once __DIR__ . '/VibeToolInheritanceTest.php';

class VibeToolMetadataTest extends TestCase
{
    #[Test]
    public function test_can_access_basic_metadata()
    {
        $metadata = EchoVibeTool::getMetadata();

        $this->assertIsArray($metadata);
        $this->assertArrayHasKey('name', $metadata, 'Metadata must contain a name.');
        $this->assertEquals('echo', $metadata['name']);
        $this->assertArrayHasKey('description', $metadata, 'Metadata should contain a description.');
        $this->assertIsString($metadata['description']);
        
        // Add checks for other expected basic keys if necessary (e.g., parameters)
    }

    #[Test]
    public function test_metadata_structure_for_tool_with_parameters()
    {
        $metadata = CalculatorVibeTool::getMetadata();
        
        $this->assertIsArray($metadata);
        $this->assertEquals('calculator', $metadata['name']);
        $this->assertArrayHasKey('parameters', $metadata, 'Calculator tool metadata should define parameters.');
        
        $params = $metadata['parameters'];
        $this->assertIsArray($params);
        $this->assertArrayHasKey('type', $params);
        $this->assertEquals('object', $params['type']);
        $this->assertArrayHasKey('properties', $params);
        $this->assertIsArray($params['properties']);
        $this->assertArrayHasKey('a', $params['properties']);
        $this->assertArrayHasKey('b', $params['properties']);
        $this->assertArrayHasKey('required', $params);
        $this->assertEquals(['operation', 'a', 'b'], $params['required']);
    }

    #[Test]
    public function test_metadata_is_replaced_not_merged_in_subclass()
    {
        $parentMetadata = SimpleInheritedTool::getMetadata();
        $childMetadata = OverridingTool::getMetadata();

        // Assert description is replaced (as tested before)
        $this->assertNotEquals($parentMetadata['description'], $childMetadata['description']);
        $this->assertEquals('Overrides Simple Tool', $childMetadata['description']);

        // Assert name is also replaced
        $this->assertNotEquals($parentMetadata['name'], $childMetadata['name']);
        $this->assertEquals('overriding_tool', $childMetadata['name']);
    }

    // Add tests for inheritance/overriding, defaults, etc.
} 