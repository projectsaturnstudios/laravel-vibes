<?php

namespace ProjectSaturnStudios\Vibes\Tests\Tools\Stubs;

class NonDiscoverableTool
{
    protected string $name = 'non-discoverable';
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function doSomething(): string
    {
        return 'This class does not implement the required interface';
    }
} 