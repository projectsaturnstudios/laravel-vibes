<?php

namespace ProjectSaturnStudios\Vibes\Services\ResponseBuilders;

use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;

class AgentInitializeResponse extends SSEResponseBuilder
{
    public string $protocol_version = '2024-11-05';
    public array $server_info = [];
    public bool $show_capabilities = false;
    public bool $reveal_all = false;
    protected bool $reveal_tools = false;
    protected bool $reveal_resources = false;
    protected bool $reveal_prompts = false;
    protected bool $reveal_logging = false;
    protected bool $reveal_experimental = false;
    protected bool $reveal_roots = false;
    protected bool $reveal_sampling = false;

    public function addProtocolVersion(string $protocol_version) : static
    {
        $this->protocol_version = $protocol_version;
        return $this;
    }

    public function addServerInfo() : static
    {
        $this->server_info = [
            'name' => config('vibes.service_info.server_name', 'Laravel Vibes'),
            'version' => config('vibes.service_info.server_version', '0.4.0'),
        ];
        return $this;
    }

    public function withCapabilities() : static
    {
        $this->show_capabilities = true;
        return $this;
    }

    public function revealEverything() : static
    {
        $this->reveal_all = true;
        return $this;
    }

    public function revealTools() : static
    {
        Log::info("Revealing tools!");
        $this->reveal_tools = config('vibes.features.tools', false);
        return $this;
    }

    public function revealResources() : static
    {
        $this->reveal_resources = config('vibes.features.resources', false);
        return $this;
    }

    public function revealPrompts() : static
    {
        $this->reveal_prompts = config('vibes.features.prompts', false);
        return $this;
    }

    public function supply() : array
    {
        $results = $this->toArray();

        $results['result'] = [
            'protocolVersion' => $this->protocol_version,
            'serverInfo' => $this->server_info,
        ];

        if($this->show_capabilities) $results['result']['capabilities'] = [];

        $this->reveal_sampling      = $this->reveal_all ? config('vibes.features.sampling', false) : $this->reveal_sampling;
        $this->reveal_tools         = $this->reveal_all ? config('vibes.features.tools', false) : $this->reveal_tools;
        $this->reveal_resources     = $this->reveal_all ? config('vibes.features.resources', false) : $this->reveal_resources;
        $this->reveal_prompts       = $this->reveal_all ? config('vibes.features.prompts', false) : $this->reveal_prompts;
        $this->reveal_logging       = $this->reveal_all ? config('vibes.features.logging', false) : $this->reveal_logging;
        $this->reveal_roots         = $this->reveal_all ? config('vibes.features.roots', false) : $this->reveal_roots;
        $this->reveal_experimental  = $this->reveal_all ? config('vibes.features.experimental', false) : $this->reveal_experimental;

        if($this->reveal_tools) $results['result']['capabilities']['tools'] = ['listChanged' => true];
        if($this->reveal_resources) $results['result']['capabilities']['resources'] = ['listChanged' => true];
        if($this->reveal_prompts) $results['result']['capabilities']['prompts'] = ['listChanged' => false];

        if($this->show_capabilities && (empty($results['result']['capabilities'])))
        {
            $results['result']['capabilities'] = new \stdClass();
        }

        return $results;
    }
}
