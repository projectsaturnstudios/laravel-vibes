<?php

namespace ProjectSaturnStudios\Vibes\Concerns\Vibes;

use ProjectSaturnStudios\Vibes\Primitives\PrimitiveHandlerCollection;

/**
 * Trait for managing resource primitive handlers within TheAgency.
 *
 * Provides functionality to initialize and store a collection of resource primitives.
 * Resources are data sources that AI agents can query.
 * This is intended to be used by TheAgency class.
 *
 * @property PrimitiveHandlerCollection $resources Collection of registered resource primitives.
 *
 * @package ProjectSaturnStudios\Vibes\Concerns\Vibes
 * @since 0.4.0
 */
trait HasResources
{
    /**
     * @var PrimitiveHandlerCollection Collection storing the resource primitive handlers.
     */
    protected PrimitiveHandlerCollection $resources;

    /**
     * Initializes the collection for resource primitive handlers.
     *
     * @return PrimitiveHandlerCollection An empty collection ready for resources.
     */
    public function init_resources() : PrimitiveHandlerCollection
    {
        return new PrimitiveHandlerCollection();
    }
}
