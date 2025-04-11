<?php

use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

if (!function_exists('mcp_tool')) {
    /**
     * Retrieve a specific VibeTool by its name.
     *
     * This function provides a convenient way to access a registered VibeTool from anywhere
     * in the application by looking it up in the VibeTool repository. It uses the Laravel
     * service container to fetch the 'vibe-tools' binding and calls the find method.
     *
     * @param string $name The name identifier of the tool to retrieve
     *
     * @return VibeTool|null The requested VibeTool instance if found, null otherwise
     *
     * @since 0.4.0
     *
     * @example
     * ```php
     * $echoTool = mcp_tool('echo');
     * if ($echoTool) {
     *     // Use the tool...
     * }
     * ```
     */
    function mcp_tool(string $name) : ?VibeTool
    {
        return app('vibe-tools')->find($name);
    }
}

if (!function_exists('mcp_tools')) {
    /**
     * Get all registered VibeTool instances as an array.
     *
     * Retrieves the complete collection of VibeTool instances that have been
     * registered with the laravel-vibes package. This provides access to all
     * available tools for the AI agent integration.
     *
     * @return array<VibeTool> Array of all registered VibeTool instances
     *
     * @since 0.4.0
     *
     * @example
     * ```php
     * $allTools = mcp_tools();
     * foreach ($allTools as $tool) {
     *     echo $tool->getName();
     * }
     * ```
     */
    function mcp_tools() : \Illuminate\Support\Collection
    {
        $tools_repo = app('vibe-tools');
        $tools = $tools_repo->retrieveAll();
        \Illuminate\Support\Facades\Log::info($tools->first());
        return $tools;
    }
}
