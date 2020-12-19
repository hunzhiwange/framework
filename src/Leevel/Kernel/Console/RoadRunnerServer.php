<?php

declare(strict_types=1);

namespace Leevel\Kernel\Console;

use Exception;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Leevel\Console\Command;
use Leevel\Http\Request;
use Leevel\Kernel\IApp;
use Leevel\Kernel\IKernel;
use Spiral\Goridge\StreamRelay;
use Spiral\RoadRunner\PSR7Client;
use Spiral\RoadRunner\Worker;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Throwable;

/**
 * RoadRunner.
 *
 * @see https://github.com/spiral/roadrunner
 * @codeCoverageIgnore
 */
class RoadRunnerServer extends Command
{
    /**
     * 命令名字.
    */
    protected string $name = 'rr:server';

    /**
     * 命令行描述.
    */
    protected string $description = 'Start road runner server';

    /**
     * 响应命令.
     */
    public function handle(IApp $app): int
    {
        $this->checkEnvironment();
        $this->setDisplayErrors();
        $kernel = $app->container()->make(IKernel::class);
        $psr7 = $this->getPsr7();
        $httpFoundationFactory = new HttpFoundationFactory();
        $psrHttpFactory = new PsrHttpFactory(
            new ServerRequestFactory(),
            new StreamFactory(),
            new UploadedFileFactory(),
            new ResponseFactory(),
        );

        while ($req = $psr7->acceptRequest()) {
            try {
                $symfonyRequest = $httpFoundationFactory->createRequest($req);
                $request = Request::createFromSymfonyRequest($symfonyRequest);
                $response = $kernel->handle($request);
                $psr7->respond($psrHttpFactory->createResponse($response));
                $kernel->terminate($request, $response);
            } catch (Throwable $e) {
                $psr7->getWorker()->error((string) $e);
            }
        }

        return 0;
    }

    /**
     * 校验环境.
     * 
     * @throws \Exception
     */
    protected function checkEnvironment(): void
    {
        if(!class_exists(Worker::class) ||
            !class_exists(HttpFoundationFactory::class) ||
            !class_exists(ResponseFactory::class)) {
            $message = 'Go RoadRunner needs the following packages'.PHP_EOL.
                'composer require spiral/roadrunner ^1.9.0'.PHP_EOL.
                'composer require spiral/dumper ^2.6.3.'.PHP_EOL.
                'composer require symfony/psr-http-message-bridge ^2.0';
            throw new Exception($message);
        }
    }

    /**
     * 设置显示错误为 stderr.
     */
    protected function setDisplayErrors(): void
    {
        ini_set('display_errors', 'stderr');
    }

    /**
     * 取得 Psr 桥接.
     */
    protected function getPsr7(): PSR7Client
    {
        $relay = new StreamRelay(STDIN, STDOUT);

        return new PSR7Client(new Worker($relay));
    }
}
