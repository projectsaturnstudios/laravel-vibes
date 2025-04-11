<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods\Resources\Templates;

use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use Symfony\Component\VarDumper\VarDumper;

#[MCPMethod('resources/templates/list')]
class ListResourceTemplates extends AgentMethod
{
    use AsAction;

    public function handle() : void
    {
        VarDumper::dump($this->method_name());
    }


}
