<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;
use Symfony\Component\VarDumper\VarDumper;

#[MCPMethod('ping')]
class Ping extends AgentMethod
{
    use AsAction;

    /**
     * @param VibeSesh $sesh
     * @param string|int|null $request_id
     * @param array $params
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(VibeSesh $sesh, string|int|null $request_id, ?array $params = null) : void
    {
        Log::info('Ping! - '.$this->method_name());
        Log::info($params);

        $response = (new AgentSuccess)
            ->addId($request_id ?? 0)
            ->sendBackNothing();

        send_good_vibes($sesh,$response);
    }
}
