# Laravel Vibes Real-World Examples

This document provides comprehensive real-world examples for implementing Laravel Vibes in various applications. These examples illustrate practical use cases and implementation patterns to help you get started quickly.

## Table of Contents

- [Simple Customer Support AI Assistant](#simple-customer-support-ai-assistant)
- [Content Generation System](#content-generation-system)
- [Data Analysis Tool](#data-analysis-tool)
- [Integration with External APIs](#integration-with-external-apis)
- [AI-Powered Search Enhancement](#ai-powered-search-enhancement)

## Simple Customer Support AI Assistant

This example demonstrates how to build a customer support assistant that can answer questions and perform actions using Laravel Vibes.

### Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── SupportAgentController.php
├── MCPTools/
│   ├── ProductInfoTool.php
│   ├── OrderLookupTool.php
│   ├── TicketCreationTool.php
│   └── FAQSearchTool.php
├── Models/
│   ├── Product.php
│   ├── Order.php
│   └── SupportTicket.php
├── Providers/
│   └── MCPToolServiceProvider.php
resources/
└── views/
    └── support/
        ├── chat.blade.php
        └── agent.js
```

### Tool Implementations

**ProductInfoTool.php**
```php
<?php

namespace App\MCPTools;

use App\Models\Product;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;

class ProductInfoTool extends VibeTool
{
    protected string $name = 'product_info';

    public static function getMetadata(): array
    {
        return [
            'name' => 'product_info',
            'description' => 'Get information about a product by SKU or name',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Product SKU or name to search for'
                    ]
                ],
                'required' => ['query']
            ]
        ];
    }
    
    public function handle(string $query)
    {
        // Search by SKU or name
        $product = Product::where('sku', $query)
            ->orWhere('name', 'LIKE', "%{$query}%")
            ->first();
            
        if (!$product) {
            return [
                'found' => false,
                'message' => "Product not found with query: {$query}"
            ];
        }
        
        return [
            'found' => true,
            'id' => $product->id,
            'sku' => $product->sku,
            'name' => $product->name,
            'price' => $product->price,
            'description' => $product->description,
            'in_stock' => $product->quantity > 0,
            'stock_quantity' => $product->quantity
        ];
    }
}
```

**OrderLookupTool.php**
```php
<?php

namespace App\MCPTools;

use App\Models\Order;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Exceptions\InvalidToolParameters;

class OrderLookupTool extends VibeTool
{
    protected string $name = 'order_lookup';

    public static function getMetadata(): array
    {
        return [
            'name' => 'order_lookup',
            'description' => 'Look up order information by order number or customer email',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'order_number' => [
                        'type' => 'string',
                        'description' => 'Order number to look up'
                    ],
                    'customer_email' => [
                        'type' => 'string',
                        'description' => 'Customer email to find orders for'
                    ]
                ],
                'required' => []
            ]
        ];
    }
    
    public function handle(?string $order_number = null, ?string $customer_email = null)
    {
        if (!$order_number && !$customer_email) {
            throw new InvalidToolParameters('Either order_number or customer_email must be provided');
        }
        
        $query = Order::query();
        
        if ($order_number) {
            $query->where('order_number', $order_number);
        }
        
        if ($customer_email) {
            $query->where('customer_email', $customer_email);
        }
        
        $orders = $query->limit(5)->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'date' => $order->created_at->format('Y-m-d'),
                    'status' => $order->status,
                    'total' => $order->total,
                    'items' => $order->items->count(),
                    'shipping_method' => $order->shipping_method,
                    'tracking_number' => $order->tracking_number
                ];
            });
            
        return [
            'count' => $orders->count(),
            'orders' => $orders
        ];
    }
}
```

### Service Provider Registration

**MCPToolServiceProvider.php**
```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use ProjectSaturnStudios\Vibes\TheAgency;
use App\MCPTools\ProductInfoTool;
use App\MCPTools\OrderLookupTool;
use App\MCPTools\TicketCreationTool;
use App\MCPTools\FAQSearchTool;

class MCPToolServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $agency = app(TheAgency::class);
        
        // Register support tools
        $agency->addTools([
            ProductInfoTool::class,
            OrderLookupTool::class,
            TicketCreationTool::class,
            FAQSearchTool::class,
        ]);
    }
}
```

### Controller Implementation

**SupportAgentController.php**
```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SupportAgentController extends Controller
{
    /**
     * Display the support chat interface.
     */
    public function index()
    {
        return view('support.chat', [
            'agent_endpoint' => route('vibes.sse'),
            'messages_endpoint' => route('vibes.messages')
        ]);
    }
    
    /**
     * Handle an incoming user message.
     */
    public function message(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'session_id' => 'required|string'
        ]);
        
        // Connect to your AI provider (e.g., Anthropic)
        $response = Http::withHeaders([
            'x-api-key' => config('services.anthropic.key'),
            'content-type' => 'application/json',
        ])->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-opus-20240229',
            'max_tokens' => 1000,
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $request->message
                ]
            ],
            'tools' => [
                [
                    'name' => 'product_info',
                    'description' => 'Get information about a product by SKU or name'
                ],
                [
                    'name' => 'order_lookup',
                    'description' => 'Look up order information by order number or customer email'
                ],
                [
                    'name' => 'ticket_creation',
                    'description' => 'Create a support ticket for the customer'
                ],
                [
                    'name' => 'faq_search',
                    'description' => 'Search the FAQ knowledge base for relevant information'
                ]
            ]
        ]);
        
        return response()->json($response->json());
    }
}
```

### Frontend Implementation

**chat.blade.php**
```html
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Support</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Include your CSS here -->
</head>
<body>
    <div class="chat-container">
        <div class="chat-messages" id="chat-messages">
            <!-- Messages will appear here -->
        </div>
        <div class="chat-input">
            <form id="chat-form">
                <input type="text" id="message-input" placeholder="Ask a question...">
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    
    <script>
        // SSE connection parameters
        const agentEndpoint = "{{ $agent_endpoint }}";
        const messagesEndpoint = "{{ $messages_endpoint }}";
    </script>
    <script src="{{ asset('js/agent.js') }}"></script>
