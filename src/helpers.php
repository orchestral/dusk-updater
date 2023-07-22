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
