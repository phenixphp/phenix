<?php

namespace Core\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeController extends Command
{
    public const EMPTY_LINE = '';

    protected static $defaultName = 'make:controller';

    protected static $defaultDescription = 'Creates a new controller.';

    protected function configure(): void
    {
        $this->setHelp('This command allows you to create a new controller.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The controller name');

        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force creates controller');
        $this->addOption('api', 'a', InputOption::VALUE_NONE, 'Add API methods to controller');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $force = $input->getOption('force');
        $api = $input->getOption('api');

        $namespace = explode('/', $name);

        $search = ['{name}', '{namespace}'];

        $controller = array_pop($namespace);

        $path = base_path('app/Http/Controllers');

        if (empty($namespace)) {
            $search[1] = '\\' . $search[1];

            $namespace = '';
        } else {
            foreach ($namespace as $directory) {
                $path .= '/' . $directory;

                if (! is_dir($path)) {
                    mkdir($path, 0755);
                }
            }

            $namespace = implode('\\', $namespace);
        }

        $controllerPath = "{$path}/{$controller}.php";

        if (file_exists($controllerPath) && !$force) {
            $output->writeln(['Controller already exists!', self::EMPTY_LINE]);

            return Command::FAILURE;
        }

        $file = $api ? 'controller.api.stub' : 'controller.stub';

        $stub = file_get_contents(base_path("core/stubs/{$file}"));
        $stub = str_replace($search, [$controller, $namespace], $stub);

        file_put_contents($controllerPath, $stub);

        $output->writeln(['Controller successfully generated!', self::EMPTY_LINE]);

        return Command::SUCCESS;
    }
}
