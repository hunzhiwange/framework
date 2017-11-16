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
namespace queryyetsimple\log\provider;

use queryyetsimple\log\manager;
use queryyetsimple\support\provider;

/**
 * log 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.12
 * @version 1.0
 */
class register extends provider
{

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->logs();
        $this->log();
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
            'logs' => 'queryyetsimple\log\manager',
            'log' => [
                'queryyetsimple\log\log',
                'queryyetsimple\log\ilog'
            ],
            'queryyetsimple\log\middleware\log'
        ];
    }

    /**
     * 注册 logs 服务
     *
     * @return void
     */
    protected function logs()
    {
        $this->singleton('logs', function ($oProject) {
            return new manager($oProject);
        });
    }

    /**
     * 注册 log 服务
     *
     * @return void
     */
    protected function log()
    {
        $this->singleton('log', function ($oProject) {
            return $oProject['logs']->connect();
        });
    }

    /**
     * 注册 middleware 服务
     *
     * @return void
     */
    protected function middleware()
    {
        $this->singleton('queryyetsimple\log\middleware\log');
    }
}
