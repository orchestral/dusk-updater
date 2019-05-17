<?php

namespace Orchestra\DuskUpdater\Concerns;

trait ChromeVersions
{
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
     * @var array
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
     *
     * @param  string|null $version
     *
     * @return string
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

        return trim(file_get_contents(
            sprintf($this->versionUrl, $version)
        ));
    }

    /**
     * Get the latest stable ChromeDriver version.
     *
     * @return string
     */
    protected function latestVersion(): string
    {
        $home = file_get_contents($this->homeUrl);

        preg_match('/Latest stable release:.*?\?path=([\d.]+)/', $home, $matches);

        return $matches[1];
    }
}
