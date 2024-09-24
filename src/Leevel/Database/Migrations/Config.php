<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

use Leevel\Support\Dto;

class Config extends Dto
{
    public string $migrationsPath = '';

    public string $seedsPath = '';

    public string $databaseDefault = 'default';

    public string $database = 'default';

    public array $databases = [
        'default' => 'mysql',
    ];

    public function getDatabaseDriver(): string
    {
        return $this->databases[$this->database] ?? $this->databases[$this->databaseDefault];
    }

    /**
     * @throws \RuntimeException
     */
    public static function fromPhp(string $configFilePath): self
    {
        ob_start();
        $configArray = include $configFilePath;
        ob_end_clean();

        if (!\is_array($configArray)) {
            throw new \RuntimeException(sprintf(
                'PHP file \'%s\' must return an array',
                $configFilePath
            ));
        }

        return new static($configArray);
    }
}
