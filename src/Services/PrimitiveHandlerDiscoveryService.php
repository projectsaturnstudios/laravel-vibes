<?php

namespace ProjectSaturnStudios\Vibes\Services;

use ReflectionClass;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use ProjectSaturnStudios\Vibes\TheAgency;
use Symfony\Component\Finder\SplFileInfo;
use ProjectSaturnStudios\Vibes\Contracts\PrimitiveHandler;
use Symfony\Component\VarDumper\VarDumper;

/**
 * Service responsible for discovering and registering primitive handlers.
 *
 * This class scans specified directories for classes that implement the
 * PrimitiveHandler interface and adds them to TheAgency instance.
 * It allows configuration of base paths, root namespaces, and ignored files.
 *
 * @package ProjectSaturnStudios\Vibes\Services
 * @since 0.4.0
 */
class PrimitiveHandlerDiscoveryService
{
    /**
     * @var array Directories to scan for primitive handlers.
     */
    protected array $directories = [];

    /**
     * @var string Base path used for resolving class names from file paths.
     */
    protected string $basePath = '';

    /**
     * @var string Root namespace to prepend to discovered class names.
     */
    protected string $rootNamespace = '';

    /**
     * @var array List of file paths to ignore during discovery.
     */
    protected array $ignoredFiles = [];

    /**
     * Create a new PrimitiveHandlerDiscoveryService instance.
     *
     * Initializes the base path to the application's path.
     */
    public function __construct()
    {
        $this->basePath = app()->path();
    }

    /**
     * Set the directories to scan for primitive handlers.
     *
     * @param array $directories An array of directory paths.
     * @return self
     */
    public function within(array $directories): self
    {
        $this->directories = $directories;

        return $this;
    }

    /**
     * Set the base path for resolving class names.
     *
     * @param string $basePath The base path (e.g., app path or base path).
     * @return self
     */
    public function useBasePath(string $basePath): self
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * Set the root namespace to prepend to discovered class names.
     *
     * @param string $rootNamespace The root namespace (e.g., 'App\\').
     * @return self
     */
    public function useRootNamespace(string $rootNamespace): self
    {
        $this->rootNamespace = $rootNamespace;

        return $this;
    }

    /**
     * Set the list of files to ignore during discovery.
     *
     * @param array $ignoredFiles An array of full file paths to ignore.
     * @return self
     */
    public function ignoringFiles(array $ignoredFiles): self
    {
        $this->ignoredFiles = $ignoredFiles;

        return $this;
    }

    /**
     * Discover primitive handlers in the configured directories and add them to TheAgency.
     *
     * Scans files, filters for instantiable classes implementing PrimitiveHandler,
     * and adds them to the provided TheAgency instance.
     *
     * @param TheAgency $agency The TheAgency instance to add handlers to.
     * @return void
     */
    public function addToTheAgency(TheAgency $agency)
    {
        if (empty($this->directories)) {
            return;
        }

        $files = (new Finder())->files()->in($this->directories);

        collect($files)
            ->reject(fn (SplFileInfo $file) => in_array($file->getPathname(), $this->ignoredFiles))
            ->map(fn (SplFileInfo $file) => $this->fullQualifiedClassNameFromFile($file))
            ->filter(fn (string $primitiveHandlerClass) => $this->isValidPrimitiveHandler($primitiveHandlerClass))
            ->pipe(function (Collection $primitiveHandlers) use ($agency) {
                $agency->addPrimitiveHandlers($primitiveHandlers->toArray());
            });
    }

    /**
     * Determine the fully qualified class name from a file path.
     *
     * @param SplFileInfo $file The file info object.
     * @return string The fully qualified class name.
     */
    private function fullQualifiedClassNameFromFile(SplFileInfo $file): string
    {
        $class = trim(Str::replaceFirst($this->basePath, '', $file->getRealPath()), DIRECTORY_SEPARATOR);

        $class = str_replace(
            [DIRECTORY_SEPARATOR, 'App\\'],
            ['\\', app()->getNamespace()],
            ucfirst(Str::replaceLast('.php', '', $class))
        );

        return $this->rootNamespace.$class;
    }

    /**
     * Check if a class name represents a valid, instantiable primitive handler.
     *
     * @param string $className The class name to check.
     * @return bool True if it's a valid primitive handler, false otherwise.
     */
    private function isValidPrimitiveHandler(string $className): bool
    {
        if (!class_exists($className)) {
            return false;
        }

        $reflection = new ReflectionClass($className);

        return $reflection->isInstantiable() && $reflection->implementsInterface(PrimitiveHandler::class);
    }
}
