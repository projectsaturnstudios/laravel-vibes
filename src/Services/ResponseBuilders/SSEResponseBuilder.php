<?php

namespace ProjectSaturnStudios\Vibes\Services\ResponseBuilders;

abstract class SSEResponseBuilder
{
    public string $jsonrpc = '2.0';
    public string|int|null $id = null;

    public function __construct(

    ) {
        $this->jsonrpc = '2.0';
    }

    public function addId(string|int|null $id) : static
    {
        $this->id = $id;
        return $this;
    }



    public function toArray() : array
    {
        $payload = [
            'jsonrpc'   => $this->jsonrpc,
        ];

        if(!is_null($this->id)) $payload['id'] = $this->id;

        return $payload;
    }
}
