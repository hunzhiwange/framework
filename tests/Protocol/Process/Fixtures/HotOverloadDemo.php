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

namespace Tests\Protocol\Process\Fixtures;

use Leevel\Option\IOption;
use Leevel\Protocol\IServer;
use Leevel\Protocol\Process\HotOverload;
use PHPUnit\Framework\TestCase;
use Swoole\Coroutine;

class HotOverloadDemo extends HotOverload
{
    protected $phpunit;

    public function __construct(IOption $option, TestCase $phpunit)
    {
        parent::__construct($option);
        $this->phpunit = $phpunit;
    }

    public function handle(IServer $server): void
    {
        Coroutine::create(function () use ($server) {
            $n = 0;
            while ($n < 10) {
                Coroutine::sleep($this->timeInterval / 1000);

                if (true === $this->serverNeedReload()) {
                    $this->reload($server);
                }

                if (3 === $n) {
                    file_put_contents($tmpFile = __DIR__.'/tmp.php', 'demo'.rand(0, 1000));
                    defer(function () use ($tmpFile) {
                        if (is_file($tmpFile)) {
                            unlink($tmpFile);
                        }
                    });
                }
                $n++;
            }
        });
    }

    protected function log(string $log): void
    {
        $this->phpunit->assertSame($log, 'The Swoole server is start reloading.');
    }
}
