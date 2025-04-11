<?php

namespace ProjectSaturnStudios\Vibes\Services\ResponseBuilders;

use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;

class AgentError extends SSEResponseBuilder
{
    public function supply(MCPErrorCode $code, string $message) : array
    {
        $results = $this->toArray();

        $results['error'] = [
            'code' => $code->value,
            'message' => $message,
        ];

        return $results;
    }
}
