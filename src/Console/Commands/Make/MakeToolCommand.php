<?php

namespace ProjectSaturnStudios\Vibes\Console\Commands\Make;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Attribute\AsCommand;

//#[AsCommand('make:tool ', 'Create a new MCPTool class')]
class MakeToolCommand extends GeneratorCommand
{
    protected $name = 'make:tool';

    protected $description = "Create a new MCPTool class";

    protected $type = 'Agent tool';

    public function handle(): void
    {
        parent::handle();

        $this->rewriteToSyncTool();
    }

    protected function rewriteToSyncTool() : void
    {
        $name = $this->qualifyClass($this->getNameInput());

        $us_name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this->getNameInput()));

        $path = $this->getPath($name);

        $content = file_get_contents($path);

        $content = str_replace('{{ tool_name }}', $us_name, $content);
        $content = str_replace('{{ description }}', 'The Agent will read this when getting the list.', $content);

        file_put_contents($path, $content);
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/tool.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     */
    protected function resolveStubPath(string $stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__ . "/../../../..{$stub}";
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\AgentTools';
    }
}
