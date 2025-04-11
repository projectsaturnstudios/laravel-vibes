<?php

namespace ProjectSaturnStudios\Vibes\Concerns\Vibes;

use ProjectSaturnStudios\Vibes\Primitives\PrimitiveHandlerCollection;

/**
 * Trait for managing prompt primitive handlers within TheAgency.
 *
 * Provides functionality to initialize and store a collection of prompt primitives.
 * This is intended to be used by TheAgency class.
 *
 * @property PrimitiveHandlerCollection $prompts Collection of registered prompt primitives.
 *
 * @package ProjectSaturnStudios\Vibes\Concerns\Vibes
 * @since 0.4.0
 */
trait HasPrompts
{
    /**
     * @var PrimitiveHandlerCollection Collection storing the prompt primitive handlers.
     */
    protected PrimitiveHandlerCollection $prompts;

    /**
     * Initializes the collection for prompt primitive handlers.
     *
     * @return PrimitiveHandlerCollection An empty collection ready for prompts.
     */
    public function init_prompts() : PrimitiveHandlerCollection
    {
        return new PrimitiveHandlerCollection();
    }
}
