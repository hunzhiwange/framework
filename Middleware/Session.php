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
namespace Queryyetsimple\Session\middleware;

use Closure;
use Queryyetsimple\{
    Http\Request,
    Http\Response,
    Session\Manager as Manager
};

/**
 * session 中间件
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.11.14
 * @version 1.0
 */
class session
{

    /**
     * session 管理
     *
     * @var \Queryyetsimple\Session\Manager
     */
    protected $manager;

    /**
     * 构造函数
     *
     * @param \queryyetsimple\Session\Manager $manager
     * @return void
     */
    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * 请求
     *
     * @param \Closure $next
     * @param \Queryyetsimple\Http\Request $request
     * @return void
     */
    public function handle(Closure $next, Request $request)
    {
        $this->startSession();
        $next($request);
    }

    /**
     * 响应
     *
     * @param \Closure $next
     * @param \Queryyetsimple\Http\Request $request
     * @param \Queryyetsimple\Http\Response $response
     * @return void
     */
    public function terminate(Closure $next, Request $request, Response $response)
    {
        $this->unregisterFlash();
        $this->setPrevUrl($request);
        $next($request, $response);
    }

    /**
     * 启动 session
     *
     * @return void
     */
    protected function startSession()
    {
        $this->manager->start();
    }

    /**
     * 清理闪存
     *
     * @return void
     */
    protected function unregisterFlash()
    {
        $this->manager->unregisterFlash();
    }

    /**
     * 保存当期请求 URL
     *
     * @param \Queryyetsimple\Http\Request $request
     * @return void
     */
    protected function setPrevUrl(Request $request)
    {
        $this->manager->setPrevUrl($request->url());
    }
}
