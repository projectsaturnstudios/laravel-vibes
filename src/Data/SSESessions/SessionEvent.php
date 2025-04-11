<?php

namespace ProjectSaturnStudios\Vibes\Data\SSESessions;

use ProjectSaturnStudios\Vibes\Data\AgentMessage;
use Spatie\LaravelData\Data;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;

/**
 * Represents an event within a server-sent events (SSE) session.
 *
 * This data class encapsulates an event that will be sent through a server-sent
 * events connection as part of the MCP protocol. It contains the session identifier,
 * the type of event from the MCPResponseEvent enum, and any associated payload data.
 *
 * @package ProjectSaturnStudios\Vibes\Data\SSESessions
 * @since 0.4.0
 */
class SessionEvent extends Data
{
    /**
     * Create a new SessionEvent instance.
     *
     * @param string $session_id The unique session identifier this event belongs to
     * @param MCPResponseEvent $occasion The type of event from the MCPResponseEvent enum
     * @param string|array|null $payload [optional] The data payload to include with the event
     * 
     * @since 0.4.0
     */
    public function __construct(
        public readonly string $session_id,
        public readonly MCPResponseEvent $occasion,
        public readonly string|array|null $payload = null,
    ) {}

    /**
     * Convert a SessionEvent to an AgentMessage.
     *
     * This utility method creates an AgentMessage instance from a SessionEvent by
     * loading the corresponding VibeSesh and transferring event data.
     *
     * @param SessionEvent $message The session event to convert
     * 
     * @return AgentMessage|null The converted agent message
     * 
     * @since 0.4.0
     */
    public static function convertToAgentMessage(SessionEvent $message) : ?AgentMessage
    {
        return new AgentMessage(
            sesh: VibeSesh::load($message->session_id),
            event: $message->occasion,
            payload: $message->payload,
        );
    }
}
