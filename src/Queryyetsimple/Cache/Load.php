<?php
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
namespace Queryyetsimple\Cache;

use InvalidArgumentException;
use Queryyetsimple\Di\IContainer;

/**
 * cache 快捷载入
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.20
 * @version 1.0
 */
class Load
{

    /**
     * IOC Container
     *
     * @var \Queryyetsimple\Di\IContainer
     */
    protected $objContainer;

    /**
     * cache 仓储
     *
     * @var \Queryyetsimple\Cache\ICache
     */
    protected $objCache;

    /**
     * 已经载入的缓存数据
     *
     * @var array
     */
    protected $arrCacheLoaded = [];

    /**
     * 构造函数
     *
     * @param \Queryyetsimple\Di\IContainer $objContainer
     * @param \Queryyetsimple\Cache\ICache $objCache
     * @return void
     */
    public function __construct(IContainer $objContainer, ICache $objCache)
    {
        $this->objContainer = $objContainer;
        $this->objCache = $objCache;
    }

    /**
     * 切换缓存仓储
     *
     * @param \Queryyetsimple\Cache\ICache $objCache
     * @return void
     */
    public function switchCache(ICache $objCache)
    {
        $this->objCache = $objCache;
    }

    /**
     * 返回缓存仓储
     *
     * @return \Queryyetsimple\Cache\ICache
     */
    public function getCache()
    {
        return $this->objCache;
    }

    /**
     * 载入缓存数据
     * 系统自动存储缓存到内存，可重复执行不会重复载入数据
     * 获取缓存建议使用本函数
     *
     * @param string|array $mixCacheName
     * @param array $arrOption
     * @param bool $booForce
     * @return array
     */
    public function data($mixCacheName, array $arrOption = [], $booForce = false)
    {
        $mixCacheName = is_array($mixCacheName) ? $mixCacheName : [
            $mixCacheName
        ];

        foreach ($mixCacheName as $strCacheName) {
            if (! isset($this->arrCacheLoaded[$strCacheName]) || $booForce) {
                $this->arrCacheLoaded[$strCacheName] = $this->cache($strCacheName, $arrOption, $booForce);
            }
        }

        $arrResult = [];
        foreach ($mixCacheName as $strCacheName) {
            $arrResult[$strCacheName] = $this->arrCacheLoaded[$strCacheName];
        }
        return count($arrResult) > 1 ? $arrResult : reset($arrResult);
    }

    /**
     * 刷新缓存数据
     * 刷新缓存建议使用本函数
     *
     * @param string|array $mixCacheName
     * @param array $arrOption
     * @return void
     */
    public function refresh($mixCacheName, array $arrOption = [])
    {
        $mixCacheName = is_array($mixCacheName) ? $mixCacheName : [
            $mixCacheName
        ];
        $this->deletes($mixCacheName, $arrOption);
    }

    /**
     * 从载入缓存数据中获取
     * 不存在不用更新缓存，返回 false
     * 获取已载入缓存建议使用本函数
     *
     * @param string|array $mixCacheName
     * @return array
     */
    public function dataLoaded($mixCacheName, array $arrOption = [], $booForce = false)
    {
        $arrResult = [];
        $mixCacheName = is_array($mixCacheName) ? $mixCacheName : [
            $mixCacheName
        ];
        foreach ($mixCacheName as $strCacheName) {
            $arrResult[$strCacheName] = array_key_exists($strCacheName, $this->arrCacheLoaded) ? $this->arrCacheLoaded[$strCacheName] : false;
        }
        return count($arrResult) > 1 ? $arrResult : reset($arrResult);
    }

    /**
     * 批量删除缓存数据
     * 不建议直接操作
     *
     * @param array $arrCacheName
     * @param array $arrOption
     * @return void
     */
    public function deletes(array $arrCacheName, $arrOption = [])
    {
        foreach ($arrCacheName as $strCacheName) {
            $this->delete($strCacheName, $arrOption);
        }
    }

