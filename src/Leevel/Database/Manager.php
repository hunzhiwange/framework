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

namespace Leevel\Database;

use InvalidArgumentException;
use Leevel\Event\IDispatch;
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
class Manager extends Managers implements IDatabase
{
    use Proxy;

    /**
     * 返回代理.
     *
     * @return \Leevel\Database\IDatabase
     * @codeCoverageIgnore
     */
    public function proxy(): IDatabase
    {
        return $this->connect();
    }

    /**
     * 取得配置命名空间.
     *
     * @return string
     */
    protected function normalizeOptionNamespace(): string
    {
        return 'database';
    }

    /**
     * 创建 mysql 连接.
     *
     * @param array $option
     *
     * @return \Leevel\Database\Mysql
     */
    protected function makeConnectMysql(array $option = []): Mysql
    {
        return new Mysql(
            $this->normalizeConnectOption('mysql', $option),
            $this->container->make(IDispatch::class)
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
    protected function normalizeConnectOption(string $connect, array $extendOption = []): array
    {
        return $this->parseDatabaseOption(
            parent::normalizeConnectOption($connect, $extendOption)
        );
    }

    /**
     * 分析数据库配置参数.
     *
     * @param array $option
     *
     * @return array
     */
    protected function parseDatabaseOption(array $option): array
    {
        $temp = $option;
        $type = ['distributed', 'separate', 'driver', 'master', 'slave'];

        foreach (array_keys($option) as $t) {
            if (in_array($t, $type, true)) {
                if (isset($temp[$t])) {
                    unset($temp[$t]);
                }
            } elseif (isset($option[$t])) {
                unset($option[$t]);
            }
        }

        foreach (['master', 'slave'] as $t) {
            if (!is_array($option[$t])) {
                $e = sprintf('Database option `%s` must be an array.', $t);

                throw new InvalidArgumentException($e);
            }
        }

        $option['master'] = array_merge($option['master'], $temp);

        if (!$option['distributed']) {
            $option['slave'] = [];
        } elseif ($option['slave']) {
            if (count($option['slave']) === count($option['slave'], COUNT_RECURSIVE)) {
                $option['slave'] = [$option['slave']];
            }

            foreach ($option['slave'] as &$slave) {
                $slave = array_merge($slave, $temp);
            }
        }

        return $option;
    }
}
