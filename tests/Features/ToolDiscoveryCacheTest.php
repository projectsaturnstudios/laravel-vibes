<?php

namespace ProjectSaturnStudios\Vibes\Tests\Features;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;
use ProjectSaturnStudios\Vibes\Services\PrimitiveHandlerDiscoveryService;
use Spatie\EventSourcing\Support\Composer;

class ToolDiscoveryCacheTest extends TestCase
{
    protected Filesystem $files;
    protected string $cachePath;
    protected string $cacheFile;
    protected TheAgency $agency;

    protected function setUp(): void
    {
        parent::setUp();
        $this->files = new Filesystem();
        $this->cachePath = config('vibes.service_info.cache_path');
        $this->files->ensureDirectoryExists($this->cachePath);
        $this->cacheFile = $this->cachePath . '/vibes.php';

        $this->files->delete($this->cacheFile);
        $this->agency = $this->app->make(TheAgency::class);
        $existingTools = $this->agency->getTools();
        foreach ($existingTools as $tool) {
            $this->agency->removeTool($tool);
        }
    }

    protected function tearDown(): void
    {
        $this->files->delete($this->cacheFile);
        parent::tearDown();
    }

    protected function simulateDiscoveryOrCacheLoad(): void
    {
        $cachedPrimitiveHandlers = null;
        if (file_exists($this->cacheFile)) {
            $cachedPrimitiveHandlers = require $this->cacheFile;
        }

        if (! is_null($cachedPrimitiveHandlers)) {
            $this->agency->addPrimitiveHandlers($cachedPrimitiveHandlers);
        } else {
            $discoveryBasePath = realpath(__DIR__ . '/../Tools');
            $discoveryRootNamespace = 'ProjectSaturnStudios\\Vibes\\Tests\\Tools\\';

            (new PrimitiveHandlerDiscoveryService())
                ->within([$discoveryBasePath])
                ->useBasePath($discoveryBasePath)
                ->useRootNamespace($discoveryRootNamespace)
                ->ignoringFiles(Composer::getAutoloadedFiles(base_path('composer.json')))
                ->addToTheAgency($this->agency);
        }
    }

    #[Test]
    public function test_discovery_uses_cache_file_when_present()
    {
        $cachedHandlers = [ EchoVibeTool::class ];
        $cacheContent = '<?php return ' . var_export($cachedHandlers, true) . ';';
        $this->files->put($this->cacheFile, $cacheContent);
        $this->assertTrue($this->files->exists($this->cacheFile));

        $this->simulateDiscoveryOrCacheLoad();

        $tools = $this->agency->getTools();
        $this->assertCount(1, $tools, 'Only handlers from cache should be loaded.');
        $this->assertSame(EchoVibeTool::class, $tools->first());
        $this->assertNull($this->agency->getTool('calculator'), 'Non-cached tool should not be loaded.');
    }

    #[Test]
    public function test_discovery_runs_when_cache_file_is_absent()
    {
        $this->assertFalse($this->files->exists($this->cacheFile));

        $this->simulateDiscoveryOrCacheLoad();

        $tools = $this->agency->getTools();
        $this->assertGreaterThanOrEqual(3, $tools->count(), 'Expected tools to be discovered when cache is absent.');
        $this->assertNotNull($this->agency->getTool('echo'), 'Echo tool should be discovered.');
        $this->assertNotNull($this->agency->getTool('calculator'), 'Calculator tool should be discovered.');
    }
}
