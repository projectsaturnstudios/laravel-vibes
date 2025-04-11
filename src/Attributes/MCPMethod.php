<?php

namespace ProjectSaturnStudios\Vibes\Attributes;

#[\Attribute]
readonly class MCPMethod
{
    public function __construct(public string $method)
    {

    }

    public function fuck() : string
    {
        return 'yea!';
    }
}