</body>
</html>
```

**agent.js**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Generate a unique session ID
    const sessionId = 'session-' + Math.random().toString(36).substring(2, 15);
    let messageId = 1;
    
    // Set up SSE connection
    const eventSource = new EventSource(agentEndpoint);
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const messageInput = document.getElementById('message-input');
    
    // Add initial welcome message
    addMessage('assistant', 'Hello! I\'m your support assistant. How can I help you today?');
    
    // Handle SSE events
    eventSource.addEventListener('message', function(event) {
        const data = JSON.parse(event.data);
        console.log('SSE message received:', data);
        
        if (data.event === 'tool_result') {
            // Handle tool result
            console.log('Tool result:', data.payload);
        } else if (data.event === 'response') {
            // Handle AI response
            addMessage('assistant', data.payload.content);
        }
    });
    
    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = messageInput.value.trim();
        if (message) {
            // Add user message to chat
            addMessage('user', message);
            messageInput.value = '';
            
            // Send message to backend
            sendMessage(message, sessionId);
        }
    });
    
    // Send message to backend
    function sendMessage(message, sessionId) {
        const reqId = 'req-' + messageId++;
        
        fetch(messagesEndpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                jsonrpc: '2.0',
                method: 'process_message',
                id: reqId,
                session_id: sessionId,
                params: {
                    message: message
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Message sent successfully:', data);
        })
        .catch(error => {
            console.error('Error sending message:', error);
        });
    }
    
    // Add message to chat UI
    function addMessage(role, content) {
        const messageElement = document.createElement('div');
        messageElement.classList.add('message', role);
        messageElement.textContent = content;
        chatMessages.appendChild(messageElement);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});
```

## Integration with External APIs

This example demonstrates how to create a tool that integrates with external APIs, specifically a weather service.

### Weather Tool Implementation

**WeatherTool.php**
```php
<?php

namespace App\MCPTools;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use ProjectSaturnStudios\Vibes\Exceptions\ToolExecutionError;

class WeatherTool extends VibeTool
{
    protected string $name = 'weather';
    protected string $apiKey;
    protected string $baseUrl = 'https://api.openweathermap.org/data/2.5';
    
    public function __construct()
    {
        $this->apiKey = config('services.openweather.key');
        
        if (empty($this->apiKey)) {
            throw new \RuntimeException('OpenWeather API key is not configured');
        }
    }
    
    public static function getMetadata(): array
    {
        return [
            'name' => 'weather',
            'description' => 'Get current weather information for a location',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'location' => [
                        'type' => 'string',
                        'description' => 'City name, zip code, or location name'
                    ],
                    'units' => [
                        'type' => 'string',
                        'enum' => ['metric', 'imperial'],
                        'description' => 'Units for temperature (metric=Celsius, imperial=Fahrenheit)'
                    ]
                ],
                'required' => ['location']
            ]
        ];
    }
    
    public function handle(string $location, string $units = 'metric')
    {
        // Check cache first (5-minute TTL)
        $cacheKey = "weather:{$location}:{$units}";
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $response = Http::get("{$this->baseUrl}/weather", [
                'q' => $location,
                'units' => $units,
                'appid' => $this->apiKey
            ]);
            
            if ($response->failed()) {
                throw new ToolExecutionError('Failed to retrieve weather data: ' . $response->body());
            }
            
            $weatherData = $response->json();
            
            $result = [
                'location' => $weatherData['name'],
                'country' => $weatherData['sys']['country'],
                'temperature' => $weatherData['main']['temp'],
                'feels_like' => $weatherData['main']['feels_like'],
                'humidity' => $weatherData['main']['humidity'],
                'pressure' => $weatherData['main']['pressure'],
                'wind_speed' => $weatherData['wind']['speed'],
                'wind_direction' => $weatherData['wind']['deg'],
                'conditions' => $weatherData['weather'][0]['main'],
                'description' => $weatherData['weather'][0]['description'],
                'units' => $units,
                'timestamp' => now()->timestamp
            ];
            
            // Cache the result for 5 minutes
            Cache::put($cacheKey, $result, now()->addMinutes(5));
            
            return $result;
        } catch (\Exception $e) {
            throw new ToolExecutionError('Error retrieving weather data: ' . $e->getMessage());
        }
    }
}
```

These examples showcase real-world implementations of Laravel Vibes tools and integrations, helping you understand how to build practical AI-powered features in your Laravel applications.