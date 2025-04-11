<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Repositories\VibeToolRepo;
use ProjectSaturnStudios\Vibes\TheAgency;
use Symfony\Component\VarDumper\VarDumper;

class ToolRepositoryBasicOperationsTest extends TestCase
{
    protected VibeToolRepo $repository;
    protected TheAgency $agency;
    protected EchoVibeTool $echoToolInstance;
    protected CalculatorVibeTool $calculatorToolInstance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new VibeToolRepo();
        $this->agency = $this->app->make(TheAgency::class);

        // Clear any existing tools from previous tests using removeTool
        $existingTools = $this->agency->getTools();
        foreach ($existingTools as $tool) {
            $this->agency->removeTool($tool);
        }

        $this->echoToolInstance = new EchoVibeTool();
        $this->calculatorToolInstance = new CalculatorVibeTool();
    }

    #[Test]
    public function test_can_retrieve_tool_from_repository()
    {
        $this->agency->addTool($this->echoToolInstance);

        $retrievedTool = $this->repository->find('echo');

        $this->assertInstanceOf(EchoVibeTool::class, $retrievedTool);
        $this->assertEquals('echo', $retrievedTool->getName());
        $this->assertSame($this->echoToolInstance->getName(), $retrievedTool->getName());
    }

    #[Test]
    public function test_find_returns_null_for_nonexistent_tool()
    {
        // Ensure no tools are present initially
        // This is handled by the setUp method now
        // $this->agency->removeAllTools(); // Removed

        $result = $this->repository->find('nonexistent');

        $this->assertNull($result);
    }

    #[Test]
    public function test_can_list_all_registered_tools()
    {
        $this->agency->addTool($this->echoToolInstance);
        $this->agency->addTool($this->calculatorToolInstance);

        $allTools = $this->repository->retrieveAll();

        $this->assertCount(2, $allTools);
        $this->assertTrue($allTools->contains($this->echoToolInstance));
        $this->assertTrue($allTools->contains($this->calculatorToolInstance));
    }

    #[Test]
    public function test_can_filter_tools_by_name()
    {
        $this->agency->addTool($this->echoToolInstance);
        $this->agency->addTool($this->calculatorToolInstance);

        $allTools = $this->repository->retrieveAll();

        $echoTools = $allTools->filter(fn($tool) => $tool->getName() === 'echo');

        $this->assertCount(1, $echoTools);
        $this->assertEquals('echo', $echoTools->first()->getName());
        $this->assertSame($this->echoToolInstance->getName(), $echoTools->first()->getName());
    }
}
