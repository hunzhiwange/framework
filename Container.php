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
use ArrayAccess;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
use ReflectionParameter;
use BadMethodCallException;
use InvalidArgumentException;

/**
 * IOC 容器
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.04.13
 * @version 1.0
 */
class Container implements IContainer, ArrayAccess
{
    
    /**
     * 注册服务
     *
     * @var array
     */
    protected $services = [];

    /**
     * 注册的实例
     *
     * @var array
     */
    protected $instances = [];

    /**
     * 单一实例
     *
     * @var array
     */
    protected $singletons = [];

    /**
     * 别名支持
     *
     * @var array
     */
    protected $alias = [];

    /**
     * 分组
     *
     * @var array
     */
    protected $groups = [];

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
        if (is_array($name)) {
            list($name, $alias) = $this->parseAlias($name);
            $this->alias($name, $alias);
        }

        if (is_null($service)) {
            $service = $name;
        }

        $this->services[$name] = $service;

        if ($share) {
            $this->singletons[] = $name;
        }

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
        if (is_array($name)) {
            list($name, $alias) = $this->parseAlias($name);
            $this->alias($name, $alias);
        }

        if (is_null($service)) {
            $service = $name;
        }
        $this->instances[$name] = $service;

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
        return $this->bind($name, $service, true);
    }

    /**
     * 创建共享的闭包
     *
     * @param \Closure $closures
     * @return \Closure
     */
    public function share(Closure $closures)
    {
        return function ($container) use ($closures) {
            static $obj;

            if (is_null($obj)) {
                $obj = $closures($container);
            }

            return $obj;
        };
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
        if (is_array($alias)) {
            foreach ($alias as $key => $item) {
                if (is_int($key)) {
                    continue;
                }
                $this->alias($key, $item);
            }
        } else {
            $value = ( array ) $value;
            foreach ($value as $item) {
                $this->alias[$item] = $alias;
            }
        }

        return $this;
    }

    /**
     * 分组注册
     *
     * @param string $group
     * @param mixed $data
     * @return $this
     */
    public function group($group, $data)
    {
        if (! isset($this->groups[$group])) {
            $this->groups[$group] = [];
        }
        $this->groups[$group] = $data;

        return $this;
    }

    /**
     * 分组制造
     *
     * @param string $group
     * @param array $args
     * @return array
     */
    public function groupMake($group, array $args = [])
    {
        if (! isset($this->groups[$group])) {
            return [];
        }

        $result = [];
        $instance = (array) $this->groups[$group];
        foreach ($instance as $item) {
            $result[$item] = $this->make($item, $args);
        }

        return $result;
    }

    /**
     * 服务容器返回对象
     *
     * @param string $name
     * @param array $args
     * @return object|false
     */
    public function make($name, ?array $args = null)
    {
        // 别名
        $name = $this->getAlias($name);

        // 存在直接返回
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if (! $args) {
            $args = [];
        }

        // 生成实例
        if (! isset($this->services[$name])) {
            return $this->getInjectionObject($name, $args);
        }
 
        if (! is_string($this->services[$name]) && is_callable($this->services[$name])) {
            array_unshift($args, $this);
            $instance = call_user_func_array($this->services[$name], $args);
        } else {
            if (is_string($this->services[$name])) {
                $instance = $this->getInjectionObject($this->services[$name], $args);
            } else {
                $instance = $this->services[$name];
            }
        }

        // 单一实例
        if (in_array($name, $this->singletons)) {
            return $this->instances[$name] = $instance;
        }

        // 多个实例
        else {
            return $instance;
        }
    }

    /**
     * 实例回调自动注入
     *
     * @param callable $callback
     * @param array $args
     * @return mixed
     */
    public function call($callback, array $args = [])
    {
        if (! $args) {
            $args = $this->parseInjection($callback);
        }

        return call_user_func_array($callback, $args);
    }

    /**
     * 统一去掉前面的斜杠
     *
     * @param string $name
     * @return mixed
     */
    protected function normalize($name)
    {
        return ltrim($name, '\\');
    }

    /**
     * 返回对象别名
     *
     * @param string $name
     * @return string
     */
    protected function getAlias($name)
    {
        return isset($this->alias[$name]) ? $this->alias[$name] : $name;
    }

    /**
     * 根据 class 名字创建实例
     *
     * @param string $classname
     * @param array $args
     * @return object
     */
    protected function getInjectionObject($classname, array $args = [])
    {
        if (! class_exists($classname)) {
            return false;
        }

        if (! $args) {
            $args = $this->parseInjection($classname);
        }

        return $this->newInstanceArgs($classname, $args);
    }

    /**
     * 分析自动依赖注入
     *
     * @param mixed $injection
     * @return array
     */
    protected function parseInjection($injection)
    {
        $result = [];

        $param = $this->parseReflection($injection);

        foreach ($param as $item) {
            try {
                switch (true) {
                    case $argsclass = $this->parseParameterClass($item):
                        $data = $this->parseClassInstance($argsclass);
                        break;

                    case $item->isDefaultValueAvailable():
                        $data = $item->getDefaultValue();
                        break; 

                    default:
                        $data = null;
                        break;
                }

                $result[$item->name] = $data;
            } catch (ReflectionException $e) {
                throw new InvalidArgumentException($e->getMessage());
            }
        }

        return $result;
    }

    /**
     * 分析反射参数的类
     * 
     * @param \ReflectionParameter $param
     * @return boolean|string
     */
    protected function parseParameterClass(ReflectionParameter $param)
    {
        $classObject = $param->getClass();
        if (! $classObject || ! ($classObject instanceof ReflectionClass)) {
            return false;
        }

        return $classObject->getName();
    }

    /**
     * 解析反射参数类实例
     * 
     * @param string $argsclass
     * @return array
     */
    protected function parseClassInstance(string $argsclass)
    {
        switch (true) {
            case $result = $this->parseClassFromContainer($argsclass):
                break;

            case $result = $this->parseClassNotExists($argsclass):
                break;

            default:
                throw new InvalidArgumentException(sprintf('Class or interface %s is not register in container', $argsclass));
                break;
        }

        return $result;
    }

    /**
     * 从服务容器中获取解析反射参数类实例
     * 
     * @param string $argsclass
     * @return boolean|object
     */
    protected function parseClassFromContainer(string $argsclass)
    {
        $itemMake = $this->make($argsclass);
        if ($itemMake === false) {
            return false;
        }

        // 实例对象
        if (is_object($itemMake)) {
            return $itemMake;
        }

        // 接口绑定实现
        if (class_exists($itemMake)) {
            $result = $this->make($itemMake);
            if (! is_object($result)) {
                throw new InvalidArgumentException(sprintf('Class or interface %s is register in container is not object.', $argsclass));
            }

            return $result;
        }

        throw new InvalidArgumentException(sprintf('Class or interface %s is not register in container', $argsclass));
    }

    /**
     * 独立类作为解析反射参数类实例
     * 
     * @param string $argsclass
     * @return boolean|object
     */
    protected function parseClassNotExists(string $argsclass)
    {
        if (! class_exists($argsclass)) {
            return false;
        }

        $result = $this->make($argsclass);
        if (! is_object($result)) {
            throw new InvalidArgumentException(sprintf('Class or interface %s is register in container is not object.', $argsclass));
        }

        return $result;
    }

    /**
     * 不同的类型不同的反射
     * 
     * @param mixed $injection
     * @return array
     */
    protected function parseReflection($injection)
    {
        switch (true) {
            case $injection instanceof Closure:
                return $this->parseClosureReflection($injection);

            case ! is_string($injection) && is_callable($injection):
                return $this->parseMethodReflection($injection);

            case is_string($injection):
                return $this->parseClassReflection($injection);
            
            default:
                throw new InvalidArgumentException('Unsupported callback types.');
        }
    }

    /**
     * 解析闭包反射参数
     * 
     * @param Closure $injection
     * @return array
     */
    protected function parseClosureReflection(Closure $injection)
    {
        $reflection = new ReflectionFunction($injection);
        if (! ($param = $reflection->getParameters())) {
            $param = [];
        }

        return $param;
    }

    /**
     * 解析数组回调反射参数
     * 
     * @param array&callback $injection
     * @return array
     */
    protected function parseMethodReflection($injection)
    {
        $reflection = new ReflectionMethod($injection[0], $injection[1]);
        if (! ($param = $reflection->getParameters())) {
            $param = [];
        }

        return $param;
    }

    /**
     * 解析类反射参数
     * 
     * @param string $injection
     * @return array
     */
    protected function parseClassReflection(string $injection)
    {
        $reflection = new ReflectionClass($injection);

        if (! $reflection->isInstantiable()) {
            throw new InvalidArgumentException(sprintf('Class %s is not instantiable.', $injection));
        }

        $param = [];

        if (($constructor = $reflection->getConstructor())) {
            $param = $constructor->getParameters();
        }

        return $param;
    }

    /**
     * 动态创建实例对象
     *
     * @param string $classname
     * @param array $args
     * @return mixed
     */
    protected function newInstanceArgs($classname, $args)
    {
        return (new ReflectionClass($classname))->newInstanceArgs($args);
    }

    /**
     * 解析注册容器对象别名
     *
     * @param array $name
     * @return array
     */
    protected function parseAlias(array $name)
    {
        return [
            key($name),
            current($name)
        ];
    }

    /**
     * 实现 ArrayAccess::offsetExits
     *
     * @param string $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        return isset($this->services[$this->normalize($offset)]);
    }

    /**
     * 实现 ArrayAccess::offsetGet
     *
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->make($offset);
    }

    /**
     * 实现 ArrayAccess::offsetSet
     *
     * @param string $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        return $this->bind($offset, $value);
    }

    /**
     * 实现 ArrayAccess::offsetUnset
     *
     * @param string $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
        $offset = $this->normalize($offset);

        $prop = [
            'services',
            'instances',
            'singletons'
        ];

        foreach ($prop as $item) {
            if (isset($this->{$item}[$offset])) {
                unset($this->{$item}[$offset]);
            }
        }
    }

    /**
     * 捕捉支持属性参数
     *
     * @param string $key
     * @return 设置项
     */
    public function __get($key)
    {
        return $this[$key];
    }

    /**
     * 设置支持属性参数
     *
     * @param string $key
     * @param mixed $service
     * @return void
     */
    public function __set($key, $service)
    {
        $this[$key] = $service;
        return $this;
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
        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }
}
