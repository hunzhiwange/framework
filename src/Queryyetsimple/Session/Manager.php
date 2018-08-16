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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Session;

use Leevel\Manager\Manager as Managers;

/**
 * manager 入口.
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
    protected function normalizeOptionNamespace()
    {
        return 'session';
    }

    /**
     * 创建连接对象
     *
     * @param object $connect
     *
     * @return object
     */
    protected function createConnect($connect)
    {
        return new Session(
            $connect,
            $this->getCommonOption()
        );
    }

    /**
     * 创建 file 缓存.
     *
     * @param array $options
     */
    protected function makeConnectFile($options = [])
    {
        return new File(
            $this->normalizeConnectOption('file', $options)
        );
    }

    /**
     * 创建 redis 缓存.
     *
     * @param array $options
     *
     * @return \Leevel\Session\redis
     */
    protected function makeConnectRedis($options = [])
    {
        return new Redis(
            $this->normalizeConnectOption('redis', $options)
        );
    }

    /**
     * 分析连接配置.
     *
     * @param string $connect
     *
     * @return array
     */
    protected function getConnectOption($connect)
    {
        return $this->filterNullOfOption(
            parent::getConnectOption($connect)
        );
    }
}
