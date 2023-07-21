<?php

namespace Orchestra\DuskUpdater;

class OperatingSystem
{
    /**
     * Returns the current OS identifier.
     */
    public static function id(): string
    {
        if (static::onWindows()) {
            return 'win';
        } elseif (static::onMac()) {
            return static::macArchitectureId();
        }

        return 'linux';
    }

    /**
     * Determine if the operating system is Windows or Windows Subsystem for Linux.
     */
    public static function onWindows(): bool
    {
        if (\defined('PHP_OS_FAMILY')) {
            return PHP_OS_FAMILY === 'Windows';
        }

        return PHP_OS === 'WINNT' || mb_strpos(php_uname(), 'Microsoft') !== false;
    }

    /**
     * Determine if the operating system is macOS.
     */
    public static function onMac(): bool
    {
        if (\defined('PHP_OS_FAMILY')) {
            return PHP_OS_FAMILY === 'Darwin';
        }

        return PHP_OS === 'Darwin';
    }

    /**
     * Mac platform architecture ID.
     */
    public static function macArchitectureId(): string
    {
        switch (php_uname('m')) {
            case 'arm64':
                return 'mac-arm';
            case 'x86_64':
                return 'mac-intel';
            default:
                return 'mac';
        }
    }
}
