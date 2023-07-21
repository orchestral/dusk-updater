<?php

namespace Orchestra\DuskUpdater;

use Exception;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    use Concerns\DetectsChromeVersion,
        InteractsWithIO;

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
        $this->withoutSslVerification = $input->getOption('ssl-no-verify') === false;
    }

    /**
     * Get contents from URL.
     *
     *
     * @throws \Exception
     */
    protected function fetchUrl(string $url): string
    {
        $streamOptions = [];

        if ($this->withoutSslVerification === false) {
            $streamOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ];
        }

        if (! empty($this->httpProxy)) {
            $streamOptions['http'] = ['proxy' => $this->httpProxy, 'request_fulluri' => true];
        }

        $contents = file_get_contents($url, false, stream_context_create($streamOptions));

        return \is_string($contents) ? $contents : throw new Exception("Unable to fetch contents from [{$url}]");
    }

    /**
     * Resolve the download url.
     *
     * @throws \Exception
     */
    protected function resolveDownloadUrl(string $version, string $slug): string
    {
        if (version_compare($version, '113.0', '<')) {
            if ($slug == 'mac_arm64' && version_compare($version, '106.0.5249', '<')) {
                $slug == 'mac64_m1';
            } elseif ($slug === 'win64') {
                $slug = 'win';
            }

            return sprintf('https://chromedriver.storage.googleapis.com/%s/chromedriver_%s.zip', $version, $slug);
        }

        $milestone = (int) $version;

        $slugs = [
            'mac64' => 'mac-x64',
            'mac_arm64' => 'mac-x64',
        ];

        $slug = $slugs[$slug] ?? $slug;

        $versions = $this->resolveChromeVersionsPerMilestone();

        /** @var array<string, mixed> $chromedrivers */
        $chromedrivers = $versions['milestones'][$milestone]['downloads']['chromedriver']
            ?? throw new Exception('Could not get the ChromeDriver version.');

        return collect($chromedrivers)->firstWhere('platform', $slug)['url']
            ?? throw new Exception('Could not get the ChromeDriver version.');
    }
}
