<?php

namespace Orchestra\DuskUpdater;

use InvalidArgumentException;

/**
 * ChromeDriver name by Operating System.
 *
 * @throws \InvalidArgumentException
 */
function chromedriver(string $operatingSystem): string
{
    $filenames = [
        'linux' => 'chromedriver-linux',
        'mac' => 'chromedriver-mac',
        'mac-intel' => 'chromedriver-mac-intel',
        'mac-arm' => 'chromedriver-mac-arm',
        'win' => 'chromedriver-win.exe',
    ];

    if (is_null($filename = ($filenames[$operatingSystem] ?? null))) {
        throw new InvalidArgumentException("Unable to find ChromeDriver for Operating System [{$operatingSystem}]");
    }

    return $filename;
}

/**
 * Determine ChromeDriver slug.
 *
 * @throws \InvalidArgumentException
 */
function chromedriver_slug(string $version, string $operatingSystem): string
{
    $slugs = [
        'linux' => 'linux64',
        'mac' => 'mac-x64',
        'mac-intel' => 'mac-x64',
        'mac-arm' => 'mac-arm64',
        'win' => 'win32',
    ];

    if (is_null($slug = ($slugs[$operatingSystem] ?? null))) {
        throw new InvalidArgumentException("Unable to find ChromeDriver slug for Operating System [{$operatingSystem}]");
    }

    if (version_compare($version, '115.0', '<')) {
        if ($slug === 'mac-arm64') {
            return version_compare($version, '106.0.5249', '<') ? 'mac64_m1' : 'mac_arm64';
        } elseif ($slug === 'mac-x64') {
            return 'mac64';
        }
    }

    return $slug;
}

/**
 * Rename exported ChromeDriver binary filename.
 */
function rename_chromedriver_binary(string $binary, string $operatingSystem): string
{
    return strpos($binary, DIRECTORY_SEPARATOR) > 0
        ? array_reverse(explode(DIRECTORY_SEPARATOR, str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary), 2))[0]
        : str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary);
}

/**
 * Define the stream context payload
 *
 * @param  string|null  $proxy
 */
function request_context_payload($proxy = null, bool $withoutSslVerification = false): array
{
    $streamOptions = [];

    if ($withoutSslVerification === true) {
        $streamOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];
    }

    if (! empty($proxy)) {
        $streamOptions['http'] = ['proxy' => $proxy, 'request_fulluri' => true];
    }

    return $streamOptions;
}
