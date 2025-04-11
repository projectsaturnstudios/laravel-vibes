<?php

namespace ProjectSaturnStudios\Vibes\Http\Controllers;

use Illuminate\Http\Response;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsController;
use ProjectSaturnStudios\Vibes\Data\AgentVibe;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Controller responsible for handling incoming agent requests via the MCP protocol.
 *
 * This controller manages the entry points for establishing SSE connections
 * and processing messages from AI agents.
 *
 * @package ProjectSaturnStudios\Vibes\Http\Controllers
 * @since 0.4.0
 */
class MCPAgentEntryController
{
    use AsController;

    /**
     * Establishes a new Server-Sent Events (SSE) channel for an agent session.
     *
     * Creates a new agent session and returns a StreamedResponse to keep the
     * connection open for SSE communication.
     *
     * @return StreamedResponse The SSE stream response.
     */
    public function open_a_channel() : StreamedResponse
    {
        return create_sse_stream(with_a_new_agent_session())->then_respond();
    }

    /**
     * Define validation rules for incoming JSON-RPC agent messages.
     *
     * Ensures that incoming requests adhere to the expected MCP message format.
     *
     * @return array Validation rules for the request.
     */
    public function rules() : array
    {
        return [
            'jsonrpc'   => 'required|string|in:2.0',
            'method'    => 'required|string',
            'session_id' => 'required|string',
            'id'        => 'sometimes|nullable',
            'params'    => 'sometimes|array',
        ];
    }

    /**
     * Handles incoming agent messages.
     *
     * Validates the incoming JSON-RPC request, converts it into an AgentVibe
     * data object, and passes it to the appropriate handler via the
     * `read_incoming_request` helper. Returns a 204 No Content response
     * as the actual response is handled asynchronously via SSE.
     *
     * @param ActionRequest $request The validated Laravel request.
     * @return Response HTTP response (typically 204 No Content).
     */
    public function asController(ActionRequest $request) : Response
    {
        return response()->noContent(
            read_incoming_request(new AgentVibe(...$request->validated()))
        );
    }

}
