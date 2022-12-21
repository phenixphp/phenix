<?php

declare(strict_types=1);

namespace Core\Console\Commands;

use Core\Console\AbstractMake;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class MakeController extends AbstractMake
{
    /**
     * @var string
     */
    protected static $defaultName = 'make:controller';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Creates a new controller.';

    protected function configure(): void
    {
        $this->setHelp('This command allows you to create a new controller.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The controller name');

        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force to create controller');
        $this->addOption('api', 'a', InputOption::VALUE_NONE, 'Add API methods to controller');
    }

    protected function outputDirectory(InputInterface $input): string
    {
        return 'app/Http/Controllers';
    }

    protected function stub(InputInterface $input): string
    {
        return $input->getOption('api') ? 'controller.api.stub' : 'controller.stub';
    }

    protected function suffix(): string
    {
        return 'Controller';
    }
}
