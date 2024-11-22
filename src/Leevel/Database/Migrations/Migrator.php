<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

use Leevel\Database\IDatabase;
use Leevel\Database\Proxy\Db;
use Leevel\Kernel\Utils\ClassParser;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

class Migrator
{
    protected ?array $migrations = null;

    protected ?array $seeds = null;

    protected InputInterface $input;

    protected OutputInterface $output;

    protected Config $config;

    public function __construct(Config $config, InputInterface $input, OutputInterface $output)
    {
        $this->setConfig($config);
        $this->input = $input;
        $this->output = $output;
    }

    public function migrate(string $environment, ?string $target = null): void
    {
        $migrations = $this->getMigrations($environment);
        $database = $this->getDatabase();
        $this->initMigrationLog($database);

        /** @var Migration $migration */
        foreach ($migrations as $migrationName => $migration) {
            if ($target && $migrationName !== $target) {
                continue;
            }

            $migration->setDatabase($database);

            // Check if migration has already been logged
            if ($this->isMigrationLogged($database, $migrationName)) {
                continue;
            }

            try {
                $migration->handle();
                $this->output->writeln("Migration <info>{$migrationName}</info> executed successfully.");

                // Log the migration in the migration_log table
                $this->logMigration($database, $migrationName);
            } catch (\Throwable $e) {
                $this->output->writeln("Error occurred while executing migration <error>{$migrationName}</error>: ".$e->getMessage());

                throw $e;
            }
        }
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function seed(string $environment, ?string $target = null): void
    {
        $seeds = $this->getSeeds($environment);
        $database = $this->getDatabase();

        /** @var Seed $seeder */
        foreach ($seeds as $seedName => $seeder) {
            if ($target && $seedName !== $target) {
                continue;
            }

            $seeder->setDatabase($database);

            try {
                $seeder->handle();
                $this->output->writeln("Seed <info>{$seedName}</info> executed successfully.");
            } catch (\Throwable $e) {
                $this->output->writeln("Error occurred while executing seed <error>{$seedName}</error>: ".$e->getMessage());

                throw $e;
            }
        }
    }

    public function setConfig(Config $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function setSeeds(array $seeds): self
    {
        $this->seeds = $seeds;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getSeeds(string $environment): array
    {
        if (null !== $this->seeds) {
            return $this->seeds;
        }

        $phpFiles = $this->getSeedFiles();
        $seeds = [];
        foreach ($phpFiles as $filePath) {
            $className = $this->parseClassName($filePath);

            $this->requireFilePath($filePath);
            if (!class_exists($className)) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find class "%s" in file "%s"',
                    $className,
                    $filePath
                ));
            }

            $seedName = basename($filePath, '.php');

            /** @var Seed $seed */
            $seed = new $className($environment, $this->input, $this->output);
            if (!$seed instanceof Seed) {
                throw new \InvalidArgumentException(sprintf(
                    'The class "%s" in file "%s" must extend %s',
                    $className,
                    $filePath,
                    $className
                ));
            }

            $seeds[$seedName] = $seed;
        }

        $this->setSeeds($seeds);

        return $seeds;
    }

    public function setMigrations(array $migrations): self
    {
        $this->migrations = $migrations;

        return $this;
    }

    /**
     * @throws \InvalidArgumentException
     */
    public function getMigrations(string $environment): array
    {
        if (null !== $this->migrations) {
            return $this->migrations;
        }

        $phpFiles = $this->getMigrationFiles();
        $fileNames = [];
        $migrations = [];
        foreach ($phpFiles as $filePath) {
            $className = $this->parseClassName($filePath);

            if (isset($fileNames[$className])) {
                throw new \InvalidArgumentException(sprintf(
                    'Migration "%s" has the same name as "%s"',
                    basename($filePath),
                    $className,
                ));
            }

            $migrationName = basename($filePath, '.php');
            $fileNames[$className] = $migrationName;

            $this->requireFilePath($filePath);

            if (!class_exists($className)) {
                throw new \InvalidArgumentException(sprintf(
                    'Could not find class "%s" in file "%s"',
                    $className,
                    $filePath
                ));
            }

            /** @var Migration $migration */
            $migration = new $className($environment, $this->input, $this->output);

            if (!$migration instanceof Migration) {
                throw new \InvalidArgumentException(sprintf(
                    'The class "%s" in file "%s" must extend %s',
                    $className,
                    $filePath,
                    Migration::class,
                ));
            }

            $migrations[$migrationName] = $migration;
        }

        $this->setMigrations($migrations);

        return $migrations;
    }

    protected function isMigrationLogged(IDatabase $database, string $migrationName): bool
    {
        $migrationLogQuery = "SELECT id FROM `migration_log` WHERE `migration` = '{$migrationName}' AND delete_at = 0;";
        $migrationLogResult = $database->query($migrationLogQuery);

        return !empty($migrationLogResult[0]) && $migrationLogResult[0]->id;
    }

    protected function logMigration(IDatabase $database, string $migrationName): void
    {
        $sql = "INSERT INTO `migration_log` (`migration`) VALUES ('{$migrationName}');";
        $this->executeSql($database, $sql);
    }

    protected function initMigrationLog(IDatabase $database): void
    {
        try {
            $sql = <<<'EOT'
            CREATE TABLE `migration_log` (
              `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'ID',
              `migration` varchar(300) NOT NULL DEFAULT '' COMMENT '迁移名字',
              `create_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
              `update_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
              `delete_at` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间 0=未删除;大于0=删除时间;',
              PRIMARY KEY (`id`),
              UNIQUE KEY `uniq_migration` (`migration`,`delete_at`) USING BTREE COMMENT '迁移'
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='迁移日志';
            EOT;
            $this->executeSql($database, $sql);
        } catch (\Throwable $e) {
            if (!str_contains($e->getMessage(), "Table 'migration_log' already exists")) {
                throw $e;
            }
        }
    }

    protected function executeSql(IDatabase $database, string $sql): false|int
    {
        return $database->getPdo(true)->exec($sql);
    }

    protected function getDatabase(): IDatabase
    {
        return Db::proxy()->connect($this->getConfig()->getDatabaseDriver());
    }

    protected function getSeedFiles(): array
    {
        return $this->getFilesByPath($this->getConfig()->seedsPath);
    }

    protected function getMigrationFiles(): array
    {
        return $this->getFilesByPath($this->getConfig()->migrationsPath);
    }

    protected function getFilesByPath(string $path): array
    {
        $finder = new Finder()
            ->in([$path])
            ->exclude(['vendor', 'node_modules'])
            ->followLinks()
            ->name('*.php')
            ->sortByName()
            ->files()
        ;

        $files = [];
        foreach ($finder as $file) {
            $files[] = $file->getRealPath();
        }

        return $files;
    }

    protected function parseClassName(string $filePath): string
    {
        return new ClassParser()->handle($filePath);
    }

    protected function requireFilePath(string $filePath): void
    {
        $oldDisplayErrors = \ini_get('display_errors');
        ini_set('display_errors', 'On');

        require_once $filePath;
        ini_set('display_errors', $oldDisplayErrors);
    }
}
