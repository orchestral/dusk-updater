<?php

namespace Orchestra\DuskUpdater\Tests;

use function Orchestra\DuskUpdater\{
    rename_chromedriver_binary,
    request_context_payload,
    resolve_chromedriver_slug
};
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider chromedriverBinaryFilenameDataProvider
     */
    public function test_it_can_resolve_rename_chromedriver_binary($os, $given, $expected)
    {
        $this->assertSame(rename_chromedriver_binary($given, $os), $expected);
    }

    public static function chromedriverBinaryFilenameDataProvider()
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

    public function test_it_can_resolve_request_context_payload()
    {
        $this->assertSame([], request_context_payload());

        $this->assertSame([
            'http' => ['proxy' => 'tcp://127.0.0.1:9000', 'request_fulluri' => true],
        ], request_context_payload('tcp://127.0.0.1:9000'));

        $this->assertSame([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
        ], request_context_payload(null, true));

        $this->assertSame([
            'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
            'http' => ['proxy' => 'tcp://127.0.0.1:9000', 'request_fulluri' => true],
        ], request_context_payload('tcp://127.0.0.1:9000', true));
    }

    /**
     * @dataProvider resolveChromeDriverSlugDataProvider
     */
    public function test_it_can_resolve_chromedriver_slug($version, $os, $expected)
    {
        $this->assertSame($expected, resolve_chromedriver_slug($version, $os));
    }

    public static function resolveChromeDriverSlugDataProvider()
    {
        yield ['115.0', 'linux', 'linux64'];
        yield ['113.0', 'linux', 'linux64'];
        yield ['105.0', 'linux', 'linux64'];

        yield ['115.0', 'mac', 'mac-x64'];
        yield ['113.0', 'mac', 'mac64'];
        yield ['105.0', 'mac', 'mac64'];

        yield ['115.0', 'mac-intel', 'mac-x64'];
        yield ['113.0', 'mac-intel', 'mac64'];
        yield ['105.0', 'mac-intel', 'mac64'];

        yield ['115.0', 'mac-arm', 'mac-arm64'];
        yield ['113.0', 'mac-arm', 'mac_arm64'];
        yield ['105.0', 'mac-arm', 'mac64_m1'];

        yield ['115.0', 'win', 'win32'];
        yield ['113.0', 'win', 'win32'];
        yield ['105.0', 'win', 'win32'];
    }
}
