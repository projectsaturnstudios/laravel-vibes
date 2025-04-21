<?php

namespace ProjectSaturnStudios\Vibes\Models;

use Illuminate\Database\Eloquent\Model;

class UserAgentAccessTokenModel extends Model
{
    protected $table = 'user_agent_tokens';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setConnection(config('vibes.database.connection'));
    }
}
