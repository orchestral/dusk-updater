<?php

namespace Orchestra\DuskUpdater;

use Orchestra\DuskUpdaterApi\ChromeVersionFinder;
use Orchestra\DuskUpdaterApi\OperatingSystem;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\note;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 */
#[AsCommand(name: 'update', description: 'Install the ChromeDriver binary')]
class UpdateCommand extends Command
{
    /** {@inheritDoc} */
    #[\Override]
    protected function configure(): void
    {
        $this->addArgument('version', InputArgument::OPTIONAL)
            ->addOption('all', null, InputOption::VALUE_NONE, 'Install a ChromeDriver binary for every OS');

        parent::configure();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (\is_null($this->directory)) {
            throw new RuntimeException('Unable to update ChromeDriver without --install-dir');
        }

        $finder = new ChromeVersionFinder;

        $version = $finder->findVersionUrl($input->getArgument('version'));
        $all = $input->getOption('all');
        $currentOS = OperatingSystem::id();

        foreach (OperatingSystem::all() as $operatingSystem) {
            if ($all || ($operatingSystem === $currentOS)) {
                $url = $finder->resolveChromeDriverDownloadUrl($version, $operatingSystem);
                $archive = (new Actions\DownloadChromeDriver($this->directory, $version))->handle($url);
                $binary = (new Actions\PublishChromeDriver($this->directory, $operatingSystem))->handle($archive);
            }
        }

        note(\sprintf(
            '<info>ChromeDriver %s successfully installed for version %s.</info>', $all ? 'binaries' : 'binary', $version
        ));

        return self::SUCCESS;
    }
}
