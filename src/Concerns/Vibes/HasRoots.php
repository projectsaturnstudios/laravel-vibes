<?php

namespace ProjectSaturnStudios\Vibes\Concerns\Vibes;

use ProjectSaturnStudios\Vibes\Primitives\PrimitiveHandlerCollection;

/**
 * Trait for managing root primitive handlers within TheAgency.
 *
 * Provides functionality to initialize and store a collection of root primitives.
 * Roots serve as entry points for custom workflows in the MCP protocol.
 * This is intended to be used by TheAgency class.
 *
 * @property PrimitiveHandlerCollection $roots Collection of registered root primitives.
 *
 * @package ProjectSaturnStudios\Vibes\Concerns\Vibes
 * @since 0.4.0
 */
trait HasRoots
{
    /**
     * @var PrimitiveHandlerCollection Collection storing the root primitive handlers.
     */
    protected PrimitiveHandlerCollection $roots;

    /**
     * Initializes the collection for root primitive handlers.
     *
     * @return PrimitiveHandlerCollection An empty collection ready for roots.
     */
    public function init_roots() : PrimitiveHandlerCollection
    {
        return new PrimitiveHandlerCollection();
    }
}
