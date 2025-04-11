<?php

namespace ProjectSaturnStudios\Vibes\Data\SSESessions;

use Illuminate\Support\Str;
use ProjectSaturnStudios\Vibes\Concerns\VibeSesh\SessionEvents;
use ProjectSaturnStudios\Vibes\Concerns\VibeSesh\SSEMethodInvocation;
use ProjectSaturnStudios\Vibes\Concerns\VibeSesh\SSEToolInvocation;
use Spatie\LaravelData\Data;
use Illuminate\Support\Facades\Cache;

/**
 * Represents a session for server-sent events (SSE) communication with an AI agent.
 *
 * This data class manages the lifecycle of a session for communication with an AI agent.
 * It provides methods for creating, loading, and managing sessions along with their
 * associated events. Sessions are stored in the Laravel cache with configurable expiration.
 *
 * @package ProjectSaturnStudios\Vibes\Data\SSESessions
 * @since 0.4.0
 */
class VibeSesh extends Data
{
    use SessionEvents;
    use SSEMethodInvocation;
    use SSEToolInvocation;

    /**
     * Create a new VibeSesh instance.
     *
     * @param string $session_id The unique identifier for this session
     *
     * @since 0.4.0
     */
    public function __construct(
        public string $session_id,
    ) {}

    /**
     * Create a new VibeSesh with a generated unique ID.
     *
     * This static factory method creates a new VibeSesh instance with a
     * unique ULID (Universally Unique Lexicographically Sortable Identifier)
     * as the session_id.
     *
     * @return static A new VibeSesh instance
     *
     * @since 0.4.0
     */
    public static function make() : static
    {
        $session_id = Str::ulid(now());
        $res = new static($session_id);
        return $res;
    }

    /**
     * Save the session to cache.
     *
     * Stores the session in the Laravel cache with an expiration time
     * defined in the configuration (defaults to 5 minutes).
     *
     * @return static The current instance for method chaining
     *
     * @since 0.4.0
     */
    public function save() : static
    {
        Cache::put('vibe_sesh-'.$this->session_id, $this, now()->addMinutes(config('vibes.service_info.session_cache_length', 5)));
        return $this;
    }

    /**
     * Load a session from cache by its ID.
     *
     * This static method retrieves a previously saved VibeSesh from the
     * cache using its session_id.
     *
     * @param string $session_id The unique identifier for the session to load
     *
     * @return static|null The loaded session or null if not found
     *
     * @since 0.4.0
     */
    public static function load(string $session_id) : ?static
    {
        return Cache::get('vibe_sesh-'.$session_id, null);
    }
}
