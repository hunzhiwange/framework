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

namespace Leevel\Debug;

use Symfony\Component\VarDumper\VarDumper;

/**
 * 调试一个变量.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 * @codeCoverageIgnore
 */
class Dump
{
    /**
     * 调试变量.
     *
     * @param mixed $var
     * @param array $moreVars
     *
     * @return mixed
     */
    public static function dump($var, ...$moreVars)
    {
        VarDumper::dump($var);

        foreach ($moreVars as $var) {
            VarDumper::dump($var);
        }

        if (1 < func_num_args()) {
            return func_get_args();
        }

        return $var;
    }

    /**
     * 调试变量并中断.
     *
     * @param mixed $var
     * @param array $moreVars
     */
    public static function dumpDie($var, ...$moreVars)
    {
        static::dump($var, ...$moreVars);

        die;
    }

    /**
     * 调试栈信息.
     */
    public static function backtrace()
    {
        $result = [];

        foreach (debug_backtrace() as $k => $v) {
            if (isset($v['class']) && $v['function']) {
                $tmp = '\\'.$v['class'].'::'.$v['function'].'()';
            } else {
                $tmp = $v['function'].'()';
            }

            if (0 === strpos($tmp, '\\PHPUnit\\') || in_array($tmp, [
                'db()', '\Leevel::backtrace()',
                '\Leevel\Debug\Dump::backtrace()',
            ], true)) {
                continue;
            }

            $result[] = $tmp;
        }

        static::dump(implode(PHP_EOL, array_reverse($result)));

        die;
    }
}
