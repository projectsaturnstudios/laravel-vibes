<?php

namespace ProjectSaturnStudios\Vibes\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Middleware responsible for setting up the environment for a Server-Sent Events (SSE) connection.
 *
 * This middleware prepares the request handling pipeline for SSE by:
 * - Cleaning output buffers
 * - Disabling PHP output buffering and compression
 * - Configuring the session driver to 'array' to avoid session locks
 * - Setting appropriate script execution time limits
 * - Ignoring user aborts to keep the connection alive
 *
 * @package ProjectSaturnStudios\Vibes\Http\Middleware
 * @since 0.4.0
 */
class ScaffoldSSEConnection
{
    /**
     * Handle an incoming request.
     *
     * Applies necessary configurations for an SSE connection before passing
     * the request to the next middleware or controller.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware closure.
     * @return mixed The response from the next middleware or controller.
     */
    public function handle(Request $request, Closure $next) : mixed
    {
        Log::info("ScaffoldSSEConnection middleware triggered");
        // Only attempt to disable output buffering if headers haven't been sent yet
        if (!headers_sent() && ob_get_level()) {
            ob_end_clean();
        }

        // Only set INI options if headers haven't been sent yet
        if (!headers_sent()) {
            // Prevent Laravel from buffering the response
            ini_set('output_buffering', 'off');
            ini_set('zlib.output_compression', false);
        }

        // Disable the session cookie
        config(['session.driver' => 'array']);

        // Set time limit based on config
        $maxExecutionTime = config('mcp.sse.max_execution_time', 0);
        if ($maxExecutionTime > 0) {
            set_time_limit($maxExecutionTime);
        } else {
            set_time_limit(0); // No time limit
        }

        // Prevent client disconnections from triggering PHP errors
        ignore_user_abort(true);

        return $next($request);
    }
}
