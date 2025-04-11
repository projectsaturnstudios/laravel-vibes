<?php

namespace ProjectSaturnStudios\Vibes\Services;

use Illuminate\Support\Facades\Log;
use JetBrains\PhpStorm\NoReturn;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Data\AgentMessage;
use Illuminate\Support\Facades\Event as SystemEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\MethodInvocationRequest;
use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\ToolInvocationRequest;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Contracts\SSEStreamService;
use ProjectSaturnStudios\Vibes\Contracts\SSEResponseTransporter;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

/**
 * Service responsible for managing the server-sent event (SSE) loop for agent communication.
 *
 * This class implements the SSEStreamService interface and handles the continuous loop
 * that listens for events and sends messages to the connected AI agent via the
 * configured SSEResponseTransporter.
 *
 * @package ProjectSaturnStudios\Vibes\Services
 * @since 0.4.0
 */
class VibeStreamLoopService implements SSEStreamService
{
    /**
     * Starts the SSE event loop for the given session.
     *
     * Initializes the transporter and begins the continuous event loop.
     *
     * @param VibeSesh $sesh The session to start the stream for.
     * @return void
     */
    public function start(VibeSesh $sesh) : void
    {
        $mini_van = config('vibes.sse.transport_provider');
        /** @var SSEResponseTransporter $transporter */
        $transporter = new $mini_van($sesh);
        $transporter->hot(fn() => static::event_loop($sesh->session_id, $transporter));
    }

    /**
     * Checks if the session has any pending events.
     *
     * @param VibeSesh|null $newly_loaded_sesh The session instance.
     * @return bool True if there are pending events, false otherwise.
     */
    protected static function session_has_messages(?VibeSesh $newly_loaded_sesh): bool
    {
        return !is_null($newly_loaded_sesh?->pending_event ?? null);
    }

    /**
     * Checks if the session has any pending method invocation requests.
     *
     * @param VibeSesh|null $newly_loaded_sesh The session instance to check.
     * @return bool True if there are pending method requests, false otherwise.
     */
    protected static function session_has_method_request(?VibeSesh $newly_loaded_sesh): bool
    {
        return !is_null($newly_loaded_sesh?->pending_method ?? null);
    }

    protected static function session_has_tool_request(?VibeSesh $newly_loaded_sesh): bool
    {
        return !is_null($newly_loaded_sesh?->pending_tool ?? null);
    }

    /**
     * The main event loop that continuously checks for messages and sends heartbeats.
     *
     * This method runs indefinitely until the connection is aborted or a
     * 'finished-with-agent' event is received.
     *
     * @param string $current_session_id The ID of the current session.
     * @param SSEResponseTransporter $transporter The transporter instance for sending messages.
     * @return void
     */
    #[NoReturn]
    protected static function event_loop(string $current_session_id, SSEResponseTransporter $transporter) : void
    {
        //vibe_hook(config('vibes.events.vibe-loop-created.name'), ['sesh' => $sesh, 'transport' => $transport]);
        SystemEvent::listen("send-message-to-agent-{$current_session_id}",
            fn(AgentMessage $message) => static::event_reaction($current_session_id, $transporter, $message)
        );

        $keep_going = true;
        $listening_for_organic_end = false;
        while($keep_going)
        {
            if(!$listening_for_organic_end)
            {
                SystemEvent::listen("finished-with-agent-{$current_session_id}", fn() => $keep_going = false);
                $listening_for_organic_end = true;
            }

            if(static::session_has_messages($updated_sesh = VibeSesh::load($current_session_id)))
            {
                if($sesh_event = $updated_sesh->getPendingEvent())
                {
                    $message = SessionEvent::convertToAgentMessage($sesh_event);
                    SystemEvent::dispatch("send-message-to-agent-{$current_session_id}", $message);
                    $updated_sesh->clearPendingEvent()->save();
                }
            }
            elseif(static::session_has_method_request($updated_sesh))
            {
                /** @var MethodInvocationRequest $sesh_method */
                if($sesh_method = $updated_sesh->getPendingMethod())
                {
                    /** @var AgentMethod $action */
                    $action = app($sesh_method->method);
                    $action->handle($updated_sesh->clearPendingMethod()->save(), $sesh_method->request_id, $sesh_method->request_body);
                }
            }
            elseif(static::session_has_tool_request($updated_sesh))
            {
                /** @var ToolInvocationRequest $sesh_tool */
                if($sesh_tool_request = $updated_sesh->getPendingTool())
                {
                    /** @var VibeTool $action */
                    $action = app($sesh_tool_request->tool_class);
                    $action->execute($updated_sesh->clearPendingTool()->save(), $sesh_tool_request->request_id, $sesh_tool_request->request_body);
                }
            }

            // @todo - check for method invocations

            $transporter->heartbeat();
            $keep_going = !connection_aborted();
        }
    }

    /**
     * Reacts to a 'send-message-to-agent' event by sending the message via the transporter.
     *
     * @param string $current_session_id The ID of the current session.
     * @param SSEResponseTransporter $transport The transporter instance.
     * @param AgentMessage $message The message to send.
     * @return void
     */
    protected static function event_reaction(string $current_session_id, SSEResponseTransporter $transport, AgentMessage $message) : void
    {
        if($message->sesh->session_id == $current_session_id)
        {
            Log::info("VibeStreamLoopService - event_reaction : sending event", [$message->payload]);
            $transport->send_message($message->payload, $message->event->value);
        }
    }
}
