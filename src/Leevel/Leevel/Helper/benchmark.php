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

namespace Leevel\Leevel\Helper;

if (!function_exists('Leevel\\Leevel\\Helper\\dump')) {
    include_once __DIR__.'/dump.php';
}

/**
 * 性能基准测试.
 *
 * @param int   $time
 * @param array $call
 * @codeCoverageIgnore
 */
function benchmark(int $time = 1, ...$call): void
{
    $prev = [];

    foreach ($call as $i => $c) {
        ob_start();
        $current = benchmark_call($c, $time);
        ob_end_clean();

        if (0 === $i) {
            $timeTrend = $memoryTrend = 0;
        } else {
            $timeTrend = $current[0] ? round(($prev[0] - $current[0]) / $current[0], 10) * 100 : 100;
            $memoryTrend = $current[1] ? round(($prev[1] - $current[1]) / $current[1], 10) * 100 : 100;
        }

        dump([
            'Index'           => $i,
            'Time(s)'         => $current[0],
            'Memory(s)'       => $current[1],
            'Time trend(%)'   => $timeTrend,
            'Memory trend(%)' => $memoryTrend,
        ]);

        $prev = $current;
    }
}

/**
 * 执行一个调用.
 *
 * @param callable|closure|string $call
 * @param int                     $time
 *
 * @return array
 */
function benchmark_call($call, int $time = 1): array
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
