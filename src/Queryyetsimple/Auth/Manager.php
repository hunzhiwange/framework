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
namespace Queryyetsimple\Auth;

use Exception;
use InvalidArgumentException;
use Queryyetsimple\Support\Manager as SupportManager;

/**
 * manager 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class Manager extends SupportManager
{

    /**
     * 取得配置命名空间
     *
     * @return string
     */
    protected function getOptionNamespace()
    {
        return 'auth';
    }

    /**
     * 创建连接对象
     *
     * @param object $connect
     * @return object
     */
    protected function createConnect($connect)
    {
        return new auth($connect);
    }

    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        $option = $this->container['option'][$this->getOptionName('default')];
        $option = $this->getOptionName($option . '_default');
        return $this->container['option'][$option];
    }

    /**
     * 设置默认驱动
     *
     * @param string $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $option = $this->container['option'][$this->getOptionName('default')];
        $option = $this->getOptionName($option . '_default');
        $this->container['option'][$option] = $name;
    }

    /**
     * 创建 session 连接
     *
     * @param array $options
     * @return \Queryyetsimple\Auth\session
     */
    protected function makeConnectSession($options = [])
    {
        $options = array_merge($this->getOption('session', $options));
        return new session($this->container[$options['model']], $this->container['encryption'], $this->container['validate'], $this->container['session'], $options);
    }

    /**
     * 创建 token 连接
     *
     * @param array $options
     * @return \Queryyetsimple\Auth\token
     */
    protected function makeConnectToken($options = [])
    {
        $options = array_merge($this->getOption('token', $options));
        return new token($this->container[$options['model']], $this->container['encryption'], $this->container['validate'], $this->container['cache'], $options);
    }
}

if (! function_exists('__')) {
    /**
     * lang
     *
     * @param array $arr
     * @return string
     */
    function __(...$arr)
    {
        return count($arr) == 0 ? '' : (count($arr) > 1 ? sprintf(...$arr) : $arr[0]);
    }
}
