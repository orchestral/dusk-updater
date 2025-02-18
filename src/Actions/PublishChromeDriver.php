<?php

namespace Orchestra\DuskUpdater\Actions;

use ZipArchive;

use function Orchestra\DuskUpdater\rename_chromedriver_binary;
use function Orchestra\Sidekick\join_paths;

class PublishChromeDriver
{
    /**
     * Construct a new action.
     */
    public function __construct(
        public string $directory,
        public string $operatingSystem,
    ) {}

    /**
     * Handle the action.
     *
     * @throws \RuntimeException
     */
    public function handle(string $archive): string
    {
        return $this->rename($this->extract($archive));
    }

    /**
     * Extract the ChromeDriver binary from the archive and delete the archive.
     *
     * @throws \RuntimeException
     */
    protected function extract(string $archive): string
    {
        $binary = null;

        $zip = new ZipArchive;

        $zip->open($archive);

        $zip->extractTo($this->directory);

        for ($fileIndex = 0; $fileIndex < $zip->numFiles; $fileIndex++) {
            /** @var string $filename */
            $filename = $zip->getNameIndex($fileIndex);

            if (str_starts_with(basename($filename), 'chromedriver')) {
                $binary = $filename;

                $zip->extractTo($this->directory, $binary);

                break;
            }
        }

        $zip->close();

        unlink($archive);

        return (string) $binary;
    }

    /**
     * Rename the ChromeDriver binary and make it executable.
     *
     * @throws \RuntimeException
     */
    protected function rename(string $binary): string
    {
        $newName = rename_chromedriver_binary($binary, $this->operatingSystem);

        $from = join_paths($this->directory, $binary);
        $to = join_paths($this->directory, $newName);

        rename($from, $to);

        chmod($to, 0755);

        return $to;
    }
}
