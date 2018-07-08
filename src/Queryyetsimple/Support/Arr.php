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

namespace Leevel\Support;

/**
 * 数组辅助函数.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.05.05
 *
 * @version 1.0
 */
class Arr
{
    /**
     * 数组数据格式化.
     *
     * @param mixed  $inputs
     * @param string $delimiter
     * @param bool   $allowedEmpty
     *
     * @return mixed
     */
    public static function normalize($inputs, $delimiter = ',', bool $allowedEmpty = false)
    {
        if (is_array($inputs) || is_string($inputs)) {
            if (!is_array($inputs)) {
                $inputs = explode($delimiter, $inputs);
            }

            $inputs = array_filter($inputs); // 过滤null

            if (true === $allowedEmpty) {
                return $inputs;
            }

            $inputs = array_map('trim', $inputs);

            return array_filter($inputs, 'strlen');
        }

        return $inputs;
    }

    /**
     * 数组合并支持 + 算法.
     *
     * @param array $option
     * @param bool  $recursion
     *
     * @return array
     */
    public static function merge(array $option, bool $recursion = true): array
    {
        $extend = [];

        foreach ($option as $key => $value) {
            if (0 === strpos($key, '+')) {
                $extend[ltrim($key, '+')] = $value;
                unset($option[$key]);
            }
        }

        foreach ($extend as $key => $value) {
            if (isset($option[$key]) &&
                is_array($option[$key]) &&
                is_array($value)) {
                $option[$key] = array_merge($option[$key], $value);
            } else {
                $option[$key] = $value;
            }
        }

        if (true === $recursion) {
            foreach ($option as $key => $value) {
                if (is_array($value)) {
                    $option[$key] = static::merge($value);
                }
            }
        }

        return $option;
    }
}
