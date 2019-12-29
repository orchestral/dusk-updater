Updater for Laravel Dusk ChromeDriver binaries
==============

[![Build Status](https://travis-ci.org/orchestral/dusk-updater.svg?branch=master)](https://travis-ci.org/orchestral/dusk-updater)
[![Latest Stable Version](https://poser.pugx.org/orchestra/dusk-updater/version)](https://packagist.org/packages/orchestra/dusk-updater)
[![Total Downloads](https://poser.pugx.org/orchestra/dusk-updater/downloads)](https://packagist.org/packages/orchestra/dusk-updater)
[![Latest Unstable Version](https://poser.pugx.org/orchestra/dusk-updater/v/unstable)](//packagist.org/packages/orchestra/dusk-updater)
[![License](https://poser.pugx.org/orchestra/dusk-updater/license)](https://packagist.org/packages/orchestra/dusk-updater)

> This is a fork based on [staudenmeir/dusk-updater](https://github.com/staudenmeir/dusk-updater) but uses Symfony Console to allow it to be use outside of Laravel installation.

## Introduction

This Symfony command updates your Laravel Dusk ChromeDriver binaries to the latest or specified release.

Supports all versions of Laravel Dusk especially used outside of Laravel installation.

## Installation

    composer require --dev orchestra/dusk-updater

## Usage

### Updating ChromeDriver

Download the latest stable ChromeDriver release:

    ./vendor/bin/dusk-updater update

You can also specify the major Chrome/Chromium version you are using:

    ./vendor/bin/dusk-updater update 74

Or you directly specify the desired ChromeDriver version:

    ./vendor/bin/dusk-updater update 74.0.3729.6

> If Dusk is still using the previous version after the update, there is probably an old ChromeDriver process running that you need to terminate first. 

### Checking Chrome Versions

You can check if the installed Chrome and ChromeDriver version using:

    ./vendor/bin/dusk-updater detect

> The command will prompt you to download new ChromeDriver if it is outdated.

Specify the absolute path to your custom Chrome/Chromium installation (not supported on Windows):

    ./vendor/bin/dusk-updater detect --chrome-dir=/usr/bin/google-chrome

Finally, you can also tell the command to automatically downlad new version if it is outdated using:

    ./vendor/bin/dusk-updater detect --auto-update
