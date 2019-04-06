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

namespace Leevel\Support;

use Closure;
use Error;

/**
 * 函数自动导入.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.04.05
 *
 * @version 1.0
 */
class Fn
{
    /**
     * 自动导入函数.
     *
     * @param \Closure|string $fn
     * @param array           $args
     *
     * @return mixed
     */
    public function __invoke($fn, ...$args)
    {
        $this->validate($fn);

        try {
            return $fn(...$args);
        } catch (Error $th) {
            $fnName = $this->normalizeFn($fn, $th);

            if ($this->match($fnName)) {
                return $fn(...$args);
            }

            throw $th;
        }
    }

    /**
     * 匹配函数.
     *
     * @param string $fn
     *
     * @return bool
     */
    protected function match(string $fn): bool
    {
        foreach (['Fn', 'Prefix', 'Index'] as $type) {
            if ($this->{'match'.$type}($fn)) {
                return true;
            }
        }

        return false;
    }

    /**
     * 校验类型.
     *
     * @param \Closure|string $fn
     */
    protected function validate($fn)
    {
        if (!is_string($fn) && !($fn instanceof Closure)) {
            $e = sprintf('Fn first args must be Closure or string.');

            throw new Error($e);
        }
    }

    /**
     * 整理函数名字.
     *
     * @param string $fn
     * @param \Error $th
     *
     * @return string
     */
    protected function normalizeFn(string $fn, Error $th): string
    {
        $message = $th->getMessage();
        $undefinedFn = 'Call to undefined function ';

        if (0 !== strpos($message, $undefinedFn)) {
            throw $th;
        }

        if (is_string($fn)) {
            return $fn;
        }

        return substr($message, strlen($undefinedFn), -2);
    }

    /**
     * 匹配一个函数一个文件.
     *
     * @param string $fn
     * @param string $cl
     *
     * @return bool
     */
    protected function matchFn(string $fn, string $cl = ''): bool
    {
        if (!$cl) {
            $cl = $fn;
        }

        class_exists($cl);

        return function_exists($fn);
    }

    /**
     * 匹配前缀分隔一组函数.
     *
     * @param string $fn
     *
     * @return bool
     */
    protected function matchPrefix(string $fn): bool
    {
        if (false === strpos($fn, '_')) {
            return false;
        }

        $fnPrefix = substr($fn, 0, strpos($fn, '_'));

        return $this->matchFn($fn, $fnPrefix);
    }

    /**
     * 匹配基于 index 索引.
     *
     * @param string $fn
     *
     * @return bool
     */
    protected function matchIndex(string $fn): bool
    {
        if (false === strpos($fn, '\\')) {
            return false;
        }

        $fnIndex = substr($fn, 0, strripos($fn, '\\')).'\\index';

        return $this->matchFn($fn, $fnIndex);
    }
}
