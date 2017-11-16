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
namespace queryyetsimple\mail\provider;

use queryyetsimple\mail\manager;
use queryyetsimple\support\provider;

/**
 * mail 服务提供者
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
        $this->mails();
        $this->mail();
    }
    
    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
            'mails' => 'queryyetsimple\mail\manager', 
            'mail' => [
                'queryyetsimple\mail\mail', 
                'queryyetsimple\mail\imail'
            ]
        ];
    }
    
    /**
     * 注册 mails 服务
     *
     * @return void
     */
    protected function mails()
    {
        $this->singleton('mails', function ($oProject)
        {
            return new manager($oProject);
        });
    }
    
    /**
     * 注册 mail 服务
     *
     * @return void
     */
    protected function mail()
    {
        $this->singleton('mail', function ($oProject)
        {
            return $oProject['mails']->connect();
        });
    }
}
