<?php

namespace Orchestra\DuskUpdater;

/**
 * Define ChromeDriver binary filename.
 */
function chromedriver_binary_filename(string $binary, string $os): string
{
    return strpos($binary, DIRECTORY_SEPARATOR) > 0
        ? array_reverse(explode(DIRECTORY_SEPARATOR, str_replace('chromedriver', 'chromedriver-'.$os, $binary), 2))[0]
        : str_replace('chromedriver', 'chromedriver-'.$os, $binary);
}

/**
 * Define the stream context payload
 *
 * @param  string|null  $proxy
 */
function request_context_payload($proxy = null, bool $without_ssl_verification = false): array
{
    $streamOptions = [];

    if ($without_ssl_verification === false) {
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
