<?php

namespace Orchestra\DuskUpdater\Tests;

use Orchestra\DuskUpdater\OperatingSystem;
use Orchestra\DuskUpdater\UpdateCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class UpdateCommandTest extends TestCase
{
    /** @test */
    public function it_can_update_to_specific_version()
    {
        $app = new Application('Dusk Updater', '1.0.0');
        $app->add(new UpdateCommand());

        $command = $app->find('update');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'version' => '108.0.5359.71',
        ]);

        $output = $commandTester->getDisplay();

        $this->assertStringContainsString(
            OperatingSystem::onWindows()
                ? "ChromeDriver binary successfully installed for version 108.0.5359.71.".PHP_EOL
                : "ChromeDriver binary successfully installed for version 108.0.5359.71.\n",
            $output
        );
    }

    /** @test */
    public function it_can_update_to_major_version()
    {
        $app = new Application('Dusk Updater', '1.0.0');
        $app->add(new UpdateCommand());

        $command = $app->find('update');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'version' => '108',
        ]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('ChromeDriver binary successfully installed for version 108', $output);
    }

    /** @test */
    public function it_cant_update_to_invalid_version()
    {
        $this->expectException('RuntimeException');
        $this->expectExceptionMessage('Unable to retrieve ChromeDriver [74.0.3729].');

        $app = new Application('Dusk Updater', '1.0.0');
        $app->add(new UpdateCommand());

        $command = $app->find('update');

        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'version' => '74.0.3729',
        ]);
    }
}
