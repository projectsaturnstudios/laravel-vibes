<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Stubs;

// Intentionally not extending VibeTool to test exception handling
class InvalidTool
{
    public static function getMetadata(): array
    {
        return [
            'name' => 'invalid',
            'description' => 'This tool is invalid'
        ];
    }

    public function getName(): string
    {
        return 'invalid';
    }
} 