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
namespace queryyetsimple\throttler\provider;

use queryyetsimple\support\provider;
use queryyetsimple\throttler\throttler;

/**
 * throttler 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.09
 * @version 1.0
 */
class register extends provider
{
    
    /**
     * 是否延迟载入
     *
     * @var boolean
     */
    public static $booDefer = true;
    
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
                'queryyetsimple\throttler\throttler', 
                'queryyetsimple\throttler\ithrottler'
            ], 
            'queryyetsimple\throttler\middleware\throttler'
        ];
    }
    
    /**
     * 注册 throttler 服务
     *
     * @return void
     */
    protected function throttler()
    {
        $this->singleton('throttler', function ($oProject)
        {
            return (new throttler($oProject['cache']->connect($oProject['option']['throttler\driver'])))->setRequest($oProject['request']);
        });
    }
    
    /**
     * 注册 middleware 服务
     *
     * @return void
     */
    protected function middleware()
    {
        $this->singleton('queryyetsimple\throttler\middleware\throttler');
    }
}
