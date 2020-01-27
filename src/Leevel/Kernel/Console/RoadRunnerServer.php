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

namespace Leevel\Kernel\Console;

use Leevel\Console\Command;
use Leevel\Http\Request;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernel;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\PSR7Client;
use Spiral\RoadRunner\Worker;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Throwable;

/**
 * RoadRunner 的服务器端.
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
    protected string $name = 'rr:server';

    /**
     * 命令行描述.
     *
     * @var string
     */
    protected string $description = 'Start road runner server';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): void
    {
        ini_set('display_errors', 'stderr');

        $kernel = $app->container()->make(IKernel::class);
        $psr7 = $this->getPsr7();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psr7factory = new DiactorosFactory();

        while ($req = $psr7->acceptRequest()) {
            try {
                $symfonyRequest = $httpFoundationFactory->createRequest($req);
                $request = Request::createFromSymfonyRequest($symfonyRequest);
                $response = $kernel->handle($request);
                $kernel->terminate($request, $response);
                $psr7response = $psr7factory->createResponse($response);
                $psr7->respond($psr7response);
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string) $e);
            }
        }
    }

    /**
     * 取得 Psr 桥接.
     */
    protected function getPsr7(): PSR7Client
    {
        $relay = new StreamRelay(STDIN, STDOUT);

        return new PSR7Client(new Worker($relay));
    }

    /**
     * 命令参数.
     */
    protected function getArguments(): array
    {
        return [];
    }

    /**
     * 命令配置.
     */
    protected function getOptions(): array
    {
        return [];
    }
}
