<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Stubs;

use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;

#[MCPTool('calculator', 'Performs basic arithmetic operations')]
class CalculatorVibeTool extends VibeTool// implements PrimitiveHandler
{
    protected string $name = 'calculator';

    /**
     * Define the input schema for the calculator tool.
     * 
     * @return array The JSON Schema definition for the tool's input parameters
     */
    public static function input_schema(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'operation' => [
                    'type' => 'string',
                    'description' => 'Operation to perform (add, subtract, multiply, divide)',
                    'enum' => ['add', 'subtract', 'multiply', 'divide']
                ],
                'a' => [
                    'type' => 'number',
                    'description' => 'First operand'
                ],
                'b' => [
                    'type' => 'number',
                    'description' => 'Second operand'
                ]
            ],
            'required' => ['operation', 'a', 'b']
        ];
    }

    public static function getMetadata(): array
    {
        return [
            'name' => 'calculator',
            'description' => 'Performs basic arithmetic operations',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'operation' => [
                        'type' => 'string',
                        'description' => 'Operation to perform (add, subtract, multiply, divide)',
                        'enum' => ['add', 'subtract', 'multiply', 'divide']
                    ],
                    'a' => [
                        'type' => 'number',
                        'description' => 'First operand'
                    ],
                    'b' => [
                        'type' => 'number',
                        'description' => 'Second operand'
                    ]
                ],
                'required' => ['operation', 'a', 'b']
            ]
        ];
    }

    public function getName(): string
    {
        return 'calculator';
    }
}
