<?php

namespace Orchestra\DuskUpdater;

use Composer\Semver\Comparator;
use Orchestra\DuskUpdaterApi\ChromeVersionFinder;
use Orchestra\DuskUpdaterApi\OperatingSystem;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 *
 * @codeCoverageIgnore
 */
#[AsCommand(name: 'detect', description: 'Detect the installed Chrome/Chromium version')]
class DetectCommand extends Command
{
    /** {@inheritDoc} */
    #[\Override]
    protected function configure(): void
    {
        $this->addOption('chrome-dir', null, InputOption::VALUE_OPTIONAL, 'Detect the installed Chrome/Chromium version, optionally in a custom path')
            ->addOption('auto-update', null, InputOption::VALUE_NONE, 'Auto update ChromeDriver binary if outdated');

        parent::configure();
    }

    /** {@inheritDoc} */
    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $finder = new ChromeVersionFinder;

        $chromeDirectory = $input->getOption('chrome-dir');
        $driverDirectory = $input->getOption('install-dir');
        $autoUpdate = $input->getOption('auto-update');

        $currentOS = OperatingSystem::id();

        $chromeVersions = $finder->installedChromeVersion($currentOS, $chromeDirectory);
        $driverVersions = $finder->installedChromeDriverVersion($currentOS, (string) $driverDirectory);

        $updated = Comparator::equalTo(
            isset($driverVersions['semver']) ? $driverVersions['semver'] : '',
            isset($chromeVersions['semver']) ? $chromeVersions['semver'] : ''
        );

        intro(\sprintf('Running PHP %s on Platform [%s]', PHP_VERSION, $currentOS));

        table(['Tool', 'Version'], [
            ['Chrome/Chromium', $chromeVersions['semver'] ?? '<fg=yellow>✖ N/A</>'],
            ['ChromeDriver', $driverVersions['semver'] ?? '<fg=yellow>✖ N/A</>'],
        ]);

        if (! $updated) {
            if (! $autoUpdate) {
                warning('ChromeDriver is outdated!');
            }

            if ($autoUpdate || confirm('Do you want to update ChromeDriver?')) {
                $this->updateChromeDriver($input, $output, $driverDirectory, $chromeVersions['major']);
            }
        }

        return self::SUCCESS;
    }

    /**
     * Update ChromeDriver.
     */
    protected function updateChromeDriver(InputInterface $input, OutputInterface $output, string $directory, int $version): int
    {
        /** @var \Symfony\Component\Console\Application $console */
        $console = $this->getApplication();

        $command = $console->find('update');

        $arguments = array_merge([
            'command' => 'update',
            'version' => $version,
            '--install-dir' => $directory,
        ], array_filter([
            '--proxy' => $input->getOption('proxy'),
            '--ssl-no-verify' => $input->getOption('ssl-no-verify'),
        ]));

        return $command->run(new ArrayInput($arguments), $output);
    }
}
