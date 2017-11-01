<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\encryption\provider;

<<<queryphp
##########################################################
#   ____                          ______  _   _ ______   #
#  /     \       ___  _ __  _   _ | ___ \| | | || ___ \  #
# |   (  ||(_)| / _ \| '__|| | | || |_/ /| |_| || |_/ /  #
#  \____/ |___||  __/| |   | |_| ||  __/ |  _  ||  __/   #
#       \__   | \___ |_|    \__  || |    | | | || |      #
#     Query Yet Simple      __/  |\_|    |_| |_|\_|      #
#                          |___ /  Since 2010.10.03      #
##########################################################
queryphp;

use queryyetsimple\support\provider;
use queryyetsimple\encryption\encryption;

/**
 * encryption 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.03
 * @version 1.0
 */
class register extends provider {
    
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
    public function register() {
        $this->singleton ( 'encryption', function ($oProject) {
            return new encryption ( $oProject ['option'] ['app_auth_key'], $oProject ['option'] ['app_auth_expiry'] );
        } );
    }
    
    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers() {
        return [ 
                'encryption' => [ 
                        'queryyetsimple\encryption\encryption',
                        'queryyetsimple\encryption\iencryption' 
                ] 
        ];
    }
}
