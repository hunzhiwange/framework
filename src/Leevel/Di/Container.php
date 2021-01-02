<?php

declare(strict_types=1);

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
     */
    protected static ?IContainer $instance = null;

    /**
     * 注册的服务.
     *
     * - Registered services.
     */
    protected array $services = [];

    /**
     * 注册的实例.
     *
     * - Registered instances.
     */
    protected array $instances = [];

    /**
     * 注册的单一实例.
     *
     * - Registered singletons.
     */
    protected array $singletons = [];

    /**
     * 别名支持.
     */
    protected array $alias = [];

    /**
     * 协程.
     *
     * - Coroutine.
     */
    protected ?ICoroutine $coroutine = null;

    /**
     * 协程上下文注册的实例.
     *
     * - Registered instances of coroutine context.
     */
    protected array $coroutineInstances = [];

    /**
     * 是否已经初始引导.
     *
     * - Has it been initially booted.
    */
    protected bool $isBootstrap = false;

    /**
     * 延迟载入服务提供者.
     */
    protected array $deferredProviders = [];

    /**
     * 服务提供者引导.
     */
    protected array $providerBootstraps = [];

    /**
     * 实现魔术方法 __clone.
     * 
     * - 禁止克隆.
     *
     * @throws \RuntimeException
     */
    public function __clone()
    {
        $e = 'IOC container disallowed clone.';

        throw new RuntimeException($e);
    }

    /**
     * 实现魔术方法 __get.
     */
    public function __get(string $key): mixed
    {
        return $this[$key];
    }

    /**
     * 实现魔术方法 __set.
     */
    public function __set(string $key, $service): void
    {
        $this[$key] = $service;
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args): mixed
    {
        $e = sprintf('Method `%s` is not exits.', $method);

        throw new BadMethodCallException($e);
    }

    /**
     * {@inheritDoc}
     */
    public static function singletons(): static
    {
        if (null !== static::$instance) {
            return static::$instance;
        }

        return static::$instance = new static();
    }

    /**
     * {@inheritDoc}
     */
    public function bind(mixed $name, mixed $service = null, bool $share = false, bool $coroutine = false): IContainer
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
     * {@inheritDoc}
     */
    public function instance(mixed $name, mixed $service = null, int $cid = self::NOT_COROUTINE_ID): IContainer
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
     * {@inheritDoc}
     */
    public function singleton(mixed $name, mixed $service = null, bool $coroutine = false): IContainer
    {
        return $this->bind($name, $service, true, $coroutine);
    }

    /**
     * {@inheritDoc}
     */
    public function alias(array|string $alias, null|array|string $value = null): IContainer
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
     * {@inheritDoc}
     */
    public function make(string $name, array $args = [], int $cid = self::DEFAULT_COROUTINE_ID): mixed
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
     * {@inheritDoc}
     *
     * @throws \InvalidArgumentException
     */
    public function call(array|callable|string $callback, array $args = []): mixed
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function callProviderBootstrap(Provider $provider): void
    {
        if (!method_exists($provider, 'bootstrap')) {
            return;
        }

        $this->call([$provider, 'bootstrap']);
    }

    /**
     * {@inheritDoc}
     */
    public function makeProvider(string $provider): Provider
    {
        return new $provider($this);
    }

    /**
     * {@inheritDoc}
     */
    public function register(Provider|string $provider): Provider
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
     * {@inheritDoc}
     */
    public function isBootstrap(): bool
    {
        return $this->isBootstrap;
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function setCoroutine(ICoroutine $coroutine): void
    {
        $this->coroutine = $coroutine;
    }

    /**
     * {@inheritDoc}
     */
    public function getCoroutine(): ?ICoroutine
    {
        return $this->coroutine;
    }

    /**
     * {@inheritDoc}
     */
    public function existsCoroutine(string $name, int $cid = self::DEFAULT_COROUTINE_ID): bool
    {
        $cid = $this->getCoroutineId($cid);

        return isset($this->coroutineInstances[$cid][$name]);
    }

    /**
     * {@inheritDoc}
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
     * {@inheritDoc}
     */
    public function serviceCoroutine(string $service): void
    {
        if (!$this->coroutine) {
            return;
        }

        $this->coroutine->addContext($service);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetExists(mixed $index): bool
    {
        return $this->exists($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet(mixed $index): mixed
    {
        return $this->make($index);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet(mixed $index, mixed $newval): void
    {
        $this->bind($index, $newval);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset(mixed $index): void
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
     */
    protected function inCroutineContext(mixed $instance): bool
    {
        if (!$this->coroutine) {
            return false;
        }

        if (is_object($instance)) {
            $instance = $instance::class;
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
     */
    protected function getInjectionObject(string $className, array $args = []): object|string
    {
        if (interface_exists($className)) {
            $e = sprintf('Interface %s cannot be normalize because not binded.', $className);

            throw new ContainerInvalidArgumentException($e);
        }

        if (!class_exists($className)) {
            return $className;
        }

        $args = $this->normalizeInjectionArgs($className, $args);

        return $this->newInstanceArgs($className, $args);
    }

    /**
     * 格式化依赖参数.
     *
     * @throws \Leevel\Di\ContainerInvalidArgumentException
     */
    protected function normalizeInjectionArgs(mixed $value, array $args): array
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
     * @throws \InvalidArgumentException
     */
    protected function parseInjection(mixed $injection, array $args = []): array
    {
        $result = [];
        $required = 0;
        $param = $this->parseReflection($injection);
        $validArgs = count($param);
        foreach ($param as $key => $item) {
            try {
                switch (true) {
                    case $argsClass = $this->parseParamClass($item):
                        $argsClass = (string) $argsClass;
                        if (isset($args[0]) && is_object($args[0]) && $args[0] instanceof $argsClass) {
                            $data = array_shift($args);
                        } elseif (array_key_exists($argsClass, $args)) {
                            $data = $args[$argsClass];
                        } elseif ($item->isDefaultValueAvailable()) {
                            $data = $item->getDefaultValue();
                        } else {
                            $data = $this->parseClassFromContainer($argsClass);
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
     */
    protected function parseParamClass(ReflectionParameter $param): bool|string
    {
        if (($reflectionType = $param->getType()) && false === $reflectionType->isBuiltin()) {
            return $reflectionType->getName();
        }

        return false;
    }

    /**
     * 从服务容器中获取解析反射参数类实例.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseClassFromContainer(string $argsClass): object
    {
        $itemMake = $this->make($argsClass);
        if (is_object($itemMake)) {
            return $itemMake;
        }

        $e = sprintf('Class or interface %s is register in container is not object.', $argsClass);

        throw new InvalidArgumentException($e);
    }

    /**
     * 不同的类型不同的反射.
     *
     * @throws \InvalidArgumentException
     */
    protected function parseReflection(mixed $injection): array
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
     */
    protected function newInstanceArgs(string $className, array $args): mixed
    {
        return (new ReflectionClass($className))->newInstanceArgs($args);
    }

    /**
     * 解析注册容器对象别名.
     */
    protected function parseAlias(array $name): array
    {
        return [key($name), current($name)];
    }
}
