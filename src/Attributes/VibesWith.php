<?php

namespace ProjectSaturnStudios\Vibes\Attributes;

use ProjectSaturnStudios\Vibes\VibeEvents\VibeEvent;

/**
 * Attribute for connecting event handlers with specific vibe event types.
 *
 * This attribute is used to declaratively specify which event types a handler method
 * should respond to. Methods marked with this attribute will be automatically registered
 * as handlers for the specified event class.
 *
 * Example usage:
 * ```php
 * #[VibesWith(UserCreatedEvent::class)]
 * public function handleUserCreated(VibeEvent $event): void
 * {
 *     // Handle user created event
 * }
 * ```
 *
 * @package ProjectSaturnStudios\Vibes\Attributes
 * @since 0.4.0
 */
#[\Attribute]
readonly class VibesWith
{
    /**
     * @var string The fully qualified class name of the event to handle.
     */
    public string $event;

    /**
     * Create a new VibesWith attribute instance.
     *
     * @param string $event The fully qualified class name of the event (must extend VibeEvent).
     */
    public function __construct(string $event)
    {
        $this->event = $event;
    }
}
