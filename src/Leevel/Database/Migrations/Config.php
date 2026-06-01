<?php

declare(strict_types=1);

namespace Leevel\Database\Migrations;

use Leevel\Support\Dto;

class Config extends Dto
{
    public const string ENVIRONMENT_DEFAULT = 'development';

    public string $migrationsPath = '';

    public string $seedsPath = '';

    public string $environmentDefault = self::ENVIRONMENT_DEFAULT;

    public string $environment = self::ENVIRONMENT_DEFAULT;

    public array $environments = [
        self::ENVIRONMENT_DEFAULT => 'mysql',
    ];

    public function getDatabaseDriver(): string
    {
        return $this->environments[$this->environment] ?? $this->environments[$this->environmentDefault];
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
            throw new \RuntimeException(\sprintf(
                'PHP file \'%s\' must return an array',
                $configFilePath
            ));
        }

        return new static($configArray);
    }
}
