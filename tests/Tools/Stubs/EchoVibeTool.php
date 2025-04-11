<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Stubs;

use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;

#[MCPTool('echo', 'Echoes back the input text')]
class EchoVibeTool extends VibeTool //implements PrimitiveHandler
{
    protected string $name = 'echo';

    /**
     * Define the input schema for the echo tool.
     * 
     * @return array The JSON Schema definition for the tool's input parameters
     */
    public static function input_schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'text' => [
                    'type' => 'string',
                    'description' => 'Text to echo back'
                ]
            ],
            'required' => ['text']
        ];
    }

    public static function getMetadata(): array
    {
        return [
            'name' => 'echo',
            'description' => 'Echoes back the input text',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'text' => [
                        'type' => 'string',
                        'description' => 'Text to echo back'
                    ]
                ],
                'required' => ['text']
            ]
        ];
    }

    public function getName(): string
    {
        return 'echo';
    }
}
