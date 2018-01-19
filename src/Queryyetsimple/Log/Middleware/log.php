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
namespace queryyetsimple\log\middleware;

use Closure;
use queryyetsimple\{
    log\manager,
    http\request,
    http\response
};

/**
 * log 中间件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.14
 * @version 1.0
 */
class log
{

    /**
     * log 管理
     *
     * @var \queryyetsimple\log\log
     */
    protected $manager;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\throttler\ithrottler $manager
     * @return void
     */
    public function __construct(manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * 响应
     * 
     * @param \Closure $next
     * @param \queryyetsimple\http\request $request
     * @param \queryyetsimple\http\response $response
     * @return void
     */
    public function terminate(Closure $next, request $request, response $response)
    {
        $this->saveLog();
        $next($request, $response);
    }

    /**
     * 保存日志
     *
     * @return void
     */
    protected function saveLog()
    {
        if ($this->manager->container()['option'] ['log\enabled']) {
            $this->manager->save();
        }
    }
}
