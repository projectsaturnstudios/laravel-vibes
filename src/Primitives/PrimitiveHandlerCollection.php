<?php

namespace ProjectSaturnStudios\Vibes\Primitives;

use Illuminate\Support\Collection;
use ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler;

/**
 * Collection class specifically designed to store and manage primitive handlers.
 *
 * This collection extends Laravel's Collection class and provides methods
 * for adding and removing primitive handlers. It indexes the handlers by their
 * name, making them easily retrievable. This is the core storage mechanism
 * used by all primitive handler traits in TheAgency.
 *
 * @template TKey of string
 * @template TValue of PrimitiveHandler
 * @extends Collection<TKey, TValue>
 *
 * @package ProjectSaturnStudios\Vibes\Primitives
 * @since 0.4.0
 */
class PrimitiveHandlerCollection extends Collection
{
    /**
     * Create a new PrimitiveHandlerCollection instance.
     *
     * Initializes an empty collection and then adds any provided primitive handlers.
     * Unlike the parent Collection constructor, this ensures all handlers are
     * properly indexed by their name.
     *
     * @param array<PrimitiveHandler> $primitiveHandlers Initial array of primitive handlers.
     */
    public function __construct($primitiveHandlers = [])
    {
        parent::__construct([]);

        foreach ($primitiveHandlers as $primitiveHandler) {
            $this->addPrimitiveHandler($primitiveHandler);
        }
    }

    /**
     * Add a primitive handler to the collection.
     *
     * Stores the handler in the collection using the handler's name as the key,
     * allowing for easy retrieval by name and preventing duplicates with the same name.
     *
     * @param PrimitiveHandler $primitiveHandler The primitive handler to add.
     * @return void
     */
    public function addPrimitiveHandler(PrimitiveHandler|string $primitiveHandler): void
    {
        if(is_string($primitiveHandler)) {
            $primitiveHandler = app($primitiveHandler);
        }
        $this->items[$primitiveHandler->getName()] = get_class($primitiveHandler);

    }

    /**
     * Remove a primitive handler from the collection.
     *
     * Removes the handler from the collection based on its name.
     *
     * @param PrimitiveHandler $primitiveHandler The primitive handler to remove.
     * @return void
     */
    public function removePrimitiveHandler(PrimitiveHandler $primitiveHandler): void
    {
        unset($this->items[$primitiveHandler->getName()]);
    }
}
