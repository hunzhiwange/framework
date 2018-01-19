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
namespace queryyetsimple\option\provider;

use queryyetsimple\{
    option\load,
    option\option,
    support\provider
};
use qys\option\option as qys_option;

/**
 * option 服务提供者
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
        $this->option();
        $this->optionLoad();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'option' => [
                'queryyetsimple\option\option',
                'queryyetsimple\option\ioption',
                'qys\option\option',
                'qys\option\ioption'
            ],
            'load' => 'queryyetsimple\option\load'
        ];
    }

    /**
     * 注册 option 服务
     *
     * @return void
     */
    protected function option()
    {
        $this->singleton('option', function () {
            // 虽然可以直接使用 qys, 然后沙盒可以自动转换一下 qys -> queryyetsimple
            // 但是如果判断一下 qys 是否存在，性能更好
            if(class_exists('qys\option\option', false)) {
                return new qys_option();
            } else {
                return new option();
            }
        });
    }

    /**
     * 注册 option.load 服务
     *
     * @return void
     */
    protected function optionLoad()
    {
        $this->singleton('option.load', function () {
            return new load();
        });
    }
}
