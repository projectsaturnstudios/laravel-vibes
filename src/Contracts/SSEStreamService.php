<?php

namespace ProjectSaturnStudios\Vibes\Contracts;

use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;

interface SSEStreamService
{
    public function start(VibeSesh $sesh) : void;
}
