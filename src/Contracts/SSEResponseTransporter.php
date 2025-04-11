<?php

namespace ProjectSaturnStudios\Vibes\Contracts;

/**
 * Defines the contract for classes responsible for transporting SSE responses.
 *
 * Implementations of this interface manage the connection and formatting
 * of server-sent events.
 *
 * @package ProjectSaturnStudios\Vibes\Contracts
 * @since 0.4.0
 */
interface SSEResponseTransporter
{
    /**
     * Initializes the transporter and makes it ready for sending messages.
     *
     * Optionally runs a callback after initialization.
     *
     * @param callable|null $callback An optional callback to execute after booting.
     * @return void
     */
    public function hot(?callable $callback = null): void;

    /**
     * Sends a message payload through the SSE connection.
     *
     * @param string|array $payload The data to send.
     * @param string|null $event_to_send Optional event name.
     * @param string|null $id Optional event ID.
     * @return void
     */
    public function send_message(string|array $payload, ?string $event_to_send = null, ?string $id = null);

    /**
     * Sends a heartbeat message to keep the connection alive.
     *
     * Implementations should handle the timing and format of the heartbeat.
     *
     * @return void
     */
    public function heartbeat(): void;
}
