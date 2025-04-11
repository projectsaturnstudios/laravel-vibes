<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods\Notifications;

use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use Symfony\Component\VarDumper\VarDumper;

#[MCPMethod('notifications/initialization')]
class InitializationConfirmed extends AgentMethod
{
    use AsAction;

    public function handle() : void
    {
        VarDumper::dump($this->method_name());
    }

}
