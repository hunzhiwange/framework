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

namespace Leevel\Pipeline;

use Closure;
use Generator;
use InvalidArgumentException;
use Leevel\Di\IContainer;

/**
 * 管道实现类.
 */
class Pipeline
{
    /**
     * IOC 容器.
     */
    protected IContainer $container;

    /**
     * 管道传递的对象.
     */
    protected array $passed = [];

    /**
     * 管道中所有执行工序.
     */
    protected array $stage = [];

    /**
     * 迭代器.
     */
    protected Generator $generator;

    /**
     * 创建一个管道.
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 将传输对象传入管道.
     */
    public function send(array $passed): self
    {
        foreach ($passed as $item) {
            $this->passed[] = $item;
        }

        return $this;
    }

    /**
     * 设置管道中的执行工序.
     */
    public function through(array $stage): self
    {
        foreach ($stage as $item) {
            $this->stage[] = $item;
        }

        return $this;
    }

    /**
     * 执行管道工序并返回响应结果.
     */
    public function then(?Closure $end = null): mixed
    {
        $stage = $this->stage;
        if ($end) {
            $stage[] = $end;
        }
        $this->generator = $this->stageGenerator($stage);

        return $this->traverseGenerator(...$this->passed);
    }

    /**
     * 遍历迭代器.
     */
    protected function traverseGenerator(...$args): mixed
    {
        $this->generator->next();
        if (!$this->generator->valid()) {
            return null;
        }

        array_unshift($args, function (...$args) {
            return $this->traverseGenerator(...$args);
        });

        $current = $this->generator->current();

        // Pipeline 内部创建 [stage, method, params]
        if (is_array($current) && 3 === count($current)) {
            $params = array_pop($current);
        } else {
            $params = [];
        }

        return $current(...$args, ...$params);
    }

    /**
     * 工序迭代器.
     *
     * - 添加一个空的迭代器，第一次迭代 next 自动移除.
     */
    protected function stageGenerator(array $stage): Generator
    {
        array_unshift($stage, null);
        foreach ($stage as $item) {
            yield $this->stageCallback($item);
        }
    }

    /**
     * 工序回调.
     *
     * @throws \InvalidArgumentException
     */
    protected function stageCallback(null|callable|string $stages): null|array|callable
    {
        if (null === $stages) {
            return null;
        }

        if (is_callable($stages)) {
            return $stages;
        }

        list($stage, $params) = $this->parse($stages);
        if (false !== strpos($stage, '@')) {
            list($stage, $method) = explode('@', $stage);
        } else {
            $method = 'handle';
        }

        if (!is_object($stage = $this->container->make($stage))) {
            throw new InvalidArgumentException('Stage is invalid.');
        }

        return [$stage, $method, $params];
    }

    /**
     * 解析工序.
     */
    protected function parse(string $name): array
    {
        list($name, $params) = array_pad(explode(':', $name, 2), 2, []);
        if (is_string($params)) {
            $params = explode(',', $params);
        }

        $params = array_map(function (string $item) {
            return ctype_digit($item) ? (int) $item :
                (is_numeric($item) ? (float) $item : $item);
        }, $params);

        return [$name, $params];
    }
}
