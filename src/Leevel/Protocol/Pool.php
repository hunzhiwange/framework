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

namespace Leevel\Protocol;

use InvalidArgumentException;
use Leevel\Di\IContainer;
use SplStack;

/**
 * 对象池.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.12.14
 *
 * @version 1.0
 */
class Pool implements IPool
{
    /**
     * IOC 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected $container;

    /**
     * 对象池.
     *
     * @var array
     */
    protected $pools = [];

    /**
     * 构造函数.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 获取一个对象.
     *
     * @param string $className
     * @param array  $args
     *
     * @return \Object
     */
    public function get(string $className, ...$args)
    {
        $this->valid($className);
        $className = $this->normalize($className);
        $pool = $this->pool($className);

        if ($pool->count()) {
            $obj = $pool->shift();

            if (is_callable([$obj, '__construct'])) {
                $obj->__construct(...$args);
            }

            if (is_callable([$obj, 'construct'])) {
                $obj->construct();
            }

            return $obj;
        }

        return $this->container->make($className, $args);
    }

    /**
     * 返还一个对象.
     *
     * @param \Object $obj
     */
    public function back($obj): void
    {
        if (method_exists($obj, 'destruct')) {
            $obj->destruct();
        }

        $className = $this->normalize(get_class($obj));
        $pool = $this->pool($className);
        $pool->push($obj);
    }

    /**
     * 获取对象栈.
     *
     * @param string $className
     *
     * @return \SplStack
     */
    public function pool(string $className): SplStack
    {
        $this->valid($className);
        $className = $this->normalize($className);
        $pool = $this->pools[$className] ?? null;

        if (null !== $pool) {
            return $pool;
        }

        return $this->pools[$className] = new SplStack();
    }

    /**
     * 获取对象池数据.
     *
     * @return array
     */
    public function getPools(): array
    {
        return $this->pools;
    }

    /**
     * 校验类是否存在.
     *
     * @param string $className
     */
    protected function valid(string $className): void
    {
        if (!class_exists($className)) {
            throw new InvalidArgumentException(sprintf('Class `%s` was not found.', $className));
        }
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
}
