<?php

namespace ProjectSaturnStudios\Vibes\Data\SSESessions;

use Spatie\LaravelData\Data;

class ToolInvocationRequest extends Data
{
    public function __construct(
        public readonly string $session_id,
        public readonly int|string|null $request_id,
        public readonly string $tool_class,
        public readonly ?array $request_body = null,
    ) {}
}
