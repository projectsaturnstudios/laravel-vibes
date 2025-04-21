<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data\BuiltInTools;

use Illuminate\Support\Facades\Artisan;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\BuiltInTool;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;

#[MCPTool('make_mcp_tool', "Generate an MCP Tool")]
class MakeMCPTool extends VibeTool implements BuiltInTool
{
    public function execute(VibeSesh $sesh, string $request_id, ?array $params) : void
    {
        $vibe = new AgentVibe("2.0", $this->getName(), $sesh->session_id, $request_id, $params);
        if($params)
        {
            if(array_key_exists('arguments', $params))
            {
                if(array_key_exists('namespace_path', $params['arguments'])) {
                    $name = $params['arguments']['namespace_path'];

                    $exit_code = Artisan::call("make:tool $name");
                    if($exit_code !== 0) {
                        $error_message = Artisan::output();
                        $response = (new AgentSuccess)
                            ->addId($request_id ?? 0)
                            ->queueResult(['content' => [['type' => 'text', 'text' => "Error: $error_message"]]]);

                        send_good_vibes($sesh,$response);
                        return;
                    }

                    $response = (new AgentSuccess)
                        ->addId($request_id ?? 0)
                        ->queueResult(['content' => [['type' => 'text', 'text' => 'Tool created successfully!']]]);

                    send_good_vibes($sesh,$response);
                }
                else { ruin_the_vibe($sesh,$vibe, MCPErrorCode::ERROR_INTERNAL, "{$this->getName()} - Namespace Path Missing"); }
            }
            else { ruin_the_vibe($sesh,$vibe, MCPErrorCode::ERROR_INTERNAL, "{$this->getName()} - Arguments Missing"); }
        }
        else { ruin_the_vibe($sesh,$vibe, MCPErrorCode::ERROR_INVALID_PARAMS,"{$this->getName()} - Invalid parameters"); }
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
