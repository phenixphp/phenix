<?php

declare(strict_types=1);

namespace Core\Console;

use Core\Facades\File;
use Core\Util\NamespaceResolver;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Maker extends Command
{
    protected const EMPTY_LINE = '';
    protected const SEARCH = ['{namespace}', '{name}'];

    protected InputInterface $input;

    abstract protected function outputDirectory(): string;

    abstract protected function stub(): string;

    abstract protected function suffix(): string;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input = $input;

        $name = $this->input->getArgument('name');
        $force = $this->input->getOption('force');

        $namespace = explode('/', $name);
        $className = array_pop($namespace);

        $filePath = $this->preparePath($namespace) . "/{$className}.php";
        $namespace = $this->prepareNamespace($namespace);

        if (File::exists($filePath) && ! $force) {
            $output->writeln(["{$this->suffix()} already exists!", self::EMPTY_LINE]);

            return Command::SUCCESS;
        }

        $stub = File::get(base_path("core/stubs/{$this->stub()}"));
        $stub = str_replace(self::SEARCH, [$namespace, $className], $stub);

        File::put($filePath, $stub);

        $output->writeln(["{$this->suffix()} successfully generated!", self::EMPTY_LINE]);

        return Command::SUCCESS;
    }

    /**
     * @param array<int, string> $namespace
     */
    private function preparePath(array $namespace): string
    {
        $path = base_path($this->outputDirectory());

        foreach ($namespace as $directory) {
            $path .= '/' . ucfirst($directory);

            if (! File::exists($path)) {
                File::createDirectory($path);
            }
        }

        return $path;
    }

    /**
     * @param array<int, string> $namespace
     */
    private function prepareNamespace(array $namespace): string
    {
        array_unshift($namespace, NamespaceResolver::parse($this->outputDirectory()));

        return implode('\\', $namespace);
    }
}
