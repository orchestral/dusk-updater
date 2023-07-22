<?php

namespace Orchestra\DuskUpdater\Tests;

use function Orchestra\DuskUpdater\chromedriver;
use function Orchestra\DuskUpdater\rename_chromedriver_binary;
use function Orchestra\DuskUpdater\request_context_payload;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    /**
     * @dataProvider resolveChromeDriverDataProvider
     */
    public function test_it_can_resolve_chromedriver($os, $expected)
    {
        $this->assertSame($expected, chromedriver($os));
    }

    public function test_it_cant_resolve_invalid_chromedriver()
    {
        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Unable to find ChromeDriver for Operating System [window_os]');

        chromedriver('window_os');
    }

    public static function resolveChromeDriverDataProvider()
    {
        yield ['linux', 'chromedriver-linux'];
        yield ['mac', 'chromedriver-mac'];
        yield ['mac-intel', 'chromedriver-mac-intel'];
        yield ['mac-arm', 'chromedriver-mac-arm'];
        yield ['win', 'chromedriver-win.exe'];
    }

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

        yield ['linux', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver', 'chromedriver-linux'];
        yield ['mac-intel', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver', 'chromedriver-mac-intel'];
        yield ['mac-arm', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver', 'chromedriver-mac-arm'];
        yield ['win', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver.exe', 'chromedriver-win.exe'];
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
}
