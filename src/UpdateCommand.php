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
    use Concerns\DetectsChromeVersion;

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
        'mac-intel' => 'mac64',
        'mac-arm' => 'mac_arm64',
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

        $directory = getcwd().'/vendor/laravel/dusk/bin/';

        $this->setName('update')
                ->setDescription('Install the ChromeDriver binary.')
                ->addArgument('version', InputArgument::OPTIONAL)
                ->addOption('install-dir', null, InputOption::VALUE_OPTIONAL, 'Install a ChromeDriver binary in this directory', $directory)
                ->addOption('all', null, InputOption::VALUE_NONE, 'Install a ChromeDriver binary for every OS');
    }

    /**
     * Execute the command.
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->directory = $input->getOption('install-dir');

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

        return 0;
    }

    /**
     * Get the desired ChromeDriver version.
     */
    protected function version(InputInterface $input): string
    {
        return $this->findVersionUrl($input->getArgument('version'));
    }

    /**
     * Download the ChromeDriver archive.
     */
    protected function download(string $version, string $slug): string
    {
        if ($slug == 'mac_arm64' && version_compare($version, '106.0.5249', '<')) {
            $slug == 'mac64_m1';
        }

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
     */
    protected function rename(string $binary, string $os): void
    {
        $newName = str_replace('chromedriver', 'chromedriver-'.$os, $binary);

        rename($this->directory.$binary, $this->directory.$newName);

        chmod($this->directory.$newName, 0755);
    }
}
