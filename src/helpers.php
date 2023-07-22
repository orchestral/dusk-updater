<?php

namespace Orchestra\DuskUpdater;

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
