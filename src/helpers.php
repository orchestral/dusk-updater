<?php

namespace Orchestra\DuskUpdater;

use Illuminate\Support\Str;

/**
 * Rename exported ChromeDriver binary filename.
 */
function rename_chromedriver_binary(string $binary, string $operatingSystem): string
{
    return Str::contains($binary, DIRECTORY_SEPARATOR)
        ? Str::after(str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary), DIRECTORY_SEPARATOR)
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
