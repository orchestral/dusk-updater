<?php

namespace Orchestra\DuskUpdater\Tests;

use function Orchestra\DuskUpdater\chromedriver_binary_filename;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider binaryFileDataProvider
     */
    public function test_it_can_resolve_correct_filename($os, $given, $expected)
    {
        $this->assertSame(chromedriver_binary_filename($given, $os), $expected);
    }

    public static function binaryFileDataProvider()
    {
        yield ['linux', 'chromedriver', 'chromedriver-linux'];
        yield ['mac-intel', 'chromedriver', 'chromedriver-mac-intel'];
        yield ['mac-arm', 'chromedriver', 'chromedriver-mac-arm'];
        yield ['win32', 'chromedriver.exe', 'chromedriver-win32.exe'];

        yield ['linux', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver', 'chromedriver-linux'];
        yield ['mac-intel', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver', 'chromedriver-mac-intel'];
        yield ['mac-arm', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver', 'chromedriver-mac-arm'];
        yield ['win32', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver.exe', 'chromedriver-win32.exe'];
    }
}
