<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Leevel\Database\Migrations\AbstractCommand;
use Leevel\Database\Migrations\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 数据库执行填充数据.
 */
class SeedRun extends AbstractCommand
{
    use RuntimeEnvironment;

    protected function configure(): void
    {
        parent::configure();

        $this->setName('migrate:seedrun')
            ->setDescription('Run database seeders')
            ->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment', Config::ENVIRONMENT_DEFAULT)
            ->addOption('--seed', '-s', InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'What is the name of the seeder?')
        ;

        $this->setRuntimeEnvironment();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bootstrap($input, $output);

        /** @var null|array<string> $seedSet */
        $seedSet = $input->getOption('seed');

        /** @var null|string $environment */
        $environment = $input->getOption('environment');

        try {
            $migrator = $this->getMigrator();
            $migrator->setConfig($this->config);

            $start = microtime(true);
            if (empty($seedSet)) {
                $migrator->seed($environment);
            } else {
                foreach ($seedSet as $seed) {
                    $migrator->seed($environment, trim($seed));
                }
            }
            $end = microtime(true);
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->__toString().'</error>');

            return self::FAILURE;
        } catch (\Throwable $e) {
            $output->writeln('<error>'.$e->__toString().'</error>');

            return self::FAILURE;
        }

        $output->writeln('', $this->verbosityLevel);
        $output->writeln('<comment>All Done. Took '.\sprintf('%.4fs', $end - $start).'</comment>', $this->verbosityLevel);

        return self::SUCCESS;
    }
}
