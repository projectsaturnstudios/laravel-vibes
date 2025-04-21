<?php

use ProjectSaturnStudios\Vibes\Models\UserAgentAccessTokenModel;

if (!function_exists('agent_token')) {
    function agent_token(?string $uuid = null) : ?UserAgentAccessTokenModel
    {
        return empty($uuid)
            ? (new UserAgentAccessTokenModel)
            : (new UserAgentAccessTokenModel)->where('entity_id', $uuid)->first();
    }
}

if (!function_exists('token_user')) {
    function token_user(?string $token = null) : ?\Illuminate\Contracts\Auth\Authenticatable
    {
        $results = null;
        $model = (new UserAgentAccessTokenModel)->where('token', $token)->first();

        if(!is_null($model))
        {
            $entity = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($model->entity_type);
            $entity = (new $entity);
            $results = $entity->where($entity->getKeyName(), $model->entity_id)->first();
        }

        return $results;
    }
}
