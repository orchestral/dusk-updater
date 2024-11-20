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
     *
     * @var string|null
     */
    protected $directory;

    /**
     * The proxy to download binary.
     *
     * @var string|null
     */
    protected $httpProxy;

    /**
     * Determine SSL certification verification.
     *
     * @var bool
     */
    protected $withSslVerification = true;

    /** {@inheritDoc */
    #[\Override]
    protected function configure()
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
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->directory = $input->getOption('install-dir');
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
            return sprintf('https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip', $version, $slug);
        }

        $milestone = (int) $version;

        $versions = $this->resolveChromeVersionsPerMilestone();

        /** @var array<string, array{platform: string, url: string}> $chromedrivers */
        $chromedrivers = $versions['milestones'][$milestone]['downloads']['chromedriver'] ?? null;

        if (is_null($chromedrivers)) {
            throw new Exception('Could not get the ChromeDriver version.');
        }

        foreach ($chromedrivers as $chromedriver) {
            if ($chromedriver['platform'] === $slug) {
                return $chromedriver['url'];
            }
        }

        throw new Exception('Could not get the ChromeDriver version.');
    }
}
