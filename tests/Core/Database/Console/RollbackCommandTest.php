<?php

declare(strict_types=1);

use Core\Console\Phenix;
use Core\Database\Console\DatabaseCommand;
use Core\Database\Console\Rollback;
use Phinx\Config\Config;
use Phinx\Migration\Manager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    $this->config = new Config([
        'paths' => [
            'migrations' => __FILE__,
            'seeds' => __FILE__,
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

it('rollback migrations successful', function () {
    $application = new Phenix();
    $application->add(new Rollback());

    /** @var Rollback $command */
    $command = $application->find('migrate:rollback');

    // mock the manager class
    /** @var Manager|\PHPUnit\Framework\MockObject\MockObject $managerStub */
    $managerStub = $this->getMockBuilder('\Phinx\Migration\Manager')
        ->setConstructorArgs([$this->config, $this->input, $this->output])
        ->getMock();
    $managerStub->expects($this->once())
                ->method('rollback')
                ->with('default', null, false, true);

    $command->setConfig($this->config);
    $command->setManager($managerStub);

    $commandTester = new CommandTester($command);
    $exitCode = $commandTester->execute(['command' => $command->getName()], ['decorated' => false]);

    $display = $commandTester->getDisplay();

    // note that the default order is by creation time
    $this->assertStringContainsString('ordering by creation time', $display);
    $this->assertSame(DatabaseCommand::CODE_SUCCESS, $exitCode);
});

it('executes fake rollback successful', function () {
    $application = new Phenix();
    $application->add(new Rollback());

    /** @var Rollback $command */
    $command = $application->find('migrate:rollback');

    // mock the manager class
    /** @var Manager|\PHPUnit\Framework\MockObject\MockObject $managerStub */
    $managerStub = $this->getMockBuilder('\Phinx\Migration\Manager')
        ->setConstructorArgs([$this->config, $this->input, $this->output])
        ->getMock();

    $managerStub->expects($this->once())
        ->method('rollback')
        ->with('default', null, false, true);

    $command->setConfig($this->config);
    $command->setManager($managerStub);

    $commandTester = new CommandTester($command);
    $exitCode = $commandTester->execute(['command' => $command->getName(), '--fake' => true], ['decorated' => false]);

    $display = $commandTester->getDisplay();

    $this->assertStringContainsString('warning performing fake rollback', $display);
    $this->assertSame(DatabaseCommand::CODE_SUCCESS, $exitCode);
});

it('rollback migrations using date', function () {
    $application = new Phenix();
    $application->add(new Rollback());

    $date = '20160101';
    $target = '20160101000000';

    /** @var Rollback $command */
    $command = $application->find('migrate:rollback');

    // mock the manager class
    /** @var Manager|\PHPUnit\Framework\MockObject\MockObject $managerStub */
    $managerStub = $this->getMockBuilder('\Phinx\Migration\Manager')
        ->setConstructorArgs([$this->config, $this->input, $this->output])
        ->getMock();

    $managerStub->expects($this->once())
                ->method('rollback')
                ->with('default', $target, false, false);

    $command->setConfig($this->config);
    $command->setManager($managerStub);

    $commandTester = new CommandTester($command);

    $exitCode = $commandTester->execute(['command' => $command->getName(), '-d' => $date], ['decorated' => false]);

    $this->assertSame(DatabaseCommand::CODE_SUCCESS, $exitCode);
});
