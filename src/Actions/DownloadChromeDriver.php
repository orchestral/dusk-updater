<?php

namespace Orchestra\DuskUpdater\Actions;

use Exception;
use Orchestra\DuskUpdaterApi\HttpClient;
use RuntimeException;

use function Orchestra\Sidekick\join_paths;

class DownloadChromeDriver
{
    /**
     * Construct a new action.
     */
    public function __construct(
        public string $directory,
        public string $version,
    ) {}

    /**
     * Handle the action.
     *
     * @throws \RuntimeException
     */
    public function handle(string $url): string
    {
        $archive = join_paths($this->directory, 'chromedriver.zip');

        try {
            HttpClient::download($url, $archive);
        } catch (Exception $e) {
            throw new RuntimeException(\sprintf('Unable to retrieve ChromeDriver [%s].', $this->version));
        }

        return $archive;
    }
}
