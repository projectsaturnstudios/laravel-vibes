<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods\Tools;

use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Contracts\VibeToolRepository;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Data\SSESessions\ToolInvocationRequest;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\SampleTools\VibeEcho;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Repositories\VibeToolRepo;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentError;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Agent method handler for invoking tools.
 *
 * This class handles the 'tools/call' MCP method which allows AI agents to
 * invoke registered tools within the application. The method processes the
 * tool invocation request and returns the result.
 *
 * @package ProjectSaturnStudios\Vibes\Actions\AgentMethods\Tools
 * @since 0.4.0
 */
#[MCPMethod('tools/call')]
class InvokeTool extends AgentMethod
{
    use AsAction;

    /**
     * Handle a tool invocation request from an agent.
     *
     * Processes the request to call a specific tool with the provided parameters.
     * Currently only dumps the method name for debugging purposes.
     *
     * @param VibeSesh $sesh The session context for the agent interaction
     * @param string|int|null $request_id The unique identifier for this request
     * @param array $params Tool invocation parameters including tool name and arguments
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(VibeSesh $sesh, string|int|null $request_id, array $params) : void
    {
        Log::info('InvokeTool - '.$params['name']);
        Log::info($params);

        /** @var VibeToolRepository $repo */
        if($tool = mcp_tool($params['name']))
        {
            $sesh->addToolInvocation(new ToolInvocationRequest(
                $sesh->session_id,
                $request_id,
                $tool::class,
                $params
            ))->save();
        }
        else
        {
            $vibe = new AgentVibe("2.0", $this->method_name(), $sesh->session_id, $request_id, $params);
            ruin_the_vibe($sesh,$vibe, MCPErrorCode::ERROR_TOOL_NOT_FOUND, "Tool not found: {$params['name']}");
        }
    }
}
