<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentInitializeResponse;

#[MCPMethod('initialize')]
class SessionInitialized extends AgentMethod
{
    use AsAction;

    /**
     * @param VibeSesh $sesh
     * @param string|int|null $request_id
     * @param array $params
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(VibeSesh $sesh, string|int|null $request_id, array $params) : void
    {
        Log::info('SessionInitialized - '.$this->method_name());
        Log::info('SessionInitialized - ', [$params]);

        $response = (new AgentInitializeResponse)
            ->addId($request_id)
            ->addProtocolVersion($params['protocolVersion'])
            ->addServerInfo()
            ->withCapabilities();

        if(array_key_exists('clientInfo', $params) && ($params['clientInfo']['name'] == 'mcp-inspector'))
        {
            $response = $response->revealEverything();
        }
        elseif(array_key_exists('capabilities', $params) && (!empty($params['capabilities'])))
        {
            if(array_key_exists('tools', $params['capabilities']) && $params['capabilities']['tools']) $response = $response->revealTools();
            if(array_key_exists('prompts', $params['capabilities']) && $params['capabilities']['prompts']) $response = $response->revealPrompts();
            if(array_key_exists('resources', $params['capabilities']) && $params['capabilities']['resources']) $response = $response->revealResources();

        }

        send_good_vibes($sesh,$response);
    }


}
