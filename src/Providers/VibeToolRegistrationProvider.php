<?php
namespace ProjectSaturnStudios\Vibes\Providers;


use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use ProjectSaturnStudios\Vibes\Attributes\MCPTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\BuiltInTool;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Contracts\SampleTool;

/**
 * Service provider responsible for registering MCP tools with TheAgency.
 *
 * This provider handles the registration of various types of tools including sample tools,
 * development tools, tools explicitly listed in the configuration, and tools with specific
 * attributes. Each registration method is responsible for a different category of tools.
 *
 * @package ProjectSaturnStudios\Vibes\Providers
 * @since 0.4.0
 */
class VibeToolRegistrationProvider extends ServiceProvider
{
    /**
     * Register services and tools with the application.
     *
     * This method orchestrates the registration of all tool types by calling
     * specialized registration methods for each category.
     *
     * @return void
     */
    public function register(): void
    {
        $this->registerSampleTools();
        $this->registerDevTools();
        $this->registerToolsListedInConfig();
        $this->registerVagrantToolsWithAttribute();
        // @todo - attempt to register (addToAgency)  sample tools, if enabled in the config
        // @todo - attempt to register (addToAgency)  dev tools, if enabled in the config
        // @todo - attempt to register (addToAgency)  tools, et al. that are explicitly defined in the config
    }

    /**
     * Register sample tools if enabled in the configuration.
     *
     * Checks if sample tools are enabled in the configuration and registers
     * any classes that implement the SampleTool interface.
     *
     * @return void
     */
    public function registerSampleTools(): void
    {
        if(config('vibes.register_sample_tools'))
        {
            collect(config('vibes.samples.tools'))->each(function ($class) {
                $question = (new \ReflectionClass($class));

                if($question->implementsInterface(SampleTool::class))
                {
                    /** @var TheAgency $agency */
                    $agency = app('the-agency');
                    $agency->addTool($class);
                }
            });
        }
    }

    /**
     * Register built-in development tools if enabled in the configuration.
     *
     * Checks if development tools are enabled in the configuration and registers
     * any classes that implement the BuiltInTool interface.
     *
     * @return void
     */
    public function registerDevTools(): void
    {
        if(config('vibes.register_dev_tools'))
        {
            collect(config('vibes.built-in.tools'))->each(function ($class) {
                $question = (new \ReflectionClass($class));

                if($question->implementsInterface(BuiltInTool::class))
                {
                    /** @var TheAgency $agency */
                    $agency = app('the-agency');
                    $agency->addTool($class);
                }
            });
        }
    }

    /**
     * Register tools explicitly listed in the configuration file.
     *
     * Iterates through the tools defined in the configuration and
     * registers those that have the MCPTool attribute.
     *
     * @return void
     */
    public function registerToolsListedInConfig(): void
    {
            collect(config('vibes.tools'))->each(function ($class) {
                $attr = ((new \ReflectionClass($class)))->getAttributes(name: MCPTool::class);

                if($attr)
                {
                    /** @var TheAgency $agency */
                    $agency = app('the-agency');
                    $agency->addTool($class);
                }
            });
    }

    /**
     * Register tools with the MCPTool attribute found in the application.
     *
     * Currently unimplemented. This method is intended to auto-discover
     * and register tools with the MCPTool attribute throughout the application.
     *
     * @return void
     */
    public function registerVagrantToolsWithAttribute(): void
    {

    }
}
