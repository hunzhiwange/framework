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

namespace Leevel\Pipeline;

use Closure;
use Generator;
use InvalidArgumentException;
use Leevel\Di\IContainer;

/**
 * 管道实现类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.25
 *
 * @version 1.0
 */
class Pipeline implements IPipeline
{
    /**
     * 容器.
     *
     * @var \Leevel\Di\IContainer
     */
    protected IContainer $container;

    /**
     * 管道传递的对象
     *
     * @var array
     */
    protected array $passed = [];

    /**
     * 管道中所有执行工序.
     *
     * @var array
     */
    protected array $stage = [];

    /**
     * 迭代器.
     *
     * @var \Generator
     */
    protected Generator $generator;

    /**
     * 创建一个管道.
     *
     * @param \Leevel\Di\IContainer $container
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 将传输对象传入管道.
     *
     * @param array $passed
     *
     * @return $this
     */
    public function send(array $passed): IPipeline
    {
        foreach ($passed as $item) {
            $this->passed[] = $item;
        }

        return $this;
    }

    /**
     * 设置管道中的执行工序.
     *
     * @param array $stage
     *
     * @return $this
     */
    public function through(array $stage): IPipeline
    {
        foreach ($stage as $item) {
            $this->stage[] = $item;
        }

        return $this;
    }

    /**
     * 执行管道工序响应结果.
     *
     * @param \Closure $end
     *
     * @since 2018.01.03
     *
     * @return mixed
     */
    public function then(Closure $end = null)
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
     *
     * @since 2018.01.03
     *
     * @return mixed
     */
    protected function traverseGenerator(...$args)
    {
        if (!$this->generator->valid() ||
            $this->generator->next() ||
            !$this->generator->valid()) {
            return;
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
     * 工序迭代器
     * 添加一个空的迭代器，第一次迭代 next 自动移除.
     *
     * @param array $stage
     *
     * @return \Generator
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
     * @param mixed $stages
     *
     * @return null|callable
     */
    protected function stageCallback($stages)
    {
        if (null === $stages) {
            return;
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
     *
     * @param string $stage
     *
     * @return array
     */
    protected function parse(string $stage): array
    {
        list($name, $args) = array_pad(explode(':', $stage, 2), 2, []);

        if (is_string($args)) {
            $args = explode(',', $args);
        }

        $args = array_map(function (string $item) {
            return ctype_digit($item) ? (int) $item : $item;
        }, $args);

        return [$name, $args];
    }
}
