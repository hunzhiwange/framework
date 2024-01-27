<?php

declare(strict_types=1);

namespace Tests\Server\Process\Fixtures;

use Leevel\Config\IConfig;
use Leevel\Server\IServer;
use Leevel\Server\Process\HotOverload;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

class HotOverloadDemo extends HotOverload
{
    protected $phpunit;

    public function __construct(IConfig $config, TestCase $phpunit)
    {
        parent::__construct($config);
        $this->phpunit = $phpunit;
    }

    public function handle(IServer $server): void
    {
        Coroutine::create(function () use ($server): void {
            $n = 0;
            while ($n < 10) {
                Coroutine::sleep($this->timeInterval / 1000);

                if (true === $this->serverNeedReload()) {
                    $this->reload($server);
                }

                if (3 === $n) {
                    file_put_contents($tmpFile = __DIR__.'/tmp.php', 'demo'.random_int(0, 1000));
                    defer(function () use ($tmpFile): void {
                        if (is_file($tmpFile)) {
                            unlink($tmpFile);
                        }
                    });
                }
                ++$n;
            }
        });
    }

    protected function log(string $log): void
    {
        $this->phpunit->assertSame($log, 'The Swoole server is start reloading.');
    }
}
