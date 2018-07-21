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

namespace Leevel\Support\Debug;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

/**
 * 调试一个变量.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2017.04.05
 *
 * @version 1.0
 */
class Dump
{
    /**
     * 调试一个变量.
     *
     * @param mixed $var
     * @param bool  $simple
     */
    public static function dump($var, bool $simple = false)
    {
        static $dump, $varCloner;

        if (false === $simple && class_exists(CliDumper::class)) {
            // @codeCoverageIgnoreStart
            if (!$dump) {
                $dump = ('cli' === PHP_SAPI ? new CliDumper() : new HtmlDumper());
                $varCloner = new VarCloner();
            }

            $dump->dump($varCloner->cloneVar($var));
        // @codeCoverageIgnoreEnd
        } else {
            var_dump($var);
        }
    }
}
