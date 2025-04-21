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
class ValidateAgentAccessToken
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
        if(config('vibes.service_info.requires_authentication'))
        {
            $token = $request->get('token');
            if($user = token_user($token))
            {
                auth(config('vibes.service_info.auth_guard'))->login($user);
            }
            else
            {
                abort(401);
            }
        }

        return $next($request);
    }
}
