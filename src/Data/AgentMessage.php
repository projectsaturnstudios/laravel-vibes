<?php

namespace ProjectSaturnStudios\Vibes\Data;

use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;
use Spatie\LaravelData\Data;

/**
 * Represents a message to be sent to an AI agent.
 *
 * This data class encapsulates messages that will be sent to an AI agent through
 * Server-Sent Events (SSE). It contains the session information, event type from
 * the MCP protocol specification, and the payload data to be transmitted.
 *
 * @package ProjectSaturnStudios\Vibes\Data
 * @since 0.4.0
 */
class AgentMessage extends Data
{
    /**
     * Create a new AgentMessage instance.
     *
     * @param VibeSesh $sesh The session associated with this message
     * @param MCPResponseEvent $event The type of event being sent (from MCPResponseEvent enum)
     * @param string|array|null $payload [optional] The data payload to send with the message
     * 
     * @since 0.4.0
     */
    public function __construct(
        public readonly VibeSesh $sesh,
        public readonly MCPResponseEvent $event,
        public readonly string|array|null $payload = null,
    ) {}
}
