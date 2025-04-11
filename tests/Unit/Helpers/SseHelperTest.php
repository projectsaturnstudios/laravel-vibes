<?php

namespace ProjectSaturnStudios\Vibes\Tests\Unit\Helpers;

use Mockery;
use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeStreamTether;

class SseHelperTest extends TestCase
{
    protected VibeSesh $sesh;
    protected AgentVibe $vibe;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for VibeSesh
        $this->sesh = Mockery::mock(VibeSesh::class);
        $this->sesh->session_id = 'test-session-id';

        // Create AgentVibe instance using named constructor parameters
        // since properties are readonly
        $this->vibe = new AgentVibe(
            jsonrpc: '2.0',
            method: 'test_method',
            session_id: 'test-session-id',
            id: 'test-request-id',
            params: ['param1' => 'value1']
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    #[Test]
    public function it_creates_sse_stream()
    {
        // Call the function
        $result = create_sse_stream($this->sesh);

        // Assert the result is a VibeStreamTether instance
        $this->assertInstanceOf(VibeStreamTether::class, $result);

        // Assert the tether has the correct session
        $this->assertEquals($this->sesh, $result->sesh);
    }

    #[Test]
    public function it_creates_session_event()
    {
        // Test data
        $sessionId = 'test-session-id';
        $eventType = MCPResponseEvent::MESSAGE;
        $payload = ['key' => 'value'];

        // Call the function
        $result = sesh_event($sessionId, $eventType, $payload);

        // Assert the result is a SessionEvent instance
        $this->assertInstanceOf(SessionEvent::class, $result);

        // Verify the SessionEvent properties are set correctly
        $this->assertEquals($sessionId, $result->session_id);
        $this->assertEquals($eventType, $result->occasion);
        $this->assertEquals($payload, $result->payload);
    }
}
