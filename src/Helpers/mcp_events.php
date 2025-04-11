<?php

use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use ProjectSaturnStudios\Vibes\Enums\MCPErrorCode;
use ProjectSaturnStudios\Vibes\Enums\MCPResponseEvent;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentError;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;
use ProjectSaturnStudios\Vibes\Actions\AgentRequests\ProcessAgentRequest;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentAcknowledge;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentInitializeResponse;

if (!function_exists('read_the_agent_vibe')) {
    /**
     * Process an incoming Agent Vibe within the Machine Control Protocol (MCP) workflow.
     *
     * This function serves as the primary entry point for handling AI agent requests,
     * delegating the processing to a dedicated action class. It acts as a central
     * dispatcher for incoming agent interactions, ensuring consistent and standardized
     * request handling across the Laravel Vibes package.
     *
     * The function leverages the ProcessAgentRequest action to:
     * - Validate the incoming agent vibe
     * - Load the associated SSE session
     * - Route the request to appropriate handlers
     * - Manage the lifecycle of the agent interaction
     *
     * @param AgentVibe $vibe The incoming agent vibe containing the request details
     *
     * @throws \DomainException If the session cannot be found or is invalid
     * @throws \InvalidArgumentException If the vibe payload is malformed
     * @throws \RuntimeException If processing fails unexpectedly
     *
     * @return void
     *
     * @since 0.4.0
     *
     * @example
     * ```php
     * // Typical usage in an MCP-enabled Laravel application
     * public function handleAgentInteraction(Request $request) {
     *     try {
     *         // Convert incoming request to an AgentVibe
     *         $vibe = AgentVibe::fromRequest($request);
     *
     *         // Process the agent vibe
     *         read_the_agent_vibe($vibe);
     *
     *         // Successful processing will trigger appropriate MCP events
     *         return response()->json(['status' => 'processed']);
     *     } catch (\Exception $e) {
     *         // Handle any processing errors
     *         Log::error("Agent vibe processing failed: {$e->getMessage()}");
     *         return response()->json(['error' => 'Processing failed'], 500);
     *     }
     * }
     * ```
     *
     * @see ProcessAgentRequest
     * @see AgentVibe
     * @see VibeSesh
     */
    function read_the_agent_vibe(AgentVibe $vibe) : void
    {
        (new ProcessAgentRequest)($vibe);
    }
}

if (!function_exists('send_good_vibes')) {
    /**
     * Dispatch a success event in the Machine Control Protocol (MCP) workflow.
     *
     * This function sends a success message within an active SSE (Server-Sent Events) session,
     * indicating that a requested task or operation has been completed successfully.
     *
     * @param VibeSesh $sesh The active SSE session associated with the AI agent interaction
     * @param AgentSuccess $builder A builder object containing the success message details
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If the event dispatcher is not bound
     * @throws \DomainException If the session cannot be managed
     *
     * @return void
     *
     * @since 0.4.0
     *
     * @example
     * ```php
     * // In a tool or service method processing an AI agent request
     * public function processDataSanitization(AgentVibe $vibe) {
     *     try {
     *         $cleanedData = $this->sanitizeData($vibe->payload);
     *         $sesh = VibeSesh::load($vibe->session_id);
     *
     *         // Create a success builder with relevant details
     *         $successBuilder = (new AgentSuccess())
     *             ->withMessage('Data sanitization completed successfully')
     *             ->withPayload($cleanedData);
     *
     *         // Send success event back to the AI agent
     *         send_good_vibes($sesh, $successBuilder);
     *     } catch (\Exception $e) {
     *         // Handle errors using ruin_the_vibe() if needed
     *         ruin_the_vibe($sesh, $vibe, MCPErrorCode::PROCESSING_ERROR, $e->getMessage());
     *     }
     * }
     * ```
     *
     * @see VibeSesh
     * @see AgentSuccess
     * @see SessionEvent
     */
    function send_good_vibes(VibeSesh $sesh, AgentSuccess|AgentInitializeResponse $builder) : void
    {
        $msg = $builder->supply();
        $event = new SessionEvent($sesh->session_id, MCPResponseEvent::MESSAGE, $msg);
        $sesh->addSessionEvent($event)->save();
    }
}

