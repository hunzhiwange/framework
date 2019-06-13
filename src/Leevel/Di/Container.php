<?php

declare(strict_types=1);

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
 * (c) 2010-2019 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Leevel\Di;

use ArrayAccess;
use BadMethodCallException;
use Closure;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionParameter;
use RuntimeException;

/**
 * IOC 容器.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.13
 *
 * @version 1.0
 */
class Container implements IContainer, ArrayAccess
{
    /**
     * 当前应用实例.
     *
     * @var static
     */
    protected static $instance;

    /**
     * 注册服务.
     *
     * @var array
     */
    protected $services = [];

    /**
     * 注册的实例.
     *
     * @var array
     */
    protected $instances = [];

    /**
     * 单一实例.
     *
     * @var array
     */
    protected $singletons = [];

    /**
     * 别名支持.
     *
     * @var array
     */
    protected $alias = [];

    /**
     * 协程.
     *
     * @var \Leevel\Di\ICoroutine
     */
    protected $coroutine;

    /**
     * 协程上下文注册的实例.
     *
     * @var array
     */
    protected $coroutineInstances = [];

    /**
     * 是否已经初始化引导
     *
     * @var bool
     */
    protected $isBootstrap = false;

    /**
     * 延迟载入服务提供者.
     *
     * @var array
     */
    protected $deferredProviders = [];

    /**
     * 服务提供者引导
     *
     * @var array
     */
    protected $providerBootstraps = [];

    /**
     * 禁止克隆.
     *
     * @throws \RuntimeException
     */
    public function __clone()
    {
        $e = 'Ioc container disallowed clone.';

        throw new RuntimeException($e);
    }

