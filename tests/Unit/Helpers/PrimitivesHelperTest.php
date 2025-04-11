<?php

namespace ProjectSaturnStudios\Vibes\Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Collection;
use Mockery;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Contracts\VibeToolRepository;
use PHPUnit\Framework\Attributes\Skip;

class PrimitivesHelperTest extends TestCase
{
    protected $toolRepository;
    protected $echoTool;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock tool
        $this->echoTool = new EchoVibeTool();

        // Create a mock repository
        $this->toolRepository = Mockery::mock(VibeToolRepository::class);

        // Bind the mock repository to the container
        $this->app->instance('vibe-tools', $this->toolRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_retrieves_tool_by_name()
    {
        // Set up the mock repository to return our tool
        $this->toolRepository->shouldReceive('find')
            ->once()
            ->with('echo')
            ->andReturn($this->echoTool);

        // Call the helper function
        $result = mcp_tool('echo');

        // Assert the result is what we expect
        $this->assertSame($this->echoTool, $result);
    }

    #[Test]
    public function it_returns_null_for_nonexistent_tool()
    {
        // Set up the mock repository to return null
        $this->toolRepository->shouldReceive('find')
            ->once()
            ->with('nonexistent-tool')
            ->andReturn(null);

        // Call the helper function
        $result = mcp_tool('nonexistent-tool');

        // Assert the result is null
        $this->assertNull($result);
    }

    #[Test]
    public function it_returns_empty_array_when_no_tools_available()
    {
        // Create an empty collection
        $emptyCollection = new Collection();

        // Set up the mock repository to return an empty collection
        $this->toolRepository->shouldReceive('retrieveAll')
            ->once()
            ->withNoArgs()
            ->andReturn($emptyCollection);

        // Call the helper function
        $result = mcp_tools();

        // Assert the result is an empty Collection
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEmpty($result);
    }
}
