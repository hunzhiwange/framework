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
     * 创建连接对象
     *
     * @param object $connect
     *
     * @return object
     */
    protected function createConnect(object $connect): object
    {
        return $connect;
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
