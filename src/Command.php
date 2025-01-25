<?php

namespace Orchestra\DuskUpdater;

use Exception;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    use Concerns\DetectsChromeVersion;

    /**
     * The ChromeDriver binary installation directory.
     */
    protected ?string $directory;

    /**
     * The proxy to download binary.
     */
    protected ?string $httpProxy;

    /**
     * Determine SSL certification verification.
     */
    protected bool $withSslVerification = true;

    /** {@inheritDoc */
    #[\Override]
    protected function configure(): void
    {
        $this->ignoreValidationErrors();

        $directory = getcwd().'/vendor/laravel/dusk/bin/';

        if (is_dir($directory)) {
            $this->addOption('install-dir', null, InputOption::VALUE_OPTIONAL, 'Install a ChromeDriver binary in this directory', $directory);
        } else {
            $this->addOption('install-dir', null, InputOption::VALUE_REQUIRED, 'Install a ChromeDriver binary in this directory');
        }

        $this->addOption('proxy', null, InputOption::VALUE_OPTIONAL, 'The proxy to download the binary through (example: "tcp://127.0.0.1:9000")')
            ->addOption('ssl-no-verify', null, InputOption::VALUE_NONE, 'Bypass SSL certificate verification when installing through a proxy');
    }

    /** {@inheritDoc */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $directory = $input->getOption('install-dir');

        $this->directory = ! empty($directory) ? rtrim($directory, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR : null;
        $this->httpProxy = $input->getOption('proxy');
        $this->withSslVerification = $input->getOption('ssl-no-verify') === false;
    }

    /**
     * Get contents from URL.
     *
     * @throws \Exception
     */
    protected function fetchUrl(string $url): string
    {
        return fetch($url, $this->httpProxy, $this->withSslVerification);
    }

    /**
     * Resolve the download url.
     *
     * @throws \Exception
     */
    protected function resolveChromeDriverDownloadUrl(string $version, string $operatingSystem): string
    {
        $slug = OperatingSystem::chromeDriverSlug($operatingSystem, $version);

        if (version_compare($version, '115.0', '<')) {
            return \sprintf('https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip', $version, $slug);
        }

        $milestone = (int) $version;

        $versions = $this->resolveChromeVersionsPerMilestone();

        /** @var array<string, mixed> $chromedrivers */
        $chromedrivers = $versions['milestones'][$milestone]['downloads']['chromedriver']
            ?? throw new Exception('Could not get the ChromeDriver version.');

        foreach ($chromedrivers as $chromedriver) {
            if ($chromedriver['platform'] === $slug) {
                return $chromedriver['url'];
            }
        }

        throw new Exception('Could not get the ChromeDriver version.');
    }
}
