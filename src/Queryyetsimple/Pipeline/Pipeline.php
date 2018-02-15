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
namespace Queryyetsimple\Pipeline;

use Closure;
use Generator;
use InvalidArgumentException;
use Queryyetsimple\Di\IContainer;

/**
 * 管道实现类
 *
 * @author Xiangmin Liu <635750556@qq.com>
 * @package $$
 * @since 2017.05.25
 * @version 1.0
 */
class Pipeline implements IPipeline
{

    /**
     * 容器
     *
     * @var \Queryyetsimple\Di\IContainer
     */
    protected $container;

    /**
     * 管道传递的对象
     *
     * @var array
     */
    protected $passed = [];

    /**
     * 管道中所有执行工序
     *
     * @var array
     */
    protected $stage = [];

    /**
     * 迭代器
     *
     * @var \Generator
     */
    protected $generator;

    /**
     * 创建一个管道
     *
     * @param \Queryyetsimple\Di\IContainer $container
     * @return void
     */
    public function __construct(IContainer $container)
    {
        $this->container = $container;
    }

    /**
     * 将传输对象传入管道
     *
     * @param mixed $passed
     * @return $this
     */
    public function send($passed)
    {
        $passed = is_array($passed) ? $passed : func_get_args();
        foreach ($passed as $item) {
            $this->passed[] = $item;
        }

        return $this;
    }

    /**
     * 设置管道中的执行工序
     *
     * @param dynamic|array $stage
     * @return $this
     */
    public function through($stage)
    {
        $stage = is_array($stage) ? $stage : func_get_args();
        foreach ($stage as $item) {
            $this->stage[] = $item;
        }

        return $this;
    }

    /**
     * 执行管道工序响应结果
     *
     * @param callable $end
     * @since 2018.01.03
     * @return mixed
     */
    public function then(callable $end = null)
    {
        $stage = $this->stage;
        if ($end) {
            $stage[] = $end;
        }
        $this->generator = $this->stageGenerator($stage);

        return $this->traverseGenerator(...$this->passed);
    }

    /**
     * 遍历迭代器
     *
     * @since 2018.01.03
     * @return mixed
     */
    protected function traverseGenerator() {
        if(! $this->generator->valid() || $this->generator->next() || ! $this->generator->valid()) {
           return;
        }

        $args = func_get_args();
        array_unshift($args, function() {
            return $this->traverseGenerator(...func_get_args());
        });

        return $this->generator->current()(...$args);
    }

    /**
     * 工序迭代器
     * 添加一个空的迭代器，第一次迭代 next 自动移除
     *
     * @param array $stage
     * @return \Generator
     */
    protected function stageGenerator(array $stage) {
        array_unshift($stage, null);
        
        foreach ($stage as $item) {
           yield $this->stageCallback($item);
        }
    }

    /**
     * 工序回调
     *
     * @param mixed $stages
     * @return null|callable
     */
    protected function stageCallback($stages)
    {   
        if (is_null($stages)) {
            return;
        }

        if (is_callable($stages)) {
            return $stages;
        } else {
            list($stage, $params) = $this->parse($stages);

            if (strpos($stage, '@') !== false) {
                list($stage, $method) = explode('@', $stage);
            } else {
                $method = 'handle';
            }

            if (($stage = $this->container->make($stage)) === false) {
                throw new InvalidArgumentException('Stage is invalid.');
            }

            return [
                $stage,
                $method
            ];
        }
    }

    /**
     * 解析工序
     *
     * @param string $stage
     * @return array
     */
    protected function parse(string $stage)
    {
        list($name, $args) = array_pad(explode(':', $stage, 2), 2, []);
        
        if (is_string($args)) {
            $args = explode(',', $args);
        }

        return [
            $name,
            $args
        ];
    }
}
