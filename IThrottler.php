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
namespace Queryyetsimple\Throttler;

use Queryyetsimple\Http\Request;

/**
 * IThrottler 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
interface IThrottler
{

    /**
     * 创建一个节流器
     *
     * @param string|null $strKey
     * @param integer $intXRateLimitLimit
     * @param integer $intXRateLimitTime
     * @return \Queryyetsimple\Throttler\RateLimiter
     */
    public function create($strKey = null, $intXRateLimitLimit = 20, $intXRateLimitTime = 20);

    /**
     * 设置 http request
     *
     * @param \Queryyetsimple\Http\Request $objRequest
     * @return $this
     */
    public function setRequest(Request $objRequest);

    /**
     * 获取请求 key
     *
     * @param null|string $strKey
     * @return string
     */
    public function getRequestKey($strKey = null);
}
