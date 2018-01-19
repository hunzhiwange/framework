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
namespace queryyetsimple\cache;

use queryyetsimple\support\infinity;

/**
 * cache 仓储
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
class cache implements icache
{
    use infinity {
        __call as infinityCall;
    }

    /**
     * 缓存连接对象
     *
     * @var \queryyetsimple\cache\iconnect
     */
    protected $objConnect;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\cache\iconnect $objConnect
     * @return void
     */
    public function __construct(iconnect $objConnect)
    {
        $this->objConnect = $objConnect;
    }

    /**
     * 获取缓存
     *
     * @param string $sCacheName
     * @param mixed $mixDefault
     * @param array $arrOption
     * @return mixed
     */
    public function get($sCacheName, $mixDefault = false, array $arrOption = [])
    {
        return $this->objConnect->get($sCacheName, $mixDefault, $arrOption);
    }

    /**
     * 设置缓存
     *
     * @param string $sCacheName
     * @param mixed $mixData
     * @param array $arrOption
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = [])
    {
        $this->objConnect->set($sCacheName, $mixData, $arrOption);
    }

    /**
     * 清除缓存
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return void
     */
    public function delele($sCacheName, array $arrOption = [])
    {
        $this->objConnect->delele($sCacheName, $arrOption);
    }

    /**
     * 返回缓存句柄
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->objConnect->handle();
    }

    /**
     * 关闭
     *
     * @return void
     */
    public function close()
    {
        $this->objConnect->close();
    }

    /**
     * call 
     *
     * @param string $sMethod
     * @param array $arrArgs
     * @return mixed
     */
    public function __call(string $sMethod, array $arrArgs)
    {
        if (static::hasInfinity($sMethod)) {
            return $this->infinityCall($sMethod, $arrArgs);
        }

        return $this->objConnect->$sMethod(...$arrArgs);
    }
}
