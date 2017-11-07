<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\session\provider;

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

use queryyetsimple\session\manager;
use queryyetsimple\support\provider;

/**
 * session 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.05
 * @version 1.0
 */
class register extends provider {
    
    /**
     * 注册服务
     *
     * @return void
     */
    public function register() {
        $this->sessions ();
        $this->session ();
        $this->middleware ();
    }
    
    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers() {
        return [ 
                'sessions' => 'queryyetsimple\session\manager',
                'session' => [ 
                        'queryyetsimple\session\session',
                        'queryyetsimple\session\isession' 
                ],
                'queryyetsimple\session\middleware\session' 
        ];
    }
    
    /**
     * 注册 sessions 服务
     *
     * @return void
     */
    protected function sessions() {
        $this->singleton ( 'sessions', function ($oProject) {
            return new manager ( $oProject );
        } );
    }
    
    /**
     * 注册 session 服务
     *
     * @return void
     */
    protected function session() {
        $this->singleton ( 'session', function ($oProject) {
            return $oProject ['sessions']->connect ();
        } );
    }
    
    /**
     * 注册 middleware 服务
     *
     * @return void
     */
    protected function middleware() {
        $this->singleton ( 'queryyetsimple\session\middleware\session' );
    }
}
