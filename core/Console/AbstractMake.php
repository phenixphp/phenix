<?php

declare(strict_types=1);

namespace Core\Console;

use Core\Facades\File;
use Core\Util\NamespaceResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractMake extends Command
{
    protected const EMPTY_LINE = '';
    protected const SEARCH = ['{namespace}', '{name}'];

    abstract protected function outputDirectory(InputInterface $input): string;

    abstract protected function stub(InputInterface $input): string;

    abstract protected function suffix(): string;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument('name');
        $force = $input->getOption('force');

        $namespace = explode('/', $name);
        $className = array_pop($namespace);

        $filePath = $this->preparePath($namespace, $input) . "/{$className}.php";
        $namespace = $this->prepareNamespace($namespace, $input);

        if (File::exists($filePath) && ! $force) {
            $output->writeln(["{$this->suffix()} already exists!", self::EMPTY_LINE]);

            return Command::SUCCESS;
        }

        $stub = File::get(base_path("core/stubs/{$this->stub($input)}"));
        $stub = str_replace(self::SEARCH, [$namespace, $className], $stub);

        File::put($filePath, $stub);

        $output->writeln(["{$this->suffix()} successfully generated!", self::EMPTY_LINE]);

        return Command::SUCCESS;
    }

    private function preparePath(array $namespace, InputInterface $input): string
    {
        $path = base_path($this->outputDirectory($input));

        foreach ($namespace as $directory) {
            $path .= '/' . ucfirst($directory);

            if (! File::exists($path)) {
                File::createDirectory($path);
            }
        }

        return $path;
    }

    private function prepareNamespace(array $namespace, InputInterface $input): string
    {
        array_unshift($namespace, NamespaceResolver::parse($this->outputDirectory($input)));

        return implode('\\', $namespace);
    }
}
