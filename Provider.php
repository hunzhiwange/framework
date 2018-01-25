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
namespace Queryyetsimple\Di;

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
     * @var \Queryyetsimple\Di\IContainer
     */
    protected $container;

    /**
     * 创建一个服务容器提供者实例
     *
     * @param \Queryyetsimple\Di\IContainer $container
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
     * @return \Queryyetsimple\Di\IContainer
     */
    public function container()
    {
        return $this->container;
    }

    /**
     * 注册到容器
     *
     * @param mixed $name
     * @param mixed $service
     * @param boolean $share
     * @return $this
     */
    public function bind($name, $service = null, bool $share = false)
    {
        $this->container->bind($name, $service, $share);
        return $this;
    }

    /**
     * 注册为实例
     *
     * @param mixed $name
     * @param mixed $service
     * @return $this
     */
    public function instance($name, $service = null)
    {
        $this->container->instance($name, $service);
        return $this;
    }

    /**
     * 注册单一实例
     *
     * @param scalar|array $name
     * @param mixed $service
     * @return $this
     */
    public function singleton($name, $service = null)
    {
        $this->container->singleton($name, $service);
        return $this;
    }

    /**
     * 创建共享的闭包
     *
     * @param \Closure $closures
     * @return \Closure
     */
    public function share(Closure $closures)
    {
        return $this->container->share($closures);
    }

    /**
     * 设置别名
     *
     * @param array|string $alias
     * @param string|null|array $value
     * @return $this
     */
    public function alias($alias, $value = null)
    {
        $this->container->alias($alias, $value);
        return $this;
    }

    /**
     * 添加语言包目录
     *
     * @param mixed $dir
     * @return void
     */
    protected function loadI18nDir($dir)
    {
        if (! $this->container['option']['i18n\on']) {
            return;
        }

        if ($this->container['option']['i18n\develop'] == $this->container['option']['i18n\default']) {
            return;
        }
        
        if (! is_array($dir)) {
            $dir = ( array ) $dir;
        }
        $this->container['i18n.load']->addDir($dir);
    }

    /**
     * 添加命令包命名空间
     *
     * @param mixed $namespaces
     * @return void
     */
    protected function loadCommandNamespace($namespaces)
    {
        if (! $this->container->console()) {
            return;
        }

        $result = [];

        if (! is_array($namespaces)) {
            $namespaces = ( array ) $namespaces;
        }

        foreach ($namespaces as $item) {
            $result[$item] = $this->container['psr4']->namespaces($item);
        }

        $this->container['console.load']->addNamespace($result);
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
