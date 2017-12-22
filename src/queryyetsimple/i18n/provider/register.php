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
namespace queryyetsimple\i18n\provider;

use queryyetsimple\{
    i18n\i18n,
    i18n\load,
    support\provider
};

/**
 * i18n 服务提供者
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
        $this->i18n();
        $this->i18nLoad();
    }

    /**
     * bootstrap
     *
     * @return void
     */
    public function bootstrap()
    {
        $this->console();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'i18n' => [
                'queryyetsimple\i18n\i18n',
                'queryyetsimple\i18n\ii18n'
            ],
            'load' => 'queryyetsimple\i18n\load'
        ];
    }

    /**
     * 注册 i18n 服务
     *
     * @return void
     */
    protected function i18n()
    {
        $this->singleton('i18n', function ($oProject) {
            return new i18n($oProject['cookie'], array_merge($oProject['option']['i18n\\'], [
                'app_name' => $oProject['app_name']
            ]));
        });
    }

    /**
     * 注册 i18n.load 服务
     *
     * @return void
     */
    protected function i18nLoad()
    {
        $this->singleton('i18n.load', function () {
            return new load();
        });

        $this->console();
    }

    /**
     * 载入命令包
     *
     * @return void
     */
    protected function console()
    {
        $this->loadCommandNamespace('queryyetsimple\i18n\console');
    }
}
