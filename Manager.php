<?php
/*
 * This file is part of the ************************ package.
 * ##########################################################
 * #   ____                          ______  _   _ ______   #
 * #  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
 * # |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
 * #  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
 * #       \__   | \___ |_|    \__  || |    | | | || |      #
 * #     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
 * #                          |___ /  Since 2010.10.03      #
 * ##########################################################
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Queryyetsimple\Database;

use Queryyetsimple\Support\Arr;
use Queryyetsimple\Manager\Manager as Managers;

/**
 * database 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class Manager extends Managers
{

    /**
     * 取得配置命名空间
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
     * @param object $objConnect
     * @return object
     */
    protected function createConnect($objConnect)
    {
        return new Database($objConnect);
    }

    /**
     * 创建 mysql 连接
     *
     * @param array $arrOption
     * @return \Queryyetsimple\Database\mysql
     */
    protected function makeConnectMysql($arrOption = [])
    {
        return new Mysql($this->objContainer['log'], $this->objContainer['cache'], $this->getOption('mysql', is_array($arrOption) ? $arrOption : []), $this->objContainer->development());
    }

    /**
     * 读取默认配置
     *
     * @param string $strConnect
     * @param array $arrExtendOption
     * @return array
     */
    protected function getOption($strConnect, array $arrExtendOption = null)
    {
        return $this->parseOption(parent::getOption($strConnect, $arrExtendOption));
    }

    /**
     * 分析数据库配置参数
     *
     * @param array $arrOption
     * @return array
     */
    protected function parseOption($arrOption)
    {
        $arrTemp = $arrOption;

        foreach (array_keys($arrOption) as $strType) {
            if (in_array($strType, [
                'distributed',
                'readwrite_separate',
                'driver',
                'master',
                'slave',
                'fetch',
                'log'
            ])) {
                if (isset($arrTemp[$strType])) {
                    unset($arrTemp[$strType]);
                }
            } else {
                if (isset($arrOption[$strType])) {
                    unset($arrOption[$strType]);
                }
            }
        }

        // 纠正数据库服务器参数
        foreach ([
            'master',
            'slave'
        ] as $strType) {
            if (! is_array($arrOption[$strType])) {
                $arrOption[$strType] = [];
            }
        }

        // 填充数据库服务器参数
        $arrOption['master'] = array_merge($arrOption['master'], $arrTemp);

        // 是否采用分布式服务器，非分布式关闭附属服务器
        if (! $arrOption['distributed']) {
            $arrOption['slave'] = [];
        } elseif ($arrOption['slave']) {
            if (count($arrOption['slave']) == count($arrOption['slave'], COUNT_RECURSIVE)) {
                $arrOption['slave'] = [
                    $arrOption['slave']
                ];
            }
            foreach ($arrOption['slave'] as &$arrSlave) {
                $arrSlave = array_merge($arrSlave, $arrTemp);
            }
        }

        // + 合并支持
        $arrOption = Arr::merge($arrOption);

        // 返回结果
        unset($arrTemp);
        
        return $arrOption;
    }
}
