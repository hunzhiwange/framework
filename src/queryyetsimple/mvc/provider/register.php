<?php
// [$QueryPHP] The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
// ©2010-2017 http://queryphp.com All rights reserved.
namespace queryyetsimple\mvc\provider;

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

use queryyetsimple\mvc\view;
use queryyetsimple\mvc\meta;
use queryyetsimple\mvc\model;
use queryyetsimple\event\idispatch;
use queryyetsimple\support\provider;

/**
 * mvc 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.13
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
        $this->singleton('view', function ($oProject) {
            return (new view($oProject ['view.theme']))->setResponseFactory(function () use ($oProject) {
                return $oProject ['response'];
            });
        });
    }

    /**
     * bootstrap
     *
     * @param \queryyetsimple\event\idispatch $objEvent
     * @return void
     */
    public function bootstrap(idispatch $objEvent)
    {
        $this->eventDispatch($objEvent);
        $this->console();
        $this->meta();
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [
                'view' => [
                        'queryyetsimple\mvc\view',
                        'queryyetsimple\mvc\iview'
                ]
        ];
    }

    /**
     * 设置模型事件
     *
     * @param \queryyetsimple\event\idispatch $objEvent
     * @return void
     */
    protected function eventDispatch(idispatch $objEvent)
    {
        model::setEventDispatch($objEvent);
    }

    /**
     * 载入命令包
     *
     * @return void
     */
    protected function console()
    {
        $this->loadCommandNamespace('queryyetsimple\mvc\console');
    }

    /**
     * Meta 设置数据库管理
     *
     * @return void
     */
    protected function meta()
    {
        meta::setDatabaseManager($this->objContainer ['databases']);
    }
}
