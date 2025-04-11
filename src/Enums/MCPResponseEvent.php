<?php

namespace ProjectSaturnStudios\Vibes\Enums;

enum MCPResponseEvent: string
{
    case ENDPOINT = 'endpoint';
    case HEARTBEAT = 'heartbeat';
    case MESSAGE = 'message';
    case PING = 'ping';
    case ERROR = 'error';
    case OPEN = 'open';
    case CLOSE = 'close';


    public static function custom(string $custom): string
    {
        return strtolower($custom);
    }
}
