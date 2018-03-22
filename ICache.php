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

/**
 * ICache 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.23
 * @version 1.0
 */
interface ICache
{

    /**
     * 获取缓存
     *
     * @param string $sCacheName
     * @param mixed $mixDefault
     * @param array $arrOption
     * @return mixed
     */
    public function get($sCacheName, $mixDefault = false, array $arrOption = []);

    /**
     * 设置缓存
     *
     * @param string $sCacheName
     * @param mixed $mixData
     * @param array $arrOption
     * @return void
     */
    public function set($sCacheName, $mixData, array $arrOption = []);

    /**
     * 清除缓存
     *
     * @param string $sCacheName
     * @param array $arrOption
     * @return void
     */
    public function delele($sCacheName, array $arrOption = []);

    /**
     * 返回缓存句柄
     *
     * @return mixed
     */
    public function handle();

    /**
     * 关闭
     *
     * @return void
     */
    public function close();
}
