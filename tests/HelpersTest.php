<?php

namespace Orchestra\DuskUpdater\Tests;

use PHPUnit\Framework\TestCase;

use function Orchestra\DuskUpdater\rename_chromedriver_binary;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider chromedriverBinaryFilenameDataProvider
     */
    public function test_it_can_rename_chromedriver_binary($os, $given, $expected)
    {
        $this->assertSame(rename_chromedriver_binary($given, $os), $expected);
    }

    public static function chromedriverBinaryFilenameDataProvider()
    {
        yield ['linux', 'chromedriver', 'chromedriver-linux'];
        yield ['mac-intel', 'chromedriver', 'chromedriver-mac-intel'];
        yield ['mac-arm', 'chromedriver', 'chromedriver-mac-arm'];
        yield ['win', 'chromedriver.exe', 'chromedriver-win.exe'];

        yield ['linux', 'chromedriver-115/chromedriver', 'chromedriver-linux'];
        yield ['mac-intel', 'chromedriver-115/chromedriver', 'chromedriver-mac-intel'];
        yield ['mac-arm', 'chromedriver-115/chromedriver', 'chromedriver-mac-arm'];
        yield ['win', 'chromedriver-115/chromedriver.exe', 'chromedriver-win.exe'];
        yield ['win', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver.exe', 'chromedriver-win.exe'];
    }
}
