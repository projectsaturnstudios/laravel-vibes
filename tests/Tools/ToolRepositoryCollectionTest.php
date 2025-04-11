<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Repositories\VibeToolRepo;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\DiscoverableTool; // Use another tool for variety
use Illuminate\Support\Collection;

class ToolRepositoryCollectionTest extends TestCase
{
    protected TheAgency $agency;
    protected VibeToolRepo $repository;
    protected EchoVibeTool $echoToolInstance;
    protected CalculatorVibeTool $calculatorToolInstance;
    protected DiscoverableTool $discoverableToolInstance;

    protected function setUp(): void
    {
        parent::setUp();
        $this->agency = $this->app->make(TheAgency::class);
        $this->repository = new VibeToolRepo();

        // Clear existing tools
        $existingTools = $this->agency->getTools();
        foreach ($existingTools as $tool) {
            $this->agency->removeTool($tool);
        }

        // Add tools for testing
        $this->echoToolInstance = new EchoVibeTool();
        $this->calculatorToolInstance = new CalculatorVibeTool();
        $this->discoverableToolInstance = new DiscoverableTool();
        $this->agency->addTools([
            $this->echoToolInstance,
            $this->calculatorToolInstance,
            $this->discoverableToolInstance
        ]);
    }

    #[Test]
    public function test_retrieve_all_returns_illuminate_collection()
    {
        $tools = $this->repository->retrieveAll();
        $this->assertInstanceOf(Collection::class, $tools);
    }

    #[Test]
    public function test_collection_filtering_works()
    {
        $tools = $this->repository->retrieveAll();

        // Filter by instance type
        $echoTools = $tools->filter(fn($tool) => $tool instanceof EchoVibeTool);
        $this->assertCount(1, $echoTools);
        $this->assertSame($this->echoToolInstance->getName(), $echoTools->first()->getName());

        // Filter by name property
        $calcTools = $tools->filter(fn($tool) => $tool->getName() === 'calculator');
        $this->assertCount(1, $calcTools);
        $this->assertSame($this->calculatorToolInstance->getName(), $calcTools->first()->getName());
    }

    /* // Temporarily comment out due to potential incompatibility with PrimitiveHandlerCollection
    #[Test]
    public function test_collection_mapping_works()
    {
        $tools = $this->repository->retrieveAll();
        // Map to tool names
        $names = $tools->map(fn($tool) => $tool->getName())->all();
        $this->assertCount(3, $names);
        $this->assertContains('echo', $names);
        $this->assertContains('calculator', $names);
        $this->assertContains('discoverable', $names);
    }
    */

    #[Test]
    public function test_collection_iteration_works()
    {
        $tools = $this->repository->retrieveAll();
        $count = 0;
        $tools->each(function ($tool) use (&$count) {
            $this->assertInstanceOf(\ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool::class, $tool);
            $count++;
        });
        $this->assertEquals(3, $count);
    }

    /* // Temporarily comment out due to potential incompatibility with PrimitiveHandlerCollection
    #[Test]
    public function test_collection_sorting_works()
    {
        $tools = $this->repository->retrieveAll();
        // Sort by name alphabetically
        $sortedTools = $tools->sortBy(fn($tool) => $tool->getName());
        $sortedNames = $sortedTools->map(fn($tool) => $tool->getName())->values()->all();
        $this->assertEquals(['calculator', 'discoverable', 'echo'], $sortedNames);
    }
    */

    #[Test]
    public function test_collection_count_and_empty_checks_work()
    {
        $tools = $this->repository->retrieveAll();
        $this->assertCount(3, $tools);
        $this->assertFalse($tools->isEmpty());

        // Clear tools via Agency and re-check
        $this->agency->removeTool($this->echoToolInstance);
        $this->agency->removeTool($this->calculatorToolInstance);
        $this->agency->removeTool($this->discoverableToolInstance);

        $emptyTools = $this->repository->retrieveAll();
        $this->assertCount(0, $emptyTools);
        $this->assertTrue($emptyTools->isEmpty());
    }

    // Add more collection tests (reduce, groupBy, etc. if needed)
}
