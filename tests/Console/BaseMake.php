<?php

declare(strict_types=1);

namespace Tests\Console;

use Closure;
use Leevel\Console\Application;
use Leevel\Console\Command;
use Leevel\Di\Container;
use Leevel\Di\IContainer;
use Leevel\Kernel\App;
use Leevel\Kernel\IApp;
use Leevel\Router\IRouter;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * base make.
 */
trait BaseMake
{
    protected function runCommand(Command $command, array $inputs, ?Closure $call = null)
    {
        $container = Container::singletons();
        $container->clear();
        $container->instance('app', new App($container, __DIR__));
        $container->alias('app', [IApp::class, App::class]);
        $container->singleton(IContainer::class, $container);

        $application = new Application($container, '1.0');
        $application->setAutoExit(false);

        if ($call) {
            call_user_func($call, $container, $application);
        }

        $application->add($command);

        // 注册 router
        $router = $this->createMock(IRouter::class);
        $this->assertInstanceof(IRouter::class, $router);

        $router->method('getControllerDir')->willReturn('');
        $this->assertEquals('', $router->getControllerDir());

        $container->singleton('router', $router);
        $container->alias('router', IRouter::class);

        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();

        $application->run($input, $output);
        $result = $output->fetch();
        $container->clear();

        return $result;
    }
}
