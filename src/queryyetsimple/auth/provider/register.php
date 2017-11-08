<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\auth\provider;

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

use queryyetsimple\auth\manager;
use queryyetsimple\support\provider;

/**
 * auth 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.09.08
 * @version 1.0
 */
class register extends provider {
    
    /**
     * 注册服务
     *
     * @return void
     */
    public function register() {
        $this->auths ();
        $this->auth ();
    }
    
    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers() {
        return [ 
                'auths' => 'queryyetsimple\auth\manager',
                'auth' => [ 
                        'queryyetsimple\auth\auth',
                        'queryyetsimple\auth\iauth' 
                ] 
        ];
    }
    
    /**
     * 注册 auths 服务
     *
     * @return void
     */
    protected function auths() {
        $this->singleton ( 'auths', function ($oProject) {
            return new manager ( $oProject );
        } );
    }
    
    /**
     * 注册 auth 服务
     *
     * @return void
     */
    protected function auth() {
        $this->singleton ( 'auth', function ($oProject) {
            return $oProject ['auths']->connect ();
        } );
    }
}
