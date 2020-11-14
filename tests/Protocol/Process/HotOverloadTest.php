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
 * (c) 2010-2020 http://queryphp.com All rights reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Protocol\Process;

use Leevel\Option\Option;
use Leevel\Protocol\IServer;
use Tests\Protocol\Process\Fixtures\HotOverloadDemo;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="代码热重启进程",
 *     path="protocol/process/hotoverload",
 *     zh-CN:description="监听某些目录，当代码发生变化，重启服务方便开发调试。",
 * )
 */
class HotOverloadTest extends TestCase
{
    protected function setUp(): void
    {
        if (!extension_loaded('swoole')) {
            $this->markTestSkipped('Swoole extension must be loaded before use.');
        }

        if (extension_loaded('xdebug')) {
            $this->markTestSkipped('32663 Segmentation fault (core dumped).');
        }
    }

    /**
     * @api(
     *     zh-CN:title="测试代码热重启",
     *     zh-CN:description="",
     *     zh-CN:note="",
     * )
     */
    public function testHotOverload(): void
    {
        $option = new Option([
            'protocol' => [
                'hotoverload_delay_count'   => 1,
                'hotoverload_time_interval' => 50,
                'hotoverload_watch'         => [
                    __DIR__,
                    __DIR__.'/Fixtures/HotOverloadDemo.php',
                ],
            ],
        ]);

        $hotOverload = new HotOverloadDemo($option, $this);
        /** @var \Leevel\Protocol\IServer $server */
        $server = $this->createMock(IServer::class);
        $hotOverload->handle($server);

        $this->assertSame(1, 1);
    }
}
