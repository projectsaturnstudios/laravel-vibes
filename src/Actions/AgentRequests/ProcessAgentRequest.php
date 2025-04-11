<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentRequests;

use AWS\CRT\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Data\SSESessions\MethodInvocationRequest;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;

/**
 * Action class for processing incoming agent requests.
 *
 * This class processes incoming MCP protocol requests from AI agents,
 * validates the session, and routes the request to the appropriate
 * method handler based on configuration. If the method exists, it creates
 * a method invocation request; otherwise, it returns an error.
 *
 * @package ProjectSaturnStudios\Vibes\Actions\AgentRequests
 * @since 0.4.0
 */
class ProcessAgentRequest
{
    use AsAction;

    /**
     * @param AgentVibe $arguments
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function __invoke(AgentVibe $arguments) : void
    {
        $this->handle($arguments);
    }

    /**
     * @param AgentVibe $vibe
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(AgentVibe $vibe) : void
    {
        if(is_null($sesh = VibeSesh::load($vibe->session_id))) throw new \DomainException("Session not found");
        if(array_key_exists($vibe->method, config('vibes.invocable_methods')))
        {
            $sesh->addMethodInvocation(new MethodInvocationRequest(
                $vibe->session_id,
                $vibe->id,
                $vibe->method,
                $vibe->params
            ))->save();
        }
        else
        {
            ruin_the_vibe($sesh, $vibe, MCPErrorCode::ERROR_METHOD_NOT_FOUND, "Method '{$vibe->method}' not found");
        }
    }
}
