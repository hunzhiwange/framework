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
namespace Queryyetsimple\Mvc\Provider;

use Queryyetsimple\{
    Mvc\View,
    Mvc\Meta,
    Mvc\Model,
    Di\Provider,
    Event\IDispatch
};

/**
 * mvc 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.07.13
 * @version 1.0
 */
class Register extends Provider
{

    /**
     * 注册服务
     *
     * @return void
     */
    public function register()
    {
        $this->singleton('view', function ($project) {
            return (new view($project['view.view']))->setResponseFactory(function () use ($project) {
                return $project['response'];
            });
        });
    }

    /**
     * bootstrap
     *
     * @param \Queryyetsimple\Event\IDispatch $objEvent
     * @return void
     */
    public function bootstrap(IDispatch $objEvent)
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
                'Queryyetsimple\Mvc\View',
                'queryyetsimple\Mvc\IView'
            ]
        ];
    }

    /**
     * 设置模型事件
     *
     * @param \Queryyetsimple\Event\IDispatch $objEvent
     * @return void
     */
    protected function eventDispatch(IDispatch $objEvent)
    {
        Model::setEventDispatch($objEvent);
    }

    /**
     * 载入命令包
     *
     * @return void
     */
    protected function console()
    {
        $this->loadCommandNamespace('Queryyetsimple\Mvc\Console');
    }

    /**
     * Meta 设置数据库管理
     *
     * @return void
     */
    protected function meta()
    {
        Meta::setDatabaseManager($this->container['databases']);
    }
}
