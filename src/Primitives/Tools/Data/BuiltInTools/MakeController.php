<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data\BuiltInTools;

use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\BuiltInTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\SampleTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

#[MCPTool('make_cotroller', "Generate an Laravel Controller")]
class MakeController extends VibeTool implements BuiltInTool
{
    public function execute(VibeSesh $sesh, string $request_id, ?array $params) : void
    {

    }

    public static function input_schema() : array
    {
        return [
            'type' => 'object',
            'properties' => [
                'namespace_path'   => [ 'type' => 'string'],
            ],
            'required' => ['namespace_path'],
        ];
    }
}
