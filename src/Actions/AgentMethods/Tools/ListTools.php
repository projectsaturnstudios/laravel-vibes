<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods\Tools;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;

#[MCPMethod('tools/list')]
class ListTools extends AgentMethod
{
    use AsAction;

    /**
     * @param VibeSesh $sesh
     * @param string|int|null $request_id
     * @param array $params
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(VibeSesh $sesh, string|int|null $request_id, ?array $params) : void
    {
        Log::info('ListTools - '.$this->method_name());
        Log::info($params);

        $tools = mcp_tools();
        $defs = [];
        foreach ($tools as $tool)
        {
            $defs[] = $tool::getMetadata();
        }

        $response = (new AgentSuccess)
            ->addId($request_id ?? 0)
            ->queueResult(['tools' => $defs]);

        send_good_vibes($sesh,$response);
    }


}
