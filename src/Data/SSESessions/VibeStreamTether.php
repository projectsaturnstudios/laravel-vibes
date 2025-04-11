<?php

namespace ProjectSaturnStudios\Vibes\Data\SSESessions;

use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\Data;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ProjectSaturnStudios\Vibes\Contracts\SSEStreamService;

/**
 * Manages the connection between a VibeSesh and an SSE stream.
 *
 * This data class acts as a bridge between a VibeSesh and a server-sent events (SSE)
 * stream. It provides the ability to start the streaming response with the appropriate
 * VibeSesh context, allowing for event-based communication with AI agents.
 *
 * @package ProjectSaturnStudios\Vibes\Data\SSESessions
 * @since 0.4.0
 */
class VibeStreamTether extends Data
{
    /**
     * Create a new VibeStreamTether instance.
     *
     * @param VibeSesh $sesh The session to tether to the SSE stream
     * 
     * @since 0.4.0
     */
    public function __construct(public readonly VibeSesh $sesh) {}

    /**
     * Create and return a streamed response for the tethered session.
     *
     * This method initializes an SSE stream through the application's SSEStreamService.
     * It creates a StreamedResponse that maintains an open connection to send events
     * to the client as they occur.
     *
     * @return StreamedResponse A HTTP response that streams SSE content
     * 
     * @since 0.4.0
     */
    public function then_respond() : StreamedResponse
    {
        /** @var SSEStreamService $loop */
        $loop = app('vibe-stream');
        return new StreamedResponse(function() use($loop) {
            $loop->start($this->sesh);
        }, 200, config('vibes.sse.headers'));
    }
}
