<?php

namespace ProjectSaturnStudios\Vibes\Enums;

enum MCPErrorCode: int
{
    case ERROR_PARSE = -32700;
    case ERROR_INVALID_REQUEST = -32600;
    case ERROR_METHOD_NOT_FOUND = -32601;
    case ERROR_INVALID_PARAMS = -32602;
    case ERROR_INTERNAL = -32603;
    case ERROR_SESSION_NOT_FOUND = -31000;
    case ERROR_TOOL_NOT_FOUND = -31001;
    case ERROR_RESOURCE_NOT_FOUND = -31002;
    case ERROR_RESOURCE_TYPE_NOT_FOUND = -31003;

}
