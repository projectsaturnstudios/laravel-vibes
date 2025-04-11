<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data\SampleTools;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\SampleTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;

#[MCPTool('unsplash', "Get a Random Unsplash Img URL")]
class RandomUnsplashImg extends VibeTool implements SampleTool
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
        //$vibe = new AgentVibe("2.0", $this->getName(), $sesh->session_id, $request_id, $params);
        //$response = Http::get('https://plus.unsplash.com/premium_photo-1678914346628-8f38ba948356?q=80&w=3270&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
        //$message = base64_encode($response->body());
        $response = (new AgentSuccess)
            ->addId($request_id ?? 0)
            ->queueResult(['content' => [
                ['type' => 'text', 'text' => 'https://plus.unsplash.com/premium_photo-1678914346628-8f38ba948356?q=80&w=3270&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D']
            ]]);

        send_good_vibes($sesh,$response);

    }
    public static function input_schema() : array
    {
        return [
            'type' => 'object',
            'properties' => new \stdClass(),
            //'required' => ['message'],
        ];
    }
}
