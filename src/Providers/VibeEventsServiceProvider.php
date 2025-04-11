<?php

namespace ProjectSaturnStudios\Vibes\Providers;

use ReflectionClass;
use ReflectionException;
use Illuminate\Support\ServiceProvider;
use ProjectSaturnStudios\Vibes\Attributes\VibesWith;
use Symfony\Component\EventDispatcher\EventDispatcher;
use ProjectSaturnStudios\Vibes\Services\MCPEventSubscriber;

/**
 * Service provider for registering event listeners marked with VibesWith attribute.
 *
 * This provider automatically discovers and registers event listeners in the
 * MCPEventSubscriber class that are marked with the VibesWith attribute. It uses
 * PHP 8's Attribute reflection to find these methods and register them with
 * Laravel's event system.
 *
 * @package ProjectSaturnStudios\Vibes\Providers
 * @since 0.4.0
 */
class VibeEventsServiceProvider extends ServiceProvider
{
    /**
     * Register event listeners found with the VibesWith attribute.
     *
     * This method scans the MCPEventSubscriber class for methods attributed
     * with VibesWith and registers them with Laravel's event system.
     *
     * @return void
     */
    public function register(): void
    {
        //Event::subscribe(MCPEventSubscriber::class);
        foreach ($this->resolveVibesWithListeners() as [$event, $listener]
        ) {
            app('events')->listen($event, $listener);
        }
    }

    /**
     * Discover and resolve listeners marked with the VibesWith attribute.
     *
     * Uses reflection to find methods in the MCPEventSubscriber class that have
     * the VibesWith attribute, and returns them as event-listener pairs.
     *
     * @return array Array of [eventClass, [listenerClass, methodName]] pairs.
     */
    private function resolveVibesWithListeners(): array
    {
        $reflectionClass = new ReflectionClass(MCPEventSubscriber::class);

        $listeners = [];

        foreach ($reflectionClass->getMethods() as $method) {
            $attributes = $method->getAttributes(VibesWith::class);

            foreach ($attributes as $attribute) {
                $listener = $attribute->newInstance();

                $listeners[] = [
                    // The event that's configured on the attribute
                    $listener->event,

                    // The listener for this event
                    [MCPEventSubscriber::class, $method->getName()],
                ];
            }
        }

        return $listeners;
    }
}
