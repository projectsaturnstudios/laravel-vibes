<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidPrimitiveHandler;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;
use stdClass;
use Symfony\Component\VarDumper\VarDumper;

class TheAgencyRegistrationTest extends TestCase
{
    protected TheAgency $agency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = new TheAgency(['service_info' => ['catch_exceptions' => false]]);
    }

    #[Test]
    public function test_can_register_tool_by_class_name()
    {
        $this->agency->addTool(EchoVibeTool::class);

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
        $this->assertInstanceOf(EchoVibeTool::class, app($this->agency->getTool('echo')));
    }

    #[Test]
    public function test_can_register_tool_instance()
    {
        $toolInstance = new EchoVibeTool();
        $this->agency->addTool($toolInstance);

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
        $this->assertSame($toolInstance->getName(), app($this->agency->getTool('echo'))->getName());
    }

    #[Test]
    public function test_can_register_multiple_tools_at_once()
    {
        $this->agency->addTools([
            EchoVibeTool::class,
            Stubs\CalculatorVibeTool::class, // Assuming CalculatorVibeTool exists in Stubs
        ]);

        $tools = $this->agency->getTools();
        $this->assertCount(2, $tools);
        $this->assertInstanceOf(EchoVibeTool::class, app($this->agency->getTool('echo')));
        $this->assertInstanceOf(Stubs\CalculatorVibeTool::class, app($this->agency->getTool('calculator')));
    }

    #[Test]
    public function test_duplicate_registration_is_ignored()
    {
        $this->agency->addTool(EchoVibeTool::class);
        $this->agency->addTool(EchoVibeTool::class); // Add the same tool again

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
    }

    #[Test]
    public function test_registration_with_same_name_replaces_tool()
    {
        // Create a stub tool with the same name as EchoVibeTool
        $conflictingTool = new class extends EchoVibeTool {
            public static function getMetadata(): array { return ['name' => 'echo', 'description' => 'Conflicting Echo']; }
        };

        $this->agency->addTool(EchoVibeTool::class);
        $this->agency->addTool($conflictingTool);

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
        $registeredTool = app($this->agency->getTool('echo'));
        $this->assertSame($conflictingTool->getName(), $registeredTool->getName());
        $this->assertEquals('Conflicting Echo', $registeredTool->getMetadata()['description']);
    }

    #[Test]
    public function test_can_remove_tool()
    {
        $this->agency->addTools([
            EchoVibeTool::class,
            CalculatorVibeTool::class,
        ]);
        $this->assertCount(2, $this->agency->getTools());

        $this->agency->removeTool(EchoVibeTool::class);

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools);
        $this->assertNull($this->agency->getTool('echo'));
        $this->assertInstanceOf(CalculatorVibeTool::class, app($this->agency->getTool('calculator')));
    }

    #[Test]
    public function test_adding_invalid_handler_throws_exception()
    {
        $this->expectException(InvalidPrimitiveHandler::class);

        // Attempt to register a class that doesn't implement PrimitiveHandler (or extend VibeTool)
        $this->agency->addPrimitiveHandler(stdClass::class);
    }
}
