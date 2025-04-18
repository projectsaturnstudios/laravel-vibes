<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods\Resources;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;
use Symfony\Component\VarDumper\VarDumper;

#[MCPMethod('resources/list')]
class ListResources extends AgentMethod
{
    use AsAction;

    public function handle(VibeSesh $sesh, string|int|null $request_id, ?array $params) : void
    {
        Log::info('ListResources - '.$this->method_name());
        Log::info($params);

        $tools = mcp_tools();
        $defs = [];
        foreach ($tools as $tool)
        {
            $defs[] = $tool::getMetadata();
        }

        $response = (new AgentSuccess)
            ->addId($request_id ?? 0)
            ->queueResult(['resources' => []]);

        send_good_vibes($sesh,$response);
    }


}
