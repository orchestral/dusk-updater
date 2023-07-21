<?php

namespace Orchestra\DuskUpdater;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use ZipArchive;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 */
class UpdateCommand extends Command
{
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
        'win64' => 'win64',
    ];

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('update')
            ->setDescription('Install the ChromeDriver binary.')
            ->addArgument('version', InputArgument::OPTIONAL)
            ->addOption('all', null, InputOption::VALUE_NONE, 'Install a ChromeDriver binary for every OS');

        parent::configure();
    }

    /**
     * Execute the command.
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $version = $this->version($input);
        $all = $input->getOption('all');
        $currentOS = OperatingSystem::id();

        foreach ($this->slugs as $os => $slug) {
            if ($all || ($os === $currentOS)) {
                $archive = $this->download($version, $slug);
                $binary = $this->extract($version, $archive);
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
        $url = $this->resolveDownloadUrl($version, $slug);

        try {
            file_put_contents(
                $archive = $this->directory.'chromedriver.zip',
                $this->fetchUrl($url)
            );
        } catch (Exception $e) {
            throw new RuntimeException("Unable to retrieve ChromeDriver [{$version}].");
        }

        return $archive;
    }

    /**
     * Extract the ChromeDriver binary from the archive and delete the archive.
     *
     * @throws \RuntimeException
     */
    protected function extract(string $version, string $archive): string
    {
        if (\is_null($this->directory)) {
            throw new RuntimeException("Unable to extract {$archive} without --install-dir");
        }

        $zip = new ZipArchive();

        $zip->open($archive);

        $zip->extractTo($this->directory);

        $binary = $zip->getNameIndex(version_compare($version, '113.0', '<') ? 0 : 1);

        $zip->close();

        unlink($archive);

        return (string) $binary;
    }

    /**
     * Rename the ChromeDriver binary and make it executable.
     *
     * @throws \RuntimeException
     */
    protected function rename(string $binary, string $os): void
    {
        if (\is_null($this->directory)) {
            throw new RuntimeException("Unable to rename {$binary} without --install-dir");
        }

        $newName = chromedriver_binary_filename($binary, $os);

        rename($this->directory.$binary, $this->directory.$newName);

        chmod($this->directory.$newName, 0755);
    }
}
