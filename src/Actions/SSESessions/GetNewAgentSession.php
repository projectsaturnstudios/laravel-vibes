<?php

namespace ProjectSaturnStudios\Vibes\Actions\SSESessions;

use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;

/**
 * Creates a new agent session for MCP communication.
 *
 * This action class is responsible for initializing a new session for an AI agent,
 * creating the necessary session record, and setting up the initial session event
 * to provide the message endpoint URI to the agent.
 *
 * @package ProjectSaturnStudios\Vibes\Actions\SSESessions
 * @since 0.4.0
 */
class GetNewAgentSession
{
    use AsAction;

    /**
     * Invocable handler to create a new agent session.
     *
     * This method allows the action to be used as a callable,
     * delegating to the handle method.
     *
     * @return VibeSesh The newly created session instance
     */
    public function __invoke() : VibeSesh
    {
        return $this->handle();
    }

    /**
     * @return VibeSesh
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle() : VibeSesh
    {
        $sesh = vibe_sesh();
        vibe_activity('session-started', ['message' => "'Session {$sesh->session_id} Started With Agent'", 'sesh' => $sesh, 'request' => request()]);
        $sesh = $sesh
            ->addSessionEvent(
                pending_event: sesh_event(
                    session_id: $sesh->session_id,
                    occasion: MCPResponseEvent::ENDPOINT,
                    payload:config('vibes.routes.messages.uri')."?session_id={$sesh->session_id}")
            );

        if(config('vibes.service_info.requires_authentication'))
        {
            $user = auth(config('vibes.service_info.auth_guard'))->user();
            $sesh = $sesh->setUser($user);
        }

        return $sesh->save();
    }
}
