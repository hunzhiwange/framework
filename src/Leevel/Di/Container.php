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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
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
 * - IOC Container.
 */
class Container implements IContainer, ArrayAccess
{
    /**
     * 当前应用实例.
     *
     * - Current application instance.
     *
     * @var \Leevel\Di\IContainer
     */
    protected static ?IContainer $instance = null;

    /**
     * 注册的服务.
     *
     * - Registered services.
     *
     * @var array
     */
    protected array $services = [];

    /**
     * 注册的实例.
     *
     * - Registered instances.
     *
     * @var array
     */
    protected array $instances = [];

    /**
     * 注册的单一实例.
     *
     * - Registered singletons.
     *
     * @var array
     */
    protected array $singletons = [];

    /**
     * 别名支持.
     *
     * @var array
     */
    protected array $alias = [];

    /**
     * 协程.
     *
     * - Coroutine.
     *
     * @var \Leevel\Di\ICoroutine
     */
    protected ?ICoroutine $coroutine = null;

    /**
     * 协程上下文注册的实例.
     *
     * - Registered instances of coroutine context.
     *
     * @var array
     */
    protected array $coroutineInstances = [];

    /**
     * 是否已经初始引导.
     *
     * - Has it been initially booted.
     *
     * @var bool
     */
    protected bool $isBootstrap = false;

    /**
     * 延迟载入服务提供者.
     *
     * @var array
     */
    protected array $deferredProviders = [];

    /**
     * 服务提供者引导.
     *
     * @var array
     */
    protected array $providerBootstraps = [];

    /**
     * 禁止克隆.
     *
     * @throws \RuntimeException
     */
    public function __clone()
    {
        $e = 'IOC container disallowed clone.';

        throw new RuntimeException($e);
    }

    /**
     * 捕捉支持属性参数.
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
     * @param mixed $service
     */
    public function __set(string $key, $service): IContainer
    {
        $this[$key] = $service;

        return $this;
    }

    /**
     * call.
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
     *
     * @return \Leevel\Di\IContainer
     */
    public function bind($name, $service = null, bool $share = false, bool $coroutine = false): IContainer
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

