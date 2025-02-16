<?php

namespace Orchestra\DuskUpdater\Tests;

use Orchestra\DuskUpdaterApi\OperatingSystem;
use Orchestra\DuskUpdater\UpdateCommand;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

it('can update to specific version', function () {
    $app = new Application('Dusk Updater', '1.0.0');
    $app->add(new UpdateCommand);

    $command = $app->find('update');

    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'command' => $command->getName(),
        'version' => '108.0.5359.71',
        '--install-dir' => __DIR__.'/tmp',
    ]);

    $output = $commandTester->getDisplay();

    $this->assertStringContainsString(
        OperatingSystem::onWindows()
            ? 'ChromeDriver binary successfully installed for version 108.0.5359.71.'.PHP_EOL
            : "ChromeDriver binary successfully installed for version 108.0.5359.71.\n",
        $output
    );
});

it('can update to major version', function () {
    $app = new Application('Dusk Updater', '1.0.0');
    $app->add(new UpdateCommand);

    $command = $app->find('update');

    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'command' => $command->getName(),
        'version' => '108',
        '--install-dir' => __DIR__.'/tmp',
    ]);

    $output = $commandTester->getDisplay();
    $this->assertStringContainsString('ChromeDriver binary successfully installed for version 108', $output);
});

it('cannot update to invalid version', function () {
    $app = new Application('Dusk Updater', '1.0.0');
    $app->add(new UpdateCommand);

    $command = $app->find('update');

    $commandTester = new CommandTester($command);
    $commandTester->execute([
        'command' => $command->getName(),
        'version' => '74.0.3729',
        '--install-dir' => __DIR__.'/tmp',
    ]);
})->throws(RuntimeException::class, 'Unable to retrieve ChromeDriver [74.0.3729].');
