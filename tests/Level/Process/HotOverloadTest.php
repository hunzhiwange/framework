<?php

declare(strict_types=1);

namespace Tests\Level\Process;

use Leevel\Option\Option;
use Leevel\Level\IServer;
use Tests\Level\Process\Fixtures\HotOverloadDemo;
use Tests\TestCase;

/**
 * @api(
 *     zh-CN:title="代码热重启进程",
 *     path="level/process/hotoverload",
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
            'level' => [
                'hotoverload_delay_count'   => 1,
                'hotoverload_time_interval' => 50,
                'hotoverload_watch'         => [
                    __DIR__,
                    __DIR__.'/Fixtures/HotOverloadDemo.php',
                ],
            ],
        ]);

        $hotOverload = new HotOverloadDemo($option, $this);
        /** @var \Leevel\Level\IServer $server */
        $server = $this->createMock(IServer::class);
        $hotOverload->handle($server);

        $this->assertSame(1, 1);
    }
}
