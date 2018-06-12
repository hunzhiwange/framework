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

namespace Leevel\Database;

use Leevel\Manager\Manager as Managers;

/**
 * database 入口.
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
    protected function getOptionNamespace()
    {
        return 'database';
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
        return new Database($connect);
    }

    /**
     * 创建 mysql 连接.
     *
     * @param array $option
     *
     * @return \Leevel\Database\mysql
     */
    protected function makeConnectMysql($option = [])
    {
        return new Mysql(
            $this->container['log'],
            $this->container['cache'],
            $this->getOption('mysql', is_array($option) ? $option : []),
            $this->container->development()
        );
    }

    /**
     * 读取默认配置.
     *
     * @param string $connect
     * @param array  $extendOption
     *
     * @return array
     */
    protected function getOption($connect, array $extendOption = null)
    {
        return $this->parseOption(
            parent::getOption($connect, $extendOption)
        );
    }

    /**
     * 分析数据库配置参数.
     *
     * @param array $option
     *
     * @return array
     */
    protected function parseOption($option)
    {
        $temp = $option;

        foreach (array_keys($option) as $type) {
            if (in_array($type, [
                'distributed',
                'readwrite_separate',
                'driver',
                'master',
                'slave',
                'fetch',
                'log',
            ])) {
                if (isset($temp[$type])) {
                    unset($temp[$type]);
                }
            } else {
                if (isset($option[$type])) {
                    unset($option[$type]);
                }
            }
        }

        // 纠正数据库服务器参数
        foreach ([
            'master',
            'slave',
        ] as $type) {
            if (! is_array($option[$type])) {
                $option[$type] = [];
            }
        }

        // 填充数据库服务器参数
        $option['master'] = array_merge($option['master'], $temp);

        // 是否采用分布式服务器，非分布式关闭附属服务器
        if (! $option['distributed']) {
            $option['slave'] = [];
        } elseif ($option['slave']) {
            if (count($option['slave']) == count($option['slave'], COUNT_RECURSIVE)) {
                $option['slave'] = [
                    $option['slave'],
                ];
            }

            foreach ($option['slave'] as &$slave) {
                $slave = array_merge($slave, $temp);
            }
        }

        // 返回结果
        unset($temp);

        return $option;
    }
}
