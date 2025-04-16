<?php

namespace ProjectSaturnStudios\Vibes\Providers;

use Spatie\LaravelPackageTools\Package;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Support\Composer;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use ProjectSaturnStudios\Vibes\Services\MCPEventSubscriber;
use ProjectSaturnStudios\Vibes\Actions\AgentMethods\AgentMethod;
use ProjectSaturnStudios\Vibes\Console\Commands\Make\MakeToolCommand;
use ProjectSaturnStudios\Vibes\Services\PrimitiveHandlerDiscoveryService;

/**
 * Service provider for bootstrapping the Laravel Vibes package.
 *
 * This provider is responsible for setting up Laravel Vibes in a Laravel application.
 * It handles configuration loading, route registration, middleware setup, service container
 * bindings, and discovery of primitive handlers.
 *
 * @package ProjectSaturnStudios\Vibes\Providers
 * @since 0.4.0
 */
class LaravelVibesServiceProvider extends PackageServiceProvider
{
    /**
     * @var array Artisan commands provided by the package.
     */
    protected array $commands = [
        MakeToolCommand::class,
    ];

    /**
     * @var array Console commands provided by the package.
     */
    protected array $cli_commands = [];

    /**
     * @var array Configuration files to be merged with the application's config.
     */
    protected array $merge_configs = [
        'cors.vibes' => '/../config/cors.php',
        'vibes.samples' => '/../config/vibes-sample-primitives.php',
        'vibes.built-in' => '/../config/vibes-built-in-primitives.php',
    ];

    /**
     * @var array Configuration files that can be published standalone.
     */
    protected array $standalone_configs = [
        'vibes' => '/../../config/vibes.php',
        'cors.vibes' => '/../../config/cors.php',
    ];

    /**
     * Configure the Laravel Vibes package.
     *
     * Sets up the package name, commands, config files, and routes.
     * This is called automatically by the parent PackageServiceProvider.
     *
     * @param Package $package The package instance to configure.
     * @return void
     */
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-vibes')
            //->hasConsoleCommands($this->cli_commands)
            ->hasConfigFile('vibes')
            ->hasRoute('agent-endpoints')
            ->hasCommands($this->commands)
        ;
    }

    /**
     * Perform actions after the package is registered but before it's booted.
     *
     * Merges additional package configurations with the application's config.
     *
     * @return void
     */
    public function packageRegistered(): void
    {
        if(app()->runningInConsole())
        {
            $this->publishStandaloneConfigs();
        }
    }

    /**
     * Perform actions after the package is fully booted.
     *
     * Sets up publishing configurations, registers middleware and singletons,
     * discovers primitive handlers, and registers the events service provider.
     *
     * @return void
     */
    public function packageBooted(): void
    {

        $this->mergeConfigs();
        $this->registerMiddleware();
        $this->registerSingletons();
        $this->app->register(VibeEventsServiceProvider::class);

        $this->discoverPrimitiveHandlers();
        $this->registerMCPMethods();

        $this->publishStubs();
    }

    /**
     * Discover and register primitive handlers for TheAgency.
     *
     * This method either loads cached handlers or discovers them dynamically
     * using the PrimitiveHandlerDiscoveryService.
     *
     * @return void
     */
    protected function discoverPrimitiveHandlers() :  void
    {
        $agency = app(TheAgency::class);

        $cachedPrimitiveHandlers = $this->getCachedPrimitiveHandlers();

        if (! is_null($cachedPrimitiveHandlers)) {
            $agency->addPrimitiveHandlers($cachedPrimitiveHandlers);
            return;
        }

        (new PrimitiveHandlerDiscoveryService)
            ->within(config('vibes.auto_discover_all_primitives'))
            ->useBasePath(config('vibes.auto_discover_base_path', base_path()))
            ->ignoringFiles(Composer::getAutoloadedFiles(base_path('composer.json')))
            ->addToTheAgency($agency);
        $this->app->register(VibeToolRegistrationProvider::class);

    }

    /**
     * Get cached primitive handlers if available.
     *
     * Loads handlers from the cached file to avoid dynamic discovery
     * every time the application boots.
     *
     * @return array|null Array of cached handlers or null if cache doesn't exist.
     */
    protected function getCachedPrimitiveHandlers(): ?array
    {
        $cachedPrimitveHandlersPath = config('vibes.service_info.cache_path').'/vibes.php';

        if (! file_exists($cachedPrimitveHandlersPath)) {
            return null;
        }

        return require $cachedPrimitveHandlersPath;
    }

    /**
     * Merge package configurations with the application's config.
     *
     * @return void
     */
    public function mergeConfigs(): void
    {
        foreach ($this->merge_configs as $key => $config_path) {
            $this->mergeConfigFrom($this->package->basePath($config_path), $key);
        }
    }

    /**
     * Publish standalone configuration files.
     *
     * Makes configurations available for publishing with the vendor:publish command
     * when the application is running in console mode.
     *
     * @return void
     */
    protected function publishStandaloneConfigs() : void
    {
        foreach($this->standalone_configs as $key => $config) {
            $this->publishes([
                __DIR__ . $config => $this->app->configPath($key.'.php'),
            ], $key);
        }
    }

    /**
     * Register singleton services in the container.
     *
     * Binds TheAgency, stream providers, repositories, and the event subscriber
     * to the service container as singletons.
     *
     * @return void
     */
    public function registerSingletons(): void
    {
        app()->singleton(TheAgency::class, fn() => TheAgency::make());
        $this->app->alias(TheAgency::class, 'the-agency');

        $this->app->singleton('vibe-stream', config('vibes.sse.stream_provider'));
        $this->app->singleton('vibe-tools', config('vibes.tool_repository'));

        //$this->app->singleton(ResourceRepository::class, config('vibes.resource_repository'));
        //$this->app->singleton(PromptRepository::class, config('vibes.prompt_repository'));
        //$this->app->singleton(SamplesRepository::class, config('vibes.sample_repository'));
        //$this->app->singleton(RootsRepository::class, config('vibes.root_repository'));

        $this->app->singleton(
            MCPEventSubscriber::class,
            fn () => new MCPEventSubscriber(
                config('vibes.tool_repository'),
            //config('vibes.resource_repository'),
            //config('vibes.prompt_repository'),
            //config('vibes.sample_repository'),
            //config('vibes.root_repository'),
            )
        );
    }

    /**
     * Register middleware for the MCP agent routes.
     *
     * Sets up the 'mcp-agent' middleware group from configuration.
     *
     * @return void
     */
    protected function registerMiddleware() : void
    {
        $router = $this->app['router'];
        $router->middlewareGroup('mcp-agent', config('vibes.middleware', ['api']));
    }

    protected function registerMCPMethods() : void
    {
        $method_classes = config('vibes.invocable_methods');
        foreach($method_classes as $method => $class)
        {
            /** @var AgentMethod $action */
            $action = app($class);
            $this->app->bind($action->method_name(), fn() => $action);
        }
    }

    protected function publishStubs() : void
    {
        $this->publishes([
            __DIR__ . '/../../stubs/tool.stub' => base_path('stubs/tool.stub'),
        ], 'stubs');
    }
}
