<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

use Leevel\Database\IDatabase;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Migration
{
    protected string $environment;

    protected ?IDatabase $database = null;

    protected ?OutputInterface $output = null;

    protected ?InputInterface $input = null;

    final public function __construct(string $environment, ?InputInterface $input = null, ?OutputInterface $output = null)
    {
        $this->environment = $environment;

        if (null !== $input) {
            $this->setInput($input);
        }

        if (null !== $output) {
            $this->setOutput($output);
        }
    }

    public function handle(): void
    {
        if ($structSql = $this->getStructSql()) {
            $this->execute($structSql);
        }

        if ($seedSql = $this->getSeedSql()) {
            $this->execute($seedSql);
        }
    }

    public function setDatabase(IDatabase $database): self
    {
        $this->database = $database;

        return $this;
    }

    public function getDatabase(): IDatabase
    {
        if (!isset($this->database)) {
            throw new \RuntimeException('Cannot access `database` it has not been set');
        }

        return $this->database;
    }

    public function setInput(InputInterface $input): self
    {
        $this->input = $input;

        return $this;
    }

    public function getInput(): ?InputInterface
    {
        return $this->input;
    }

    public function setOutput(OutputInterface $output): self
    {
        $this->output = $output;

        return $this;
    }

    public function getOutput(): ?OutputInterface
    {
        return $this->output;
    }

    public function getName(): string
    {
        return static::class;
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function execute(string $sql): false|int
    {
        return $this->getDatabase()
            ->getPdo(true)
            ->exec($sql)
        ;
    }

    public function query(string $sql, array $params = []): mixed
    {
        return $this->getDatabase()->query($sql, $params);
    }

    protected function getStructSql(): string
    {
        return '';
    }

    protected function getSeedSql(): string
    {
        return '';
    }
}
