<?php

namespace ProjectSaturnStudios\Vibes\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * Middleware to handle Cross-Origin Resource Sharing (CORS) headers for agent communication.
 *
 * This middleware reads the CORS configuration specific to the 'vibes' service
 * (from `config/cors.vibes.php`) and applies the appropriate
 * `Access-Control-Allow-*` headers to the response, handling preflight OPTIONS requests.
 *
 * @package ProjectSaturnStudios\Vibes\Http\Middleware
 * @since 0.4.0
 */
class ValidAgentCorsHeaders
{
    /**
     * Handle an incoming request.
     *
     * Checks the request origin against allowed origins and adds necessary CORS headers
     * to the response based on the `cors.vibes` configuration.
     *
     * @param Request $request The incoming HTTP request.
     * @param Closure $next The next middleware closure.
     * @return mixed The response with added CORS headers.
     */
    public function handle(Request $request, Closure $next) : mixed
    {
        // Get CORS configuration from config
        $allowedOrigins = config('cors.vibes.allowed_origins', ['*']);
        $allowedMethods = config('cors.vibes.allowed_methods', ['GET', 'POST', 'OPTIONS']);
        $allowedHeaders = config('cors.vibes.allowed_headers', ['Content-Type', 'X-Requested-With', 'Authorization']);
        $maxAge = config('cors.vibes.max_age', 86400);

        // Handle preflight OPTIONS request
        if ($request->isMethod('OPTIONS')) {
            $response = response('', 204);
        } else {
            $response = $next($request);
        }

        // Determine the origin
        $origin = $request->header('Origin');

        // If the origin is allowed or we allow all origins
        if ($origin && (in_array('*', $allowedOrigins) || in_array($origin, $allowedOrigins))) {
            $this->addHeaderToResponse($response, 'Access-Control-Allow-Origin', $origin);
        } elseif (in_array('*', $allowedOrigins)) {
            $this->addHeaderToResponse($response, 'Access-Control-Allow-Origin', '*');
        }

        // Add other CORS headers
        $this->addHeaderToResponse($response, 'Access-Control-Allow-Methods', implode(', ', $allowedMethods));
        $this->addHeaderToResponse($response, 'Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        $this->addHeaderToResponse($response, 'Access-Control-Allow-Credentials', 'true');
        $this->addHeaderToResponse($response, 'Access-Control-Max-Age', $maxAge);

        return $response;
    }

    /**
     * Add a header to a response, handling different response types gracefully.
     *
     * Checks if the response object has a `header` or `headers->set` method
     * before attempting to add the header.
     *
     * @param mixed $response The response object (e.g., Illuminate\Http\Response, Symfony\Component\HttpFoundation\Response).
     * @param string $name The name of the header.
     * @param string $value The value of the header.
     * @return void
     */
    protected function addHeaderToResponse(mixed $response, string $name, string $value): void
    {
        if (method_exists($response, 'header')) {
            $response->header($name, $value);
        } elseif (method_exists($response, 'headers') && method_exists($response->headers, 'set')) {
            $response->headers->set($name, $value);
        }
    }
}
