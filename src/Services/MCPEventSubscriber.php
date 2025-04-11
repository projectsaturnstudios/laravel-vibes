<?php

namespace ProjectSaturnStudios\Vibes\Services;

use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Enums\MCPSubscribedEvents;
use ProjectSaturnStudios\Vibes\VibeEvents\VibeEvent;
use ProjectSaturnStudios\Vibes\VibeEvents\VibeActivity;
use ProjectSaturnStudios\Vibes\Attributes\VibesWith;
use ProjectSaturnStudios\Vibes\Contracts\VibeToolRepository;

/**
 * Subscribes to VibeEvents and logs them.
 *
 * This class acts as a general subscriber for events dispatched within the
 * Laravel Vibes package. Currently, it primarily logs event occurrences.
 * Future implementations might involve injecting various repositories
 * (tools, resources, etc.) to perform actions based on events.
 *
 * @package ProjectSaturnStudios\Vibes\Services
 * @since 0.4.0
 */
class MCPEventSubscriber
{
    /**
     * @var VibeToolRepository Repository for vibe tools (currently unused).
     */
    private VibeToolRepository $tool_repo;
    //private ResourceRepository $resource_repo;
    //private PromptRepository $prompt_repo;
    //private SamplesRepository $samples_repo;
    //private RootsRepository $roots_repo;

    /**
     * Create a new MCPEventSubscriber instance.
     *
     * The constructor is designed to accept repository implementations via dependency injection,
     * although they are currently commented out and unused.
     *
     * @param string $vibeToolRepository Class name of the VibeToolRepository implementation.
     * // @param string $vibeResourceRepository Class name of the ResourceRepository implementation.
     * // @param string $vibePromptRepository Class name of the PromptRepository implementation.
     * // @param string $vibeSampleRepository Class name of the SampleRepository implementation.
     * // @param string $vibeRootRepository Class name of the RootRepository implementation.
     */
    public function __construct(
        string $vibeToolRepository,
        //string $vibeResourceRepository,
        //string $vibePromptRepository,
        //string $vibeSampleRepository,
        //string $vibeRootRepository,
    )
    {
        // Currently, repository assignment is commented out.
        // $this->tool_repo = app($vibeToolRepository);
        // $this->resource_repo = app($vibeResourceRepository);
        // $this->prompt_repo = app($vibePromptRepository);
        // $this->samples_repo = app($vibeSampleRepository);
        // $this->roots_repo = app($vibeRootRepository);
    }


    /**
     * Handle incoming VibeEvents.
     *
     * This method is automatically called by Laravel's event dispatcher when
     * a VibeEvent (or specifically a VibeActivity, due to the VibedWith attribute)
     * is dispatched. It currently logs the name of the event.
     *
     * @param VibeEvent $event The dispatched event instance.
     * @return void
     */
    #[VibesWith(VibeActivity::class)]
    public function handle(VibeEvent $event): void
    {
        $event_obj = MCPSubscribedEvents::from($event->event_name);
        $event_obj->process_event($event->payload);
    }
}
