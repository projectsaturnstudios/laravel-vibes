<?php

namespace ProjectSaturnStudios\Vibes\Actions\AgentMethods;

use Lorisleiva\Actions\Concerns\AsAction;
use ProjectSaturnStudios\Vibes\Attributes\MCPMethod;
use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;

/**
 * Abstract base class for all Agent Method handlers in the Laravel Vibes package.
 *
 * This class represents a method that can be invoked by an AI agent through the MCP protocol.
 * Each concrete method implementation must extend this class and provide a handle method
 * to process agent requests. Methods use the MCPMethod attribute to define their names.
 *
 * @package ProjectSaturnStudios\Vibes\Actions\AgentMethods
 * @since 0.4.0
 */
abstract class AgentMethod
{
    use AsAction;

    /**
     * Handle an incoming agent method invocation.
     *
     * This method is called when an agent invokes a specific method. It processes
     * the request parameters and performs the method's functionality.
     *
     * @param VibeSesh $sesh The session context for the agent interaction
     * @param string|int|null $request_id The unique identifier for this request
     * @param array $params Method-specific parameters from the agent
     * @return void
     */
    abstract public function handle(VibeSesh $sesh, string|int|null $request_id, array $params) : void;

    /**
     * Gets the attribute instance of the specified class from this class.
     *
     * Uses PHP's Reflection API to retrieve the specified attribute from the class.
     * If the attribute is not found, throws a RuntimeException.
     *
     * @param string $class The fully-qualified class name of the attribute to retrieve
     * @return object The instantiated attribute object
     * @throws \RuntimeException If the specified attribute is not found on the class
     */
    protected function getAttribute(string $class)
    {
        // Create a ReflectionClassConstant instance for the enum case
        // and attempt to read the Friendly attribute.
        $attributes = (new \ReflectionClass($this::class)
        )->getAttributes(
            name: $class,
        );

        if ($attributes === []) {
            throw new \RuntimeException(
                message: "No $class attribute found for " . $this::class,
            );
        }

        return $attributes[0]->newInstance();
    }

    /**
     * Retrieves the method name from the MCPMethod attribute.
     * 
     * This method uses reflection to extract the method name from the
     * MCPMethod attribute that decorates the class.
     * 
     * @return string The name of the method as defined in the MCPMethod attribute
     */
    public function method_name() : string
    {
        return $this->getAttribute(MCPMethod::class)->method;
    }
}