if (!function_exists('mkay')) {
    /**
     * Send an acknowledgment event in the Machine Control Protocol (MCP) workflow.
     *
     * This function sends an acknowledgment message within an active SSE (Server-Sent Events) session,
     * indicating that an AI agent's request or vibe has been received and is being processed.
     *
     * The "mkay" (casual for "okay") function provides a lightweight way to confirm
     * receipt of an agent's message without completing the full task.
     *
     * @param VibeSesh $sesh The active SSE session associated with the AI agent interaction
     * @param AgentVibe $vibe The original agent vibe being acknowledged
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If the event dispatcher is not bound
     * @throws \DomainException If the session cannot be managed or updated
     *
     * @return void
     *
     * @since 0.4.0
     *
     * @example
     * ```php
     * // In a tool or service method receiving an AI agent request
     * public function handleAgentRequest(AgentVibe $vibe) {
     *     try {
     *         // Load the active session
     *         $sesh = VibeSesh::load($vibe->session_id);
     *
     *         // Immediately acknowledge the request
     *         mkay($sesh, $vibe);
     *
     *         // Queue the request for async processing
     *         $this->processAgentRequestAsync($vibe);
     *     } catch (\Exception $e) {
     *         // Handle any session or processing errors
     *         ruin_the_vibe(
     *             $sesh,
     *             $vibe,
     *             MCPErrorCode::PROCESSING_ERROR,
     *             "Failed to process agent request"
     *         );
     *     }
     * }
     * ```
     *
     * @see VibeSesh
     * @see AgentVibe
     * @see AgentAcknowledge
     * @see SessionEvent
     */
    function mkay(VibeSesh $sesh, AgentVibe $vibe) : void
    {
        $msg = (new AgentAcknowledge)->addId($vibe->id)->supply();
        $event = new SessionEvent($sesh->session_id, MCPResponseEvent::MESSAGE, $msg);
        $sesh->addSessionEvent($event)->save();
    }
}

if (!function_exists('ruin_the_vibe')) {
    /**
     * Dispatch an error event in the Machine Control Protocol (MCP) workflow.
     *
     * This function sends an error message within an active SSE (Server-Sent Events) session,
     * indicating that an exception or error occurred during AI agent interaction.
     * It allows for detailed error reporting with specific error codes and messages.
     *
     * @param VibeSesh $sesh The active SSE session associated with the AI agent interaction
     * @param AgentVibe $vibe The original agent vibe that triggered the error
     * @param MCPErrorCode $code Standardized error code representing the type of error
     * @param string $message Detailed error message describing the specific failure
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException If the event dispatcher is not bound
     * @throws \DomainException If the session cannot be managed or updated
     *
     * @return void
     *
     * @since 0.4.0
     *
     * @example
     * ```php
     * // In a tool or service method processing an AI agent request
     * public function processDataSanitization(AgentVibe $vibe) {
     *     try {
     *         $sesh = VibeSesh::load($vibe->session_id);
     *
     *         // Simulate a data validation failure
     *         if (!$this->isValidPayload($vibe->payload)) {
     *             throw new \InvalidArgumentException('Invalid data payload');
     *         }
     *
     *         // Successful processing...
     *     } catch (\InvalidArgumentException $e) {
     *         // Report a specific validation error
     *         ruin_the_vibe(
     *             $sesh,
     *             $vibe,
     *             MCPErrorCode::VALIDATION_ERROR,
     *             "Data validation failed: {$e->getMessage()}"
     *         );
     *     } catch (\Exception $e) {
     *         // Fallback to a generic processing error
     *         ruin_the_vibe(
     *             $sesh,
     *             $vibe,
     *             MCPErrorCode::PROCESSING_ERROR,
     *             "Unexpected error during data processing"
     *         );
     *     }
     * }
     * ```
     *
     * @see VibeSesh
     * @see AgentVibe
     * @see MCPErrorCode
     * @see SessionEvent
     */
    function ruin_the_vibe(VibeSesh $sesh, AgentVibe $vibe, MCPErrorCode $code, string $message) : void
    {
        $msg = (new AgentError)->addId($vibe->id)->supply($code, $message);
        $event = new SessionEvent($sesh->session_id, MCPResponseEvent::ERROR, $msg);
        $sesh->addSessionEvent($event)->save();

    }
}
