<?php

namespace ProjectSaturnStudios\Vibes\Concerns\VibeSesh;

use ProjectSaturnStudios\Vibes\Data\SSESessions\ToolInvocationRequest;

trait SSEToolInvocation
{
    /**
     * The pending Tool to be invoked.
     *
     * @var ToolInvocationRequest|null
     */
    public ?ToolInvocationRequest $pending_tool = null;

    public function addToolInvocation(ToolInvocationRequest $pending_tool) : static
    {
        $this->pending_tool = $pending_tool;
        return $this;
    }

    public function getPendingTool() : ?ToolInvocationRequest { return $this->pending_tool; }

    public function clearPendingTool() : static
    {
        $this->pending_tool = null;
        return $this;
    }
}
