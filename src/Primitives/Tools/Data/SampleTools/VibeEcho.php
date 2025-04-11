<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data\SampleTools;

use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\SampleTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;

#[MCPTool('echo', "Echoes back the message you send it")]
class VibeEcho extends VibeTool implements SampleTool
{
    /**
     * @param VibeSesh $sesh
     * @param string $request_id
     * @param array|null $params
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function execute(VibeSesh $sesh, string $request_id, ?array $params) : void
    {
        $vibe = new AgentVibe("2.0", $this->getName(), $sesh->session_id, $request_id, $params);
        if($params)
        {
            if(array_key_exists('arguments', $params))
            {
                if(array_key_exists('message', $params['arguments'])) {
                    $message = $params['arguments']['message'];
                    $response = (new AgentSuccess)
                        ->addId($request_id ?? 0)
                        ->queueResult(['content' => [['type' => 'text', 'text' => $message]]]);

                    send_good_vibes($sesh,$response);
                }
                else { ruin_the_vibe($sesh,$vibe, MCPErrorCode::ERROR_INTERNAL, "{$this->getName()} - Arguments Missing"); }
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
                'message' => [ 'type' => 'string']
            ],
            'required' => ['message'],
        ];
    }
}
