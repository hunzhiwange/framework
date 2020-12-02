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

use BadMethodCallException;

/**
 * 服务提供者.
 */
abstract class Provider
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 创建一个服务容器提供者实例.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
        $this->registerAlias();
    }

    /**
     * 实现魔术方法 __call.
     *
     * @throws \BadMethodCallException
     */
    public function __call(string $method, array $args): void
    {
        if ('bootstrap' === $method) {
            return;
        }

        $e = sprintf('Method %s is not exits.', $method);

        throw new BadMethodCallException($e);
    }

    /**
     * 注册服务.
     */
    abstract public function register(): void;

    /**
     * 注册服务别名.
     */
    public function registerAlias(): void
    {
        if (!static::isDeferred() && $providers = static::providers()) {
            $this->container->alias($providers);
        }
    }

    /**
     * 是否延迟载入.
     */
    public static function isDeferred(): bool
    {
        return false;
    }

    /**
     * 可用服务提供者.
     */
    public static function providers(): array
    {
        return [];
    }

    /**
     * 返回 IOC 容器.
     */
    public function container(): IContainer
    {
        return $this->container;
    }
}
