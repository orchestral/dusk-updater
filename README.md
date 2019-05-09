Updater for Laravel Dusk ChromeDriver binaries
==============

> This is a fork based on [staudenmeir/dusk-updater](https://github.com/staudenmeir/dusk-updater) but uses Symfony Console to allow it to be use outside of Laravel installation.

## Introduction

This Symfony command updates your Laravel Dusk ChromeDriver binaries to the latest or specified release.

Supports all versions of Dusk.

## Installation

    composer require --dev orchestra/dusk-updater

## Usage

Download the latest stable ChromeDriver release:

    ./vendor/bin/dusk-updater update

You can also specify the major Chrome/Chromium version you are using:

    ./vendor/bin/dusk-updater update 74

Or you directly specify the desired ChromeDriver version:

    ./vendor/bin/dusk-updater update 74.0.3729.6
     

If Dusk is still using the previous version after the update, there is probably an old ChromeDriver process running that you need to terminate first. 
