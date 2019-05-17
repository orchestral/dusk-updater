<?php

namespace Orchestra\DuskUpdater;

use ZipArchive;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 */
class UpdateCommand extends Command
{
    /**
     * URL to the home page.
     *
     * @var string
     */
    protected $homeUrl = 'http://chromedriver.chromium.org/home';

    /**
     * URL to the latest release version.
     *
     * @var string
     */
    protected $versionUrl = 'https://chromedriver.storage.googleapis.com/LATEST_RELEASE_%d';

    /**
     * URL to the ChromeDriver download.
     *
     * @var string
     */
    protected $downloadUrl = 'https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip';

    /**
     * Download slugs for the available operating systems.
     *
     * @var array
     */
    protected $slugs = [
        'linux' => 'linux64',
        'mac' => 'mac64',
        'win' => 'win32',
    ];

    /**
     * The legacy versions for the ChromeDriver.
     *
     * @var array
     */
    protected $legacyVersions = [
        43 => '2.20',
        44 => '2.20',
        45 => '2.20',
        46 => '2.21',
        47 => '2.21',
        48 => '2.21',
        49 => '2.22',
        50 => '2.22',
        51 => '2.23',
        52 => '2.24',
        53 => '2.26',
        54 => '2.27',
        55 => '2.28',
        56 => '2.29',
        57 => '2.29',
        58 => '2.31',
        59 => '2.32',
        60 => '2.33',
        61 => '2.34',
        62 => '2.35',
        63 => '2.36',
        64 => '2.37',
        65 => '2.38',
        66 => '2.40',
        67 => '2.41',
        68 => '2.42',
        69 => '2.44',
    ];

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->ignoreValidationErrors();

        $this->setName('update')
                ->setDescription('Install the ChromeDriver binary.')
                ->addArgument('version', InputArgument::OPTIONAL)
                ->addOption('all', null, InputOption::VALUE_NONE, 'Install a ChromeDriver binary for every OS');
    }

    /**
     * Execute the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Input\OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->directory = getcwd().'/vendor/laravel/dusk/bin/';

        $version = $this->version($input);
        $all = $input->getOption('all');
        $currentOS = OperatingSystem::id();

        foreach ($this->slugs as $os => $slug) {
            if ($all || ($os === $currentOS)) {
                $archive = $this->download($version, $slug);
                $binary = $this->extract($archive);
                $this->rename($binary, $os);
            }
        }

        $output->writeln(sprintf(
            '<info>ChromeDriver %s successfully installed for version %s.</info>', $all ? 'binaries' : 'binary', $version
        ));
    }

    /**
     * Get the desired ChromeDriver version.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     *
     * @return string
     */
    protected function version(InputInterface $input): string
    {
        $version = $input->getArgument('version');

        if (! $version) {
            return $this->latestVersion();
        }

        if (! ctype_digit($version)) {
            return $version;
        }

        $version = (int) $version;

        if ($version < 70) {
            return $this->legacyVersions[$version];
        }

        return trim(file_get_contents(
            sprintf($this->versionUrl, $version)
        ));
    }

    /**
     * Get the latest stable ChromeDriver version.
     *
     * @return string
     */
    protected function latestVersion(): string
    {
        $home = file_get_contents($this->homeUrl);

        preg_match('/Latest stable release:.*?\?path=([\d.]+)/', $home, $matches);

        return $matches[1];
    }

    /**
     * Download the ChromeDriver archive.
     *
     * @param  string  $version
     * @param  string  $slug
     *
     * @return string
     */
    protected function download(string $version, string $slug): string
    {
        $url = sprintf($this->downloadUrl, $version, $slug);

        file_put_contents(
            $archive = $this->directory.'chromedriver.zip',
            $resource = @fopen($url, 'r')
        );

        if (! is_resource($resource) || ! file_exists($archive)) {
            throw new RuntimeException("Unable to retrieve ChromeDriver [{$version}].");
        } else {
            fclose($resource);
        }

        return $archive;
    }

    /**
     * Extract the ChromeDriver binary from the archive and delete the archive.
     *
     * @param  string  $archive
     *
     * @return string
     */
    protected function extract(string $archive): string
    {
        $zip = new ZipArchive();

        $zip->open($archive);

        $zip->extractTo($this->directory);

        $binary = $zip->getNameIndex(0);

        $zip->close();

        unlink($archive);

        return $binary;
    }

    /**
     * Rename the ChromeDriver binary and make it executable.
     *
     * @param  string  $binary
     * @param  string  $os
     *
     * @return void
     */
    protected function rename(string $binary, string $os): void
    {
        $newName = str_replace('chromedriver', 'chromedriver-'.$os, $binary);

        rename($this->directory.$binary, $this->directory.$newName);

        chmod($this->directory.$newName, 0755);
    }
}
