<?php

declare(strict_types=1);

namespace Tests\Console;

use Leevel\Console\Application;
use Leevel\Di\Container;
use Symfony\Component\Console\Application as SymfonyApplication;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * base command.
 */
trait BaseCommand
{
    protected function runCommand(Command $command, array $inputs, \Closure $call, array $extendCommands = [])
    {
        $container = Container::singletons();
        $container->clear();

        $application = new ApplicationForBaseCommand($container, '1.0');
        $application->setAutoExit(false);
        \call_user_func($call, $container, $application);
        $application->add($command);
        foreach ($extendCommands as $v) {
            $application->add($v);
        }

        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();

        $application->run($input, $output);
        $result = $output->fetch();
        $container->clear();

        return $result;
    }
}

class ApplicationForBaseCommand extends Application
{
    public function run(?InputInterface $input = null, ?OutputInterface $output = null): int
    {
        return SymfonyApplication::run($input, $output);
    }
}
