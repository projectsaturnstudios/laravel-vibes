<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidPrimitiveHandler;
use stdClass; // Use a standard class for invalid type testing

class TheAgencyValidationTest extends TestCase
{
    protected TheAgency $agency;

    protected function setUp(): void
    {
        parent::setUp();
        // Resolve TheAgency from the container
        $this->agency = $this->app->make(TheAgency::class);
        // Clear tools before each test (using the loop from previous tests)
        $existingTools = $this->agency->getTools();
        foreach ($existingTools as $tool) {
            $this->agency->removeTool($tool);
        }
    }

    #[Test]
    public function test_add_primitive_handler_throws_exception_for_invalid_class_type()
    {
        $this->expectException(InvalidPrimitiveHandler::class);
        $this->expectExceptionMessage('must implement ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler');

        $this->agency->addPrimitiveHandler(stdClass::class);
    }

    #[Test]
    public function test_add_tool_throws_exception_for_invalid_class_string()
    {
        $this->expectException(InvalidPrimitiveHandler::class);
        $this->expectExceptionMessage('must extend');

        $this->app->bind(stdClass::class, fn() => new stdClass());
        $this->agency->addTool(stdClass::class);
    }

    #[Test]
    public function test_add_tool_throws_exception_for_invalid_object_instance()
    {
        $this->expectException(\TypeError::class);

        $invalidObject = new stdClass();
        $this->agency->addTool($invalidObject);
    }

    // Add more validation tests...
} 