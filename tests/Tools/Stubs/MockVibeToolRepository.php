<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Stubs;

use Illuminate\Support\Collection;
use ProjectSaturnStudios\Vibes\Contracts\VibeToolRepository;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidPrimitiveHandler;

class MockVibeToolRepository implements VibeToolRepository
{
    protected Collection $tools;

    public function __construct()
    {
        $this->tools = new Collection();
    }

    public function add(VibeTool $tool): self
    {
        $this->tools->put($tool->getName(), $tool);
        return $this;
    }

    public function remove(string $name): self
    {
        $this->tools->forget($name);
        return $this;
    }

    public function find(string $name): ?VibeTool
    {
        return $this->tools->get($name);
    }

    public function retrieveAll(?string $name = null): Collection
    {
        $tools = $this->tools;
        
        if ($name) {
            $tools = $tools->filter(fn (VibeTool $tool) => $tool->getName() === $name);
        }
        
        return $tools;
    }
    
    public function count(): int
    {
        return $this->tools->count();
    }
    
    public function clear(): self
    {
        $this->tools = new Collection();
        return $this;
    }
} 