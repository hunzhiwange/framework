<?php

declare(strict_types=1);

namespace Leevel\Console;

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * 运行 command.
 */
class RunCommand
{
    /**
     * 创建一个命令行运行器.
     */
    public function __construct(protected Application $application)
    {
        $application->setAutoExit(false);
    }

    /**
     * 运行一个命令.
     */
    public function handle(Command|string $command, array $inputs): string
    {
        $this->normalizeCommand($command);
        $input = new ArrayInput($inputs);
        $output = new BufferedOutput();
        $this->application->run($input, $output);

        return $output->fetch();
    }

    /**
     * 格式化一个命令行.
     */
    public function normalizeCommand(Command|string $command): ?SymfonyCommand
    {
        if (\is_string($command)) {
            return $this->application->normalizeCommand($command);
        }

        return $this->application->add($command);
    }
}
