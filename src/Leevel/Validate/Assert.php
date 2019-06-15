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

namespace Leevel\Validate;

use BadMethodCallException;
use Closure;
use InvalidArgumentException;
use Leevel\Support\FunctionNotFoundException;
use function Leevel\Support\Str\un_camelize;
use Leevel\Support\Str\un_camelize;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * 断言.
 *
 * 提供一套精简版本的断言方便业务中很愉快地使用。
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.04.27
 *
 * @version 1.0
 *
 * @see https://github.com/beberlei/assert 参考这里，断言和验证器复用一致的规则
 */
class Assert
{
    /**
     * 校验值
     *
     * @var mixed
     */
    protected $value;

    /**
     * 默认消息.
     *
     * @var string
     */
    protected $message;

    /**
     * 是否延后提示错误.
     *
     * @var bool
     */
    protected $lazy;

    /**
     * 是否验证所有.
     *
     * @var bool
     */
    protected $all;

    /**
     * 验证错误消息.
     *
     * @var array
     */
    protected $error = [];

    /**
     * PHPUnit.
     *
     * @var \PHPUnit\Framework\TestCase
     */
    protected static $phpUnit;

    /**
     * 构造函数.
     *
     * @param mixed       $value
     * @param null|string $message
     * @param bool        $lazy
     * @param bool        $all
     */
    public function __construct($value, ?string $message = null, bool $lazy = false, bool $all = true)
    {
        $this->value = $value;
        $this->message = $message;
        $this->lazy = $lazy;
        $this->all = $all;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \Leevel\Validate\AssertException
     *
     * @return bool|mixed
     */
    public function __call(string $method, array $args)
    {
        if (false === $this->all && $this->error) {
            return $this;
        }

        array_unshift($args, $this->value);

        if (false === self::validateAssert($method, $args)) {
            $message = self::normalizeMessage($args, $this->message);

            if (false === $this->lazy) {
                throw new AssertException($message);
            }

            $this->error[] = $message;
        }

        return $this;
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \Leevel\Validate\AssertException
     *
     * @return bool|mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        if (false === self::validateAssert($method, $args)) {
            $message = self::normalizeMessage($args);

            throw new AssertException($message);
        }
    }

    /**
     * 创建断言对象已支持链式表达式.
     *
     * @example
     * Assert::make($value)
     *     ->notEmpty()
     *     ->lessThan([7]);
     *
     * @param mixed       $value
     * @param null|string $message
     *
     * @return \Leevel\Validate\Assert
     */
    public static function make($value, ?string $message = null): self
    {
        return new static($value, $message);
    }

    /**
     * 创建断言对象延迟抛出错误.
     *
     * @example
     * Assert::lazy($value)
     *     ->notEmpty()
     *     ->lessThan([7])
     *     ->flush();
     *
     * @param mixed       $value
     * @param null|string $message
     * @param bool        $all
     *
     * @return \Leevel\Validate\Assert
     */
    public static function lazy($value, ?string $message = null, bool $all = true): self
    {
        return new static($value, $message, true, $all);
    }

    /**
     * 释放并抛出验证
     *
     * @param null|\Closure $format
     *
     * @throws \Leevel\Validate\AssertException
     *
     * @return bool
     */
    public function flush(Closure $format = null): bool
    {
        if ($this->error) {
            if (!$format) {
                $format = 'json_encode';
            }

            $e = $format($this->error);

            throw new AssertException($e);
        }

        return true;
    }

    /**
     * 设置 PHPUnit.
     *
     * @param null|\PHPUnit\Framework\TestCase $phpUnit
     */
    public static function setPhpUnit(TestCase $phpUnit = null): void
    {
        self::$phpUnit = $phpUnit;
    }

    /**
     * 校验断言
     *
     * @param string $method
     * @param array  $args
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    protected static function validateAssert(string $method, array $args): bool
    {
        if (!array_key_exists(0, $args)) {
            $e = 'Missing the first argument.';

            throw new InvalidArgumentException($e);
        }

        // 匹配可选
        if (true === $result = self::matchOptional($method, $args)) {
            return true;
        }

        list($method, $optional) = $result;

        // 匹配多个值，可支持 optionalMulti
        if (true === $result = self::matchMulti($method, $args, $optional)) {
            return true;
        }

        list($method, $multi) = $result;

        // 验证
        if (false === self::validateRule($method, $multi)) {
            return false;
        }

        return true;
    }

    /**
     * 参数校验和消息整理.
     *
     * @param array       $args
     * @param null|string $message
     *
     * @return string
     */
    protected static function normalizeMessage(array $args, ?string $message = null): string
    {
        if (count($args) >= 2 && is_string($args[array_key_last($args)])) {
            $message = array_pop($args);
        } else {
            $message = $message ?? 'No exception messsage specified.';
        }

        return $message;
    }

    /**
     * 匹配可选规则.
     *
     * @param string $method
     * @param array  $args
     *
     * @return array|bool
     */
    protected static function matchOptional(string $method, array $args)
    {
        if (0 !== strpos($method, 'optional')) {
            return [$method, false];
        }

        if (null === $args[0]) {
            self::countPhpUnit();

            return true;
        }

        $method = substr($method, 8);

        return [$method, true];
    }

    /**
     * 匹配多个值
     *
     * @param string $method
     * @param array  $args
     * @param bool   $optional
     *
     * @throws \InvalidArgumentException
     *
     * @return array|bool
     */
    protected static function matchMulti(string $method, array $args, bool $optional)
    {
        if (0 !== stripos($method, 'multi')) {
            return [$method, [$args]];
        }

        if (!is_array($args[0]) && !$args instanceof Traversable) {
            $e = sprintf('Invalid first argument for multi assert.');

            throw new InvalidArgumentException($e);
        }

        $multi = [];
        $argsSource = $args;

        foreach ($args[0] as $v) {
            if (null === $v && true === $optional) {
                continue;
            }

            $argsSource[0] = $v;
            $multi[] = $argsSource;
        }

        if (!$multi) {
            self::countPhpUnit();

            return true;
        }

        $method = substr($method, 5);

        return [$method, $multi];
    }

    /**
     * 校验规则.
     *
     * @param string $method
     * @param array  $multi
     *
     * @throws \BadMethodCallException
     *
     * @return bool
     */
    protected static function validateRule(string $method, array $multi): bool
    {
        try {
            $fn = __NAMESPACE__.'\\Helper\\validate_'.un_camelize($method);

            foreach ($multi as $m) {
                if (!function_exists($fn)) {
                    class_exists($fn);
                }

                if (false === $fn(...$m)) {
                    return false;
                }

                self::countPhpUnit();
            }
        } catch (FunctionNotFoundException $th) {
            $e = sprintf('Method `%s` is not exits.', $method);

            throw new BadMethodCallException($e);
        }

        return true;
    }

    /**
     * 记录 PHPUnit 断言数量.
     */
    protected static function countPhpUnit(): void
    {
        if (self::$phpUnit) {
            self::$phpUnit->assertSame(1, 1);
        }
    }
}

// import fn.
class_exists(un_camelize::class);
