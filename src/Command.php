<?php

namespace Orchestra\DuskUpdater;

use Orchestra\DuskUpdaterApi\HttpClient;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Command extends SymfonyCommand
{
    use Concerns\ConfiguresPrompts;

    /**
     * The ChromeDriver binary installation directory.
     */
    protected ?string $directory;

    /** {@inheritDoc} */
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

    /** {@inheritDoc} */
    #[\Override]
    protected function interact(InputInterface $input, OutputInterface $output): void
    {
        parent::interact($input, $output);

        $this->configurePrompts($input, $output);
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $directory = $input->getOption('install-dir');

        $this->directory = ! empty($directory) ? rtrim($directory, DIRECTORY_SEPARATOR) : null;

        HttpClient::$proxy = $input->getOption('proxy');
        HttpClient::$verifySsl = $input->getOption('ssl-no-verify') === false;
    }
}
