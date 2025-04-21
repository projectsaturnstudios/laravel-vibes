<?php

use ProjectSaturnStudios\Vibes\Http\Middleware\ScaffoldSSEConnection;
use ProjectSaturnStudios\Vibes\Http\Middleware\AuthenticateAgentsUser;
use ProjectSaturnStudios\Vibes\Http\Middleware\ValidateAgentAccessToken;

return [
    /*
    |--------------------------------------------------------------------------
    | Laravel Vibes Server Information
    |--------------------------------------------------------------------------
    |
    | These settings define basic metadata about your Laravel Vibes server
    | implementation. The server name and version are exposed to clients during
    | the MCP initialization process. Timing settings control how the server
    | handles sessions and event processing.
    |
    */
    'service_info' => [
        'server_name' => env('VIBE_SVC_NAME', 'laravel-vibes-server'),
        'server_version' => env('VIBE_SVC_VERSION', '1.0.0'),
        'requires_authentication' => true, // Set to true if your server requires authentication
        'auth_guard' => 'admin', //'web'

        'heartbeat_interval' => 20, // seconds
        'listener_execution_time' => 0, // seconds
        'session_cache_length' => 5, // minutes,
        'mcp_event_prefix' => 'vibes', // Prefix for MCP events

        'catch_exceptions' => false, // Catch exceptions in the MCP event loop,
        'cache_path' => base_path('bootstrap/cache'),
    ],
    'sse' => [
        'stream_provider' => \ProjectSaturnStudios\Vibes\Services\VibeStreamLoopService::class,
        'transport_provider' => \ProjectSaturnStudios\Vibes\Data\SSEMessageTransports\VibeTransporter::class,
        'headers' => [
            'Content-Type' =>  'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]
    ],
    'database' => [
        'connection' => env('AI_DB_CONNECTION', 'mysql'),
    ],
    /*
    |--------------------------------------------------------------------------
    | Enabled Features
    |--------------------------------------------------------------------------
    |
    | Laravel Vibes supports multiple MCP features that can be enabled or
    | disabled individually. Set a feature to 'true' to enable it or 'false'
    | to disable it. Only advertise features that your implementation actually
    | supports.
    |
    */
    'features' => [
        'tools'         => true, // Tool registration and execution
        'resources'     => false, // Resource discovery and access
        'prompts'       => false, // Prompt templates and execution
        'logging'       => false, // Logging for AI agent actions
        'roots'         => false, // Custom workflow roots
        'sampling'      => false, // Model sampling configuration
        'experimental'  => false, // Experimental MCP features
    ],
    /*
    |--------------------------------------------------------------------------
    | Middleware Configuration
    |--------------------------------------------------------------------------
    |
    | Define which middleware should be applied to the Vibes routes.
    |
    */
    'middleware' => [
        'api',
        \ProjectSaturnStudios\Vibes\Http\Middleware\ValidAgentCorsHeaders::class,
    ],
    'entry_middleware' => [
        ValidateAgentAccessToken::class,
        ScaffoldSSEConnection::class
    ],
    'messages_middleware' => [
        AuthenticateAgentsUser::class
    ],
    /*
    |--------------------------------------------------------------------------
    | Routes Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how the Laravel Vibes routes are registered and what endpoints
    | they use. The file name controls where route definitions are stored,
    | while the route configurations define the endpoints for SSE streaming
    | and message processing.
    |
    */
    'routes' => [
        'sse' => [
            'uri' => '/mcp/sse',
            'controller' => \ProjectSaturnStudios\Vibes\Http\Controllers\MCPAgentEntryController::class,
            'action' => 'open_a_channel',
            'name' => 'vibes.sse'
        ],
        'messages' => [
            'uri' => '/mcp/sse/messages',
            'controller' => \ProjectSaturnStudios\Vibes\Http\Controllers\MCPAgentEntryController::class,
            'action' => null,
            'name' => 'vibes.messages'
        ]
    ],
    /*
    |--------------------------------------------------------------------------
    | Primitive AutoDiscovery
    |--------------------------------------------------------------------------
    |
    | These directories will be scanned for tools, resources, prompts, samples and roots.
    | They will be registered to TheAgency automatically.
    | The base path directory will be used as the base path when scanning
    | for tools, resources, prompts, samples and roots.
    */
    'auto_discover_all_primitives' => [app()->path()],
    'auto_discover_base_path' => base_path(),


    'invocable_methods' => [
        'initialize' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\SessionInitialized::class,
        'ping' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Ping::class,

        'tools/list' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Tools\ListTools::class,
        'tools/call' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Tools\InvokeTool::class,

        'resources/list' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Resources\ListResources::class,
        //'resources/read' => '',
        //'resources/templates/list' => '',
        //'resources/subscribe' => '',
        //'resources/unsubscribe' => '',

        'prompts/list' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Prompts\ListPrompts::class,
        //'prompts/call' => '', // Not implemented yet

        // Notification-related methods
        'notifications/initialized' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Notifications\InitializationConfirmed::class,
        'notifications/cancellation' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Notifications\CancelRequest::class,
        'notifications/cancelled' => \ProjectSaturnStudios\Vibes\Actions\AgentMethods\Notifications\NotyCancelRequest::class,
    ],


    /*
    |--------------------------------------------------------------------------
    | Global Tools Registry
    |--------------------------------------------------------------------------
    |
    | Register tools that should be available to AI agents. Each entry maps a
    | tool name to the class that implements its functionality. Tools must
    | implement the VibeToolContract interface.
    |
    */
    'tool_repository' => \ProjectSaturnStudios\Vibes\Primitives\Tools\Repositories\VibeToolRepo::class,
    'register_sample_tools' => true,
    'register_dev_tools' => true,
    'tools' => [
        //'echo' => EchoTool::class,
    ],
];
