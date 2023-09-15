<?php

declare(strict_types=1);

use Core\Console\Phenix;
use Core\Database\Console\DatabaseCommand;
use Core\Database\Console\SeedRun;
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

it('run seeders successful', function () {
    $application = new Phenix();
    $application->add(new SeedRun());

    /** @var SeedRun $command */
    $command = $application->find('seed:run');

    // mock the manager class
    /** @var Manager|\PHPUnit\Framework\MockObject\MockObject $managerStub */
    $managerStub = $this->getMockBuilder('\Phinx\Migration\Manager')
        ->setConstructorArgs([$this->config, $this->input, $this->output])
        ->getMock();

    $managerStub->expects($this->once())
                ->method('seed')
                ->with($this->identicalTo('default'), $this->identicalTo(null));

    $command->setConfig($this->config);
    $command->setManager($managerStub);

    $commandTester = new CommandTester($command);

    $exitCode = $commandTester->execute(['command' => $command->getName()], ['decorated' => false]);

    $this->assertSame(DatabaseCommand::CODE_SUCCESS, $exitCode);
    $this->assertStringContainsString('no environment specified', $commandTester->getDisplay());
});
