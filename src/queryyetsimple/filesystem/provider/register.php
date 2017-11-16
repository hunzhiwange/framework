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
namespace queryyetsimple\filesystem\provider;

use queryyetsimple\support\provider;
use queryyetsimple\filesystem\manager;

/**
 * filesystem 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
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
        $this->filesystems();
        $this->filesystem();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'filesystems' => 'queryyetsimple\filesystem\manager',
            'filesystem' => [
                'queryyetsimple\filesystem\filesystem',
                'queryyetsimple\filesystem\ifilesystem'
            ]
        ];
    }

    /**
     * 注册 filesystems 服务
     *
     * @return void
     */
    protected function filesystems()
    {
        $this->singleton('filesystems', function ($oProject) {
            return new manager($oProject);
        });
    }

    /**
     * 注册 filesystem 服务
     *
     * @return void
     */
    protected function filesystem()
    {
        $this->singleton('filesystem', function ($oProject) {
            return $oProject['filesystems']->connect();
        });
    }
}
