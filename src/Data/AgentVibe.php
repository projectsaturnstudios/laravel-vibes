<?php

namespace ProjectSaturnStudios\Vibes\Data;

use Spatie\LaravelData\Data;

/**
 * Represents an incoming request from an AI agent in the MCP protocol.
 *
 * This data class encapsulates the structure of inbound agent requests following the
 * Model-Context-Protocol (MCP) format. It stores essential information about the request
 * including the protocol version, method being called, session identifier, and any
 * associated parameters.
 *
 * @package ProjectSaturnStudios\Vibes\Data
 * @since 0.4.0
 */
class AgentVibe extends Data
{
    /**
     * Create a new AgentVibe instance.
     *
     * @param string $jsonrpc The JSON-RPC protocol version (typically "2.0")
     * @param string $method The method/action being requested by the agent
     * @param string $session_id The unique session identifier for this agent interaction
     * @param string|null $id [optional] The request identifier, used for matching responses to requests
     * @param array|null $params [optional] Additional parameters for the requested method
     * 
     * @since 0.4.0
     */
    public function __construct(
        public readonly string $jsonrpc,
        public readonly string $method,
        public readonly string $session_id,
        public readonly ?string $id = null,
        public readonly ?array $params = null
    )
    {}
}
