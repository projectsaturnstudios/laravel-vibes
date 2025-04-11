<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Stubs;

use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;

#[MCPTool('discoverable', 'A tool that can be discovered by the discovery service')]
class DiscoverableTool extends VibeTool //implements PrimitiveHandler
{
    protected string $name = 'discoverable';

    /**
     * Define the input schema for the tool.
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
                    'description' => 'Some parameter text'
                ]
            ],
            'required' => ['text']
        ];
    }

    public static function getMetadata(): array
    {
        return [
            'name' => 'discoverable',
            'description' => 'A tool that can be discovered by the discovery service',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'text' => [
                        'type' => 'string',
                        'description' => 'Some parameter text'
                    ]
                ],
                'required' => ['text']
            ]
        ];
    }
}
