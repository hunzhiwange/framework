<?php

declare(strict_types=1);

namespace Leevel\Database\Console;

use Leevel\Console\RuntimeEnvironment;
use Leevel\Database\Console\Base\AbstractCommand;
use Leevel\Database\Migrations\Config;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * 数据库迁移运行数据库脚本.
 */
class Migrate extends AbstractCommand
{
    use RuntimeEnvironment;

    protected function configure(): void
    {
        parent::configure();

        $this->addOption('--environment', '-e', InputOption::VALUE_REQUIRED, 'The target environment', Config::ENVIRONMENT_DEFAULT);

        $this->setName('migrate:migrate')
            ->setDescription('Migrate the database')
            ->addOption('--target', '-t', InputOption::VALUE_REQUIRED, 'The target to migrate to')
        ;

        $this->setRuntimeEnvironment();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->bootstrap($input, $output);

        $target = null !== $input->getOption('target') ? (int) $input->getOption('target') : null;

        /** @var null|string $environment */
        $environment = $input->getOption('environment');

        try {
            $migrator = $this->getMigrator();
            $migrator->setConfig($this->config);

            $start = microtime(true);
            $migrator->migrate($environment, $target);
            $end = microtime(true);
        } catch (\Exception $e) {
            $output->writeln('<error>'.$e->__toString().'</error>');

            return self::FAILURE;
        } catch (\Throwable $e) {
            $output->writeln('<error>'.$e->__toString().'</error>');

            return self::FAILURE;
        }

        $output->writeln('', $this->verbosityLevel);
        $output->writeln('<comment>All Done. Took '.sprintf('%.4fs', $end - $start).'</comment>', $this->verbosityLevel);

        return self::SUCCESS;
    }
}
