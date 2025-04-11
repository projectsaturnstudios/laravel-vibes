<?php

namespace ProjectSaturnStudios\Vibes\Data\SSEMessageTransports;

use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Concerns\Transports\OutputBufferManipulation;

/**
 * Implements the SSEResponseTransporter interface for handling SSE communication.
 *
 * This class manages the server-sent event connection for a specific VibeSesh,
 * handling message formatting, heartbeats, and output buffer manipulation.
 *
 * @package ProjectSaturnStudios\Vibes\Data\SSEMessageTransports
 * @since 0.4.0
 */
class VibeTransporter extends SSETransporter
{
    use OutputBufferManipulation;

    /**
     * Timestamp of the last heartbeat sent.
     *
     * @var int
     */
    public int $lastHeartbeat;

    /**
     * Create a new VibeTransporter instance.
     *
     * @param VibeSesh $sesh The session associated with this transporter.
     */
    public function __construct(protected VibeSesh $sesh) {
        $this->lastHeartbeat = time();
    }

    /**
     * Initializes the SSE connection environment.
     *
     * Sets required headers, cleans output buffers, sets execution time limits,
     * and registers a shutdown function.
     *
     * @return void
     */
    protected function boot(): void
    {
        // Set appropriate headers for SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // Disable buffering for Nginx
        Log::info("VibeTransporter - starting");

        // Disable output buffering
        $this->clean_output_buffer();
        $this->override_execution_time();
        $this->boot_shutdown_function();
        flush();
    }

    /**
     * Boots the transporter and optionally runs a callback.
     *
     * @param callable|null $callback An optional callback to execute after booting.
     * @return void
     */
    public function hot(?callable $callback = null): void
    {
        $this->boot();

        // Run the callback if provided
        if ($callback) {
            $callback($this);
        }
    }

    /**
     * Sends a message payload through the SSE connection.
     *
     * @param string|array $payload The data to send.
     * @param string|null $event_to_send Optional event name.
     * @param string|null $id Optional event ID.
     * @return void
     */
    public function send_message(string|array $payload, ?string $event_to_send = null, ?string $id = null) : void
    {
        echo $this->prepare_output($payload, $event_to_send, $id);
        $this->flushings();
    }

    /**
     * Sends a heartbeat message if the configured interval has passed.
     *
     * @return void
     */
    public function heartbeat(): void
    {
        // Send heartbeat if interval has passed
        $now = time();

        // Send heartbeat if interval has passed
        $now = time();
        if ($now - $this->lastHeartbeat >= config('vibes.service_info.heartbeat_interval')) {
            // Send heartbeat
            Log::info("Heartbeat interval has expired. Bah-dump");

            $this->send_message([
                "jsonrpc" => "2.0",
                "id" => null,
                "result" => [
                    'timestamp' => $now,
                ]
            ], 'heartbeat');
            $this->lastHeartbeat = $now;
        }

        // Sleep for a short time to avoid CPU hogging
        usleep(100000); // 100ms
    }

    /**
     * Formats the payload into the SSE message format.
     *
     * @param string|array $payload The data to format.
     * @param string|null $event_to_send Optional event name.
     * @param string|null $id Optional event ID.
     * @return string The formatted SSE message string.
     */
    private function prepare_output(string|array $payload, ?string $event_to_send = null, ?string $id = null) : string
    {
        // Convert data to JSON if it's an array or object
        $data = is_string($payload) ? $payload : json_encode($payload);

        // Format the event
        $output = '';

        if ($id) {
            $output .= "id: {$id}\n";
        }

        if ($event_to_send) {
            $output .= "event: {$event_to_send}\n";
        }

        // Split data by newlines to ensure proper formatting
        foreach (explode("\n", $data) as $line) {
            $output .= "data: {$line}\n\n";
        }

        // Send the event
        return $output;
    }

    /**
     * Registers a shutdown function to send a 'close' event when the script ends.
     *
     * @return void
     */
    private function boot_shutdown_function() : void
    {
        register_shutdown_function(function () {
            $this->send_message([
                "jsonrpc" => "2.0",
                "id" => null,
                "method" => "message",
                "result" => [
                    'type' => 'close',
                    'id' => $this->sesh->session_id,
                    'timestamp' => time(),
                ]
            ],'close');
            exit();
        });
    }
}
