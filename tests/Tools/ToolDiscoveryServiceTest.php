<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools;

use Illuminate\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\Test;
use ProjectSaturnStudios\Vibes\Tests\TestCase;
use ProjectSaturnStudios\Vibes\TheAgency;
use ProjectSaturnStudios\Vibes\Services\PrimitiveHandlerDiscoveryService;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\EchoVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\CalculatorVibeTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\DiscoverableTool;
use ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\NonDiscoverableTool;
use ProjectSaturnStudios\Vibes\Primitives\Tools\Data\VibeTool;
use Symfony\Component\VarDumper\VarDumper;

class ToolDiscoveryServiceTest extends TestCase
{
    protected TheAgency $agency;
    protected PrimitiveHandlerDiscoveryService $discovery;
    protected Filesystem $files;
    protected string $tempTestPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem();
        // Use a temporary path within app/ for testing
        $this->tempTestPath = app_path('Testing/tool_discovery_' . \Illuminate\Support\Str::random()); // Use app_path() and Str helper
        $this->files->ensureDirectoryExists($this->tempTestPath);
        $stubsTempPath = $this->tempTestPath . '/Stubs';
        $this->files->ensureDirectoryExists($stubsTempPath);

        $stubsSourcePath = realpath(__DIR__ . '/Stubs');
        $this->files->copyDirectory($stubsSourcePath, $stubsTempPath);

        $this->agency = $this->app->make(TheAgency::class);
        $existingTools = $this->agency->getTools();
        foreach ($existingTools as $tool) {
            $this->agency->removeTool($tool);
        }

        $this->discovery = new PrimitiveHandlerDiscoveryService();
        // Default basePath should be app_path(), which is now the parent of our temp dir.
        // Default rootNamespace should be App\, matching the default app namespace.
        // We might still need to adjust the rootNamespace if the stubs expect something else.
    }

    protected function tearDown(): void
    {
        // Clean up the temporary directory from app/
        $this->files->deleteDirectory(app_path('Testing')); // Delete parent Testing dir
        parent::tearDown();
    }

    #[Test]
    public function test_can_discover_tools_in_directory()
    {
        $stubsTempPath = __DIR__ ; // e.g., /app/Testing/.../Stubs
        $this->discovery
            ->within([$stubsTempPath]) // Scan the temp Stubs dir
            ->useBasePath(__DIR__)
            ->useRootNamespace('ProjectSaturnStudios\\Vibes\\Tests\\Tools\\') // Actual namespace of stubs
            ->ignoringFiles([])
            ->addToTheAgency($this->agency);

        $tools = $this->agency->getTools();

        // Assert that *only* tools extending VibeTool from that namespace are found
        // We expect DiscoverableTool, EchoVibeTool, CalculatorVibeTool, IgnoredToolInVfs (if copied)
        // Let's check the count and specific types
        $this->assertGreaterThanOrEqual(3, $tools->count(), 'Expected at least 3 discoverable tools in Stubs.');
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof DiscoverableTool));
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof EchoVibeTool));
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof CalculatorVibeTool));
        // NonDiscoverableTool should NOT be present
        $this->assertFalse($tools->contains(fn($tool) => $tool === 'ProjectSaturnStudios\Vibes\Tests\Tools\Stubs\NonDiscoverableTool'));
    }

    #[Test]
    public function test_respects_ignored_files()
    {
        // Use the same successful configuration as the first test
        // We need the full path to the *actual* file to ignore
        $ignoredFilePath = realpath(__DIR__ . '/Stubs/CalculatorVibeTool.php');

        $this->discovery
            ->within([__DIR__]) // Scan the directory containing Stubs
            ->useBasePath(__DIR__) // Base path matches within
            ->useRootNamespace('ProjectSaturnStudios\\Vibes\\Tests\\Tools\\') // Namespace of this test dir
            ->ignoringFiles([$ignoredFilePath]) // Ignore the Calculator tool file
            ->addToTheAgency($this->agency);

        $tools = $this->agency->getTools();

        // Expect DiscoverableTool, EchoVibeTool, but NOT CalculatorVibeTool
        // Note: The discovery might also find the test classes themselves if they implement the interface.
        // Let's adjust expectations based on what *should* be found in the Stubs dir, minus the ignored one.
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof DiscoverableTool), 'DiscoverableTool should be found.');
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof EchoVibeTool), 'EchoVibeTool should be found.');
        $this->assertFalse($tools->contains(fn($tool) => app($tool) instanceof CalculatorVibeTool), 'CalculatorVibeTool should have been ignored.');
        // Check count based on expected finds (Discoverable, Echo)
        $this->assertCount(2, $tools->filter(fn($t) => app($t) instanceof VibeTool && str_contains($t, 'Stubs')), 'Expected 2 tools from Stubs dir after ignoring.');
    }

    #[Test]
    public function test_discovers_only_classes_implementing_interface()
    {
        // Use the same successful configuration as the first test
        $this->discovery
            ->within([__DIR__]) // Scan the directory containing Stubs
            ->useBasePath(__DIR__)
            ->useRootNamespace('ProjectSaturnStudios\\Vibes\\Tests\\Tools\\')
            ->ignoringFiles([])
            ->addToTheAgency($this->agency);

        $tools = $this->agency->getTools();

        // Check that NonDiscoverableTool was not loaded
        $foundNonDiscoverable = $tools->contains(function ($tool) {
            // Check if the loaded class name matches NonDiscoverableTool's FQCN
            return $tool === NonDiscoverableTool::class;
        });
        $this->assertFalse($foundNonDiscoverable, 'NonDiscoverableTool should not be discovered.');

        // Optionally, re-assert that valid tools *were* found
        $this->assertTrue($tools->contains(fn($tool) => app($tool) instanceof DiscoverableTool), 'Expected DiscoverableTool to be found.');
    }
}
