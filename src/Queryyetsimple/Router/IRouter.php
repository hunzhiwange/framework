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
namespace Queryyetsimple\Router;

/**
 * 路由解析接口
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2018.03.08
 * @version 1.0
 */
interface IRouter
{

    /**
     * 应用参数名
     *
     * @var string
     */
    const APP = '_app';

    /**
     * 控制器参数名
     *
     * @var string
     */
    const CONTROLLER = '_c';

    /**
     * 方法参数名
     *
     * @var string
     */
    const ACTION = '_a';

    /**
     * 解析参数名
     *
     * @var string
     */
    const PARAMS = '_params';

    /**
     * 控制器前缀
     *
     * @var string
     */
    const PREFIX = '_prefix';

    /**
     * restful show
     *
     * @var string
     */
    const RESTFUL_SHOW = 'show';

    /**
     * restful store
     *
     * @var string
     */
    const RESTFUL_STORE = 'store';

    /**
     * restful update
     *
     * @var string
     */
    const RESTFUL_UPDATE = 'update';

    /**
     * restful destroy
     *
     * @var string
     */
    const RESTFUL_DESTROY = 'destroy';
}
