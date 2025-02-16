<?php

use function Orchestra\DuskUpdater\rename_chromedriver_binary;

it('can rename chromedriver binary', function (string $operatingSystem, string $given, string $expected) {
    expect(rename_chromedriver_binary($given, $operatingSystem))->toBe($expected);
})->with([
    ['linux', 'chromedriver', 'chromedriver-linux'],
    ['mac-intel', 'chromedriver', 'chromedriver-mac-intel'],
    ['mac-arm', 'chromedriver', 'chromedriver-mac-arm'],
    ['win', 'chromedriver.exe', 'chromedriver-win.exe'],

    ['linux', 'chromedriver-115/chromedriver', 'chromedriver-linux'],
    ['mac-intel', 'chromedriver-115/chromedriver', 'chromedriver-mac-intel'],
    ['mac-arm', 'chromedriver-115/chromedriver', 'chromedriver-mac-arm'],
    ['win', 'chromedriver-115/chromedriver.exe', 'chromedriver-win.exe'],
    ['win', 'chromedriver-115'.DIRECTORY_SEPARATOR.'chromedriver.exe', 'chromedriver-win.exe'],
]);
