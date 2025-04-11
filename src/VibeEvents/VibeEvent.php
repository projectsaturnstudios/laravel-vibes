<?php

namespace ProjectSaturnStudios\Vibes\VibeEvents;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;

/**
 * Abstract base class for all vibe events in the system.
 *
 * This class serves as the foundation for the event system within the Laravel-Vibes
 * package. It provides common properties and behavior for all event types using the
 * Laravel event system traits. Events extending this class can be dispatched through
 * the application's event system to trigger various actions or notifications.
 *
 * @package ProjectSaturnStudios\Vibes\VibeEvents
 * @since 0.4.0
 */
abstract class VibeEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param string $event_name The name identifier for this event (defaults to 'VibeEvent')
     * @param mixed $payload [optional] Any data associated with this event
     *
     * @since 0.4.0
     */
    public function __construct(
        public readonly string $event_name = 'VibeEvent',
        public readonly ?array $payload = null,
    )
    {
        //
    }
}
