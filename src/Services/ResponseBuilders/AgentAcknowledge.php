<?php

namespace ProjectSaturnStudios\Vibes\Services\ResponseBuilders;

use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;

class AgentAcknowledge extends SSEResponseBuilder
{
    public function supply() : array
    {
        $results = $this->toArray();

        $results['result'] = new \stdClass();

        return $results;
    }
}
