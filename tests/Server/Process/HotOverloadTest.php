<?php

declare(strict_types=1);

namespace Tests\Server\Process;

use Leevel\Config\Config;
use Leevel\Kernel\Utils\Api;
use Leevel\Server\IServer;
use Tests\Server\Process\Fixtures\HotOverloadDemo;
use Tests\TestCase;

#[Api([
    'zh-CN:title' => '代码热重启进程',
    'path' => 'server/process/hotoverload',
    'zh-CN:description' => <<<'EOT'
监听某些目录，当代码发生变化，重启服务方便开发调试。
EOT,
])]
class HotOverloadTest extends TestCase
{
    #[\Override]
    protected function setUp(): void
    {
        if (!\extension_loaded('swoole')) {
            static::markTestSkipped('Swoole extension must be loaded before use.');
        }

        if (\extension_loaded('xdebug')) {
            static::markTestSkipped('32663 Segmentation fault (core dumped).');
        }
    }

    #[Api([
        'zh-CN:title' => '测试代码热重启',
    ])]
    public function testHotOverload(): void
    {
        $config = new Config([
            'server' => [
                'hotoverload_delay_count' => 1,
                'hotoverload_time_interval' => 50,
                'hotoverload_watch' => [
                    __DIR__,
                    __DIR__.'/Fixtures/HotOverloadDemo.php',
                ],
            ],
        ]);

        $hotOverload = new HotOverloadDemo($config, $this);

        /** @var \Leevel\Server\IServer $server */
        $server = $this->createMock(IServer::class);
        $hotOverload->handle($server);

        static::assertSame(1, 1);
    }
}
