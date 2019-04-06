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

namespace Leevel\Leevel\Console;

use Leevel\Console\Command;
use Leevel\Http\Leevel2Psr;
use Leevel\Http\Psr2Leevel;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernel;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\PSR7Client;
use Spiral\RoadRunner\Worker;
use Throwable;

/**
 * RoadRunner 的服务器端.
 *
 * @author Xiangmin Liu <635750556@qq.com>
 *
 * @since 2019.03.11
 *
 * @version 1.0
 *
 * @see https://github.com/spiral/roadrunner
 * @codeCoverageIgnore
 */
class RoadRunnerServer extends Command
{
    /**
     * 命令名字.
     *
     * @var string
     */
    protected $name = 'rrserver';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected $description = 'Start road runner server.';

    /**
     * 响应命令.
     *
     * @param \Leevel\Kernel\IApp $app
     */
    public function handle(IApp $app): void
    {
        ini_set('display_errors', 'stderr');

        $kernel = $app->make(IKernel::class);
        list($psr7, $psr2Leevel, $leevel2Psr) = $this->getPsrBridge();

        while ($req = $psr7->acceptRequest()) {
            try {
                $request = $psr2Leevel->createRequest($req);
                $response = $kernel->handle($request);
                $psr7->respond($leevel2Psr->createResponse($response));
                $kernel->terminate($request, $response);
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string) $e);
            }
        }
    }

    /**
     * 取得 Psr 桥接.
     *
     * @return \Spiral\RoadRunner\PSR7Client
     */
    protected function getPsr7(): PSR7Client
    {
        $relay = new StreamRelay(STDIN, STDOUT);

        return new PSR7Client(new Worker($relay));
    }

    /**
     * 取得 Psr 桥接.
     *
     * @return array
     */
    protected function getPsrBridge(): array
    {
        return [
            $this->getPsr7(),
            new Psr2Leevel(),
            new Leevel2Psr(),
        ];
    }

    /**
     * 命令参数.
     *
     * @return array
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
     *
     * @return array
     */
    protected function getOptions(): array
    {
        return [];
    }
}
