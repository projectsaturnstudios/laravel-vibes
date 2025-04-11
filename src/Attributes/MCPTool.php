<?php

namespace ProjectSaturnStudios\Vibes\Attributes;

#[\Attribute]
readonly class MCPTool
{
    public function __construct(
        public string $tool_name,
        public string $description = "No description provided"
    )
    {

    }
}
