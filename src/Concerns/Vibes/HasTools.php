<?php

namespace ProjectSaturnStudios\Vibes\Concerns\Vibes;

use Illuminate\Support\Collection;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidPrimitiveHandler;
use ProjectSaturnStudios\Vibes\Primitives\PrimitiveHandlerCollection;

/**
 * Trait for managing tool primitive handlers within TheAgency.
 *
 * Provides functionality to initialize, add, remove, and retrieve tool primitives.
 * Tools are functions that AI agents can call to perform actions.
 * This trait is more developed than other primitives traits as tools are the
 * primary functionality currently used in the MCP implementation.
 *
 * @property PrimitiveHandlerCollection $tools Collection of registered tool primitives.
 *
 * @package ProjectSaturnStudios\Vibes\Concerns\Vibes
 * @since 0.4.0
 */
trait HasTools
{
    /**
     * @var PrimitiveHandlerCollection Collection storing the tool primitive handlers.
     */
    protected PrimitiveHandlerCollection $tools;

    /**
     * Initializes the collection for tool primitive handlers.
     *
     * @return PrimitiveHandlerCollection An empty collection ready for tools.
     */
    public function init_tools() : PrimitiveHandlerCollection
    {
        return new PrimitiveHandlerCollection();
    }

    /**
     * Adds a single tool to the collection.
     *
     * Accepts either a class name or an instance of VibeTool.
     * If a class name is provided, it will be resolved through the container.
     *
     * @param string|VibeTool $tool The tool to add (class name or instance).
     * @return static Returns $this for method chaining.
     * @throws InvalidPrimitiveHandler When the provided tool is not a valid VibeTool.
     */
    public function addTool(string | VibeTool $tool): static
    {
        if (is_string($tool)) {
            $tool = app($tool);
        }

        if (! $tool instanceof VibeTool) {
            throw InvalidPrimitiveHandler::notATool($tool);
        }

        $this->tools->addPrimitiveHandler($tool);
        return $this;
    }

    /**
     * Removes a tool from the collection.
     *
     * Accepts either a class name or an instance of VibeTool.
     * If a class name is provided, it will be resolved through the container.
     *
     * @param string|VibeTool $tool The tool to remove (class name or instance).
     * @return static Returns $this for method chaining.
     * @throws InvalidPrimitiveHandler When the provided tool is not a valid VibeTool.
     */
    public function removeTool(string | VibeTool $tool): static
    {
        if (is_string($tool)) {
            $tool = app($tool);
        }

        if (! $tool instanceof VibeTool) {
            throw InvalidPrimitiveHandler::notATool($tool);
        }

        $this->tools->removePrimitiveHandler($tool);

        return $this;
    }

    /**
     * Adds multiple tools to the collection.
     *
     * @param array $tools An array of tool class names or instances.
     * @return static Returns $this for method chaining.
     */
    public function addTools(array $tools = []) : static
    {
        foreach ($tools as $tool) {
            $this->addTool($tool);
        }
        return $this;
    }

    /**
     * Gets all registered tools.
     *
     * @return Collection<VibeTool> Collection of VibeTool instances.
     */
    public function getTools(): Collection
    {
        return $this->tools;
    }

    /**
     * Gets a specific tool by name.
     *
     * @param string $name The name of the tool to retrieve.
     * @return VibeTool|null The found tool or null if not found.
     */
    public function getTool(string $name) : ?string
    {
        return $this->tools->first(fn (string $tool) => app($tool)->getName() === $name);
    }
}
