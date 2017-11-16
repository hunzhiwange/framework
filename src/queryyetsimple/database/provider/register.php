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
namespace queryyetsimple\database\provider;

use queryyetsimple\support\provider;
use queryyetsimple\database\manager;

/**
 * database 服务提供者
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
        $this->databases();
        $this->database();
    }

    /**
     * 载入命令包
     *
     * @return void
     */
    public function bootstrap()
    {
        $this->loadCommandNamespace('queryyetsimple\database\console');
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'databases' => 'queryyetsimple\database\manager',
            'database' => [
                'queryyetsimple\database\database',
                'queryyetsimple\database\idatabase'
            ]
        ];
    }

    /**
     * 注册 databases 服务
     *
     * @return void
     */
    protected function databases()
    {
        $this->singleton('databases', function ($oProject) {
            return new manager($oProject);
        });
    }

    /**
     * 注册 database 服务
     *
     * @return void
     */
    protected function database()
    {
        $this->singleton('database', function ($oProject) {
            return $oProject['databases']->connect();
        });
    }
}
