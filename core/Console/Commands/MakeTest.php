<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\AbstractMake;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MakeTest extends AbstractMake
{
    /**
     * @var string
     */
    protected static $defaultName = 'make:test';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Creates a new test.';

    protected function configure(): void
    {
        $this->setHelp('This command allows you to create a new test class.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The test name');

        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force to create test');
        $this->addOption('unit', 'u', InputOption::VALUE_NONE, 'Create unit testing');
    }

    protected function outputDirectory(InputInterface $input): string
    {
        $base = 'tests' . DIRECTORY_SEPARATOR;

        return $input->getOption('unit')
            ? $base . 'Unit'
            : $base . 'Feature';
    }

    protected function stub(InputInterface $input): string
    {
        return 'test.stub';
    }

    protected function suffix(): string
    {
        return 'Test';
    }
}
