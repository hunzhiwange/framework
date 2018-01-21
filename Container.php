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
namespace Queryyetsimple\Support;

use Closure;
use ArrayAccess;
use ReflectionClass;
use ReflectionMethod;
use ReflectionFunction;
use ReflectionException;
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
    use FlowControl;

    /**
     * 注册工厂
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
     * @param boolean $booShare
     * @return $this
     */
    public function bind($name, $service = null, $booShare = false)
    {
        if (is_array($name)) {
            list($name, $alias) = $this->parseAlias($name);
            $this->alias($name, $alias);
        }

        if (is_null($service)) {
            $service = $name;
        }

        $this->services[$name] = $service;

        if ($booShare) {
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
     * @param \Closure $closure
     * @return \Closure
     */
    public function share(Closure $closure)
    {
        return function ($container) use ($closure) {
            static $obj;

            if (is_null($obj)) {
                $obj = $container($container);
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
            foreach ($alias as $key => $value) {
                if (is_int($key)) {
                    continue;
                }
                $this->alias($key, $value);
            }
        } else {
            foreach (( array ) $value as $value) {
                $this->alias[$value] = $alias;
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
        foreach (( array ) $this->groups[$group] as $instance) {
            $result[$instance] = $this->make($instance, $args);
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
    public function make($name, array $args = [])
    {
        // 别名
        $name = $this->getAlias($name);

        // 存在直接返回
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
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
        if (($injection = $this->parseInjection($callback, $args)) && isset($injection['args'])) {
            $args = $this->getInjectionArgs($injection['args'], $args, $injection['class']);
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
     * @param string $class
     * @param array $args
     * @return object
     */
    protected function getInjectionObject($class, array $args = [])
    {
        if (! class_exists($class)) {
            return false;
        }

        // 注入构造器
        if (($injection = $this->parseInjection($class, $args)) && isset($injection['args'])) {
            return $this->newInstanceArgs($class, $this->getInjectionArgs($injection['args'], $args, $injection['class']));
        } else {
            return $this->newInstanceArgs($class, $args);
        }
    }

    /**
     * 分析自动依赖注入
     *
     * @param mixed $callback
     * @param array $args
     * @return array
     */
    protected function parseInjection($mixClassOrCallback, array &$arrArgs = [])
    {
        $arrResult = $arrParameter = [];
        $booFind = $booFindClass = false;

        list($objReflection, $arrParameter) = $this->parseReflection($mixClassOrCallback);

        foreach ($arrParameter as $intKey => $objParameter) {
            try {
                switch (true) {
                    case $objParameterClass = $this->parseParameterClass($objParameter):
                        $args = $this->parseClassParameter($objParameterClass, $arrArgs, $intKey);
                        $booFindClass = true;
                        break;

                    case $objParameter->isDefaultValueAvailable():
                        $args = $objParameter->getDefaultValue();
                        break; 

                    default:
                        $args = '';
                        break;
                }

                $arrResult['args'][$objParameter->name] = $args;
            } catch (ReflectionException $e) {
                throw new InvalidArgumentException($e->getMessage());
            }
        }

        $arrResult['class'] = $booFindClass;

        return $arrResult;
    }

    /**
     * [parseParameterClass description]
     * 
     * @param  [type] $objParameter [description]
     * @return [type]               [description]
     */
    protected function parseParameterClass($objParameter)
    {
        $class = $objParameter->getClass();
        if (! $class || ! ($class instanceof ReflectionClass)) {
            return false;
        }

        return $class->getName();
    }

    /**
     * [parseClassParameter description]
     * 
     * @param  [type] $objParameterClass [description]
     * @param  [type] $arrArgs           [description]
     * @param  [type] $intKey            [description]
     * @return [type]                    [description]
     */
    protected function parseClassParameter($objParameterClass, $arrArgs, $intKey)
    {
        switch (true) {
            // 参数中含有实例化
            case $args = $this->parseClassAlreadyInstance($objParameterClass, $arrArgs[$intKey] ?? false);
                unset($arrArgs[$intKey]);
                break;
            
            case $args = $this->parseClassFromContainer($objParameterClass):
                break;

            case $args = $this->parseClassNotExists($objParameterClass):
                break;

            default:
                throw new InvalidArgumentException(sprintf('Class or interface %s is not register in container', $objParameterClass));
                break;
        }

        return $args;
    }

    /**
     * [parseClassAlreadyInstance description]
     * 
     * @param  [type] $objParameterClass [description]
     * @param  [type] $args              [description]
     * @return [type]                    [description]
     */
    protected function parseClassAlreadyInstance($objParameterClass, $args)
    {
        if (! $args) {
            return false;
        }

        if (! ($args instanceof $objParameterClass)) {
            return false;
        } 

        return $args;
    }

    /**
     * [parseClassFromContainer description]
     * 
     * @param  [type] $objParameterClass [description]
     * @return [type]                    [description]
     */
    protected function parseClassFromContainer($objParameterClass)
    {
        $objParameterMake = $this->make($objParameterClass);

        if ($objParameterMake === false) {
            return false;
        }

        // 实例对象
        if (is_object($objParameterMake)) {
            return $objParameterMake;
        }

        // 接口绑定实现
        if (class_exists($objParameterMake)) {
            return $this->make($objParameterMake);
        }

        throw new InvalidArgumentException(sprintf('Class or interface %s is not register in container', $objParameterClass));
    }

    /**
     * [parseClassNotExists description]
     * 
     * @param  [type] $objParameterClass [description]
     * @return [type]                    [description]
     */
    protected function parseClassNotExists($objParameterClass)
    {
        if (! class_exists($objParameterClass)) {
            return false;
        }

        return $this->make($objParameterClass);
    }

    /**
     * [parseReflection description]
     * 
     * @param  [type] $mixClassOrCallback [description]
     * @return [type]                     [description]
     */
    protected function parseReflection($mixClassOrCallback)
    {
        switch (true) {
            case $mixClassOrCallback instanceof Closure:
                return $this->parseClosureReflection($mixClassOrCallback);
                break;

            case ! is_string($mixClassOrCallback) && is_callable($mixClassOrCallback):
                return $this->parseMethodReflection($mixClassOrCallback);
                break;

            case is_string($mixClassOrCallback):
                return $this->parseClassReflection($mixClassOrCallback);
                break;
            
            default:
                throw new InvalidArgumentException('Unsupported callback types.');
                break;
        }
    }

    /**
     * [parseClosureReflection description]
     * 
     * @param  [type] $mixClassOrCallback [description]
     * @return [type]                     [description]
     */
    protected function parseClosureReflection($mixClassOrCallback)
    {
        $objReflection = new ReflectionFunction($mixClassOrCallback);
        if (! ($arrParameter = $objReflection->getParameters())) {
            $arrParameter = [];
        }

        return [$objReflection, $arrParameter];
    }

    /**
     * [parseMethodReflection description]
     * 
     * @param  [type] $mixClassOrCallback [description]
     * @return [type]                     [description]
     */
    protected function parseMethodReflection($mixClassOrCallback)
    {
        $objReflection = new ReflectionMethod($mixClassOrCallback[0], $mixClassOrCallback[1]);
        if (! ($arrParameter = $objReflection->getParameters())) {
            $arrParameter = [];
        }

        return [$objReflection, $arrParameter];
    }

    /**
     * [parseClassReflection description]
     * 
     * @param  [type] $mixClassOrCallback [description]
     * @return [type]                     [description]
     */
    protected function parseClassReflection($mixClassOrCallback)
    {
        $objReflection = new ReflectionClass($mixClassOrCallback);
        if (! $objReflection->isInstantiable()) {
            throw new InvalidArgumentException(sprintf('Class %s is not instantiable.', $mixClassOrCallback));
        }
        if (($objConstructor = $objReflection->getConstructor()) && ! ($arrParameter = $objConstructor->getParameters())) {
            $arrParameter = [];
        }

        return [$objReflection, $arrParameter];
    }

    /**
     * 注入参数分析
     *
     * @param array $args
     * @param array $extends
     * @param boolean $findclass
     * @return array
     */
    protected function getInjectionArgs(array $args, array $extends = [], $findclass = false)
    {
        $result = [];

        foreach ($extends as $key => $extend) {
            if (isset($args[$key])) {
                unset($args[$key]);
            } elseif ($findclass === false) {
                array_shift($args);
            }
            $result[] = $extend;
        }

        foreach ($args as $arg) {
            $result[] = $arg;
        }

        unset($args, $extends);

        return $result;
    }

    /**
     * 动态创建实例对象
     *
     * @param string $class
     * @param array $args
     * @return mixed
     */
    protected function newInstanceArgs($class, $args)
    {
        return (new ReflectionClass($class))->newInstanceArgs($args);
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
     * 判断容器对象是否存在
     *
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return isset($this->services[$this->normalize($name)]);
    }

    /**
     * 获取一个容器对象
     *
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->make($name);
    }

    /**
     * 注册容器对象
     *
     * @param string $name
     * @param mixed $service
     * @return void
     */
    public function offsetSet($name, $service)
    {
        return $this->bind($name, $service);
    }

    /**
     * 删除一个容器对象
     *
     * @param string $name
     * @return void
     */
    public function offsetUnset($name)
    {
        $name = $this->normalize($name);

        foreach ([
            'Factorys',
            'Instances',
            'Singletons'
        ] as $item) {
            $item = 'arr' . $item;
            if (isset($this->{$item}[$name])) {
                unset($this->{$item}[$name]);
            }
        }
    }

    /**
     * 捕捉支持属性参数
     *
     * @param string $name 支持的项
     * @return 设置项
     */
    public function __get($name)
    {
        return $this[$name];
    }

    /**
     * 设置支持属性参数
     *
     * @param string $name 支持的项
     * @param mixed $service 支持的值
     * @return void
     */
    public function __set($name, $service)
    {
        $this[$name] = $service;
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
        if ($this->placeholderFlowControl($method)) {
            return $this;
        }

        throw new BadMethodCallException(sprintf('Method %s is not exits.', $method));
    }
}
