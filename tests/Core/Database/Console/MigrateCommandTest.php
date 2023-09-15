<?php

declare(strict_types=1);

use Core\Console\Phenix;
use Core\Database\Console\DatabaseCommand;
use Core\Database\Console\Migrate;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->config = new Config([
        'paths' => [
            'migrations' => __FILE__,
        ],
        'environments' => [
            'default_migration_table' => 'migrations',
            'default_environment' => 'default',
            'default' => [
                'adapter' => 'mysql',
                'host' => 'host',
                'name' => 'development',
                'user' => '',
                'pass' => '',
                'port' => 3006,
            ],
        ],
    ]);

    $this->input = new ArrayInput([]);
    $this->output = new StreamOutput(fopen('php://memory', 'a', false));
});

it('migrates successful', function () {
    $application = new Phenix();
    $application->add(new Migrate());

    /** @var Migrate $command */
    $command = $application->find('migrate');

    // mock the manager class
    /** @var Manager|\PHPUnit\Framework\MockObject\MockObject $managerStub */
    $managerStub = $this->getMockBuilder('\Phinx\Migration\Manager')
        ->setConstructorArgs([$this->config, $this->input, $this->output])
        ->getMock();

    $managerStub->expects($this->once())
                ->method('migrate');

    $command->setConfig($this->config);
    $command->setManager($managerStub);

    $commandTester = new CommandTester($command);
    $exitCode = $commandTester->execute(['command' => $command->getName()], ['decorated' => false]);

    $output = $commandTester->getDisplay();

    $this->assertStringContainsString('ordering by creation time', $output);
    $this->assertStringContainsString('using database development', $output);
    $this->assertSame(DatabaseCommand::CODE_SUCCESS, $exitCode);
});

it('executes fake migration', function () {
    $application = new Phenix();
    $application->add(new Migrate());

    /** @var Migrate $command */
    $command = $application->find('migrate');

    // mock the manager class
    /** @var Manager|\PHPUnit\Framework\MockObject\MockObject $managerStub */
    $managerStub = $this->getMockBuilder('\Phinx\Migration\Manager')
        ->setConstructorArgs([$this->config, $this->input, $this->output])
        ->getMock();
    $managerStub->expects($this->once())
        ->method('migrate');

    $command->setConfig($this->config);
    $command->setManager($managerStub);

    $commandTester = new CommandTester($command);
    $exitCode = $commandTester->execute(['command' => $command->getName(), '--fake' => true], ['decorated' => false]);

    $this->assertStringContainsString('warning performing fake migrations', $commandTester->getDisplay());
    $this->assertSame(DatabaseCommand::CODE_SUCCESS, $exitCode);
});
