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
namespace Queryyetsimple\Router;

/**
 * IUrl 生成
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.01.10
 * @version 1.0
 */
interface IUrl
{

    /**
     * 生成路由地址
     *
     * @param string $url
     * @param array $params
     * @param array $option
     * @sub boolean suffix 是否包含后缀
     * @sub boolean normal 是否为普通 url
     * @sub string subdomain 子域名
     * @return string
     */
    public function make($url, $params = [], $option = []);

    /**
     * 设置路由 app
     *
     * @param string $app
     * @return $this
     */
    public function setApp($app);

    /**
     * 设置路由 controller
     *
     * @param string $controller
     * @return $this
     */
    public function setController($controller);

    /**
     * 设置路由 action
     *
     * @param string $action
     * @return $this
     */
    public function setAction($action);

    /**
     * 设置路由 URL 入口
     *
     * @param string $urlEnter
     * @return $this
     */
    public function setUrlEnter($urlEnter);
}
