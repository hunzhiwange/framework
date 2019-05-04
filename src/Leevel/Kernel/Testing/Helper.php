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

namespace Leevel\Kernel\Testing;

use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * 助手方法.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2018.08.26
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
trait Helper
{
    /**
     * 执行方法.
     *
     * @param mixed  $classObj
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function invokeTestMethod($classObj, string $method, array $args = [])
    {
        $method = $this->parseTestMethod($classObj, $method);

        if ($args) {
            return $method->invokeArgs($classObj, $args);
        }

        return $method->invoke($classObj);
    }

    /**
     * 执行静态方法.
     *
     * @param mixed  $classOrObject
     * @param string $method
     * @param array  $args
     *
     * @return mixed
     */
    protected function invokeTestStaticMethod($classOrObject, string $method, array $args = [])
    {
        $method = $this->parseTestMethod($classOrObject, $method);

        if ($args) {
            return $method->invokeArgs(null, $args);
        }

        return $method->invoke(null);
    }

    /**
     * 获取反射对象属性值
     *
     * @param mixed  $classOrObject
     * @param string $prop
     *
     * @return mixed
     */
    protected function getTestProperty($classOrObject, string $prop)
    {
        return $this->parseTestProperty($classOrObject, $prop)->
        getValue($classOrObject);
    }

    /**
     * 设置反射对象属性值
     *
     * @param mixed  $classOrObject
     * @param string $prop
     * @param mixed  $value
     */
    protected function setTestProperty($classOrObject, string $prop, $value)
    {
        $this->parseTestProperty($classOrObject, $prop)->

        setValue($value);
    }

    /**
     * 分析对象反射属性.
     *
     * @param mixed  $classOrObject
     * @param string $prop
     *
     * @return \ReflectionProperty
     */
    protected function parseTestProperty($classOrObject, string $prop): ReflectionProperty
    {
        $reflected = new ReflectionClass($classOrObject);
        $property = $reflected->getProperty($prop);
        $property->setAccessible(true);

        return $property;
    }

    /**
     * 分析对象反射方法.
     *
     * @param mixed  $classOrObject
     * @param string $method
     *
     * @return \ReflectionMethod
     */
    protected function parseTestMethod($classOrObject, string $method): ReflectionMethod
    {
        $method = new ReflectionMethod($classOrObject, $method);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * 清理内容.
     *
     * @param string $content
     *
     * @return string
     */
    protected function normalizeContent(string $content): string
    {
        return str_replace([' ', "\t", "\n", "\r"], '', $content);
    }

    /**
     * 调试 JSON.
     *
     * @param array $data
     * @param int   $id
     *
     * @return string
     */
    protected function varJson(array $data, ?int $id = null): string
    {
        $method = debug_backtrace()[1]['function'].$id;

        list($traceDir, $className) = $this->makeLogsDir();

        file_put_contents(
            $traceDir.'/'.sprintf('%s::%s.log', $className, $method),
            $result = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        return $result;
    }

    /**
     * 时间波动断言.
     * 程序可能在数秒不等的时间内执行，需要给定一个范围.
     *
     * @param string $data
     * @param array  $timeRange
     */
    protected function assertTimeRange(string $data, ...$timeRange): void
    {
        $this->assertTrue(in_array($data, $timeRange, true));
    }

    /**
     * 断言真别名.
     *
     * @param bool $data
     */
    protected function assert(bool $data): void
    {
        $this->assertTrue($data);
    }
}
