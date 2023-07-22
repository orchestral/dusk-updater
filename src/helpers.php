<?php

namespace Orchestra\DuskUpdater;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;

/**
 * Rename exported ChromeDriver binary filename.
 */
function rename_chromedriver_binary(string $binary, string $operatingSystem): string
{
    return mb_strpos($binary, DIRECTORY_SEPARATOR) > 0
        ? array_reverse(explode(DIRECTORY_SEPARATOR, str_replace('chromedriver', 'chromedriver-'.$os, $binary), 2))[0]
        : str_replace('chromedriver', 'chromedriver-'.$os, $binary);
}

/**
 * Download from URL.
 *
 * @param  string|null  $proxy
 *
 * @throws \Exception
 */
function download(string $url, string $destination, $proxy = null, bool $verifySsl = true): void
{
    $client = new Client();

    $resource = Utils::tryFopen($destination, 'w');

    $response = $client->get($url, array_merge([
        'sink' => $resource,
        'verify' => $verifySsl,
    ], array_filter([
        'proxy' => $proxy,
    ])));

    if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
        throw new Exception("Unable to download from [{$url}]");
    }
}

/**
 * Get contents from URL.
 *
 * @param  string|null  $proxy
 *
 * @throws \Exception
 */
function fetch(string $url, $proxy = null, bool $verifySsl = true): string
{
    $client = new Client();

    $response = $client->get($url, array_merge([
        'verify' => $verifySsl,
    ], array_filter([
        'proxy' => $proxy,
    ])));

    if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
        throw new Exception("Unable to fetch contents from [{$url}]");
    }

    return (string) $response->getBody();
}
