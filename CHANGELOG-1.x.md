# Change for 1.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/dusk-updater`.

## 1.8.0

Released: 2023-09-13

### Added
 
* Added PHP and Platform information on `detect` command.

## 1.7.1

Released: 2023-07-27

### Fixes

* Normalize directory separator when renaming ChromeDriver binary.

## 1.7.0

Released: 2023-07-22

### Changes

* Refactor code usage. 
* Use `guzzlehttp/guzzle` to improves downloading from new API.

## 1.6.3

Released: 2023-07-21

### Fixes

* Fixes ChromeDriver download URL used for `113` and `114`. 

## 1.6.2

Released: 2023-07-21

### Fixes

* Fixes `mac_arm64` and remove `win64` platform name.

## 1.6.1

Released: 2023-07-21

### Fixes

* Retrieve ChromeDriver archive using `--proxy` and `--ssl-no-verify` options.

## 1.6.0

Released: 2023-07-21

### Changes

* Bump minimum supported PHP version to `7.3`+.
* Requires `composer-runtime-api` version `2.2`+.
* Support retrieving ChromeDriver 115 using new API.
* Use `$_composer_autoload_path` when available.

## 1.5.2

Released: 2021-12-27

### Fixes

* Fixes error retrieving ChromeDriver on M1 Mac for version 106.x.

## 1.5.1

Released: 2021-12-27

### Changes

* Support for Symfony 6.

## 1.5.0

Released: 2021-10-24

### Added

* Added support for PHP 8.1.

## 1.4.3

Released: 2021-03-24

### Changes

* Don't display current version number when using Composer 1.

## 1.4.2

Released: 2021-03-13

### Changes

* Uses `Composer\InstalledVersions::getPrettyVersion()` to get current version.

## 1.4.1

Released: 2021-02-16

### Changes

* Allow fallback to `mac` if it doesn't match `x86_64` or `arm64` validation.

## 1.4.0

Released: 2021-02-15

### Added

* Add ability to split Mac architecture `intel` and `arm64` for ChromeDriver.
* Add ability to detect if ChromeDriver isn't available in Laravel Dusk.

## 1.3.1

Released: 2021-02-03

### Added

* Added Chromium path for Debian 10.

## 1.3.0

Released: 2020-11-07

### Changes

* Draft support for PHP 8.
* Get version directly from Composer.

## 1.2.4

Released: 2020-10-30

### Changes

* Support `composer/semver` version 3.

## 1.2.3

Released: 2020-06-05

### Changes

* Support for Chrome version detection on Arch Linux.

## 1.2.2

Released: 2020-05-04

### Changes

* Use `PHP_OS_FAMILY` whenever possible to detect OS (available on PHP 7.2+).

## 1.2.1

Released: 2020-01-15

### Changes

* Set minimum `symfony/process` to `4.2`.
* Only show `"ChromeDriver is outdated!"` alert when `detect` command is executed without `--auto-update` options.

## 1.2.0

Released: 2019-11-28

### Changes

* Allow Symfony 5.

## 1.1.1

Released: 2019-09-11

### Changes

* Update regex pattern to match ChromeDriver source. ([#4](https://github.com/orchestral/dusk-updater/pull/4) by [@stevethomas](https://github.com/stevethomas))

## 1.1.0

Released: 2019-05-18

### Added

* Add `Orchestra\DuskUpdater\DetectCommand` to allows you to check whether installed ChromeDriver needs to be updated using `./vendor/bin/dusk-updater detect`.

## 1.0.1

Released: 2019-05-17

### Added

* Added `symfony/polyfill-ctype` in case PHP environment doesn't include `ctype_*` functions.
* Added tests.

### Changes

* Split reusable code under `Orchestra\DuskUpdater\UpdateCommand` to `Orchestra\DuskUpdater\Concerns\DetectsChromeVersion` trait.
* Throws `RuntimeException` if command trying to update none-existing ChromeDriver version.

## 1.0.0

Released: 2019-05-09

### Added

* Initial stable release.
