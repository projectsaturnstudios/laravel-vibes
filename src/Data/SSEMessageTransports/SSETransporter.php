<?php

namespace ProjectSaturnStudios\Vibes\Data\SSEMessageTransports;

use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Concerns\Transports\OutputBufferManipulation;
use Spatie\LaravelData\Data;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Contracts\SSEResponseTransporter;

abstract class SSETransporter extends Data implements SSEResponseTransporter
{
    abstract protected function boot(): void;
}
