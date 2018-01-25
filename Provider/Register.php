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
namespace Queryyetsimple\Page\Provider;

use Queryyetsimple\{
    page\page,
    Di\Provider,
    Router\Router
};

/**
 * page 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.19
 * @version 1.0
 */
class Register extends Provider
{

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * bootstrap
     *
     * @return void
     */
    public function bootstrap()
    {
        $this->urlResolver();
        $this->i18n();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [];
    }

    /**
     * 分页路由 url 生成
     *
     * @return void
     */
    protected function urlResolver()
    {
        page::setUrlResolver(function () {
            return call_user_func_array([
                $this->container['router'],
                'url'
            ], func_get_args());
        });
    }

    /**
     * 载入语言包
     *
     * @return void
     */
    protected function i18n()
    {
        $this->loadI18nDir(__DIR__ . '/../i18n');
    }
}
