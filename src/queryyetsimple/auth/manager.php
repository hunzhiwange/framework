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
namespace queryyetsimple\auth;

use Exception;
use InvalidArgumentException;
use queryyetsimple\support\manager as support_manager;

/**
 * manager 入口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.07
 * @version 1.0
 */
class manager extends support_manager
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
     * @param object $objConnect
     * @return object
     */
    protected function createConnect($objConnect)
    {
        return new auth($objConnect);
    }

    /**
     * 返回默认驱动
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->objContainer['option'][$this->getOptionName($this->objContainer['option'][$this->getOptionName('default')] . '_default')];
    }

    /**
     * 设置默认驱动
     *
     * @param string $strName
     * @return void
     */
    public function setDefaultDriver($strName)
    {
        $this->objContainer['option'][$this->getOptionName($this->objContainer['option'][$this->getOptionName('default')] . '_default')] = $strName;
    }

    /**
     * 创建 session 连接
     *
     * @param array $arrOption
     * @return \queryyetsimple\auth\session
     */
    protected function makeConnectSession($arrOption = [])
    {
        $arrOption = array_merge($this->getOption('session', $arrOption));
        return new session($this->objContainer[$arrOption['model']], $this->objContainer['encryption'], $this->objContainer['validate'], $this->objContainer['session'], $arrOption);
    }

    /**
     * 创建 token 连接
     *
     * @param array $arrOption
     * @return \queryyetsimple\auth\token
     */
    protected function makeConnectToken($arrOption = [])
    {
        $arrOption = array_merge($this->getOption('token', $arrOption));
        return new token($this->objContainer[$arrOption['model']], $this->objContainer['encryption'], $this->objContainer['validate'], $this->objContainer['cache'], $arrOption);
    }
}

if (! function_exists('__')) {
    /**
     * lang
     *
     * @param string $sValue
     * @return string
     */
    function __($sValue)
    {
        return func_num_args() > 1 ? call_user_func_array('sprintf', func_get_args()) : $sValue;
    }
}
