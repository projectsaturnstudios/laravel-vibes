<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data\BuiltInTools;

use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\BuiltInTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\SampleTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

/**
 * Built-in tool that provides access to Laravel Artisan commands.
 *
 * This tool allows AI agents to execute Artisan commands within the application,
 * providing them with the ability to interact with Laravel's command-line interface.
 *
 * @package ProjectSaturnStudios\Vibes\Primitives\Tools\Data\BuiltInTools
 * @since 0.4.0
 */
#[MCPTool('artisan', "Send an Artisan command")]
class ArtisanCommand extends VibeTool implements BuiltInTool
{
    public function execute(VibeSesh $sesh, string $request_id, ?array $params) : void
    {

    }
    /**
     * Define the input schema for the Artisan command tool.
     *
     * The schema requires a 'command' parameter specifying which Artisan command to run,
     * and an optional 'addl_args' parameter for additional arguments to the command.
     *
     * @return array The JSON Schema definition for the tool's input parameters
     */
    public static function input_schema() : array
    {
        return [
            'type' => 'object',
            'properties' => [
                'command'   => [ 'type' => 'string'],
                'addl_args' => [ 'type' => 'string']
            ],
            'required' => ['command'],
        ];
    }
}
