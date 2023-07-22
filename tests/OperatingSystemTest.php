<?php

namespace Orchestra\DuskUpdater\Tests;

use Orchestra\DuskUpdater\OperatingSystem;
use PHPUnit\Framework\TestCase;

class OperatingSystemTest extends TestCase
{
    public function test_it_matches_possible_os()
    {
        $this->assertTrue(in_array(OperatingSystem::id(), OperatingSystem::all()));
    }

    public function test_it_has_correct_os()
    {
        $this->assertSame([
            'linux',
            'mac',
            'mac-intel',
            'mac-arm',
            'win',
        ], OperatingSystem::all());
    }
}
