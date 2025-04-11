<?php

namespace ProjectSaturnStudios\Vibes\Enums;

use Illuminate\Support\Facades\Log;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use ProjectSaturnStudios\Vibes\Services\ResponseBuilders\AgentSuccess;

enum MCPSubscribedEvents: string
{
    case SESSION_STARTED = 'session-started';
    case COMMAND_EXECUTED = 'command-executed';
    //case PROGRESS = 'progress';
    case COMMAND_FINISHED = 'command-finished';

    /**
     * @param array|null $params
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function process_event(array|string|null $params = null) : void
    {
        switch($this)
        {
            case self::COMMAND_EXECUTED:
                if($sesh = VibeSesh::load($params['session']))
                {
                    $response = (new AgentSuccess)
                        ->addId($params['request_id'] ?? 0)
                        ->queueResult([
                            'content' => [
                                ['type' => 'text', 'text' => "Command Executed: " . $params['command']
                            ]
                        ]]);

                    send_good_vibes($sesh,$response);
                }
                break;

            case self::COMMAND_FINISHED:
                if($sesh = VibeSesh::load($params['session']))
                {
                    $response = (new AgentSuccess)
                        ->addId($request_id ?? 0)
                        ->queueResult([
                            'content' => [
                                ['type' => 'text', 'text' => "Command Finished: " . $params['command']
                            ]
                        ]]);

                    send_good_vibes($sesh,$response);
                }
                break;
            default:
                Log::info($params['message'] ?? "Unhandled Event", [$params]);

        };
    }
}
