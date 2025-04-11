<?php

namespace ProjectSaturnStudios\Vibes\Tests;

use PHPUnit\Framework\Attributes\Test;

class BasicTest extends TestCase
{
    #[Test]
    public function it_passes_a_basic_assertion()
    {
        $this->assertTrue(true);
    }
} 