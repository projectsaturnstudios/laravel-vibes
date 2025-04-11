# Laravel Vibes Class Diagram

Below is a class diagram showing the relationship between TheAgency and its related classes in the Laravel Vibes package.

```mermaid
classDiagram
    class TheAgency {
        -bool catchExceptions
        +__construct(array config)
        +addPrimitiveHandler(mixed primitiveHandlerClass) void
        +removePrimitiveHandler(string primitiveHandlerClass) self
        +addPrimitiveHandlers(array primitiveHandlers) self
        +static make() static
    }

    class PrimitiveHandlerCollection {
        +__construct(array primitiveHandlers)
        +addPrimitiveHandler(PrimitiveHandler primitiveHandler) void
        +removePrimitiveHandler(PrimitiveHandler primitiveHandler) void
    }

    class VibeTool {
        #string name
        +abstract static getMetadata() array
        +getName() string
    }

    %% Traits
    class HasTools {
        #PrimitiveHandlerCollection tools
        +init_tools() PrimitiveHandlerCollection
        +addTool(mixed tool) static
        +removeTool(mixed tool) static
        +addTools(array tools) static
        +getTools() Collection
        +getTool(string name) ?VibeTool
    }

    class HasResources {
        #PrimitiveHandlerCollection resources
        +init_resources() PrimitiveHandlerCollection
    }

    class HasPrompts {
        #PrimitiveHandlerCollection prompts
        +init_prompts() PrimitiveHandlerCollection
    }

    class HasSamples {
        #PrimitiveHandlerCollection samples
        +init_samples() PrimitiveHandlerCollection
    }

    class HasRoots {
        #PrimitiveHandlerCollection roots
        +init_roots() PrimitiveHandlerCollection
    }

    %% Interfaces
    class PrimitiveHandler {
        <<interface>>
        +getName() string
    }

    %% Relationships
    TheAgency *-- PrimitiveHandlerCollection : contains
    TheAgency o-- VibeTool : manages
    TheAgency --|> HasTools : uses
    TheAgency --|> HasResources : uses
    TheAgency --|> HasPrompts : uses
    TheAgency --|> HasSamples : uses
    TheAgency --|> HasRoots : uses
    
    PrimitiveHandlerCollection o-- PrimitiveHandler : contains
    VibeTool ..|> PrimitiveHandler : implements
    Collection <|-- PrimitiveHandlerCollection : extends
```

## Component Descriptions

### Core Classes

- **TheAgency**: Central manager for all MCP primitives in the Laravel Vibes package
- **PrimitiveHandlerCollection**: Collection class for storing and retrieving primitive handlers
- **VibeTool**: Base class for all tool primitive implementations

### Traits

- **HasTools**: Provides methods for managing tool primitives
- **HasResources**: Provides methods for managing resource primitives
- **HasPrompts**: Provides methods for managing prompt primitives
- **HasSamples**: Provides methods for managing sample primitives
- **HasRoots**: Provides methods for managing root primitives

### Interfaces

- **PrimitiveHandler**: Base interface for all MCP primitive handlers

## Relationships

- TheAgency uses multiple traits to incorporate primitive handling functionality
- Each trait manages a specific type of primitive handler through a PrimitiveHandlerCollection
- VibeTool implements the PrimitiveHandler interface to provide a consistent API
- PrimitiveHandlerCollection extends Laravel's Collection class for enhanced functionality 