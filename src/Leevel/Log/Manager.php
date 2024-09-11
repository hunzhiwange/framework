<?php

declare(strict_types=1);

namespace Leevel\Log;

use Leevel\Support\Manager as Managers;
use Monolog\Logger;

/**
 * 日志管理器.
 */
class Manager extends Managers implements ILog
{
    public function getMonolog(): Logger
    {
        // @phpstan-ignore-next-line
        return $this->__call(__FUNCTION__, []);
    }

    public function emergency(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function alert(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function critical(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function error(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function warning(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function notice(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function info(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function debug(string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$message, $context]);
    }

    public function log($level, string|\Stringable $message, array $context = []): void
    {
        $this->__call(__FUNCTION__, [$level, $message, $context]);
    }

    public function connect(?string $connect = null, bool $newConnect = false, ...$arguments): ILog
    {
        return parent::connect($connect, $newConnect, ...$arguments);
    }

    public function reconnect(?string $connect = null, ...$arguments): ILog
    {
        return parent::reconnect($connect, ...$arguments);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getConfigNamespace(): string
    {
        return 'log';
    }

    /**
     * 创建 file 日志驱动.
     */
    protected function makeConnectFile(string $connect, ?string $driverClass = null): File
    {
        $driverClass = $this->getDriverClass(File::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect),
            $this->container->make('event'),
            $this->container->enabledCoroutine(),
        );
    }

    /**
     * 创建 syslog 日志驱动.
     */
    protected function makeConnectSyslog(string $connect, ?string $driverClass = null): Syslog
    {
        $driverClass = $this->getDriverClass(Syslog::class, $driverClass);

        return new $driverClass(
            $this->normalizeConnectConfig($connect),
            $this->container->make('event'),
            $this->container->enabledCoroutine(),
        );
    }
}
