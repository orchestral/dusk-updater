<?php

namespace Orchestra\DuskUpdater;

use Composer\Semver\Comparator;
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
     *
     * @var string|null
     */
    protected ?string $httpProxy;

    /**
     * Determine SSL certification verification.
     *
     * @var bool
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
     * @param  string  $url
     * @return string|false
     */
    protected function fetchUrl(string $url)
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

        return file_get_contents($url, false, stream_context_create($streamOptions));
    }
}
