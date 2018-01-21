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
namespace Queryyetsimple\Throttler\Provider;

use Queryyetsimple\{
    Di\Provider,
    Throttler\Throttler
};

/**
 * throttler 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.09
 * @version 1.0
 */
class Register extends Provider
{

    /**
     * 是否延迟载入
     *
     * @var boolean
     */
    public static $defer = true;

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->throttler();
        $this->middleware();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'throttler' => [
                'Queryyetsimple\Throttler\Throttler',
                'Queryyetsimple\Throttler\IThrottler'
            ],
            'Queryyetsimple\Throttler\Middleware\Throttler'
        ];
    }

    /**
     * 注册 throttler 服务
     *
     * @return void
     */
    protected function throttler()
    {
        $this->singleton('throttler', function ($project) {
            return (new Throttler($project['cache']->connect($project['option']['throttler\driver'])))->setRequest($project['request']);
        });
    }

    /**
     * 注册 middleware 服务
     *
     * @return void
     */
    protected function middleware()
    {
        $this->singleton('Queryyetsimple\Throttler\Middleware\Throttler');
    }
}
