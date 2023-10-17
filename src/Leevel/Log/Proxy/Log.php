<?php

declare(strict_types=1);

namespace Leevel\Log\Proxy;

use Leevel\Di\Container;
use Leevel\Log\Manager;

/**
 * 代理 log.
 *
 * @method static void                  emergency(string|\Stringable $message, array $context = [])   系统无法使用.
 * @method static void                  alert(string|\Stringable $message, array $context = [])       必须立即采取行动.
 * @method static void                  critical(string|\Stringable $message, array $context = [])    临界条件.
 * @method static void                  error(string|\Stringable $message, array $context = [])       运行时错误，不需要立即处理. 但是需要被记录和监控.
 * @method static void                  warning(string|\Stringable $message, array $context = [])     非错误的异常事件.
 * @method static void                  notice(string|\Stringable $message, array $context = [])      正常重要事件.
 * @method static void                  info(string|\Stringable $message, array $context = [])        想记录的日志.
 * @method static void                  debug(string|\Stringable $message, array $context = [])       调试信息.
 * @method static void                  log($level, string|\Stringable $message, array $context = []) 记录日志.
 * @method static \Monolog\Logger       getMonolog()                                                  取得 Monolog.
 * @method static \Leevel\Di\IContainer container()                                                   返回 IOC 容器.
 * @method static \Leevel\Log\ILog      connect(?string $connect = null, bool $newConnect = false)    连接并返回连接对象.
 * @method static \Leevel\Log\ILog      reconnect(?string $connect = null)                            重新连接.
 * @method static void                  disconnect(?string $connect = null)                           删除连接.
 * @method static array                 getConnects()                                                 取回所有连接.
 * @method static string                getDefaultConnect()                                           返回默认连接.
 * @method static void                  setDefaultConnect(string $name)                               设置默认连接.
 * @method static mixed                 getContainerOption(?string $name = null)                      获取容器配置值.
 * @method static void                  setContainerOption(string $name, mixed $value)                设置容器配置值.
 * @method static void                  extend(string $connect, \Closure $callback)                   扩展自定义连接.
 * @method static array                 normalizeConnectOption(string $connect)                       整理连接配置.
 */
class Log
{
    /**
     * 实现魔术方法 __callStatic.
     */
    public static function __callStatic(string $method, array $args): mixed
    {
        return self::proxy()->{$method}(...$args);
    }

    /**
     * 代理服务.
     */
    public static function proxy(): Manager
    {
        // @phpstan-ignore-next-line
        return Container::singletons()->make('logs');
    }
}
