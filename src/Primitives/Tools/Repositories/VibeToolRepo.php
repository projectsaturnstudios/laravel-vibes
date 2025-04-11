<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Contracts\VibeToolRepository;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Implementation of the VibeToolRepository interface that uses TheAgency as a storage backend.
 *
 * This repository acts as a bridge between components that need to access tools
 * and TheAgency which actually stores them. This follows the repository pattern,
 * abstracting away the details of how tools are stored and retrieved.
 *
 * @package ProjectSaturnStudios\Vibes\Primitives\Tools\Repositories
 * @since 0.4.0
 */
class VibeToolRepo implements VibeToolRepository
{
    /**
     * Find a specific tool by name.
     *
     * This method retrieves TheAgency singleton from the container and delegates
     * the tool lookup to it.
     *
     * @param string $name The name of the tool to find.
     * @return VibeTool|null The found tool or null if not found.
     */
    public function find(string $name): ?VibeTool
    {
        /** @var TheAgency $agency */
        $agency = app('the-agency');
        return $agency->getTool($name) ? app($agency->getTool($name)) : null;
    }

    /**
     * Retrieve all registered tools.
     *
     * This method retrieves TheAgency singleton from the container and gets
     * all tools from it.
     *
     * @return Collection<string, VibeTool> Collection of all registered tools.
     */
    public function retrieveAll(): Collection
    {
        /** @var TheAgency $agency */
        $agency = app('the-agency');
        $tools = $agency->getTools();
        $results = [];
        foreach($tools as $tool) {
            $results[] = app($tool);
        }
        //VarDumper::dump($tools);
        return collect($results);
    }
}
