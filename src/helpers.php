<?php 

namespace Orchestra\DuskUpdater;

function chromedriver_binary_filename(string $binary, string $os): string
{
    return strpos($binary, DIRECTORY_SEPARATOR) > 0
        ? array_reverse(explode(DIRECTORY_SEPARATOR, str_replace('chromedriver', 'chromedriver-'.$os, $binary), 2))[0]
        : str_replace('chromedriver', 'chromedriver-'.$os, $binary);
}
