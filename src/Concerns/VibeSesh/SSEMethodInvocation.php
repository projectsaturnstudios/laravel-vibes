<?php

namespace ProjectSaturnStudios\Vibes\Concerns\VibeSesh;

use ProjectSaturnStudios\Vibes\Data\SSESessions\MethodInvocationRequest;

trait SSEMethodInvocation
{
    /**
     * The pending AgentMethod to be invoked.
     *
     * @var MethodInvocationRequest|null
     */
    public ?MethodInvocationRequest $pending_method = null;

    public function addMethodInvocation(MethodInvocationRequest $pending_method) : static
    {
        $this->pending_method = $pending_method;
        return $this;
    }

    public function getPendingMethod() : ?MethodInvocationRequest { return $this->pending_method; }

    public function clearPendingMethod() : static
    {
        $this->pending_method = null;
        return $this;
    }
}
