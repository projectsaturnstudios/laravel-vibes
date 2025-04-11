<?php

namespace ProjectSaturnStudios\Vibes;

use ProjectSaturnStudios\Vibes\Concerns\Vibes\HasTools;
use ProjectSaturnStudios\Vibes\Concerns\Vibes\HasRoots;
use ProjectSaturnStudios\Vibes\Concerns\Vibes\HasPrompts;
use ProjectSaturnStudios\Vibes\Concerns\Vibes\HasSamples;
use ProjectSaturnStudios\Vibes\Concerns\Vibes\HasResources;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidPrimitiveHandler;

/**
 * TheAgency is the core class of the Vibes package that manages various primitive handlers.
 *
 * This class orchestrates the integration of tools, resources, prompts, samples, and roots
 * within the application. It provides methods to add, remove, and access these primitives.
 */
class TheAgency
{
    use HasTools, HasResources, HasPrompts, HasSamples, HasRoots;

    /**
     * Determines whether exceptions should be caught during primitive handler operations.
     *
     * @var bool
     */
    protected bool $catchExceptions;

    /**
     * Create a new instance of TheAgency.
     *
     * @param array $config Configuration array with service options
     */
    public function __construct(array $config)
    {
        $this->tools = $this->init_tools();
        $this->resources = $this->init_resources();
        $this->prompts = $this->init_prompts();
        $this->samples = $this->init_samples();
        $this->roots = $this->init_roots();

        $this->catchExceptions = $config['service_info']['catch_exceptions'] ?? false;
    }

    /**
     * Add a primitive handler to the appropriate collection.
     *
     * @param string|object $primitiveHandlerClass The class name or instance of the primitive handler
     * @return void
     * @throws InvalidPrimitiveHandler When the provided class is not a valid primitive handler
     */
    public function addPrimitiveHandler($primitiveHandlerClass) : void
    {
        if (! is_string($primitiveHandlerClass)) {
            $primitiveHandlerClass = get_class($primitiveHandlerClass);
        }

        if (is_subclass_of($primitiveHandlerClass, VibeTool::class)) {
            $this->addTool($primitiveHandlerClass);

            return;
        }

        /*if (is_subclass_of($eventHandlerClass, EventHandler::class)) {
            $this->addReactor($eventHandlerClass);

            return;
        }*/

        throw InvalidPrimitiveHandler::notAnPrimitiveHandlingClassName($primitiveHandlerClass);
    }

    /**
     * Remove a primitive handler from the appropriate collection.
     *
     * @param string $primitiveHandlerClass The class name of the primitive handler to remove
     * @return self
     * @throws InvalidPrimitiveHandler When the provided class is not a valid primitive handler
     */
    public function removePrimitiveHandler(string $primitiveHandlerClass): self
    {
        if (is_subclass_of($primitiveHandlerClass, VibeTool::class)) {
            $this->removeTool($primitiveHandlerClass);

            return $this;
        }

        /*if (is_subclass_of($primitiveHandlerClass, EventHandler::class)) {
            $this->removeReactor($primitiveHandlerClass);

            return $this;
        }*/

        throw InvalidPrimitiveHandler::notAnPrimitiveHandlingClassName($primitiveHandlerClass);
    }

    /**
     * Add multiple primitive handlers at once.
     *
     * @param array $primitiveHandlers Array of primitive handler class names or instances
     * @return self
     */
    public function addPrimitiveHandlers(array $primitiveHandlers): self
    {
        foreach ($primitiveHandlers as $primitiveHandler) {
            $this->addPrimitiveHandler($primitiveHandler);
        }

        return $this;
    }

    /**
     * Create a new instance of TheAgency with default configuration.
     *
     * @return static A new instance of TheAgency
     */
    public static function make() : static
    {
        $vibeConfig = config('vibes');
        $vibes = new static($vibeConfig);

        $vibes
            ->addTools($vibeConfig['tools'] ?? [])
            //->addResources()
            //->addPrompts()
            //->addSamples()
            //->addRoots()
        ;

        return $vibes;
    }
}
