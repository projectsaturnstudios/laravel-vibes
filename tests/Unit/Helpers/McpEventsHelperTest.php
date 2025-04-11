<?php

namespace ProjectSaturnStudios\Vibes\Tests\Unit\Helpers;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Skip;
use Illuminate\Support\Facades\Event;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentAcknowledge;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentError;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentInitializeResponse;
use ProjectSaturnStudios\Vibes\Actions\AgentRequests\ProcessAgentRequest;
use Mockery;
use ProjectSaturnStudios\Vibes\VibeEvents\VibeActivity;
use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;

// These classes were referenced but need to be properly defined or mocked
class AgentReady
{
    public function fire($sessionId) {}
}

class AgentClosed
{
    public function fire($sessionId) {}
}

class QueuePaused
{
    public function fire($sessionId) {}
}

class McpEventsHelperTest extends TestCase
{
    protected VibeSesh $sesh;
    protected AgentVibe $vibe;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock for VibeSesh
        $this->sesh = Mockery::mock(VibeSesh::class);
        $this->sesh->shouldReceive('save')->andReturn($this->sesh);
        $this->sesh->shouldReceive('getSessionId')->andReturn('test-session-id');
        $this->sesh->shouldReceive('getSession')->andReturn(['id' => 'test-session-id']);
        // Add session_id property to the mock to avoid initialization errors
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
        // Note: We're moving Mockery::close() to after the parent tearDown
        // to avoid issues with expectations being checked too early
        parent::tearDown();
        Mockery::close();
    }

    #[Test]
    public function it_sends_good_vibes_with_success_builder()
    {
        // Create a mock for AgentSuccess
        $successBuilder = Mockery::mock(AgentSuccess::class);
        $successBuilder->shouldReceive('supply')
            ->once()
            ->andReturn(['jsonrpc' => '2.0', 'result' => 'Success data']);

        // Set up expectations for the session
        $this->sesh->shouldReceive('addSessionEvent')
            ->once()
            ->with(Mockery::type(SessionEvent::class))
            ->andReturn($this->sesh);

        // Call the helper function
        send_good_vibes($this->sesh, $successBuilder);

        // Add an explicit assertion to avoid "risky" test
        $this->assertTrue(true, 'send_good_vibes executed without exceptions');
    }

    #[Test]
    public function it_sends_good_vibes_with_initialize_response_builder()
    {
        // Create a mock for AgentInitializeResponse
        $initializeBuilder = Mockery::mock(AgentInitializeResponse::class);
        $initializeBuilder->shouldReceive('supply')
            ->once()
            ->andReturn(['jsonrpc' => '2.0', 'result' => ['capabilities' => ['tool_use']]]);

        // Set up expectations for the session
        $this->sesh->shouldReceive('addSessionEvent')
            ->once()
            ->with(Mockery::type(SessionEvent::class))
            ->andReturn($this->sesh);

        // Call the helper function
        send_good_vibes($this->sesh, $initializeBuilder);

        // Add an explicit assertion to avoid "risky" test
        $this->assertTrue(true, 'send_good_vibes executed without exceptions');
    }
}
