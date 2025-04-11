<?php

namespace ProjectSaturnStudios\Vibes\Primitives\Tools\Data;

use ProjectSaturnStudios\Vibes\Data\SSESessions\VibeSesh;
use Spatie\LaravelData\Data;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler;

/**
 * Abstract base class for all tool primitive handlers in the Laravel Vibes package.
 *
 * VibeTool extends Spatie's Data class to gain data object capabilities while
 * implementing the PrimitiveHandler interface. This class represents a functional
 * tool that AI agents can invoke through the MCP protocol. All concrete tools
 * in the system should extend this class.
 *
 * @package ProjectSaturnStudios\Vibes\Primitives\Tools\Data
 * @since 0.4.0
 */
abstract class VibeTool extends Data implements PrimitiveHandler
{
    /**
     * Define the input schema for the tool.
     *
     * This method should return an array representing the JSON Schema
     * that defines the required parameters and their types for the tool.
     *
     * @return array The JSON Schema definition for the tool's input parameters
     */
    abstract public static function input_schema() : array;
    abstract public function execute(VibeSesh $sesh, string $request_id, ?array $params) : void;


    /**
     * Get the unique name of the tool.
     *
     * This method implements the getName() method required by the PrimitiveHandler
     * interface. The name serves as the identifier for the tool in collections
     * and is used when the AI agent wants to invoke the tool.
     *
     * @return string The tool's name.
     */
    public function getName() : string
    {
        return $this->tool_name();
    }

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
     * Retrieves the tool name from the MCPTool attribute.
     *
     * This method uses reflection to extract the tool_name value from the
     * MCPTool attribute that decorates the tool class.
     *
     * @return string The name of the tool as defined in the MCPTool attribute
     */
    public function tool_name() : string
    {
        return $this->getAttribute(MCPTool::class)->tool_name;
    }

    /**
     * Retrieves the tool description from the MCPTool attribute.
     *
     * This method uses reflection to extract the description of the tool
     * from the MCPTool attribute that decorates the tool class.
     *
     * @return string The description of the tool as defined in the MCPTool attribute
     */
    public function tool_description() : string
    {
        return $this->getAttribute(MCPTool::class)->tool_name;
    }

    /**
     * Retrieves the metadata for the tool.
     *
     * @return array<string, mixed> An array describing the tool's name, description, and parameters.
     */
    public static function getMetadata(): array
    {
        $tool_instance = new static();

        return [
            'name' => $tool_instance->getName(),
            'description' => $tool_instance->tool_description(),
            'inputSchema' => static::input_schema(),
        ];
    }
}
