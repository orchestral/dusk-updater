<?php

namespace Orchestra\DuskUpdater;

use InvalidArgumentException;

/**
 * Define ChromeDriver binary filename.
 */
function chromedriver_binary_filename(string $binary, string $operatingSystem): string
{
    return strpos($binary, DIRECTORY_SEPARATOR) > 0
        ? array_reverse(explode(DIRECTORY_SEPARATOR, str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary), 2))[0]
        : str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary);
}

function resolve_chromedriver_slug($version, string $operatingSystem): string
{
    $slugs = [
        'linux' => 'linux64',
        'mac' => 'mac-x64',
        'mac-intel' => 'mac-x64',
        'mac-arm' => 'mac-arm64',
        'win' => 'win32',
        'win64' => 'win64',
    ];

    if (is_null($slug = $slugs[$operatingSystem])) {
        throw new InvalidArgumentException("Unable to find slug for Operating System [{$operatingSystem}]");
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
 * Define the stream context payload
 *
 * @param  string|null  $proxy
 */
function request_context_payload($proxy = null, bool $without_ssl_verification = false): array
{
    $streamOptions = [];

    if ($without_ssl_verification === true) {
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
