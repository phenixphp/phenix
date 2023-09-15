<?php

declare(strict_types=1);

namespace Core\Database\Console;

use Carbon\Carbon;
use Core\Console\Maker;
use Core\Util\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MakeMigration extends Maker
{
    /**
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected static $defaultName = 'make:migration';

    /**
     * @var string
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.PropertyTypeHint
     */
    protected static $defaultDescription = 'Creates a new migration.';

    protected string $fileName;

    protected function configure(): void
    {
        $this->setHelp('This command allows you to create a new migration.');

        $this->addArgument('name', InputArgument::REQUIRED, 'The migration name');

        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Force to create migration');
        $this->addOption('table', 't', InputOption::VALUE_OPTIONAL, 'Indicates database table name');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $datetime = Carbon::now()->format('YmdHis');

        $this->fileName = $datetime . '_' . Str::snake($input->getArgument('name'));

        return parent::execute($input, $output);
    }

    protected function outputDirectory(): string
    {
        return 'database' . DIRECTORY_SEPARATOR . 'migrations';
    }

    protected function stub(): string
    {
        return 'migration.stub';
    }

    protected function commonName(): string
    {
        return 'Migration';
    }

    protected function getCustomFileName(): string|null
    {
        return $this->fileName;
    }
}
