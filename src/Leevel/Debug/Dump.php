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

use Spiral\Debug;

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
     * 调试 RoadRunner 变量.
     *
     * @param mixed $var
     * @param array ...$moreVars
     *
     * @return mixed
     */
    public static function dumpRoadRunner($var, ...$moreVars)
    {
        static $dumper;

        if (null === $dumper) {
            $dumper = new Debug\Dumper();
            $dumper->setRenderer(Debug\Dumper::ERROR_LOG, new Debug\Renderer\ConsoleRenderer());
        }

        $dumper->dump($var, Debug\Dumper::ERROR_LOG);

        foreach ($moreVars as $v) {
            $dumper->dump($v);
        }

        if (func_num_args() > 1) {
            array_unshift($moreVars, $var);

            return $moreVars;
        }

        return $var;
    }
}
