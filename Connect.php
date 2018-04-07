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
namespace Leevel\Cache;

/**
 * 缓存抽象类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.02.15
 * @version 1.0
 */
abstract class Connect
{

    /**
     * 缓存服务句柄
     *
     * @var handle
     */
    protected $hHandle;

    /**
     * 构造函数
     *
     * @param array $arrOption
     * @return void
     */
    public function __construct(array $arrOption = [])
    {
        $this->options($arrOption);
    }

    /**
     * 批量插入
     *
     * @param string|array $mixKey
     * @param mixed $mixValue
     * @return void
     */
    public function put($mixKey, $mixValue = null)
    {
        if (! is_array($mixKey)) {
            $mixKey = [
                $mixKey => $mixValue
            ];
        }

        foreach ($mixKey as $strKey => $mixValue) {
            $this->set($strKey, $mixValue);
        }
    }

    /**
     * 返回缓存句柄
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->hHandle;
    }

    /**
     * 关闭
     *
     * @return void
     */
    public function close()
    {
    }

    /**
     * 获取缓存名字
     *
     * @param string $sCacheName
     * @param string $strPrefix
     * @return string
     */
    protected function getCacheName($sCacheName, $strPrefix = '')
    {
        return $strPrefix . $sCacheName;
    }

    /**
     * 读取缓存时间配置
     *
     * @param string $sId
     * @param int $intDefaultTime
     * @return number
     */
    protected function cacheTime($sId, $intDefaultTime = 0)
    {
        if (! $this->arrOption['time_preset']) {
            return $intDefaultTime;
        }

        if (isset($this->arrOption['time_preset'][$sId])) {
            return $this->arrOption['time_preset'][$sId];
        }

        foreach ($this->arrOption['time_preset'] as $sKey => $nValue) {
            $sKeyCache = '/^' . str_replace('*', '(\S+)', $sKey) . '$/';
            if (preg_match($sKeyCache, $sId, $arrRes)) {
                return $this->arrOption['time_preset'][$sKey];
            }
        }

        return $intDefaultTime;
    }

    /**
     * 强制不启用缓存
     *
     * @return boolean
     */
    protected function checkForce()
    {
        if (! empty($_REQUEST[$this->getOption('nocache_force')])) {
            return true;
        }
        return false;
    }
}
