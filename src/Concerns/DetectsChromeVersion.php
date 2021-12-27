<?php

namespace Orchestra\DuskUpdater\Concerns;

use InvalidArgumentException;
use Symfony\Component\Process\Process;

/**
 * @copyright Originally created by Jonas Staudenmeir: https://github.com/staudenmeir/dusk-updater
 */
trait DetectsChromeVersion
{
    /**
     * The default commands to detect the installed Chrome/Chromium version.
     *
     * @var array<string, array<int, string>>
     */
    protected $chromeCommands = [
        'linux' => [
            '/usr/bin/google-chrome --version',
            '/usr/bin/chromium-browser --version',
            '/usr/bin/chromium --version',
            '/usr/bin/google-chrome-stable --version',
        ],
        'mac' => [
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
        ],
        'mac-intel' => [
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
        ],
        'mac-arm' => [
            '/Applications/Google\ Chrome.app/Contents/MacOS/Google\ Chrome --version',
        ],
        'win' => [
            'reg query "HKEY_CURRENT_USER\Software\Google\Chrome\BLBeacon" /v version',
        ],
    ];

    /**
     * URL to the home page.
     *
     * @var string
     */
    protected $homeUrl = 'http://chromedriver.chromium.org/home';

    /**
     * URL to the latest release version.
     *
     * @var string
     */
    protected $versionUrl = 'https://chromedriver.storage.googleapis.com/LATEST_RELEASE_%d';

    /**
     * The legacy versions for the ChromeDriver.
     *
     * @var array<int, string>
     */
    protected $legacyVersions = [
        43 => '2.20',
        44 => '2.20',
        45 => '2.20',
        46 => '2.21',
        47 => '2.21',
        48 => '2.21',
        49 => '2.22',
        50 => '2.22',
        51 => '2.23',
        52 => '2.24',
        53 => '2.26',
        54 => '2.27',
        55 => '2.28',
        56 => '2.29',
        57 => '2.29',
        58 => '2.31',
        59 => '2.32',
        60 => '2.33',
        61 => '2.34',
        62 => '2.35',
        63 => '2.36',
        64 => '2.37',
        65 => '2.38',
        66 => '2.40',
        67 => '2.41',
        68 => '2.42',
        69 => '2.44',
    ];

    /**
     * Find selected ChromeDriver version URL.
     */
    protected function findVersionUrl(?string $version): string
    {
        if (! $version) {
            return $this->latestVersion();
        }

        if (! ctype_digit($version)) {
            return $version;
        }

        $version = (int) $version;

        if ($version < 70) {
            return $this->legacyVersions[$version];
        }

        return trim((string) file_get_contents(
            sprintf($this->versionUrl, $version)
        ));
    }

    /**
     * Get the latest stable ChromeDriver version.
     */
    protected function latestVersion(): string
    {
        $home = (string) file_get_contents($this->homeUrl);

        preg_match('/chromedriver.storage.googleapis.com\/index.html\?path=([\d.]+)/', $home, $matches);

        return $matches[1];
    }

    /**
     * Detect the installed Chrome/Chromium version.
     *
     * @return array<string, mixed>
     */
    protected function installedChromeVersion(string $operatingSystem, ?string $chromeDirectory = null): array
    {
        if ($chromeDirectory) {
            if ($operatingSystem === 'win') {
                throw new InvalidArgumentException('Chrome version cannot be detected in custom installation path on Windows.');
            }

            $commands = [$chromeDirectory.' --version'];
        } else {
            $commands = $this->chromeCommands[$operatingSystem];
        }

        foreach ($commands as $command) {
            $process = Process::fromShellCommandline($command);

            $process->run();

            if ($process->getExitCode() != 0) {
                continue;
            }

            preg_match('/(\d+)\.(\d+)\.(\d+)(\.\d+)?/', $process->getOutput(), $matches);

            if (! isset($matches[1])) {
                continue;
            }

            $semver = implode('.', [$matches[1], $matches[2], $matches[3]]);

            return [
                'full' => $matches[0],
                'semver' => $semver,
                'major' => (int) $matches[1],
                'minor' => (int) $matches[2],
                'patch' => (int) $matches[3],
            ];
        }

        throw new InvalidArgumentException('Chrome version could not be detected. Please submit an issue: https://github.com/orchestral/dusk-updater');
    }

    /**
     * Detect the installed ChromeDriver version.
     *
     * @return array<string, mixed>|null
     */
    protected function installedChromeDriverVersion(string $os, ?string $driverDirectory)
    {
        $filenames = [
            'linux' => 'chromedriver-linux',
            'mac' => 'chromedriver-mac',
            'mac-intel' => 'chromedriver-mac-intel',
            'mac-arm' => 'chromedriver-mac-arm',
            'win' => 'chromedriver-win.exe',
        ];

        if (! file_exists($driverDirectory.$filenames[$os])) {
            return [
                'full' => null,
                'semver' => null,
                'major' => null,
                'minor' => null,
                'patch' => null,
            ];
        }

        $command = $driverDirectory.$filenames[$os].' --version';
        $process = Process::fromShellCommandline($command);

        $process->run();

        if ($process->getExitCode() == 0) {
            preg_match('/ChromeDriver\s(\d+)\.(\d+)\.(\d+)(\.\d+)?\s[\w\D]+/', $process->getOutput(), $matches);

            if (isset($matches[1])) {
                $semver = implode('.', [$matches[1], $matches[2], $matches[3]]);

                return [
                    'full' => $semver,
                    'semver' => $semver,
                    'major' => (int) $matches[1],
                    'minor' => (int) $matches[2],
                    'patch' => (int) $matches[3],
                ];
            }
        }

        throw new InvalidArgumentException('ChromeDriver version could not be detected. Please submit an issue: https://github.com/orchestral/dusk-updater');
    }
}
