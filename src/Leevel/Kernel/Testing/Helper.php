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

namespace Leevel\Kernel\Testing;

use Closure;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * 助手方法.
 */
trait Helper
{
    /**
     * 执行方法.
     *
     * @param mixed $classObj
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
     * @param mixed $classOrObject
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
     * 获取反射对象属性值.
     *
     * @param mixed $classOrObject
     *
     * @return mixed
     */
    protected function getTestProperty($classOrObject, string $prop)
    {
        return $this
            ->parseTestProperty($classOrObject, $prop)
            ->getValue(is_object($classOrObject) ? $classOrObject : null);
    }

    /**
     * 设置反射对象属性值.
     *
     * @param mixed $classOrObject
     * @param mixed $value
     */
    protected function setTestProperty($classOrObject, string $prop, $value): void
    {
        $value = is_object($classOrObject) ? [$classOrObject, $value] : [$value];
        $this
            ->parseTestProperty($classOrObject, $prop)
            ->setValue(...$value);
    }

    /**
     * 分析对象反射属性.
     *
     * @param mixed $classOrObject
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
     * @param mixed $classOrObject
     */
    protected function parseTestMethod($classOrObject, string $method): ReflectionMethod
    {
        $method = new ReflectionMethod($classOrObject, $method);
        $method->setAccessible(true);

        return $method;
    }

    /**
     * 清理内容.
     */
    protected function normalizeContent(string $content): string
    {
        return str_replace([' ', "\t", "\n", "\r"], '', $content);
    }

    /**
     * 调试 JSON.
     */
    protected function varJson(array $data, ?int $id = null): string
    {
        $backtrace = debug_backtrace();
        if ('varJson' === ($method = $backtrace[1]['function'])) {
            $method = $backtrace[2]['function'];
        }
        $method .= $id;

        list($traceDir, $className) = $this->makeLogsDir();
        file_put_contents(
            $traceDir.'/'.sprintf('%s::%s.log', $className, $method),
            $result = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );

        return $result ?: '';
    }

    /**
     * 时间波动断言.
     *
     * - 程序可能在数秒不等的时间内执行，需要给定一个范围.
     *
     * @param array ...$timeRange
     */
    protected function assertTimeRange(string $data, ...$timeRange): void
    {
        $this->assertTrue(in_array($data, $timeRange, true));
    }

    /**
     * 断言真别名.
     */
    protected function assert(bool $data): void
    {
        $this->assertTrue($data);
    }

    /**
     * 读取缓存区数据.
     */
    protected function obGetContents(Closure $call): string
    {
        ob_start();
        $call();
        $contents = ob_get_contents();
        ob_end_clean();

        return $contents ?: '';
    }
}
