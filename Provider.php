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
namespace Leevel\Di;

use Closure;
use BadMethodCallException;

/**
 * 服务提供者
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
abstract class Provider
{

    /**
     * 是否延迟载入
     *
     * @var boolean
     */
    public static $defer = false;

    /**
     * IOC 容器
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 创建一个服务容器提供者实例
     *
     * @param \Leevel\Di\IContainer $container
     * @return void
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;

        if (! static::isDeferred()) {
            $this->registerAlias();
        }
    }

    /**
     * 注册服务
     *
     * @return void
     */
    abstract public function register();

    /**
     * 注册服务别名
     *
     * @return void
     */
    public function registerAlias()
    {
        if (! static::isDeferred() && static::providers()) {
            $this->alias(static::providers());
        }
    }

    /**
     * 可用服务提供者
     *
     * @return array
     */
    public static function providers()
    {
        return [];
    }

    /**
     * 是否延迟载入
     *
     * @return boolean
     */
    public static function isDeferred()
    {
        return static::$defer;
    }

    /**
     * 返回 IOC 容器
     *
     * @return \Leevel\Di\IContainer
     */
    public function container()
    {
        return $this->container;
    }

    /**
     * call 
     *
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        if ($method == 'bootstrap') {
            return;
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }
}
