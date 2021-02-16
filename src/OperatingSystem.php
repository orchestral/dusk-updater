<?php

namespace Orchestra\DuskUpdater;

class OperatingSystem
{
    /**
     * Returns the current OS identifier.
     *
     * @return string
     */
    public static function id()
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
     *
     * @return bool
     */
    public static function onWindows()
    {
        if (\defined('PHP_OS_FAMILY')) {
            return PHP_OS_FAMILY === 'Windows';
        }

        return PHP_OS === 'WINNT' || \mb_strpos(\php_uname(), 'Microsoft') !== false;
    }

    /**
     * Determine if the operating system is macOS.
     *
     * @return bool
     */
    public static function onMac()
    {
        if (\defined('PHP_OS_FAMILY')) {
            return PHP_OS_FAMILY === 'Darwin';
        }

        return PHP_OS === 'Darwin';
    }

    /**
     * Mac platform architecture ID.
     *
     * @return string
     */
    public static function macArchitectureId()
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

    /**
     * Mac platform architecture ID.
     *
     * @return string
     *
     * @deprecated v1.4.x
     * @see static::macArchitectureId()
     */
    public static function macArchitecture()
    {
        return static::macArchitectureId();
    }
}
