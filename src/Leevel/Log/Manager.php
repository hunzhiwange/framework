<?php

declare(strict_types=1);

/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Log;

use Leevel\Manager\Manager as Managers;

/**
 * log 入口.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.02.15
 *
 * @version 1.0
 *
 * @method static \Leevel\Log\ILog setOption(string $name, $value)              设置配置.
 * @method static void emergency(string $message, array $context = [])          系统无法使用.
 * @method static void alert(string $message, array $context = [])              必须立即采取行动.
 * @method static void critical(string $message, array $context = [])           临界条件.
 * @method static void error(string $message, array $context = [])              运行时错误，不需要立即处理. 但是需要被记录和监控.
 * @method static void warning(string $message, array $context = [])            非错误的异常事件.
 * @method static void notice(string $message, array $context = [])             正常重要事件.
 * @method static void info(string $message, array $context = [])               想记录的日志.
 * @method static void debug(string $message, array $context = [])              调试信息.
 * @method static void log(string $level, string $message, array $context = []) 记录特定级别的日志信息.
 * @method static void flush()                                                  保存日志信息.
 * @method static void clear(?string $level = null)                             清理日志记录.
 * @method static array all(?string $level = null)                              获取日志记录.
 * @method static int count(?string $level = null)                              获取日志记录数量.
 * @method static bool isMonolog()                                              是否为 Monolog.
 * @method static \Monolog\Logger getMonolog()                                  取得 Monolog.
 * @method static void store(array $data)                                       存储日志.
 */
class Manager extends Managers
{
    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'log';
    }

    /**
     * 创建 file 日志驱动.
     *
     * @param array $options
     *
     * @return \Leevel\Log\File
     */
    protected function makeConnectFile(array $options = []): File
    {
        return new File(
            $this->normalizeConnectOption('file', $options),
            $this->container->make('event')
        );
    }

    /**
     * 创建 syslog 日志驱动.
     *
     * @param array $options
     *
     * @return \Leevel\Log\Syslog
     */
    protected function makeConnectSyslog(array $options = []): Syslog
    {
        return new Syslog(
            $this->normalizeConnectOption('syslog', $options),
            $this->container->make('event')
        );
    }
}
