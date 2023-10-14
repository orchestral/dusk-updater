# Change for 2.x

This changelog references the relevant changes (bug and security fixes) done to `orchestra/dusk-updater`.

## 2.4.0

Released: 2023-10-14

### Added

* Added support for Symfony 7.

## 2.3.0

Released: 2023-09-13

### Added
 
* Added PHP and Platform information on `detect` command.

## 2.2.1

Released: 2023-07-27

### Fixes

*  Normalize directory separator when renaming ChromeDriver binary.

## 2.2.0

Released: 2023-07-22

### Changes

* Refactor code usage. 
* Use `guzzlehttp/guzzle` to improves downloading from new API.

## 2.1.3

Released: 2023-07-21

### Fixes

* Fixes ChromeDriver download URL used for `113` and `114`. 

## 2.1.2

Released: 2023-07-21

### Fixes

* Fixes `mac_arm64` and remove `win64` platform name.

## 2.1.1

Released: 2023-07-21

### Fixes

* Retrieve ChromeDriver archive using `--proxy` and `--ssl-no-verify` options.

## 2.1.0

Released: 2023-07-21

### Changes

* Support retrieving ChromeDriver 115 using new API.

## 2.0.1

Released: 2023-02-16

### Changes

* Use `$_composer_autoload_path` when available.

## 2.0.0

Released: 2023-02-14

### Changes

* Bump minimum supported PHP version to `8.1`+.
* Requires `composer-runtime-api` version `2.2`+.
* Improves PHP native type declarations.
