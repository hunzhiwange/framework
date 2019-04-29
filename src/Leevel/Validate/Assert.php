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
     * PHPUnit.
     *
     * @var \PHPUnit\Framework\TestCase
     */
    protected static $phpUnit;

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
     * @return bool|mixed
     */
    public function __call(string $method, array $args)
    {
        if (false === $this->all && $this->error) {
            return $this;
        }

        array_unshift($args, $this->value);

        // if (!array_key_exists(0, $args)) {
        //     throw new InvalidArgumentException('Missing the first argument.');
        // }
        //dump($args);
        if (count($args) >= 2 && is_string($args[array_key_last($args)])) {
            $message = array_pop($args);
        } else {
            $message = $this->message ?: 'No exception messsage specified.';
        }

        // 可选
        $optional = false;

        if (0 === strpos($method, 'optional')) {
            $optional = true;

            if (null === $args[0]) {
                self::countPhpUnit();

                return true;
            }

            $method = substr($method, 8);
        }

        // 多个，可支持 optionalMulti
        $multi = [];

        if (0 === stripos($method, 'multi')) {
            if (!is_array($args[0]) && !$args instanceof Traversable) {
                $e = sprintf('Invalid first argument for multi assert.');

                throw new InvalidArgumentException($e);
            }

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
        }

        // 验证
        try {
            $fn = '\\Leevel\\Validate\\Helper\\validate_'.un_camelize($method);

            if (!$multi) {
                $multi[] = $args;
            }

            foreach ($multi as $m) {
                if (false === fn($fn, ...$m)) {
                    if (false === $this->lazy) {
                        throw new AssertException($message);
                    }
                    $this->error[] = $message;

                    return $this;
                }

                self::countPhpUnit();
            }

            return $this;
        } catch (FunctionNotFoundException $th) {
            $e = sprintf('Method `%s` is not exits.', $method);

            throw new BadMethodCallException($e);
        }
    }

    /**
     * call.
     *
     * @param string $method
     * @param array  $args
     *
     * @return bool|mixed
     */
    public static function __callStatic(string $method, array $args)
    {
        if (!array_key_exists(0, $args)) {
            throw new InvalidArgumentException('Missing the first argument.');
        }

        if (3 > count($args) && is_string($args[array_key_last($args)])) {
            $message = array_pop($args);
        } else {
            $message = 'No exception messsage specified.';
        }

        // 可选
        $optional = false;

        if (0 === strpos($method, 'optional')) {
            $optional = true;

            if (null === $args[0]) {
                self::countPhpUnit();

                return true;
            }

            $method = substr($method, 8);
        }

        // 多个，可支持 optionalMulti
        $multi = [];

        if (0 === stripos($method, 'multi')) {
            if (!is_array($args[0]) && !$args instanceof Traversable) {
                $e = sprintf('Invalid first argument for multi assert.');

                throw new InvalidArgumentException($e);
            }

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
        }

        // 验证
        try {
            $fn = '\\Leevel\\Validate\\Helper\\validate_'.un_camelize($method);

            if (!$multi) {
                $multi[] = $args;
            }

            foreach ($multi as $m) {
                if (false === fn($fn, ...$m)) {
                    throw new AssertException($message);
                }

                self::countPhpUnit();
            }
        } catch (FunctionNotFoundException $th) {
            $e = sprintf('Method `%s` is not exits.', $method);

            throw new BadMethodCallException($e);
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
     * @return static
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
     * @return static
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
     * @return bool
     */
    public function flush(Closure $format = null): bool
    {
        $this->error;

        if ($this->error) {
            if ($format) {
                $e = $format($this->error);
            }

            throw new AssertException($e);
        }

        return true;
    }

    /**
     * 设置 PHPUnit.
     *
     * @param \PHPUnit\Framework\TestCase $phpUnit
     */
    public static function setPhpUnit(TestCase $phpUnit = null): void
    {
        self::$phpUnit = $phpUnit;
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

fns(un_camelize::class);
