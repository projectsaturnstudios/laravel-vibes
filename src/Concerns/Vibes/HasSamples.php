<?php

namespace ProjectSaturnStudios\Vibes\Concerns\Vibes;

use ProjectSaturnStudios\Vibes\Primitives\PrimitiveHandlerCollection;

/**
 * Trait for managing sample primitive handlers within TheAgency.
 *
 * Provides functionality to initialize and store a collection of sample primitives.
 * Samples are used for AI model configuration and example data.
 * This is intended to be used by TheAgency class.
 *
 * @property PrimitiveHandlerCollection $samples Collection of registered sample primitives.
 *
 * @package ProjectSaturnStudios\Vibes\Concerns\Vibes
 * @since 0.4.0
 */
trait HasSamples
{
    /**
     * @var PrimitiveHandlerCollection Collection storing the sample primitive handlers.
     */
    protected PrimitiveHandlerCollection $samples;

    /**
     * Initializes the collection for sample primitive handlers.
     *
     * @return PrimitiveHandlerCollection An empty collection ready for samples.
     */
    public function init_samples() : PrimitiveHandlerCollection
    {
        return new PrimitiveHandlerCollection();
    }
}
