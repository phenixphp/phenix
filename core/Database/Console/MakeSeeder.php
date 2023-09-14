<?php

declare(strict_types=1);

namespace Core\Database\Console;

use Core\Console\Maker;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeSeeder extends Maker
{
    /**
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected static $defaultName = 'make:seeder';

    /**
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected static $defaultDescription = 'Creates a new seeder class.';

    protected function configure(): void
    {
        $this->setHelp('This command allows you to create a new seeder.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The seeder name');

        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force to create seeder');
        $this->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'Indicates database table name');
    }

    protected function outputDirectory(): string
    {
        return 'database' . DIRECTORY_SEPARATOR . 'seeds';
    }

    protected function stub(): string
    {
        return 'seed.stub';
    }

    protected function commonName(): string
    {
        return 'Seeder';
    }
}
