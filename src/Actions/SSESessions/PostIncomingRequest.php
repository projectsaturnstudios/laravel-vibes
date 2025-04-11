<?php

namespace ProjectSaturnStudios\Vibes\Actions\SSESessions;

use Exception;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;

class PostIncomingRequest
{
    use AsAction;

    /**
     * Process an MCP request and route it to the appropriate handler
     *
     * The core handler that:
     * 1. Validates the session exists
     * 2. Finds the appropriate method handler from config
     * 3. Delegates to that handler
     * 4. Manages errors and exceptions
     *
     * @param AgentVibe $vibe The validated MCP request
     * @return int HTTP status code to return
     */
    public function handle(AgentVibe $vibe) : int
    {
        try {
            $results = 202;
            read_the_agent_vibe($vibe);
        }
        catch (Exception $e) {
            $results = 500;
        }

        return $results;
    }
}
