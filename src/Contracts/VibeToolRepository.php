<?php

namespace ProjectSaturnStudios\Vibes\Contracts;

use Illuminate\Support\Collection;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

interface VibeToolRepository
{
    public function find(string $name): ?VibeTool;

    public function retrieveAll(): Collection;
}
