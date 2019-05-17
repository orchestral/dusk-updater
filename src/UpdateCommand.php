<?php

namespace Orchestra\DuskUpdater;

use ZipArchive;
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
    use Concerns\ChromeVersions;

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
        return $this->findVersionUrl($input->getArgument('version'));
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
            fopen($url, 'r')
        );

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
