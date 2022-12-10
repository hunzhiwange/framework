<?php

declare(strict_types=1);

namespace Leevel\Log;

use Leevel\Support\Manager as Managers;

/**
 * 日志管理器.
 *
 * @method static void emergency(string $message, array $context = [])                        系统无法使用.
 * @method static void alert(string $message, array $context = [])                            必须立即采取行动.
 * @method static void critical(string $message, array $context = [])                         临界条件.
 * @method static void error(string $message, array $context = [])                            运行时错误，不需要立即处理. 但是需要被记录和监控.
 * @method static void warning(string $message, array $context = [])                          非错误的异常事件.
 * @method static void notice(string $message, array $context = [])                           正常重要事件.
 * @method static void info(string $message, array $context = [])                             想记录的日志.
 * @method static void debug(string $message, array $context = [])                            调试信息.
 * @method static void log(string $level, string $message, array $context = [])               记录特定级别的日志信息.
 * @method static void flush()                                                                保存日志信息.
 * @method static void clear(?string $level = null)                                           清理日志记录.
 * @method static array all(?string $level = null)                                            获取日志记录.
 * @method static int count(?string $level = null)                                            获取日志记录数量.
 * @method static \Monolog\Logger getMonolog()                                                取得 Monolog.
 * @method static void store(array $data)                                                     存储日志.
 * @method static \Leevel\Di\IContainer container()                                           返回 IOC 容器.
 * @method static void disconnect(?string $connect = null)                                    删除连接.
 * @method static array getConnects()                                                         取回所有连接.
 * @method static string getDefaultConnect()                                                  返回默认连接.
 * @method static void setDefaultConnect(string $name)                                        设置默认连接.
 * @method static mixed getContainerOption(?string $name = null)                              获取容器配置值.
 * @method static void setContainerOption(string $name, mixed $value)                         设置容器配置值.
 * @method static void extend(string $connect, \Closure $callback)                            扩展自定义连接.
 * @method static array normalizeConnectOption(string $connect)                               整理连接配置.
 */
class Manager extends Managers
{
    /**
     * {@inheritDoc}
     */
    public function connect(?string $connect = null, bool $newConnect = false): ILog
    {
        return parent::connect($connect, $newConnect);
    }

    /**
     * {@inheritDoc}
     */
    public function reconnect(?string $connect = null): ILog
    {
        return parent::reconnect($connect);
    }

    /**
     * 取得配置命名空间.
     */
    protected function getOptionNamespace(): string
    {
        return 'log';
    }

    /**
     * 创建 file 日志驱动.
     */
    protected function makeConnectFile(string $connect): File
    {
        return new File(
            $this->normalizeConnectOption($connect),
            $this->container->make('event')
        );
    }

    /**
     * 创建 syslog 日志驱动.
     */
    protected function makeConnectSyslog(string $connect): Syslog
    {
        return new Syslog(
            $this->normalizeConnectOption($connect),
            $this->container->make('event')
        );
    }
}