    /**
     * 删除缓存数据
     * 不建议直接操作
     *
     * @param string $strCacheName
     * @param array $arrOption
     * @return void
     */
    public function delete($strCacheName, $arrOptions = [])
    {
        $this->delelePersistence($strCacheName, $arrOptions);
    }

    /**
     * 批量读取缓存数据
     * 不建议直接操作
     *
     * @param array $arrCacheName
     * @param array $arrOption
     * @param boolean $booForce
     * @return array
     */
    public function caches(array $arrCacheName, $arrOption = [], $booForce = false)
    {
        $arrData = [];
        foreach ($arrCacheName as $strCacheName) {
            $arrData[$strCacheName] = $this->cache($strCacheName, $arrOption, $booForce);
        }
        return $arrData;
    }

    /**
     * 读取缓存数据
     * 不建议直接操作
     *
     * @param string $strCacheName
     * @param array $arrOption
     * @param boolean $booForce
     * @return mixed
     */
    public function cache($strCacheName, $arrOption = [], $booForce = false)
    {
        if ($booForce === false) {
            $mixData = $this->getPersistence($strCacheName, false, $arrOption);
        } else {
            $mixData = false;
        }

        if ($mixData === false) {
            $mixData = $this->update($strCacheName, $arrOption);
        }

        return $mixData;
    }

    /**
     * 批量更新缓存数据
     * 不建议直接操作
     *
     * @param array $arrCacheName
     * @param array $arrOption
     * @return array
     */
    public function updates(array $arrCacheName, $arrOption = [])
    {
        $arrResult = [];
        foreach ($arrCacheName as $strCacheName) {
            $arrResult[$strCacheName] = $this->update($strCacheName, $arrOption);
        }
        return $arrResult;
    }

    /**
     * 更新缓存数据
     * 不建议直接操作
     *
     * @param string $strCacheName
     * @param array $arrOption
     * @return mixed
     */
    public function update($strCacheName, $arrOption = [])
    {
        $strCacheNameSource = $strCacheName;
        list($strCacheName, $arrParams) = $this->parse($strCacheName);

        if (strpos($strCacheName, '@') !== false) {
            list($strCacheName, $strMethod) = explode('@', $strCacheName);
        } else {
            $strMethod = 'handle';
        }

        if (($objCache = $this->objContainer->make($strCacheName)) === false) {
            throw new InvalidArgumentException(sprintf('Cache %s is not valid.', $strCacheName));
        }

        $mixSourceData = $objCache->$strMethod(...$arrParams);

        $this->setPersistence($strCacheNameSource, $mixSourceData, $arrOption);

        return $mixSourceData;
    }

    /**
     * 获取缓存
     *
     * @param string $strCacheName
     * @param mixed $mixDefault
     * @param array $arrOption
     * @return mixed
     */
    protected function getPersistence($strCacheName, $mixDefault = false, array $arrOption = [])
    {
        return $this->objCache->get($strCacheName, $mixDefault, $arrOption);
    }

    /**
     * 设置缓存
     *
     * @param string $strCacheName
     * @param mixed $mixData
     * @param array $arrOption
     * @return void
     */
    protected function setPersistence($strCacheName, $mixData, array $arrOption = [])
    {
        $this->objCache->set($strCacheName, $mixData, $arrOption);
    }

    /**
     * 清除缓存
     *
     * @param string $strCacheName
     * @param array $arrOption
     * @return void
     */
    protected function delelePersistence($strCacheName, array $arrOption = [])
    {
        $this->objCache->delele($strCacheName, $arrOption);
    }

    /**
     * 解析缓存
     *
     * @param string $strCacheName
     * @return array
     */
    protected function parse($strCacheName)
    {
        list($strName, $arrArgs) = array_pad(explode(':', $strCacheName, 2), 2, []);
        if (is_string($arrArgs)) {
            $arrArgs = explode(',', $arrArgs);
        }

        return [
            $strName,
            $arrArgs
        ];
    }
}
