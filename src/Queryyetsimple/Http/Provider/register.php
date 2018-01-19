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
namespace queryyetsimple\http\provider;

use queryyetsimple\{
    http\request,
    http\response,
    support\provider
};

/**
 * http 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.08.26
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
        $this->request();
        $this->response();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'request' => 'queryyetsimple\http\request',
            'response' => 'queryyetsimple\http\response'
        ];
    }

    /**
     * 注册 request 服务
     *
     * @return void
     */
    protected function request()
    {
        $this->singleton('request', function ($oProject) {
            return new request($oProject['session'], $oProject['cookie'], [
                'var_method' => $oProject['option']['var_method'],
                'var_ajax' => $oProject['option']['var_ajax'],
                'var_pjax' => $oProject['option']['var_pjax']
            ]);
        });
    }

    /**
     * 注册 response 服务
     *
     * @return void
     */
    protected function response()
    {
        $this->singleton('response', function ($oProject) {
            return new response($oProject['router'], $oProject['view'], $oProject['session'], $oProject['cookie'], [
                'action_fail' => $oProject['option']['view\action_fail'],
                'action_success' => $oProject['option']['view\action_success'],
                'default_response' => $oProject ['option'] ['default_response']
            ]);
        });
    }
}
