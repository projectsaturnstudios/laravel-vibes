<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods\Notifications;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;
use Symfony\Component\VarDumper\VarDumper;

#[MCPMethod('notifications/initialized')]
class InitializationConfirmed extends AgentMethod
{
    use AsAction;

    public function handle(VibeSesh $sesh, string|int|null $request_id, ?array $params = null) : void
    {
        Log::info('InitializationConfirmed! - '.$this->method_name());
        Log::info($params);

        $response = (new AgentSuccess)
            ->addId($request_id ?? 0)
            ->sendBackNothing();

        send_good_vibes($sesh,$response);
    }

}
