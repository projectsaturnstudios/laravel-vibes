<?php

namespace ProjectSaturnStudios\Vibes\Concerns\AuthenticatedAccess;

use ProjectSaturnStudios\Vibes\Models\UserAgentAccessTokenModel;
use Ramsey\Uuid\Uuid;

trait AgentAccessTokens
{
    public function hasAgentAccessPresently() : bool
    {
        return UserAgentAccessTokenModel::whereEntityId($this->getKey())->exists();
    }

    protected function agent_token(): ?string
    {
        return agent_token($this->getKey())?->token ?? null;
    }

    public function generateToken() : void
    {
        agent_token()->firstOrCreate([
            'entity_type' => $this->getMorphClass(),
            'entity_id' => $this->getKey(),
            'token' => Uuid::uuid4()->toString(),
        ]);
    }

    public function removeToken() : void
    {
        agent_token($this->getKey())->delete();
    }

    public function refreshToken() : void
    {
        $model = agent_token($this->getKey());
        $model->token = Uuid::uuid4()->toString();
        $model->save();
    }
}
