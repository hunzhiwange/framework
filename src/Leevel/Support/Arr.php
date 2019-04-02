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

use InvalidArgumentException;

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
    public static function normalize($inputs, string $delimiter = ',', bool $allowedEmpty = false)
    {
        if (is_array($inputs) || is_string($inputs)) {
            if (!is_array($inputs)) {
                $inputs = explode($delimiter, $inputs);
            }

            $inputs = array_filter($inputs);

            if (true === $allowedEmpty) {
                return $inputs;
            }

            $inputs = array_map('trim', $inputs);

            return array_filter($inputs, 'strlen');
        }

        return $inputs;
    }

    /**
     * 返回白名单过滤后的数据.
     *
     * @param array $input
     * @param array $filter
     *
     * @return array
     */
    public static function only(array $input, array $filter): array
    {
        $result = [];

        foreach ($filter as $f) {
            $result[$f] = $input[$f] ?? null;
        }

        return $result;
    }

    /**
     * 返回黑名单排除后的数据.
     *
     * @param array $input
     * @param array $filter
     *
     * @return array
     */
    public static function except(array $input, array $filter): array
    {
        foreach ($filter as $f) {
            if (array_key_exists($f, $input)) {
                unset($input[$f]);
            }
        }

        return $input;
    }

    /**
     * 返回过滤后的数据.
     *
     * @param array $input
     * @param array $rules
     *
     * @return array
     */
    public static function filter(array $input, array $rules): array
    {
        foreach ($input as $k => &$v) {
            if (is_string($v)) {
                $v = trim($v);
            }

            if (isset($rules[$k])) {
                $rule = $rules[$k];

                if (!is_array($rule)) {
                    $e = sprintf('Rule of `%s` must be an array.', $k);

                    throw new InvalidArgumentException($e);
                }

                foreach ($rule as $r) {
                    if (!is_callable($r)) {
                        $e = sprintf('Rule item of `%s` must be a callback type.', $k);

                        throw new InvalidArgumentException($e);
                    }

                    $v = $r($v);
                }
            }
        }

        return $input;
    }
}
