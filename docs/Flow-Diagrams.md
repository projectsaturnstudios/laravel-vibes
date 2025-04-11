# Laravel Vibes Flow Diagrams

This document provides visual diagrams of the Laravel Vibes package architecture and interaction flows. These diagrams should help developers understand how the various components work together and how to implement the package in their applications.

## Table of Contents

- [Client-Laravel-AI Service Flow](#client-laravel-ai-service-flow)
- [Tool Registration and Discovery](#tool-registration-and-discovery)
- [Server-Sent Events (SSE) Connection Flow](#server-sent-events-sse-connection-flow)
- [MCP Message Processing Flow](#mcp-message-processing-flow)
- [Middleware Stack](#middleware-stack)
- [TheAgency Component Interactions](#theagency-component-interactions)

## Client-Laravel-AI Service Flow

This diagram illustrates the overall flow between a client application, Laravel application with Laravel Vibes, and an AI service using the MCP protocol.

```mermaid
sequenceDiagram
    participant Client as Client Application
    participant LaravelApp as Laravel Application
    participant LaravelVibes as Laravel Vibes
    participant AIService as AI Service (Claude/GPT/etc.)
    
    Client->>LaravelApp: User Request
    LaravelApp->>LaravelVibes: Process Request
    LaravelVibes->>LaravelVibes: Initialize TheAgency
    LaravelVibes-->>AIService: Establish SSE Connection
    
    AIService->>LaravelVibes: Request Available Tools
    LaravelVibes->>AIService: Return Tool Definitions
    
    AIService->>LaravelVibes: Tool Execution Request
    LaravelVibes->>LaravelVibes: Execute Tool
    LaravelVibes->>AIService: Return Tool Result
    
    AIService->>LaravelVibes: Generate Response
    LaravelVibes->>LaravelApp: Process AI Response
    LaravelApp->>Client: Return Response to User
```

## Tool Registration and Discovery

This diagram shows how tools are registered, discovered, and managed by Laravel Vibes.

```mermaid
flowchart TB
    A[Application Start] --> B{Auto-Discovery<br/>Enabled?}
    B -->|Yes| C[Scan Directories<br/>for Tools]
    C --> D[Find Classes<br/>Implementing<br/>PrimitiveHandler]
    D --> E[Register with<br/>TheAgency]
    
    B -->|No| F[Manual Tool<br/>Registration]
    F --> G[Service Provider<br/>Calls addTool]
    G --> E
    
    E --> H{Tools<br/>Cache Enabled?}
    H -->|Yes| I[Store Tools<br/>in Cache]
    I --> J[Tools Available<br/>for AI Agents]
    H -->|No| J
    
    subgraph Discovery Process
        C
        D
    end
    
    subgraph Registration Process
        F
        G
        E
    end
```

## Server-Sent Events (SSE) Connection Flow

This diagram illustrates how Server-Sent Events (SSE) connections are established and maintained between Laravel Vibes and an AI agent.

```mermaid
sequenceDiagram
    participant Client as AI Agent
    participant Router as Laravel Router
    participant Middleware as Middleware Stack
    participant Controller as MCPAgentEntryController
    participant SSE as SSE Stream Service
    participant Session as Agent Session
    
    Client->>Router: GET /mcp/sse
    Router->>Middleware: Process Request
    Middleware->>Middleware: Apply mcp-agent<br/>Middleware Group
    Middleware->>Middleware: Apply ScaffoldSSEConnection<br/>Middleware
    
    Middleware->>Controller: Call open_a_channel()
    Controller->>Session: Create New Agent Session
    Session->>Session: Generate Session ID
    
    Controller->>SSE: Create SSE Stream
    SSE->>SSE: Configure Stream Headers
    SSE->>SSE: Start Event Loop
    
    SSE->>Client: Send endpoint-info Event
    SSE->>Client: Send heartbeat Events
    
    Client->>Router: POST /mcp/sse/messages
    Router->>Controller: Process JSON-RPC Request
    Controller->>Session: Pass Message to Session Handler
    Session->>SSE: Send Response via SSE
    SSE->>Client: Event with Tool Results
```

## MCP Message Processing Flow

This diagram depicts how MCP protocol messages are processed by Laravel Vibes.

```mermaid
flowchart TB
    A[JSON-RPC Request] --> B[ValidateRequest]
    B --> C{Valid Request?}
    C -->|No| D[Return Error Response]
    
    C -->|Yes| E[Parse Method]
    E --> F{Method Type}
    
    F -->|list_tools| G[Get All Tools<br/>from TheAgency]
    G --> H[Return Tool List]
    
    F -->|run_tool| I[Find Tool<br/>by Name]
    I --> J{Tool Exists?}
    J -->|No| K[Return Error]
    J -->|Yes| L[Validate Parameters]
    L --> M{Parameters Valid?}
    M -->|No| N[Return Validation Error]
    M -->|Yes| O[Execute Tool]
    O --> P[Format Result]
    P --> Q[Send Response via SSE]
    
    F -->|Other Methods| R[Process Other<br/>MCP Methods]
    R --> Q
```

## Middleware Stack

This diagram shows the middleware stack used for Laravel Vibes endpoints and how requests flow through it.

```mermaid
flowchart TB
    A[Incoming Request] --> B[Laravel<br/>Global Middleware]
    B --> C[API Middleware]
    C --> D[ValidAgentCorsHeaders<br/>Middleware]
    
    D --> E{Request Type}
    E -->|SSE Connection| F[ScaffoldSSEConnection<br/>Middleware]
    F --> G[Modify Response<br/>for SSE]
    
    E -->|Message/Other| H[Regular<br/>Request Processing]
    
    G --> I[MCPAgentEntryController]
    H --> I
    
    I --> J[Process<br/>Request]
    J --> K[Generate<br/>Response]
    K --> L[Return to Client]
    
    subgraph mcp-agent Middleware Group
        C
        D
    end
```

## TheAgency Component Interactions

This diagram illustrates how TheAgency component interacts with other parts of Laravel Vibes.

```mermaid
classDiagram
    class TheAgency {
        +addPrimitiveHandler()
        +removePrimitiveHandler()
        +addTool()
        +getTool()
        +getToolList()
        +processRequest()
    }
    
    class VibeTool {
        #name: string
        +getMetadata() array
        +getName() string
        +handle(...args)
    }
    
    class PrimitiveHandler {
        <<interface>>
        +getName() string
    }
    
    class ToolRepository {
        +register()
        +find()
        +all()
    }
    
    class MCPAgentEntryController {
        +open_a_channel()
        +asController()
    }
    
    class VibeSesh {
        +session_id: string
        +addSessionEvent()
        +save()
    }
    
    TheAgency o-- VibeTool : manages
    VibeTool ..|> PrimitiveHandler : implements
    TheAgency o-- ToolRepository : uses
    MCPAgentEntryController --> TheAgency : processes requests through
    MCPAgentEntryController --> VibeSesh : creates and manages
```

These diagrams provide a visual representation of Laravel Vibes' architecture and interaction flows, helping developers understand how to implement and extend the package in their Laravel applications. 