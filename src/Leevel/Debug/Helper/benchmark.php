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

namespace Leevel\Debug\Helper;

use Closure;
use Leevel\Debug\Dump;
use RuntimeException;

/**
 * 性能基准测试.
 *
 * @param array|int $time
 * @param array     ...$call
 * @codeCoverageIgnore
 */
function benchmark($time = 1, ...$call): void
{
    $prev = [];
    $normalPrint = false;

    if (is_array($time)) {
        switch (count($time)) {
            case 2:
                list($time, $normalPrint) = $time;

                break;
            default:
                throw new RuntimeException('Invalid argument `time`.');

                break;
        }
    }

    foreach ($call as $i => $c) {
        ob_start();
        $current = benchmark_call($c, $time);
        ob_end_clean();

        if (0 === $i) {
            $timeTrend = $memoryTrend = 0;
        } else {
            $timeTrend = $prev[0] ? round(($current[0] - $prev[0]) / $prev[0], 10) * 100 : 100;
            $memoryTrend = $prev[1] ? round(($current[1] - $prev[1]) / $prev[1], 10) * 100 : 100;
        }

        $data = [
            'Index'           => $i,
            'Time(s)'         => $current[0],
            'Memory(s)'       => $current[1],
            'Time trend(%)'   => $timeTrend,
            'Memory trend(%)' => $memoryTrend,
        ];

        if (true === $normalPrint) {
            var_dump($data);
        } else {
            Dump::dump($data);
        }

        $prev = $current;
    }
}

/**
 * 执行一个调用.
 *
 * @param \Closure $call
 * @param int      $time
 *
 * @return array
 * @codeCoverageIgnore
 */
function benchmark_call(Closure $call, int $time = 1): array
{
    $startTime = microtime(true);
    $startMemory = memory_get_usage();

    for ($i = 0; $i < $time; $i++) {
        $call();
    }

    $endTime = microtime(true);
    $endMemory = memory_get_usage();

    return [
        round($endTime - $startTime, 10),
        round($endMemory - $startMemory, 10),
    ];
}

class benchmark
{
}
