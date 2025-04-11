<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data\BuiltInTools;

use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\BuiltInTool;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;

#[MCPTool('artisan_list', "List Artisan commands")]
class ArtisanList extends VibeTool implements BuiltInTool
{
    public function execute(VibeSesh $sesh, string $request_id, ?array $params) : void
    {
        $op = shell_exec("/opt/homebrew/opt/php@8.2/bin/php /Users/angelgonzalez/Herd/sanitizer.rdmintegrations/artisan 2>&1");
        $response = (new AgentSuccess)
            ->addId($request_id ?? 0)
            ->queueResult([
                'content' => [
                    ['type' => 'text', 'text' => $op]
                ]
            ]);

        send_good_vibes($sesh,$response);
    }

    public static function input_schema() : array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass()
        ];
    }
}