        if ($coroutine) {
            $this->serviceCoroutine($name);
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
    public function instance($name, $service = null, int $cid = self::NOT_COROUTINE_ID): IContainer
    {
        if (is_array($name)) {
            list($name, $alias) = $this->parseAlias($name);
            $this->alias($name, $alias);
        }

        if (null === $service) {
            $service = $name;
        }

        if ($cid > self::NOT_COROUTINE_ID) {
            $this->serviceCoroutine($name);
        }

        if ($this->inCroutineContext($service)) {
            $cid = $this->getCoroutineId($cid > self::NOT_COROUTINE_ID ? $cid : self::DEFAULT_COROUTINE_ID);
            $this->coroutineInstances[$cid][$name] = $service;
        } else {
            $this->instances[$name] = $service;
        }

        return $this;
    }

    /**
     * 注册单一实例.
     *
     * @param mixed $name
     * @param mixed $service
     *
     * @return \Leevel\Di\IContainer
     */
    public function singleton($name, $service = null, bool $coroutine = false): IContainer
    {
        return $this->bind($name, $service, true, $coroutine);
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
     * 创建容器服务并返回.
     *
     * @return mixed
     */
    public function make(string $name, array $args = [], int $cid = self::DEFAULT_COROUTINE_ID)
    {
        // 别名
        $name = $this->getAlias($name);
        if (isset($this->deferredProviders[$name])) {
            $this->registerDeferredProvider($name);
        }

        // 存在直接返回
        if ($this->existsCoroutine($name, $cid)) {
            return $this->coroutineInstances[$this->getCoroutineId($cid)][$name];
        }

        if (isset($this->instances[$name])) {
            return $this->instances[$name];
        }

        // 生成实例
        if (!isset($this->services[$name])) {
            $instance = $this->getInjectionObject($name, $args);
        } else {
            if (!is_string($this->services[$name]) && is_callable($this->services[$name])) {
                array_unshift($args, $this);
                $instance = $this->services[$name](...$args);
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
            if ($this->inCroutineContext($instance)) {
                return $this->coroutineInstances[$this->getCoroutineId($cid)][$name] = $instance;
            }

            return $this->instances[$name] = $instance;
        }

        return $instance;
    }

    /**
     * 回调自动依赖注入.
     *
     * @param array|callable|string $callback
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
                    $e = 'The class name must be string.';

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
     */
    public function remove(string $name, int $cid = self::DEFAULT_COROUTINE_ID): void
    {
        $name = $this->normalize($name);
        foreach (['services', 'instances', 'singletons'] as $item) {
            if (isset($this->{$item}[$name])) {
                unset($this->{$item}[$name]);
            }
        }

        if ($this->existsCoroutine($name, $cid)) {
            unset($this->coroutineInstances[$this->getCoroutineId($cid)][$name]);
        }

        foreach ($this->alias as $alias => $service) {
            if ($name === $service) {
                unset($this->alias[$alias]);
            }
        }
    }

    /**
     * 服务或者实例是否存在.
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
     * 执行服务提供者 bootstrap.
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
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * 注册服务提供者.
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
        $this->isBootstrap = true;
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
     * @return null|\Leevel\Di\ICoroutine
     */
    public function getCoroutine(): ?ICoroutine
    {
        return $this->coroutine;
    }

    /**
     * 协程服务或者实例是否存在.
     */
    public function existsCoroutine(string $name, int $cid = self::DEFAULT_COROUTINE_ID): bool
    {
        $cid = $this->getCoroutineId($cid);

        return isset($this->coroutineInstances[$cid], $this->coroutineInstances[$cid][$name]);
    }

    /**
     * 删除协程上下文服务和实例.
     */
    public function removeCoroutine(?string $name = null, int $cid = self::DEFAULT_COROUTINE_ID): void
    {
        if (!$this->coroutine) {
            return;
        }

        $cid = $this->getCoroutineId($cid);
        if (null === $name) {
            if (isset($this->coroutineInstances[$cid])) {
                unset($this->coroutineInstances[$cid]);
            }
        } else {
            $name = $this->normalize($name);
            if ($this->existsCoroutine($name, $cid)) {
                unset($this->coroutineInstances[$cid][$name]);
            }
        }
    }

    /**
     * 设置服务到协程上下文.
     */
    public function serviceCoroutine(string $service): void
    {
        if (!$this->coroutine) {
            return;
        }

        $this->coroutine->addContext($service);
    }

    /**
     * 实现 ArrayAccess::offsetExits.
     *
     * @param mixed $index
     */
    public function offsetExists($index): bool
    {
        return $this->exists($index);
    }

    /**
     * 实现 ArrayAccess::offsetGet.
     *
     * @param mixed $index
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
     * @param mixed $index
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
        foreach ($this->providerBootstraps as $item) {
            $this->callProviderBootstrap($item);
        }
    }

    /**
     * 注册延迟载入服务提供者.
     */
    protected function registerDeferredProvider(string $provider): void
    {
        $providerInstance = $this->register($this->deferredProviders[$provider]);
        $this->callProviderBootstrap($providerInstance);
        unset($this->deferredProviders[$provider]);
    }

    /**
     * 统一去掉前面的斜杠.
     */
    protected function normalize(string $name): string
    {
        return ltrim($name, '\\');
    }

    /**
     * 返回对象别名.
     */
    protected function getAlias(string $name): string
    {
        return $this->alias[$name] ?? $name;
    }

    /**
     * 是否处于协程上下文.
     *
     * @param mixed $instance
     */
    protected function inCroutineContext($instance): bool
    {
        if (!$this->coroutine) {
            return false;
        }

        if (is_object($instance)) {
            $instance = get_class($instance);
        }

        return $this->coroutine->inContext($instance);
    }

    /**
     * 当前协程 ID.
     */
    protected function getCoroutineId(int $cid = self::DEFAULT_COROUTINE_ID): int
    {
        if (self::NOT_COROUTINE_ID === $cid || !$this->coroutine) {
            return self::NOT_COROUTINE_ID;
        }

        return $cid ?: $this->coroutine->cid();
    }

    /**
     * 根据 class 名字创建实例.
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
     *
     * @throws \Leevel\Di\ContainerInvalidArgumentException
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
     *
     * @throws \InvalidArgumentException
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
                    case $argsclass = $this->parseParamClass($item):
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

        return [$result, $required, $validArgs];
    }

    /**
     * 分析反射参数的类.
     *
     * @return bool|string
     */
    protected function parseParamClass(ReflectionParameter $param)
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
     * @throws \InvalidArgumentException
     */
    protected function parseClassFromContainer(string $argsclass): object
    {
        $itemMake = $this->make($argsclass);
        if (is_object($itemMake)) {
            return $itemMake;
        }

        $e = sprintf('Class or interface %s is register in container is not object.', $argsclass);

        throw new InvalidArgumentException($e);
    }

    /**
     * 不同的类型不同的反射.
     *
     * @param mixed $injection
     *
     * @throws \InvalidArgumentException
     */
    protected function parseReflection($injection): array
    {
        switch (true) {
            case $injection instanceof Closure:
                return $this->parseClosureReflection($injection);
            case is_array($injection) && is_callable($injection):
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
     */
    protected function parseClosureReflection(Closure $injection): array
    {
        return (new ReflectionFunction($injection))->getParameters();
    }

    /**
     * 解析数组回调反射参数.
     */
    protected function parseMethodReflection(array $injection): array
    {
        return (new ReflectionMethod($injection[0], $injection[1]))->getParameters();
    }

    /**
     * 解析类反射参数.
     *
     * @throws \InvalidArgumentException
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
     * @return mixed
     */
    protected function newInstanceArgs(string $classname, array $args)
    {
        try { // @codeCoverageIgnore
            return (new ReflectionClass($classname))->newInstanceArgs($args);
            // @codeCoverageIgnoreStart
        } catch (ReflectionException $e) {
            return (new ReflectionClass($classname))->newInstanceWithoutConstructor();
        }
        // @codeCoverageIgnoreEnd
    }

    /**
     * 解析注册容器对象别名.
     */
    protected function parseAlias(array $name): array
    {
        return [key($name), current($name)];
    }
}