    /**
     * 捕捉支持属性参数.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this[$key];
    }

    /**
     * 设置支持属性参数.
     *
     * @param string $key
     * @param mixed  $service
     */
    public function __set(string $key, $service): IContainer
    {
        $this[$key] = $service;

        return $this;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \BadMethodCallException
     *
     * @return mixed
     */
    public function __call(string $method, array $args)
    {
        $e = sprintf('Method `%s` is not exits.', $method);

        throw new BadMethodCallException($e);
    }

    /**
     * 生成 IOC 容器.
     *
     * @return \Leevel\Di\IContainer
     */
    public static function singletons(): IContainer
    {
        if (null !== static::$instance) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * 注册到容器.
     *
     * @param mixed $name
     * @param mixed $service
     * @param bool  $share
     *
     * @return \Leevel\Di\IContainer
     */
    public function bind($name, $service = null, bool $share = false): IContainer
    {
        if (is_array($name)) {
            list($name, $alias) = $this->parseAlias($name);
            $this->alias($name, $alias);
        }

        if (null === $service) {
            $service = $name;
        }

        $this->services[$name] = $service;

        if ($share) {
            $this->singletons[] = $name;
        }

        return $this;
    }

    /**
     * 注册为实例.
     *
     * @param mixed $name
     * @param mixed $service
     *
     * @return \Leevel\Di\IContainer
     */
    public function instance($name, $service = null): IContainer
    {
        if (is_array($name)) {
            list($name, $alias) = $this->parseAlias($name);
            $this->alias($name, $alias);
        }

        if (null === $service) {
            $service = $name;
        }

        if ($this->coroutineContext($service)) {
            $this->coroutineInstances[$this->coroutineUid()][$name] = $service;
        } else {
            $this->instances[$name] = $service;
        }

        return $this;
    }

    /**
     * 注册单一实例.
     *
     * @param array|scalar $name
     * @param mixed        $service
     *
     * @return \Leevel\Di\IContainer
     */
    public function singleton($name, $service = null): IContainer
    {
        return $this->bind($name, $service, true);
    }

    /**
     * 设置别名.
     *
     * @param array|string      $alias
     * @param null|array|string $value
     *
     * @return \Leevel\Di\IContainer
     */
    public function alias($alias, $value = null): IContainer
    {
        if (is_array($alias)) {
            foreach ($alias as $key => $item) {
                if (is_int($key)) {
                    continue;
                }

                $this->alias($key, $item);
            }
        } else {
            $value = (array) $value;

            foreach ($value as $item) {
                $this->alias[$item] = $alias;
            }
        }

        return $this;
    }

    /**
     * 服务容器返回对象.
     *
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public function make(string $name, array $args = [])
    {
        // 别名
        $name = $this->getAlias($name);

        if (isset($this->deferredProviders[$name])) {
            $this->registerDeferredProvider($name);
        }

        // 存在直接返回
        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        if ($this->existsCoroutine($name)) {
            return $this->coroutineInstances[$this->coroutineUid()][$name];
        }

        // 生成实例
        if (!isset($this->services[$name])) {
            $instance = $this->getInjectionObject($name, $args);
        } else {
            if (!is_string($this->services[$name]) && is_callable($this->services[$name])) {
                array_unshift($args, $this);
                $instance = call_user_func_array($this->services[$name], $args);
            } else {
                if (is_string($this->services[$name])) {
                    $instance = $this->getInjectionObject($this->services[$name], $args);
                } else {
                    $instance = $this->services[$name];
                }
            }
        }

        // 单一实例
        if (in_array($name, $this->singletons, true)) {
            if ($this->coroutineContext($instance)) {
                return $this->coroutineInstances[$this->coroutineUid()][$name] = $instance;
            }

            return $this->instances[$name] = $instance;
        }

        // 多个实例

        return $instance;
    }

    /**
     * 实例回调自动注入.
     *
     * @param array|callable|string $callback
     * @param array                 $args
     *
     * @throws \InvalidArgumentException
     *
     * @return mixed
     */
    public function call($callback, array $args = [])
    {
        $isStatic = false;

        if (is_string($callback)) {
            if (false !== strpos($callback, '@')) {
                $callback = explode('@', $callback);
            } elseif (false !== strpos($callback, '::')) {
                $callback = explode('::', $callback);
                $isStatic = true;
            }
        }

        if (false === $isStatic && is_array($callback)) {
            if (!is_object($callback[0])) {
                if (!is_string($callback[0])) {
                    $e = 'The classname must be string.';

                    throw new InvalidArgumentException($e);
                }

                $callback[0] = $this->getInjectionObject($callback[0]);
            }

            if (empty($callback[1])) {
                $callback[1] = 'handle';
            }
        }

        $args = $this->normalizeInjectionArgs($callback, $args);

        return call_user_func_array($callback, $args);
    }

    /**
     * 删除服务和实例.
     *
     * @param string $name
     */
    public function remove(string $name): void
    {
        $name = $this->normalize($name);

        $prop = [
            'services',
            'instances',
            'singletons',
        ];

        foreach ($prop as $item) {
            if (isset($this->{$item}[$name])) {
                unset($this->{$item}[$name]);
            }
        }

        if ($this->existsCoroutine($name)) {
            unset($this->coroutineInstances[$this->coroutineUid()][$name]);
        }

        foreach ($this->alias as $alias => $service) {
            if ($name === $service) {
                unset($this->alias[$alias]);
            }
        }
    }

    /**
     * 删除协程上下文服务和实例.
     *
     * @param string $name
     */
    public function removeCoroutine(?string $name = null): void
    {
        if (!$this->coroutine) {
            return;
        }

        if (null === $name) {
            if (isset($this->coroutineInstances[$this->coroutineUid()])) {
                unset($this->coroutineInstances[$this->coroutineUid()]);
            }
        } else {
            $name = $this->normalize($name);

            if ($this->existsCoroutine($name)) {
                unset($this->coroutineInstances[$this->coroutineUid()][$name]);
            }
        }
    }

    /**
     * 服务或者实例是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function exists(string $name): bool
    {
        $name = $this->normalize($name);

        $name = $this->getAlias($name);

        return isset($this->services[$name]) ||
            isset($this->instances[$name]) ||
            $this->existsCoroutine($name);
    }

    /**
     * 清理容器.
     */
    public function clear(): void
    {
        $prop = [
            'services',
            'instances',
            'singletons',
            'coroutineInstances',
            'alias',
        ];

        foreach ($prop as $item) {
            $this->{$item} = [];
        }

        $this->isBootstrap = false;
    }

    /**
     * 执行 bootstrap.
     *
     * @param \Leevel\Di\Provider $provider
     */
    public function callProviderBootstrap(Provider $provider): void
    {
        if (!method_exists($provider, 'bootstrap')) {
            return;
        }

        $this->call([$provider, 'bootstrap']);
    }

    /**
     * 创建服务提供者.
     *
     * @param string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function makeProvider(string $provider): Provider
    {
        return new $provider($this);
    }

    /**
     * 注册服务提供者.
     *
     * @param \Leevel\Di\Provider|string $provider
     *
     * @return \Leevel\Di\Provider
     */
    public function register($provider): Provider
    {
        if (is_string($provider)) {
            $provider = $this->makeProvider($provider);
        }

        if (method_exists($provider, 'register')) {
            $provider->register();
        }

        if ($this->isBootstrap) {
            $this->callProviderBootstrap($provider);
        }

        return $provider;
    }

    /**
     * 是否已经初始化引导.
     *
     * @return bool
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * 框架基础提供者 register.
     */
    public function registerProviders(array $providers, array $deferredProviders = [], array $deferredAlias = []): void
    {
        if ($this->isBootstrap) {
            return;
        }

        $this->deferredProviders = $deferredProviders;

        foreach ($deferredAlias as $alias) {
            $this->alias($alias);
        }

        foreach ($providers as $provider) {
            $provider = $this->register($provider);

            if (method_exists($provider, 'bootstrap')) {
                $this->providerBootstraps[] = $provider;
            }
        }

        $this->bootstrapProviders();
    }

    /**
     * 设置协程.
     *
     * @param \Leevel\Di\ICoroutine $coroutine
     */
    public function setCoroutine(ICoroutine $coroutine): void
    {
        $this->coroutine = $coroutine;
    }

    /**
     * 返回协程.
     *
     * @return \Leevel\Di\ICoroutine
     */
    public function getCoroutine(): ?ICoroutine
    {
        return $this->coroutine;
    }

    /**
     * 协程服务或者实例是否存在.
     *
     * @param string $name
     *
     * @return bool
     */
    public function existsCoroutine(string $name): bool
    {
        return false !== $this->coroutineUid() &&
            isset($this->coroutineInstances[$this->coroutineUid()], $this->coroutineInstances[$this->coroutineUid()][$name]);
    }

    /**
     * 实现 ArrayAccess::offsetExits.
     *
     * @param string $index
     *
     * @return bool
     */
    public function offsetExists($index): bool
    {
        return $this->exists($index);
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param string $index
     *
     * @return mixed
     */
    public function offsetGet($index)
    {
        return $this->make($index);
    }

    /**
     * 实现 ArrayAccess::offsetSet.
     *
     * @param mixed $index
     * @param mixed $newval
     */
    public function offsetSet($index, $newval): void
    {
        $this->bind($index, $newval);
    }

    /**
     * 实现 ArrayAccess::offsetUnset.
     *
     * @param string $index
     */
    public function offsetUnset($index): void
    {
        $this->remove($index);
    }

    /**
     * 执行框架基础提供者引导.
     */
    protected function bootstrapProviders(): void
    {
        if ($this->isBootstrap) {
            return;
        }

        foreach ($this->providerBootstraps as $item) {
            $this->callProviderBootstrap($item);
        }

        $this->isBootstrap = true;
    }

    /**
     * 注册延迟载入服务提供者.
     *
     * @param string $provider
     */
    protected function registerDeferredProvider(string $provider): void
    {
        if (!isset($this->deferredProviders[$provider])) {
            return;
        }

        $providerInstance = $this->register($this->deferredProviders[$provider]);

        $this->callProviderBootstrap($providerInstance);

        unset($this->deferredProviders[$provider]);
    }

    /**
     * 统一去掉前面的斜杠.
     *
     * @param string $name
     *
     * @return string
     */
    protected function normalize(string $name): string
    {
        return ltrim($name, '\\');
    }

    /**
     * 返回对象别名.
     *
     * @param string $name
     *
     * @return string
     */
    protected function getAlias(string $name): string
    {
        return $this->alias[$name] ?? $name;
    }

    /**
     * 是否处于协程上下文.
     *
     * @param mixed $instance
     *
     * @return bool
     */
    protected function coroutineContext($instance): bool
    {
        if (!$this->coroutine) {
            return false;
        }

        if (is_object($instance)) {
            $instance = get_class($instance);
        }

        return $this->coroutine->context($instance);
    }

    /**
     * 当前协程 ID.
     *
     * @return false|int
     */
    protected function coroutineUid()
    {
        if (!$this->coroutine) {
            return false;
        }

        return $this->coroutine->uid();
    }

    /**
     * 根据 class 名字创建实例.
     *
     * @param string $classname
     * @param array  $args
     *
     * @throws \Leevel\Di\ContainerInvalidArgumentException
     *
     * @return object|string
     */
    protected function getInjectionObject(string $classname, array $args = [])
    {
        if (interface_exists($classname)) {
            $e = sprintf('Interface %s cannot be normalize because not binded.', $classname);

            throw new ContainerInvalidArgumentException($e);
        }

        if (!class_exists($classname)) {
            return $classname;
        }

        $args = $this->normalizeInjectionArgs($classname, $args);

        return $this->newInstanceArgs($classname, $args);
    }

    /**
     * 格式化依赖参数.
     *
     * @param mixed $value
     * @param array $args
     *
     * @throws \Leevel\Di\ContainerInvalidArgumentException
     *
     * @return array
     */
    protected function normalizeInjectionArgs($value, array $args): array
    {
        list($args, $required, $validArgs) = $this->parseInjection($value, $args);

        if ($validArgs < $required) {
            $e = sprintf('There are %d required args,but %d gived.', $required, $validArgs);

            throw new ContainerInvalidArgumentException($e);
        }

        return $args;
    }

    /**
     * 分析自动依赖注入.
     *
     * @param mixed $injection
     * @param array $args
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseInjection($injection, array $args = []): array
    {
        $result = [];
        $required = 0;

        $param = $this->parseReflection($injection);
        $validArgs = count($param);

        foreach ($param as $key => $item) {
            try {
                switch (true) {
                    case $argsclass = $this->parseParameterClass($item):
                        $argsclass = (string) $argsclass;

                        if (isset($args[0]) && is_object($args[0]) && $args[0] instanceof $argsclass) {
                            $data = array_shift($args);
                        } elseif (array_key_exists($argsclass, $args)) {
                            $data = $args[$argsclass];
                        } elseif ($item->isDefaultValueAvailable()) {
                            $data = $item->getDefaultValue();
                        } else {
                            $data = $this->parseClassFromContainer($argsclass);
                        }

                        $required++;
                        $validArgs++;

                        break;
                    case $item->isDefaultValueAvailable():
                        $data = array_key_exists($item->name, $args) ? $args[$item->name] : $item->getDefaultValue();

                        break;
                    default:
                        $required++;

                        if (array_key_exists($item->name, $args)) {
                            $data = $args[$item->name];
                            $validArgs++;
                        } else {
                            if (isset($args[0])) {
                                $data = array_shift($args);
                            } else {
                                $validArgs--;
                                $data = null;
                            }
                        }

                        break;
                }

                $result[$item->name] = $data;
            } catch (ReflectionException $e) {
                throw new InvalidArgumentException($e->getMessage());
            }
        }

        if ($args) {
            $result = array_values($result);

            // 定义函数 function(IFoo $foo)，调用方法 call([1, 2])，则进行基本的 count($result) 逻辑
            // 定义函数 function($foo)，调用方法 call([1, 2])，则进行 -($required-$validArgs) 填充 null
            $min = count($result) - ($required - $validArgs);

            foreach ($args as $k => $value) {
                if (is_int($k)) {
                    $result[$k + $min] = $value;
                    $validArgs++;
                }
            }
        }

        return [
            $result,
            $required,
            $validArgs,
        ];
    }

    /**
     * 分析反射参数的类.
     *
     * @param \ReflectionParameter $param
     *
     * @return bool|string
     */
    protected function parseParameterClass(ReflectionParameter $param)
    {
        $classObject = $param->getClass();

        if (!$classObject || !($classObject instanceof ReflectionClass)) {
            return false;
        }

        return $classObject->getName();
    }

    /**
     * 从服务容器中获取解析反射参数类实例.
     *
     * @param string $argsclass
     *
     * @throws \InvalidArgumentException
     *
     * @return bool|object
     */
    protected function parseClassFromContainer(string $argsclass)
    {
        $itemMake = $this->make($argsclass);

        // 实例对象
        if (is_object($itemMake)) {
            return $itemMake;
        }

        // 接口绑定实现
        $result = $this->make($itemMake);

        if (!is_object($result)) {
            $e = sprintf('Class or interface %s is register in container is not object.', $argsclass);

            throw new InvalidArgumentException($e);
        }

        return $result;
    }

    /**
     * 不同的类型不同的反射.
     *
     * @param mixed $injection
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseReflection($injection): array
    {
        switch (true) {
            case $injection instanceof Closure:
                return $this->parseClosureReflection($injection);
            case !is_string($injection) && is_callable($injection):
                return $this->parseMethodReflection($injection);
            case is_string($injection):
                return $this->parseClassReflection($injection);
            default:
                $e = 'Unsupported callback types.';

                throw new InvalidArgumentException($e);
        }
    }

    /**
     * 解析闭包反射参数.
     *
     * @param Closure $injection
     *
     * @return array
     */
    protected function parseClosureReflection(Closure $injection): array
    {
        $reflection = new ReflectionFunction($injection);

        if (!($param = $reflection->getParameters())) {
            $param = [];
        }

        return $param;
    }

    /**
     * 解析数组回调反射参数.
     *
     * @param array&callback $injection
     *
     * @return array
     */
    protected function parseMethodReflection(callable $injection): array
    {
        $reflection = new ReflectionMethod($injection[0], $injection[1]);

        if (!($param = $reflection->getParameters())) {
            $param = [];
        }

        return $param;
    }

    /**
     * 解析类反射参数.
     *
     * @param string $injection
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function parseClassReflection(string $injection): array
    {
        $reflection = new ReflectionClass($injection);

        if (!$reflection->isInstantiable()) {
            $e = sprintf('Class %s is not instantiable.', $injection);

            throw new InvalidArgumentException($e);
        }

        $param = [];

        if (($constructor = $reflection->getConstructor())) {
            $param = $constructor->getParameters();
        }

        return $param;
    }

    /**
     * 动态创建实例对象.
     *
     * - zephir 版本会执行到 newInstanceWithoutConstructor.
     * - 例子：Class Tests\\Event\\ListenerNotExtends does not
     * have a constructor, so you cannot pass any constructor arguments.
     *
     * @param string $classname
     * @param array  $args
     *
     * @return mixed
     */
    protected function newInstanceArgs(string $classname, array $args)
    {
        try { // @codeCoverageIgnore
            return (new ReflectionClass($classname))->newInstanceArgs($args);
        } catch (ReflectionException $e) { // @codeCoverageIgnore
            return (new ReflectionClass($classname))->newInstanceWithoutConstructor(); // @codeCoverageIgnore
        } // @codeCoverageIgnore
    }

    /**
     * 解析注册容器对象别名.
     *
     * @param array $name
     *
     * @return array
     */
    protected function parseAlias(array $name): array
    {
        return [key($name), current($name)];
    }
}
