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
 * (c) 2010-2018 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests;

use PHPUnit\Framework\TestCase as TestCases;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

/**
 * phpunit 基础测试类.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.09
 *
 * @version 1.0
 */
abstract class TestCase extends TestCases
{
    protected function invokeTestMethod($classObj, string $method, array $args = [])
    {
        $method = $this->parseTestMethod($classObj, $method);

        if ($args) {
            return $this->invokeArgs($classObj, $args);
        }

        return $this->invoke($classObj);
    }

    protected function invokeTestStaticMethod($classOrObject, string $method, array $args = [])
    {
        $method = $this->parseTestMethod($classOrObject, $method);

        if ($args) {
            return $this->invokeArgs(null, $args);
        }

        return $this->invoke(null);
    }

    protected function getTestProperty($classOrObject, string $prop)
    {
        return $this->parseTestProperty($classOrObject, $prop)->
        getValue($classOrObject);
    }

    protected function setTestProperty($classOrObject, string $prop, $value)
    {
        $this->parseTestProperty($classOrObject, $prop)->

        setValue($value);
    }

    protected function parseTestProperty($classOrObject, string $prop): ReflectionProperty
    {
        $reflected = new ReflectionClass($classOrObject);
        $property = $reflected->getProperty($prop);
        $property->setAccessible(true);

        return $property;
    }

    protected function parseTestMethod($classOrObject, string $method): ReflectionMethod
    {
        $method = new ReflectionMethod($classOrObject, $method);
        $method->setAccessible(true);

        return $method;
    }

    protected function varExport(array $data)
    {
        file_put_contents(
            // dirname(__DIR__).'/logs/'.static::class.'.log',
            dirname(__DIR__).'/logs/trace.log',
            var_export($data, true)
        );

        return var_export($data, true);
    }
}
