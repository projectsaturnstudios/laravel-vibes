<?php

namespace ProjectSaturnStudios\Vibes\Services\ResponseBuilders;

use Illuminate\Support\Facades\Log;

class AgentSuccess extends SSEResponseBuilder
{
    protected bool $empty_object = false;
    protected array $result = [];

    public function sendBackNothing() : static
    {
        $this->empty_object = true;
        return $this;
    }

    public function queueResult(array $result) : static
    {
        $this->result = $result;
        return $this;
    }


    public function supply() : array
    {
        $results = $this->toArray();

        if($this->empty_object) $results['result'] = new \stdClass();
        else
        {
            $results['result'] = $this->result;
        }


        return $results;
    }


}
