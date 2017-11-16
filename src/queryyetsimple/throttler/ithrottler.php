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
 * (c) 2010-2017 http://queryphp.com All rights reserved.
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace queryyetsimple\throttler;

use queryyetsimple\http\request;

/**
 * ithrottler 接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.07
 * @version 1.0
 */
interface ithrottler
{
    
    /**
     * 创建一个节流器
     *
     * @param string|null $strKey
     * @param integer $intXRateLimitLimit
     * @param integer $intXRateLimitTime
     * @return \queryyetsimple\throttler\rate_limiter
     */
    public function create($strKey = null, $intXRateLimitLimit = 20, $intXRateLimitTime = 20);
    
    /**
     * 设置 http request
     *
     * @param \queryyetsimple\http\request $objRequest
     * @return $this
     */
    public function setRequest(request $objRequest);
    
    /**
     * 获取请求 key
     *
     * @param null|string $strKey
     * @return string
     */
    public function getRequestKey($strKey = null);
}
