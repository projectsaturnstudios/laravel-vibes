<?php

namespace ProjectSaturnStudios\Vibes\Concerns\VibeSesh;

use ProjectSaturnStudios\Vibes\Data\SSESessions\SessionEvent;

trait SessionEvents
{
    /**
     * The pending event to be sent in this session.
     *
     * @var SessionEvent|null
     */
    public ?SessionEvent $pending_event = null;

    /**
     * Add a pending event to the session.
     *
     * Stores a SessionEvent that will be processed during the next
     * server-sent events loop iteration.
     *
     * @param SessionEvent $pending_event The event to add to the session
     *
     * @return static The current instance for method chaining
     *
     * @since 0.4.0
     */
    public function addSessionEvent(SessionEvent $pending_event) : static
    {
        $this->pending_event = $pending_event;
        return $this;
    }

    /**
     * Get the current pending event.
     *
     * @return SessionEvent|null The current pending event or null if none exists
     *
     * @since 0.4.0
     */
    public function getPendingEvent() : ?SessionEvent { return $this->pending_event; }

    /**
     * Clear the pending event.
     *
     * Removes the current pending event from the session after it has been
     * processed or if it needs to be discarded.
     *
     * @return static The current instance for method chaining
     *
     * @since 0.4.0
     */
    public function clearPendingEvent() : static
    {
        $this->pending_event = null;
        return $this;
    }
}
