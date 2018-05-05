<?php
/*
 * This file is part of the ************************ package.
 * _____________                           _______________
 *  ______/     \__  _____  ____  ______  / /_  _________
 *   ____/ __   / / / / _ \/ __`\/ / __ \/ __ \/ __ \___
 *    __/ / /  / /_/ /  __/ /  \  / /_/ / / / / /_/ /__
 *      \_\ \_/\____/\___/_/   / / .___/_/ /_/ .___/
 *         \_\                /_/_/         /_/
 *
 * The PHP Framework For Code Poem As Free As Wind. <Query Yet Simple>
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Leevel\Mvc\Provider;

use Leevel\{
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
        $this->container->singleton('view', function ($project) {
            return new view($project['view.view']);
        });
    }

    /**
     * bootstrap
     *
     * @param \Leevel\Event\IDispatch $objEvent
     * @return void
     */
    public function bootstrap(IDispatch $objEvent)
    {
        $this->eventDispatch($objEvent);
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
                'Leevel\Mvc\View',
                'leevel\Mvc\IView'
            ]
        ];
    }

    /**
     * 设置模型事件
     *
     * @param \Leevel\Event\IDispatch $objEvent
     * @return void
     */
    protected function eventDispatch(IDispatch $objEvent)
    {
        Model::setEventDispatch($objEvent);
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
