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
namespace queryyetsimple\mvc;

use Exception;

/**
 * 请求过于频繁异常
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.10
 * @version 1.0
 */
class too_many_requests_http extends http_failed
{
    
    /**
     * 构造函数
     *
     * @param int $intStatusCode
     * @param string|null $strMessage
     * @param \Exception $objPrevious
     * @param array $arrHeader
     * @param integer $intCode
     * @return void
     */
    public function __construct($strMessage = null, $intCode = 0, Exception $objPrevious = null)
    {
        parent::__construct(429, $strMessage, $intCode, $objPrevious);
    }
}
