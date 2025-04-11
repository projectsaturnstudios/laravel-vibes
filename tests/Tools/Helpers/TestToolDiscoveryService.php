<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Helpers;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler;
use ProjectSaturnStudios\Vibes\Services\PrimitiveHandlerDiscoveryService;
use Symfony\Component\VarDumper\VarDumper;

class TestToolDiscoveryService extends PrimitiveHandlerDiscoveryService
{
    protected array $tools = [];

    /**
     * Add test tool classes to be "discovered"
     */
    public function addTestTools(array $tools): self
    {
        $this->tools = array_merge($this->tools, $tools);
        return $this;
    }

    /**
     * Override the normal discovery process for testing
     */
    public function addToTheAgency(TheAgency $agency)
    {
        return collect($this->tools)
            ->filter(fn (string $primitiveHandlerClass) => class_exists($primitiveHandlerClass))
            ->filter(fn (string $primitiveHandlerClass) => is_subclass_of($primitiveHandlerClass, PrimitiveHandler::class))
            ->filter(fn (string $primitiveHandlerClass) => (new ReflectionClass($primitiveHandlerClass))->isInstantiable())
            ->filter(fn (string $primitiveHandlerClass) => !in_array($primitiveHandlerClass, $this->ignoredFiles))
            ->pipe(function (Collection $primitiveHandlers) use ($agency) {
                $agency->addPrimitiveHandlers($primitiveHandlers->toArray());
            });
    }
}
