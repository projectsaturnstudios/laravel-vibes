<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidPrimitiveHandler;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\InvalidTool;

class BasicToolRegistrationTest extends TestCase
{
    protected TheAgency $agency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = new TheAgency(['service_info' => ['catch_exceptions' => false]]);
    }

    #[Test]
    public function test_can_add_single_tool()
    {
        $this->agency->addTool(EchoVibeTool::class);

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
        $this->assertInstanceOf(EchoVibeTool::class, app($tools->first()));
    }

    #[Test]
    public function test_can_add_multiple_tools()
    {
        $this->agency->addTools([
            EchoVibeTool::class,
            CalculatorVibeTool::class,
        ]);

        $tools = $this->agency->getTools();
        $this->assertCount(2, $tools);
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof EchoVibeTool));
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof CalculatorVibeTool));
    }

    #[Test]
    public function test_can_remove_tool()
    {
        // Add two tools
        $this->agency->addTools([
            EchoVibeTool::class,
            CalculatorVibeTool::class,
        ]);

        // Verify both added
        $tools = $this->agency->getTools();
        $this->assertCount(2, $tools);

        // Remove one
        $this->agency->removeTool(EchoVibeTool::class);

        // Verify only one remains
        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
        $this->assertFalse($tools->contains(fn($tool) => app($tool) instanceof EchoVibeTool));
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof CalculatorVibeTool));
    }

    #[Test]
    public function test_adding_invalid_tool_throws_exception()
    {
        $this->expectException(InvalidPrimitiveHandler::class);

        $this->agency->addTool(InvalidTool::class);
    }

    #[Test]
    public function test_can_check_if_tool_exists()
    {
        // Add a tool
        $this->agency->addTool(EchoVibeTool::class);

        // Check if tool exists by name
        $echoTool = $this->agency->getTool('echo');
        $calculatorTool = $this->agency->getTool('calculator');

        $this->assertNotNull($echoTool);
        $this->assertNull($calculatorTool);
        $this->assertInstanceOf(EchoVibeTool::class, app($echoTool));
    }
}
