<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    protected ?Migrator $migrator = null;

    protected int $verbosityLevel = OutputInterface::OUTPUT_NORMAL | OutputInterface::VERBOSITY_NORMAL;

    protected ?Config $config = null;

    /**
     * @throws \RuntimeException
     */
    public function getMigrator(): Migrator
    {
        if (!$this->migrator) {
            throw new \RuntimeException('Migrator instance not found.');
        }

        return $this->migrator;
    }

    public function bootstrap(InputInterface $input, OutputInterface $output): void
    {
        if ($input->hasParameterOption('--no-info')) {
            $this->verbosityLevel = OutputInterface::VERBOSITY_VERBOSE;
        }

        if (!$this->hasConfig()) {
            $this->loadConfig($input, $output);
        }

        $this->migrator = new Migrator($this->getConfig(), $input, $output);

        $paths = $this->getConfig()->migrationsPath;
        $output->writeln('<info>using migration paths</info> '.$paths, $this->verbosityLevel);

        if ($paths = $this->getConfig()->seedsPath) {
            $output->writeln('<info>using seed paths</info> '.$paths, $this->verbosityLevel);
        }
    }

    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function hasConfig(): bool
    {
        return null !== $this->config;
    }

    /**
     * @throws \RuntimeException
     */
    public function getConfig(): Config
    {
        if (null === $this->config) {
            throw new \RuntimeException('No config set yet');
        }

        return $this->config;
    }

    protected function configure(): void
    {
        $this->addOption('--configuration', '-c', InputOption::VALUE_REQUIRED, 'The configuration file to load');
        $this->addOption('--no-info', null, InputOption::VALUE_NONE, 'Hides all debug information');
    }

    protected function loadConfig(InputInterface $input, OutputInterface $output): void
    {
        $configFilePath = $this->locateConfigFile($input);
        $output->writeln('<info>using config file</info> '.$configFilePath);

        $config = Config::fromPhp($configFilePath);
        $this->setConfig($config);

        if ($input->hasOption('environment')
            && ($environment = $input->getOption('environment'))) {
            $config->environment = (string) $environment;
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function locateConfigFile(InputInterface $input): string
    {
        $configFile = $input->hasOption('configuration') ? (string) $input->getOption('configuration') : null;
        if (!$configFile) {
            $configFile = 'migration.php';
        }

        if (!file_exists($configFile)) {
            throw new \InvalidArgumentException(\sprintf('Configuration file "%s" not found. Please provide a valid configuration file path.', $configFile));
        }

        return $configFile;
    }
}
