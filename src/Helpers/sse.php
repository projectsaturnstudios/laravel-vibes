<?php

use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\VibeEvents\VibeActivity;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeStreamTether;
use ProjectSaturnStudios\Vibes\Actions\SSESessions\GetNewAgentSession;
use ProjectSaturnStudios\Vibes\Actions\SSESessions\PostIncomingRequest;

if (!function_exists('create_sse_stream')) {
    /**
     * Create a new server-sent event stream tether for a session.
     * 
     * This function initializes a new VibeStreamTether tied to the provided VibeSesh
     * instance. The tether manages the connection and event dispatching for Server-Sent
     * Events (SSE) to communicate with the AI agent.
     *
     * @param VibeSesh $sesh The session instance to create the stream for
     * 
     * @return VibeStreamTether A stream tether object that will manage the SSE connection
     * 
     * @since 0.4.0
     * 
     * @example
     * ```php
     * $session = vibe_sesh();
     * $tether = create_sse_stream($session);
     * return $tether->then_respond();
     * ```
     */
    function create_sse_stream(VibeSesh $sesh) : VibeStreamTether
    {
        return new VibeStreamTether($sesh);
    }
}

if (!function_exists('with_a_new_agent_session')) {
    /**
     * Create and initialize a new AI agent session.
     * 
     * This function creates a fresh VibeSesh instance using the GetNewAgentSession action.
     * Use this when starting a new interaction with an AI agent that requires session tracking.
     *
     * @return VibeSesh A newly initialized session for AI agent interaction
     * 
     * @since 0.4.0
     * 
     * @example
     * ```php
     * $session = with_a_new_agent_session();
     * // Use the session for agent communication
     * ```
     */
    function with_a_new_agent_session() : VibeSesh
    {
        return (new GetNewAgentSession)();
    }
}

if (!function_exists('vibe_sesh')) {
    /**
     * Create a new basic VibeSesh instance.
     * 
     * This function creates a new VibeSesh instance using the static make method.
     * Use this when you need a session container for tracking SSE communication.
     *
     * @return VibeSesh A new session instance
     * 
     * @since 0.4.0
     * 
     * @example
     * ```php
     * $session = vibe_sesh();
     * $session->save();
     * ```
     */
    function vibe_sesh() : VibeSesh
    {
        return VibeSesh::make();
    }
}

if (!function_exists('read_incoming_request')) {
    /**
     * Process an incoming AI agent request.
     * 
     * This function passes the provided AgentVibe instance to the PostIncomingRequest
     * action to handle an incoming request from an AI agent. It returns a status code
     * indicating the result of processing the request.
     *
     * @param AgentVibe $agentVibe The agent request data to process
     * 
     * @return int Status code indicating the result of processing (typically HTTP status code)
     * 
     * @since 0.4.0
     * 
     * @example
     * ```php
     * $vibe = new AgentVibe($request->all());
     * $statusCode = read_incoming_request($vibe);
     * ```
     */
    function read_incoming_request(AgentVibe $agentVibe) : int
    {
        return (new PostIncomingRequest)->handle($agentVibe);
    }
}

if (!function_exists('sesh_event')) {
    /**
     * Create a new session event for SSE communication.
     * 
     * This function creates a SessionEvent instance that can be sent over an SSE connection
     * to communicate with the AI agent. The event includes a session ID, event type from the
     * MCPResponseEvent enum, and a payload of data.
     *
     * @param string $session_id The session identifier for the event
     * @param MCPResponseEvent $occasion The type of event being created (from MCPResponseEvent enum)
     * @param string|array $payload The data payload to include with the event
     * 
     * @return SessionEvent A fully constructed session event ready to be dispatched
     * 
     * @since 0.4.0
     * 
     * @example
     * ```php
     * $event = sesh_event(
     *     $session->id,
     *     MCPResponseEvent::TOOL_RESULT,
     *     ['status' => 'success', 'data' => $result]
     * );
     * $session->addSessionEvent($event);
     * ```
     */
    function sesh_event(string $session_id, MCPResponseEvent $occasion, string|array $payload) : SessionEvent
    {
        return new SessionEvent($session_id, $occasion, $payload);
    }
}

if (!function_exists('vibe_activity'))
{
    /**
     * Dispatch a VibeActivity event for logging or monitoring.
     * 
     * This function creates and dispatches a VibeActivity event with the specified
     * name and optional payload. Use this for tracking AI agent activity within the
     * application for debugging, logging, or monitoring purposes.
     *
     * @param string $name The name/identifier of the activity
     * @param mixed $payload [optional] Additional data related to the activity
     * 
     * @return void
     * 
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If the event dispatcher is not bound
     * 
     * @since 0.4.0
     * 
     * @example
     * ```php
     * // Log when a specific tool is used
     * vibe_activity('tool.executed', [
     *     'tool' => 'file_search',
     *     'query' => $query,
     *     'timestamp' => now()
     * ]);
     * ```
     */
    function vibe_activity(string $name, mixed $payload = null) : void
    {
        app('events')->dispatch(VibeActivity::class, new VibeActivity($name, $payload));
    }
}
