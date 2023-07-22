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
    protected bool $withoutSslVerification = false;

    /**
     * Configure the command options.
     */
    protected function configure(): void
    {
        $this->ignoreValidationErrors();

        $directory = getcwd().'/vendor/laravel/dusk/bin/';

        $this->addOption('install-dir', null, InputOption::VALUE_OPTIONAL, 'Install a ChromeDriver binary in this directory', $directory)
            ->addOption('proxy', null, InputOption::VALUE_OPTIONAL, 'The proxy to download the binary through (example: "tcp://127.0.0.1:9000")')
            ->addOption('ssl-no-verify', null, InputOption::VALUE_NONE, 'Bypass SSL certificate verification when installing through a proxy');
    }

    /**
     * Initializes the command after the input has been bound and before the input
     * is validated.
     *
     * This is mainly useful when a lot of commands extends one main command
     * where some things need to be initialized based on the input arguments and options.
     *
     * @see InputInterface::bind()
     * @see InputInterface::validate()
     *
     * @return void
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->directory = $input->getOption('install-dir');
        $this->httpProxy = $input->getOption('proxy');
        $this->withoutSslVerification = $input->getOption('ssl-no-verify') === true;
    }

    /**
     * Get contents from URL.
     *
     *
     * @throws \Exception
     */
    protected function fetchUrl(string $url): string
    {
        $contents = @file_get_contents(
            $url, false, stream_context_create(request_context_payload($this->httpProxy, $this->withoutSslVerification))
        );

        return \is_string($contents) ? $contents : throw new Exception("Unable to fetch contents from [{$url}]");
    }

    /**
     * Resolve the download url.
     *
     * @throws \Exception
     */
    protected function resolveChromeDriverDownloadUrl(string $version, string $os): string
    {
        $slug = resolve_chromedriver_slug($version, $os);

        if (version_compare($version, '115.0', '<')) {
            return sprintf('https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip', $version, $slug);
        }

        $milestone = (int) $version;

        $versions = $this->resolveChromeVersionsPerMilestone();

        /** @var array<string, mixed> $chromedrivers */
        $chromedrivers = $versions['milestones'][$milestone]['downloads']['chromedriver']
            ?? throw new Exception('Could not get the ChromeDriver version.');

        return collect($chromedrivers)->firstWhere('platform', $slug)['url']
            ?? throw new Exception('Could not get the ChromeDriver version.');
    }
}
