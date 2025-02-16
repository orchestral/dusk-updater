<?php

namespace Orchestra\DuskUpdater;

/**
 * Rename exported ChromeDriver binary filename.
 */
function rename_chromedriver_binary(string $binary, string $operatingSystem): string
{
    $binary = str_replace(DIRECTORY_SEPARATOR, '/', $binary);

    if (mb_strpos($binary, '/') > 0) {
        return array_reverse(explode('/', str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary), 2))[0];
    }

    return str_replace('chromedriver', 'chromedriver-'.$operatingSystem, $binary);
}
