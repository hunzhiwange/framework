<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\cache\provider;

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

use queryyetsimple\cache\manager;
use queryyetsimple\support\provider;

/**
 * cache 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.06.03
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
        $this->caches();
        $this->cache();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
                'caches' => 'queryyetsimple\cache\manager',
                'cache' => [
                        'queryyetsimple\cache\cache',
                        'queryyetsimple\cache\icache'
                ]
        ];
    }

    /**
     * 注册 caches 服务
     *
     * @return void
     */
    protected function caches()
    {
        $this->singleton('caches', function ($oProject) {
            return new manager($oProject);
        });
    }

    /**
     * 注册 cache 服务
     *
     * @return void
     */
    protected function cache()
    {
        $this->singleton('cache', function ($oProject) {
            return $oProject ['caches']->connect();
        });
    }
}
